<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Option;
use App\Models\Log;
use App\Models\LocalModel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use App\Models\ScrapeProduct;
use App\Models\SystemProduct;
use App\Models\Setting;
use Orhanerday\OpenAi\OpenAi;

class BaseController extends Controller
{
    // protected function substituteValues(string $prompt, ScrapeProduct $scrapeProduct, SystemProduct $systemProduct): string
    // {
    //     $scrapeArguments = Schema::getColumnListing((new ScrapeProduct)->getTable());
    //     $systemArguments = Schema::getColumnListing((new SystemProduct)->getTable());

    //     foreach ($scrapeArguments as $scrapeArgument) {
    //         $value = $scrapeProduct->$scrapeArgument;
    //         if (is_array($value)) {
    //             $value = json_encode($value, JSON_PRETTY_PRINT);
    //         }
    //         $prompt = str_replace("{ scrape.$scrapeArgument }", $value, $prompt);
    //     }

    //     foreach ($systemArguments as $systemArgument) {
    //         $value = $systemProduct->$systemArgument;
    //         if (is_array($value)) {
    //             $value = json_encode($value, JSON_PRETTY_PRINT);
    //         }
    //         $prompt = str_replace("{ system.$systemArgument }", $value, $prompt);
    //     }

    //     return $prompt;
    // }
    protected function substituteValues(string $prompt, ScrapeProduct $scrapeProduct, SystemProduct $systemProduct): string
    {
        $scrapeArguments = Schema::getColumnListing((new ScrapeProduct)->getTable());
        $systemArguments = Schema::getColumnListing((new SystemProduct)->getTable());

        foreach ($scrapeArguments as $scrapeArgument) {
            $value = $scrapeProduct->$scrapeArgument;
            if (is_array($value)) {
                $value = $this->formatArrayAsKeyValuePairs($value);
            }
            $prompt = str_replace("{ scrape.$scrapeArgument }", $value, $prompt);
        }

        foreach ($systemArguments as $systemArgument) {
            $value = $systemProduct->$systemArgument;
            if (is_array($value)) {
                $value = $this->formatArrayAsKeyValuePairs($value);
            }
            $prompt = str_replace("{ system.$systemArgument }", $value, $prompt);
        }

        return $prompt;
    }

    protected function formatArrayAsKeyValuePairs(array $array): string
    {
        $formatted = [];
        foreach ($array as $key => $value) {
            $key = str_replace('_', ' ', $key); // Replace underscores with spaces
            if (is_array($value)) {
                $value = json_encode($value); // Handle nested arrays if necessary
            }
            $formatted[] = "$key: $value";
        }
        return implode(",\n ", $formatted);
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

    protected function systemProduct($userId, $code,$payload)
    {
        try {

            $productPath = Option::where('key', 'product-url')->first()->value;
            $response = Http::get($productPath, [
                $payload => $userId,
                'json' => true,
            ]);
            if ($response->successful()) {
                $productData = $response->json();
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
            return ['status' => 'error', 'message' => 'System Api error:'.$e->getMessage(), 'code' => 500];
        }
    }

    protected function gptVisionResponse($scrapeProduct, $systemProduct)
    {
        try {
            $setting = Setting::first();
            $content = $this->substituteValues($setting->image_prompt, $scrapeProduct, $systemProduct);
            $open_ai = new OpenAi($setting->key);

            $chat = $open_ai->chat([
                "model" => $setting->image_model,
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
                'temperature' => $setting->image_model_temperature,
                'max_tokens' => 300,
                "stream" => false,
            ]);


            $d = json_decode($chat);
            if($d->error){
                return response()->json(['status' => 'error', 'message' => 'Chatgpt Imgae compage error:'.$d->error->message]);
            }
            $response = $d->choices[0]->message->content;
            $data = $this->formatJsonContent($response);
            return ['status' => 'success', 'data' => $response];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Chatgpt Imgae compage error:'.$e->getMessage()];
        }
    }

    protected function formatJsonContent(string $content): string
    {
        if (preg_match('/```json\n(.*?)\n```/s', $content, $matches)) {
            $jsonContent = $matches[1];

            $jsonContent = trim(preg_replace('/\s+/', ' ', $jsonContent));

            return $jsonContent;
        }

        return $content;
    }

    public function gptresponse($userId, $productIds, $additionalData = [])
    {
        try {
            $setting = Setting::firstOrFail();
            $logs = [];

            foreach ($productIds as $id) {
                $scrapeProduct = ScrapeProduct::find($id);
                $systemProduct = SystemProduct::where('code', $scrapeProduct->code)->first();

                $content = $this->substituteValues($setting->product_prompt, $scrapeProduct, $systemProduct);
                $open_ai = new OpenAi($setting->key);

                $chat = $open_ai->chat([
                    "model" => $setting->model,
                    "messages" => [
                        ['role' => 'system', 'content' => "You are a helpful assistant"],
                        ['role' => 'user', 'content' => $content],
                    ],
                    'temperature' => $setting->model_temperature,
                ]);

                $d = json_decode($chat);
                if (isset($d->error)) {
                    return ['status' => 'error', 'message' => 'Chatgpt error: ' . $d->error->message];
                }

                $summary = $d->choices[0]->message->content;

                if ($additionalData['reqFrom'] == "ScrapeProduct") {
                    $log = new Log();
                    $log->user_id = $userId;
                    $log->asin = $scrapeProduct->asin;
                    $log->prompt = $content;
                    $log->summary = $summary;
                    $log->image_match = "Image not compared";
                } elseif ($additionalData['reqFrom'] == "ScrapeCompare") {
                    $log = [
                        "asin" => $scrapeProduct->asin,
                        "is_retried"=> $additionalData['is_retried'],
                        "summary" => $summary,
                        "image_match" => "Image not compared"
                    ];
                } elseif ($additionalData['reqFrom'] == "ErpProductCompare") {
                    $log = [
                        "summary" => $summary,
                        "image_match" => "Image not compared"
                    ];
                }
                if (isset($additionalData['is_image_compared']) && $additionalData['is_image_compared']) {
                    $image_match = $this->gptVisionResponse($scrapeProduct, $systemProduct);
                    if ($image_match['status'] === 'error') {
                        return ['status' => $image_match['status'], "message" => $image_match['message']];
                    }
                    $log["image_match"] = $image_match['data'];
                }
                if ($additionalData['reqFrom'] == "ScrapeProduct") {
                    $log->save();
                }
                ScrapeProduct::find($id)->delete();
                SystemProduct::where('code', $scrapeProduct->code)->first()->delete();
            }

            return ['status' => 'success', 'message' => 'Chatgpt Response Created Successfully', 'data' => $log];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Chatgpt error: ' . $e->getMessage()];
        }
    }

    protected function handleLocalModel($userId, $productIds, $additionalData = [])
    {
        try {
            foreach ($productIds as $id) {
                // Retrieve the API configuration by ID
                $setting = Setting::firstOrFail();
                $localModel = LocalModel::findOrFail($setting->local_model_id);
                $scrapeProduct = ScrapeProduct::find($id);
                $systemProduct = SystemProduct::where('code', $scrapeProduct->code)->first();

                if($localModel->prompt){
                    $content = $this->substituteValues($localModel->prompt, $scrapeProduct, $systemProduct);
                }else{
                    $content = $this->substituteValues($setting->product_prompt, $scrapeProduct, $systemProduct);
                }
                // Prepare the data payload dynamically based on the type
                $data = [];
                $type = $localModel->type == 'completions' ? 'completions' : 'chat/completions';
                if($localModel->json){
                    $data = $localModel->json;
                    
                }
                elseif ($localModel->type == 'completions') {
                    $data['prompt'] = $content;
                    
                    if ($localModel->max_tokens) {
                        $data['max_tokens'] = $localModel->max_tokens;
                    }
                    if ($localModel->temp) {
                        $data['temperature'] = $localModel->temp;
                    }
                    if ($localModel->top_p) {
                        $data['top_p'] = $localModel->top_p;
                    }
                    if ($localModel->seed) {
                        $data['seed'] = $localModel->seed;
                    }
                } else {
                    $data['messages'] = [
                        ['role' => 'user', 'content' => $content]
                    ];
                    
                    if ($localModel->mode) {
                        $data['mode'] = $localModel->mode;
                    }
                    
                    if ($localModel->type == 'chat-completions') {
                        if ($localModel->instruction_template) {
                            $data['instruction_template'] = $localModel->instruction_template;
                        }
                    } elseif ($localModel->type == 'chat-completions-with-characters') {
                        if ($localModel->character) {
                            $data['character'] = $localModel->character;
                        }
                    }
                }


                // Make the HTTP request using Laravel's HTTP client
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->post($localModel->baseUrl . '/v1/' . $type, $data);
                $data = $response->json();
                $summary = $data['choices'][0]['message']['content'] ;

                if ($additionalData['reqFrom'] == "ScrapeProduct") {
                    $log = new Log();
                    $log->user_id = $userId;
                    $log->asin = $scrapeProduct->asin;
                    $log->prompt = $content;
                    $log->summary = $summary;
                    $log->image_match = "Image not compared";
                    $log->save();
                } elseif ($additionalData['reqFrom'] == "ScrapeCompare") {
                    $log = [
                        "asin" => $scrapeProduct->asin,
                        "is_retried"=> $additionalData['is_retried'],
                        "summary" => $summary,
                        "image_match" => "Image not compared"
                    ];
                } elseif ($additionalData['reqFrom'] == "ErpProductCompare") {
                    $log = [
                        "summary" => $summary,
                        "image_match" => "Image not compared"
                    ];
                }

                ScrapeProduct::find($id)->delete();
                SystemProduct::where('code', $scrapeProduct->code)->first()->delete();
            }   
                // Return the response from the API
                return ['status' => 'success', 'message' => 'Local Model Response Created Successfully', 'data' => $log];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Chatgpt error:'.$e->getMessage()];
        }
    }

}
