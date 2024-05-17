<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ScrapeProduct;
use App\Models\SystemProduct;
use App\Models\Log;
use App\Models\Setting;
use App\Models\Option;
use Orhanerday\OpenAi\OpenAi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use App\Http\Controllers\Api\BaseController;

class ErpProductCompareController extends BaseController
{
    public function save(Request $request)
    {
        $product = $request->product;     
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

            $scrapeProductResponse = $this->saveErpProduct($product, $code);
            if ($scrapeProductResponse['status'] === 'error') {
                DB::rollBack();
                return response()->json(["message" => $scrapeProductResponse['message']]);
            }
            $Id = $scrapeProductResponse['id'];

            $additionalData = ['is_image_compared' => $request->is_image_compared, "reqFrom"=> "ErpProductCompare"];

            if($setting->model_type=="openAI_model"){
                $data = $this->gptresponse($productId, [$Id], $additionalData);
                if ($data['status'] === 'error') {
                    DB::rollBack();
                    return response()->json(["message" => $data['message']], 500);
                }
            }elseif($setting->model_type=="local_model"){
                $data = $this->handleLocalModel($productId, [$Id], $additionalData);
                if ($data['status'] === 'error') {
                    DB::rollBack();
                    return response()->json(["message" => $data['message']], 500);
                }
            }else{
                return response()->json(["message" => "something went wrong"], 500);
            }
            DB::commit();
            return response()->json(["message" => "Products processed successfully", 'data' => $data['data']], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["message" => "An unexpected error occurred", "error" => $e->getMessage()], 500);
        }
    }

    protected function saveErpProduct($product, $code)
    {
        try {
            $scrapeproduct = new ScrapeProduct();
            $scrapeproduct->title = $product['title'] ?? "";
            $scrapeproduct->price = $product['price'] ?? 0;
            $scrapeproduct->unit = $product['unit'] ?? "$";
            $scrapeproduct->asin = $product['asin'] ?? "";
            $scrapeproduct->priceUnit = $product['priceUnit'] ?? "0. $";
            $scrapeproduct->image = $product['image'] ?? '';
            $scrapeproduct->sizes = $product['sizes'] ?? ''; 
            $scrapeproduct->categories = $product['categories'] ?? '';
            $scrapeproduct->colorVariations = $product['colorVariations'] ?? [];
            $scrapeproduct->brandDetails = $product['brandDetails'] ?? [];
            $scrapeproduct->dimension = $product['dimension'] ?? [];
            $scrapeproduct->manufacturer = isset ($product['manufacturer']) ?? [];
            $scrapeproduct->shippingCost = $product['shippingCost'] ?? "";
            $scrapeproduct->about_this_item = $product['about_this_item'] ?? [];
            $scrapeproduct->detailInfo = $product['detailInfo'] ?? [];
            $scrapeproduct->description = $product['description'] ?? "";
            $scrapeproduct->code = $code;
            $scrapeproduct->save();

            return ['status' => 'success', 'id' => $scrapeproduct->id];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Scrape product error:'.$e->getMessage()];
        }
    }
}
