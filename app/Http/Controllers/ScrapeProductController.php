<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScrapeProduct;
use App\Models\SystemProduct;
use App\Models\Log;
use App\Models\GptKey;
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
        $code = $this->generateCode();

        if (empty($userId)) {
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
            $gptresponse = $this->gptresponse($Ids, $userId);
            if ($gptresponse['status'] === 'error') {
                DB::rollBack();
                return response()->json(["message" => $gptresponse['message']], 500);
            }

            DB::commit();
            return response()->json(["message" => "Products saved and processed successfully"], 200);
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
            $scrapeproduct->title = isset($product['title']) ? $product['title'] : "";
            $scrapeproduct->price = isset($product['price']) ? $product['price'] : 0;
            $scrapeproduct->unit = isset($product['unit']) ? $product['unit'] : "$";
            $scrapeproduct->asin = isset($product['asin']) ? $product['asin'] : "";
            $scrapeproduct->priceUnit = isset($product['priceUnit']) ? $product['priceUnit'] : "0. $";
            $scrapeproduct->image = isset($product['image']) ? $product['image'] : [];
            $scrapeproduct->colorVariations = isset($product['colorVariations']) ? $product['colorVariations'] : [];
            $scrapeproduct->dimension = isset($product['dimension']) ? $product['dimension'] : [];
            $scrapeproduct->shippingCost = isset($product['shippingCost']) ? $product['shippingCost'] : "";
            $scrapeproduct->about_this_item = isset($product['about_this_item']) ? $product['about_this_item'] : "";
            $scrapeproduct->detailInfo = isset($product['detailInfo']) ? $product['detailInfo'] : [];
            $scrapeproduct->description = isset($product['description']) ? $product['description'] : "";
            $scrapeproduct->code = $code;
            $scrapeproduct->save();
            $createdId = $scrapeproduct->id;
            return ['status' => 'success', 'id' => $createdId];
        } catch (\Exception $e) {
            // StorageLog::error("An error occurred: line:98 " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Failed to save scrape product'];
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
    //         StorageLog::error("An error occurred: line:112 disptachgptresp function" . $e->getMessage());
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
                $value = json_encode($value);
            }
            $prompt = str_replace("{ scrape.$scrapeArgument }", $value, $prompt);
        }

        foreach ($systemArguments as $systemArgument) {
            $value = $systemProduct->$systemArgument;
            // Same check for system product arguments
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $prompt = str_replace("{ system.$systemArgument }", $value, $prompt);
        }

        return $prompt;
    }

    public function gptresponse($data, $userId)
    {
        try {
            foreach ($data as $id) {
                $gptKey = GptKey::first();
                $scrapeProduct = ScrapeProduct::find($id);
                $systemProduct = SystemProduct::where('code', $scrapeProduct->code)->first();

                $content = $this->substituteValues($gptKey->product_prompt, $scrapeProduct, $systemProduct);
                // StorageLog::info($content);
                $open_ai = new OpenAi($gptKey->key);

                $chat = $open_ai->chat([
                    "model" => $gptKey->model,
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
                ]);

                $d = json_decode($chat);
                $summary = $d->choices[0]->message->content;
                // $image_match = $this->gptVisionResponse($scrapeProduct, $systemProduct);
                // StorageLog::info($image_match);
                // if ($image_match['status'] === 'error') {
                //     return response()->json(['status' => $image_match['status'], "message" => $image_match['message'], 500]);
                // }

                $log = new Log();
                $log->user_id = $userId;
                $log->asin = $scrapeProduct->asin;
                $log->prompt = $content;
                $log->summary = $summary;
                if ($log->save()) {
                    ScrapeProduct::find($id)->delete();
                    SystemProduct::where('code', $scrapeProduct->code)->first()->delete();
                }
            }
            return ['status' => 'success', 'message' => 'Chatgpt Response Created Successfully'];
        } catch (\Exception $e) {
            StorageLog::error("An error occurred:  " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Failed to create Chatgpt response'];
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
            StorageLog::error("An error occurred:" . $e->getMessage());
            return ['status' => 'error', 'message' => 'Failed to process system product', 'code' => 500];
        }
    }

    public function generateCode()
    {
        $part1 = Str::random(5);
        $part2 = Str::random(5);
        $part3 = Str::random(5);
        $part4 = Str::random(5);
        $code = $part1 . "-" . $part2 . "-" . $part3 . "-" . $part4;
        return $code;
    }
    public function gptVisionResponse($scrapeProduct, $systemProduct)
    {
        try {
            $gptKey = GptKey::first();
            // $content ="You will help to compare these images first object is array of images string and second object is string \n" . $scrapeProduct->imageUrls .
            //     " \n " . $systemProduct->image . "If you provide me with a text like this if image match:'yes' else text like this:'no'";
            $content = $this->substituteValues($gptKey->image_prompt, $scrapeProduct, $systemProduct);
            // StorageLog::info($content);
            $open_ai = new OpenAi($gptKey->key);

            $chat = $open_ai->chat([
                "model" => 'gpt-4-vision-preview',
                "messages" => [
                    [
                        'role' => 'system',
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
            $data = json_decode($this->formatJsonContent($response));
            // StorageLog::info(print_r($data));
            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            // StorageLog::info($e->getMessage());
            return ['status' => 'error', 'message' => 'Failed to create Image compression response'];
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
            $gptKey = GptKey::first();
            $open_ai = new OpenAi($gptKey->key);


            $content = json_encode([
                [
                    'type' => 'text',
                    'text' => 'You will help to compare these images, provide me with a text like this if image match: {"match":"yes","reason":"why"} else text like this: {"match":"no","reason":"give short reason why"}',
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
            StorageLog::info($e->getMessage());
            return $e->getMessage();
        }
    }
}
