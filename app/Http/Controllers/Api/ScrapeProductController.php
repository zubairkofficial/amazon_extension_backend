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
use Illuminate\Support\Facades\Log as StorageLog;
use Illuminate\Support\Facades\Schema;
use App\Models\Option;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;


class ScrapeProductController extends Controller
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
            // $systemProductResponse = $this->dispatchSystemProduct($userId, $code);
            $systemProductResponse = $this->systemProduct($userId, $code);
            if ($systemProductResponse['status'] === 'error') {
                DB::rollBack();
                return response()->json(["message" => $systemProductResponse['message']], $systemProductResponse['code']);
            }

            $Ids = [];
            foreach ($products as $product) {
                $scrapeProductResponse = $this->saveScrapeProduct($product, $code, $userId);
                if ($scrapeProductResponse['status'] === 'error') {
                    DB::rollBack();
                    return response()->json(["message" => $scrapeProductResponse['message']], 500);
                }
                $Ids[] = $scrapeProductResponse['id'];
            }

            // $gptresponse = $this->dispatchGptResp($Ids, $userId);
            $gptresponse = $this->gptresponse($request, $Ids, $userId);
            if ($gptresponse['status'] === 'error') {
                DB::rollBack();
                return response()->json(["message" => $gptresponse['message']], 500);
            }

            DB::commit();
            return response()->json(["message" => "Products saved and processed successfully", 'data' => $gptresponse['data'],'tabId'=>$request->tabId], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["message" => "An unexpected error occurred", "error" => $e->getMessage()], 500);
        }
    }

    // protected function dispatchSystemProduct($userId, $code)
    // {
    //     try {
    //         dispatch(function () use ($userId, $code) {
    //             $this->systemProduct($userId, $code);
    //         });
    //         return ['status' => 'success'];
    //     } catch (\Exception $e) {
    //         StorageLog::error("An error occurred:" . $e->getMessage());
    //         return ['status' => 'error', 'message' => 'Failed to process system product', 'code' => 500];
    //     }
    // }

    protected function saveScrapeProduct($product, $code, $userId)
    {
        try {
            $scrapeproduct = new ScrapeProduct();
            $scrapeproduct->title = $product['title'] ?? "";
            $scrapeproduct->price = $product['price'] ?? 0;
            $scrapeproduct->unit = $product['unit'] ?? "$";
            $scrapeproduct->asin = $product['asin'] ?? "";
            $scrapeproduct->priceUnit = $product['priceUnit'] ?? "0. $";
            $scrapeproduct->image = $product['image'] ?? '';
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
            // StorageLog::error("An error occurred while saving scrape product: " . $e->getMessage());
            // return ['status' => 'error', 'message' => 'Failed to save scrape product'];
            return ['status' => 'error', 'message' => 'Scrape product error:'.$e->getMessage()];
        }
    }

    // protected function dispatchGptResp($Ids, $userId)
    // {
    //     try {

    //         dispatch(function () use ($Ids, $userId) {
    //             $this->gptresponse($Ids, $userId);
    //         });
    //         return ['status' => 'success'];
    //     } catch (\Exception $e) {
    //         StorageLog::error("An error occurred: line:114 disptachgptresp function" . $e->getMessage());
    //         return ['status' => 'error', 'message' => 'Failed to create Chatgpt response'];
    //     }
    // }

    private function substituteValues(string $prompt, ScrapeProduct $scrapeProduct, SystemProduct $systemProduct): string
    {
        $scrapeArguments = Schema::getColumnListing((new ScrapeProduct)->getTable());
        $systemArguments = Schema::getColumnListing((new SystemProduct)->getTable());

        foreach ($scrapeArguments as $scrapeArgument) {
            $value = $scrapeProduct->$scrapeArgument;
            // Check if the value is an array and convert it to a JSON string if it is
            if (is_array($value)) {
                $value = json_encode($value, JSON_PRETTY_PRINT);
            }
            $prompt = str_replace("{ scrape.$scrapeArgument }", $value, $prompt);
        }

        foreach ($systemArguments as $systemArgument) {
            $value = $systemProduct->$systemArgument;
            // Same check for system product arguments
            if (is_array($value)) {
                $value = json_encode($value, JSON_PRETTY_PRINT);
            }
            $prompt = str_replace("{ system.$systemArgument }", $value, $prompt);
        }

        return $prompt;
    }

    public function gptresponse(Request $request, $data, $userId)
    {
        try {
            foreach ($data as $id) {
                // $setting = Setting::first();
                $setting = Setting::firstOrFail();
                $scrapeProduct = ScrapeProduct::find($id);
                $systemProduct = SystemProduct::where('code', $scrapeProduct->code)->first();


                $content = $this->substituteValues($setting->product_prompt, $scrapeProduct, $systemProduct);
                // StorageLog::info($content);
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
                    'temperature' => 1,
                    // 'max_tokens' => 130,
                ]);

                $d = json_decode($chat);
                $summary = $d->choices[0]->message->content;
                // StorageLog::info($image_match);


                // $image_match = $this->gptVisionResponse($scrapeProduct, $systemProduct);
                // if ($image_match['status'] === 'error') {
                //     return response()->json(['status' => $image_match['status'], "message" => $image_match['message']], 500);
                // }


                // $image_match_data = $image_match['data'];
                // StorageLog::info($image_match_data->match);
                // StorageLog::info($image_match_data->reason);
                // $log->image_match = $image_match_data->match;
                // $log->image_match_reason = $image_match_data->reason;


                $log = new Log();
                $log->user_id = $userId;
                $log->asin = $scrapeProduct->asin;
                $log->prompt = $content;
                // $log->image_match = $image_match['data'];
                // $log->image_match = "Image not compared";
                $log->summary = $summary;
                $log->image_match = "Image not compared"; // Default value

                if ($setting->is_image_compared) {
                    $image_match = $this->gptVisionResponse($scrapeProduct, $systemProduct);
                    if ($image_match['status'] === 'error') {
                        return response()->json(['status' => $image_match['status'], "message" => $image_match['message']], 500);
                    }
                    $log->image_match = $image_match['data'];
                }

                if ($log->save()) {
                    ScrapeProduct::find($id)->delete();
                    SystemProduct::where('code', $scrapeProduct->code)->first()->delete();
                }
                ;
            }
            return ['status' => 'success', 'message' => 'Chatgpt Response Created Successfully', 'data' => $log];
        } catch (\Exception $e) {
            // StorageLog::error("An error occurred:  " . $e->getMessage());
            // return ['status' => 'error', 'message' => 'Failed to create Chatgpt response'];
            return ['status' => 'error', 'message' => 'Chatgpt error:'.$e->getMessage()];
        }
    }

    public function systemProduct($userId, $code)
    {
        try {

            $productPath = Option::where('key', 'product-url')->first()->value;
            // StorageLog::info('User id: ' . print_r($userId, true));
            $response = Http::get($productPath, [
                'userID' => $userId,
                'json' => true,
            ]);
            if ($response->successful()) {
                $productData = $response->json();
                // StorageLog::info('Array content: ' . print_r($productData, true));
                $systemProduct = new SystemProduct();
                $systemProduct->title = $productData['title'];
                $systemProduct->description = $productData['description'];
                $systemProduct->mpn = $productData['mpn'];
                $systemProduct->UPC = $productData['UPC'];
                $systemProduct->price = $productData['price'];
                $systemProduct->price_map = $productData['price_map'];
                $systemProduct->shipping = $productData['shipping'];
                $systemProduct->brand = $productData['brand'];
                $systemProduct->main_category = $productData['main_category'];
                $systemProduct->sub_category = $productData['sub_category'];
                $systemProduct->condition = $productData['condition'];
                $systemProduct->length = $productData['length'];
                $systemProduct->width = $productData['width'];
                $systemProduct->height = $productData['height'];
                $systemProduct->weight = $productData['weight'];
                $systemProduct->image = $productData['image'];
                $systemProduct->code = $code;
                $systemProduct->save();
            }
            return ['status' => 'success'];
        } catch (\Exception $e) {
            // StorageLog::error("An error occurred:" . $e->getMessage());
            // return ['status' => 'error', 'message' => 'Failed to process system product', 'code' => 500];
            return ['status' => 'error', 'message' => 'System Api error:'.$e->getMessage(), 'code' => 500];
        }
    }


    public function gptVisionResponse($scrapeProduct, $systemProduct)
    {
        try {
            $setting = Setting::first();
            $content = $this->substituteValues($setting->image_prompt, $scrapeProduct, $systemProduct);
            // StorageLog::info($content);
            $open_ai = new OpenAi($setting->key);

            $chat = $open_ai->chat([
                // "model" => 'gpt-4-vision-preview',
                "model" => $setting->image_model,
                "messages" => [
                    [
                        'role' => 'system',
                        // 'content' => "You are a helpful assistant designed to output JSON",
                        'content' => "You are a helpful assistant.",
                    ],
                    [
                        'role' => 'user',
                        'content' => $content,
                    ],
                ],
                'temperature' => 1,
                'max_tokens' => 300,
                "stream" => false,
            ]);


            $d = json_decode($chat);
            // return $d;
            $response = $d->choices[0]->message->content;
            $data = $this->formatJsonContent($response);
            // StorageLog::info(print_r($data));

            // return ['status' => 'success', 'data' => json_decode($data)];
            return ['status' => 'success', 'data' => $response];
        } catch (\Exception $e) {
            // StorageLog::info($e->getMessage());
            // return ['status' => 'error', 'message' => 'Failed to create Image compression response'];
            return ['status' => 'error', 'message' => 'Chatgpt Imgae compage error:'.$e->getMessage()];
        }
    }

    private function formatJsonContent(string $content): string
    {
        if (preg_match('/```json\n(.*?)\n```/s', $content, $matches)) {
            $jsonContent = $matches[1];

            $jsonContent = trim(preg_replace('/\s+/', ' ', $jsonContent));

            return $jsonContent;
        }

        return $content;
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
            // StorageLog::info($content);
            $chat = $open_ai->chat([
                "model" => 'gpt-4-vision-preview',
                "messages" => [
                    [
                        'role' => 'system',
                        'content' => "You are a helpful assistant designed to output JSON",
                        // 'content' => "You are a helpful assistant.",
                    ],
                    [
                        'role' => 'user',
                        'content' => $content,
                    ],
                ],
                'temperature' => 1,
                'max_tokens' => 300,
                "stream" => false,
            ]);

            $d = json_decode($chat);
            // return $d;
            $response = $d->choices[0]->message->content;
            $data = $this->formatJsonContent($response);
            return response()->json(json_decode($data));
        } catch (\Exception $e) {
            // StorageLog::info($e->getMessage());
            return $e->getMessage();
        }
    }
}
