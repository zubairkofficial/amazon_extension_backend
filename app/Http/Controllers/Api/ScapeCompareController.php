<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ScrapeProduct;
use App\Models\Setting;
use App\Models\Option;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use App\Http\Controllers\Api\BaseController;

class ScapeCompareController extends BaseController
{   
    protected $fastAp;
    
    public function __construct(){
        $fastApiUrl = Option::where('key', 'fastapi-url')->first()->value;
        $this->fastAp=new Client([
            'base_uri' => $fastApiUrl,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);

    }

    public function save(Request $request)
    {
        $response = $this->fastAp->get("/scrape?asins={$request->asins}");
        if ($response->getStatusCode() == 200) {
            $products = json_decode($response->getBody()->getContents(), true);
        }else{
            return response()->json(["message" => "Product not found"], 404);
        }
        $is_retried=false;
        if(count($products)==0){
            $response = $this->fastAp->get("/proxyScrape?asins={$request->asins}");
            if ($response->getStatusCode() == 200) {
                $products = json_decode($response->getBody()->getContents(), true);
                $is_retried=true;
            }else{
                return response()->json(["message" => "Product not found"], 404);
            }
            if(count($products)==0){
                return response()->json(["message" => "No Data return from FastApi"], 404);
            }
        }
        

        $productId = $request->productId;

        $code = Str::random(5) . "-" . Str::random(5) . "-" . Str::random(5) . "-" . Str::random(5);

        if (empty ($productId)) {
            return response()->json(["message" => "Invalid Product ID provided"], 400);
        }

        DB::beginTransaction();

        try {
            $systemProductResponse = $this->systemProduct($productId, $code, "id");
            if ($systemProductResponse['status'] === 'error') {
                DB::rollBack();
                return response()->json(["message" => $systemProductResponse['message']], $systemProductResponse['code']);
            }

            $Ids = [];
            foreach ($products as $product) {
                $scrapeProductResponse = $this->saveScrapeProduct($product, $code);
                if ($scrapeProductResponse['status'] === 'error') {
                    DB::rollBack();
                    return response()->json(["message" => $scrapeProductResponse['message']]);
                }
                $Ids[] = $scrapeProductResponse['id'];
            }
            $setting = Setting::firstOrFail();
            $additionalData = ['is_image_compared' => $request->is_image_compared, 'is_retried' => $is_retried, "reqFrom"=> "ScrapeCompare"];
            if($setting->model_type=="openAI_model"){
                $data = $this->gptresponse($productId, $Ids, $additionalData);
                if ($data['status'] === 'error') {
                    DB::rollBack();
                    return response()->json(["message" => $data['message']], 500);
                }
            }elseif($setting->model_type=="local_model"){
                $data = $this->handleLocalModel($productId, $Ids, $additionalData);
                if ($data['status'] === 'error') {
                    DB::rollBack();
                    return response()->json(["message" => $data['message']], 500);
                }
            }else{
                return response()->json(["message" => "something went wrong"], 500);
            }

            DB::commit();
            return response()->json(["message" => "Products processed successfully", 'data' => $gptresponse['data']], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["message" => "An unexpected error occurred", "error" => $e->getMessage()], 500);
        }
    }

    protected function saveScrapeProduct($product, $code)
    {
        try {
            $scrapeproduct = new ScrapeProduct();
            $scrapeproduct->title = $product['title'] ?? "";
            $scrapeproduct->price = $product['product_price'] ?? 0;
            $scrapeproduct->unit = $product['price'] ?? "$";
            $scrapeproduct->asin = $product['asin'] ?? "";
            $scrapeproduct->priceUnit = $product['product_price'] ?? "0. $";
            $scrapeproduct->image = $product['landingImage'] ?? '';
            $scrapeproduct->categories = $product['categories'] ?? '';
            $scrapeproduct->sizes = $product['sizes'] ?? '';
            $scrapeproduct->colorVariations = $product['AvailableColors'] ?? [];
            $scrapeproduct->brandDetails = $product['brandDetails'] ?? [];
            $scrapeproduct->dimension = $product['dimension'] ?? [];
            $scrapeproduct->manufacturer = $product['manufacturer'] ?? [];
            $scrapeproduct->shippingCost = $product['shipping_cost'] ?? "";
            $scrapeproduct->about_this_item = $product['feature_bullets'] ?? [];
            $scrapeproduct->detailInfo = $product['prodDetails'] ?? [];
            $scrapeproduct->description = $product['description'] ?? "";
            $scrapeproduct->code = $code;
            $scrapeproduct->save();

            return ['status' => 'success', 'id' => $scrapeproduct->id];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Scrape product error:'.$e->getMessage()];
        }
    }

}
