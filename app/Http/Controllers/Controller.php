<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
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

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

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
            // Sanitize and JSON-encode the value
            $value = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $prompt = str_replace("{ scrape.$scrapeArgument }", trim($value, '"'), $prompt);
        }

        foreach ($systemArguments as $systemArgument) {
            $value = $systemProduct->$systemArgument;
            if (is_array($value)) {
                $value = $this->formatArrayAsKeyValuePairs($value);
            }
            // Sanitize and JSON-encode the value
            $value = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $prompt = str_replace("{ system.$systemArgument }", trim($value, '"'), $prompt);
        }

        return $prompt;
    }

    protected function promptsubstituteValues(string $prompt, ScrapeProduct $scrapeProduct, SystemProduct $systemProduct): string
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
            $setting = Setting::with("imageCompare_model")->first();
            $open_ai = new OpenAi($setting->key);

            $content = $this->substituteValues($setting->imageCompare_model->json, $scrapeProduct, $systemProduct);

            if (json_decode($content) === null) {
                throw new \Exception('Invalid JSON after substitution: ' . json_last_error_msg());
            }

            $json = json_decode($content, true);
            $chat = $open_ai->chat($json);


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
        
        $startTime = microtime(true);
        try {
            $setting = Setting::with("openai_model")->firstOrFail();
            $logs = [];

            foreach ($productIds as $id) {
                $scrapeProduct = ScrapeProduct::find($id);
                $systemProduct = SystemProduct::where('code', $scrapeProduct->code)->first();
                // $prompt = $this->promptsubstituteValues($setting->openai_model->openai_prompt, $scrapeProduct, $systemProduct);
                $prompt = $this->promptsubstituteValues($setting->openai_model->json, $scrapeProduct, $systemProduct);
                $content = $this->substituteValues($setting->openai_model->json, $scrapeProduct, $systemProduct);

                if (json_decode($content) === null) {
                    throw new \Exception('Invalid JSON after substitution: ' . json_last_error_msg());
                }
    
                $open_ai = new OpenAi($setting->key);
    
                $json = json_decode($content, true);
    
                $chat = $open_ai->chat($json);

                $d = json_decode($chat);
                if (isset($d->error)) {
                    return ['status' => 'error', 'message' => 'Chatgpt error: ' . $d->error->message];
                }

                $summary = $d->choices[0]->message->content;

                $endTime = microtime(true); // End time
                $executionTime = $endTime - $startTime;

                if ($additionalData['reqFrom'] == "ScrapeProduct") {
                    $log = new Log();
                    $log->user_id = $userId;
                    $log->asin = $scrapeProduct->asin;
                    $log->prompt = $prompt;
                    $log->summary = $summary;
                    $log->fullsummary = $chat;
                    $log->execution_time = $executionTime;
                    $log->image_match = "Image not compared";
                } elseif ($additionalData['reqFrom'] == "ScrapeCompare") {
                    $log = [
                        "asin" => $scrapeProduct->asin,
                        "is_retried"=> $additionalData['is_retried'],
                        "summary" => $summary,
                        "fullsummary" => $chat,
                        "image_match" => "Image not compared",
                        'execution_time' => $executionTime 
                    ];
                } elseif ($additionalData['reqFrom'] == "ErpProductCompare") {
                    $log = [
                        "summary" => $summary,
                        "fullsummary" => $chat,
                        "image_match" => "Image not compared",
                        'execution_time' => $executionTime 
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
            $startTime = microtime(true);
            try {
                foreach ($productIds as $id) {
                    // Retrieve the API configuration by ID
                    $setting = Setting::with("local_model")->firstOrFail();
                    $scrapeProduct = ScrapeProduct::find($id);
                    $systemProduct = SystemProduct::where('code', $scrapeProduct->code)->first();

                    $type = $setting->local_model->type == 'completions' ? 'completions' : 'chat/completions';
                    // $prompt = $this->promptsubstituteValues($setting->local_model->prompt, $scrapeProduct, $systemProduct);
                    $prompt = $this->promptsubstituteValues($setting->local_model->json, $scrapeProduct, $systemProduct);
                    $content = $this->substituteValues($setting->local_model->json, $scrapeProduct, $systemProduct);

                    if (json_decode($content) === null) {
                        throw new \Exception('Invalid JSON after substitution: ' . json_last_error_msg());
                    }
        
                    $json = json_decode($content, true);
                    

                    // Make the HTTP request using Laravel's HTTP client
                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                    ])->post($setting->local_model->baseUrl . '/v1/' . $type, $json);
                    $respdata = $response->json();
                    $summary = $respdata['choices'][0]['message']['content'] ;


                    $endTime = microtime(true); // End time
                    $executionTime = $endTime - $startTime;

                    if ($additionalData['reqFrom'] == "ScrapeProduct") {
                        $log = new Log();
                        $log->user_id = $userId;
                        $log->asin = $scrapeProduct->asin;
                        $log->prompt = $prompt;
                        $log->summary = $summary;
                        $log->fullsummary = $response;
                        $log->execution_time = $executionTime;
                        $log->image_match = "Image not compared";
                        $log->save();
                    } elseif ($additionalData['reqFrom'] == "ScrapeCompare") {
                        $log = [
                            "asin" => $scrapeProduct->asin,
                            "is_retried"=> $additionalData['is_retried'],
                            "summary" => $summary,
                            "fullsummary" => $response,
                            "image_match" => "Image not compared",
                            'execution_time' => $executionTime 
                        ];
                    } elseif ($additionalData['reqFrom'] == "ErpProductCompare") {
                        $log = [
                            "summary" => $summary,
                            "fullsummary" => $response,
                            "image_match" => "Image not compared",
                            'execution_time' => $executionTime 
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

