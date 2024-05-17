<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ScrapeProduct;
use App\Models\SystemProduct;
use App\Models\Log;
use App\Models\Setting;
use Orhanerday\OpenAi\OpenAi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Controllers\Api\BaseController;


class ScrapeProductController extends BaseController
{
    public function save(Request $request)
    {
        $products = $request->productList;
        $userId = $request->userId;

        $code = Str::random(5) . "-" . Str::random(5) . "-" . Str::random(5) . "-" . Str::random(5);

        if (empty ($userId)) {
            return response()->json(["message" => "Invalid user ID provided"], 400);
        }

        DB::beginTransaction();

        try {
            $systemProductResponse = $this->systemProduct($userId, $code, "userID");
            if ($systemProductResponse['status'] === 'error') {
                DB::rollBack();
                return response()->json(["message" => $systemProductResponse['message']], $systemProductResponse['code']);
            }

            $Ids = [];
            foreach ($products as $product) {
                $scrapeProductResponse = $this->saveScrapeProduct($product, $code);
                if ($scrapeProductResponse['status'] === 'error') {
                    DB::rollBack();
                    return response()->json(["message" => $scrapeProductResponse['message']], 500);
                }
                $Ids[] = $scrapeProductResponse['id'];
            }

            $setting = Setting::firstOrFail();
            
            $additionalData = ['is_image_compared' => $setting->is_image_compared, "reqFrom"=> "ScrapeProduct"];

            if($setting->model_type=="openAI_model"){
                $data = $this->gptresponse($userId, $Ids, $additionalData);
                if ($data['status'] === 'error') {
                    DB::rollBack();
                    return response()->json(["message" => $data['message']], 500);
                }
            }elseif($setting->model_type=="local_model"){
                $data = $this->handleLocalModel($userId, $Ids, $additionalData);
                if ($data['status'] === 'error') {
                    DB::rollBack();
                    return response()->json(["message" => $data['message']], 500);
                }
            }else{
                return response()->json(["message" => "something went wrong"], 500);
            }
            

            DB::commit();
            return response()->json(["message" => "Products saved and processed successfully", 'data' => $data['data'],'tabId'=>$request->tabId], 200);
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
            $scrapeproduct->price = $product['price'] ?? 0;
            $scrapeproduct->unit = $product['unit'] ?? "$";
            $scrapeproduct->asin = $product['asin'] ?? "";
            $scrapeproduct->priceUnit = $product['priceUnit'] ?? "0. $";
            $scrapeproduct->image = $product['image'] ?? '';
            $scrapeproduct->categories = $product['categories'] ?? '';
            $scrapeproduct->sizes = $product['sizes'] ?? '';
            $scrapeproduct->colorVariations = $product['colorVariations'] ?? [];
            $scrapeproduct->brandDetails = $product['brandDetails'] ?? [];
            $scrapeproduct->dimension = $product['dimension'] ?? [];
            $scrapeproduct->manufacturer = $product['manufacturer'] ?? [];
            $scrapeproduct->shippingCost = $product['shippingCost'] ?? "";
            $scrapeproduct->about_this_item = $product['about_this_item'] ?? "";
            $scrapeproduct->detailInfo = $product['detailInfo'] ?? [];
            $scrapeproduct->description = $product['description'] ?? "";
            $scrapeproduct->code = $code;
            $scrapeproduct->save();
            $createdId = $scrapeproduct->id;
            return ['status' => 'success', 'id' => $createdId];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Scrape product error:'.$e->getMessage()];
        }
    }

    public function testgptapi()
    {
        try {
            $setting = Setting::first();
            $open_ai = new OpenAi($setting->key);

            $content = json_encode([
                [
                    'type' => 'text',
                    'text' => 'What are in these images? Is there any difference between them?, provide me with a text like this if image match: {"match":"yes","reason":"why"} else text like this: {"match":"no","reason":"give short reason why"}',
                ],
                [
                    'type' => 'image_url',
                    'image_url' => [
                        'url' => 'https://m.media-amazon.com/images/I/71pCJAcjfmL._AC_SX466_.jpg',
                    ],
                ],
                [
                    'type' => 'image_url',
                    'image_url' => [
                        'url' => 'http://listingapp.netray.org:8898/images/7/S86T725PG8WBR___789ad5cd23304b73684f24c87d37486d-full.jpg',
                    ],
                ],
            ]);
            $chat = $open_ai->chat([
                "model" => 'gpt-4-vision-preview',
                "messages" => [
                    [
                        'role' => 'system',
                        'content' => "You are a helpful assistant designed to output JSON",
                    ],
                    [
                        'role' => 'user',
                        'content' => $content,
                    ],
                ],
                'temperature' => $setting->image_model_temperature,
                'max_tokens' => 300,
                "stream" => false,
            ]);

            $d = json_decode($chat);
            if($d->error){
                return response()->json(['status' => 'error', 'message' => 'Chatgpt error:'.$d->error->message]);
            }
            $response = $d->choices[0]->message->content;
            $data = $this->formatJsonContent($response);
            return response()->json(json_decode($data));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
