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

            $scrapeProductResponse = $this->saveErpProduct($product, $code, $productId);
            if ($scrapeProductResponse['status'] === 'error') {
                DB::rollBack();
                return response()->json(["message" => $scrapeProductResponse['message']]);
            }
            $Id = $scrapeProductResponse['id'];

            $gptresponse = $this->gptresponse($request, $Id, $productId);
            
            if ($gptresponse['status'] === 'error') {
                DB::rollBack();
                return response()->json(["message" => $gptresponse['message']]);
            }

            DB::commit();
            return response()->json(["message" => "Products processed successfully", 'data' => $gptresponse['data']], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["message" => "An unexpected error occurred", "error" => $e->getMessage()], 500);
        }
    }

    protected function saveErpProduct($product, $code, $productId)
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

    public function gptresponse(Request $request, $id, $productId)
    {
        try {
                $setting = Setting::firstOrFail();
                $scrapeProduct = ScrapeProduct::find($id);
                $systemProduct = SystemProduct::where('code', $scrapeProduct->code)->first();


                $content = $this->substituteValues($setting->product_prompt, $scrapeProduct, $systemProduct);
                $open_ai = new OpenAi($setting->key);

                $chat = $open_ai->chat([
                    "model" => $setting->model,
                    "messages" => [
                        [
                            'role' => 'system',
                            'content' => "You are a helpful assistant",
                        ],
                        [
                            'role' => 'user',
                            'content' => $content,
                        ],
                    ],
                    'temperature' => $setting->model_temperature,
                ]);

                $d = json_decode($chat);
                $summary = $d->choices[0]->message->content;

                $log = [
                            "summary" => $summary,
                            "image_match" => "Image not compared"
                        ];

                if ($request->is_image_compared) {
                    $image_match = $this->gptVisionResponse($scrapeProduct, $systemProduct);
                    if ($image_match['status'] === 'error') {
                        return response()->json(['status' => $image_match['status'], "message" => $image_match['message']], 500);
                    }
                    $log["image_match"] = $image_match['data'];
                }

                ScrapeProduct::find($id)->delete();
                SystemProduct::where('code', $scrapeProduct->code)->first()->delete();

            return ['status' => 'success', 'message' => 'Chatgpt Response Created Successfully', 'data' => $log];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Chatgpt error:'.$e->getMessage()];
        }
    }

}
