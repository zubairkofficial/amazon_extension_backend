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
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ErpProductCompareController extends Controller
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
            $systemProductResponse = $this->systemProduct($productId, $code);
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
            $scrapeproduct->colorVariations = $product['colorVariations'] ?? [];
            $scrapeproduct->brandDetails = $product['brandDetails'] ?? [];
            $scrapeproduct->dimension = $product['dimension'] ?? [];
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



    private function substituteValues(string $prompt, ScrapeProduct $scrapeProduct, SystemProduct $systemProduct): string
    {
        $scrapeArguments = Schema::getColumnListing((new ScrapeProduct)->getTable());
        $systemArguments = Schema::getColumnListing((new SystemProduct)->getTable());

        foreach ($scrapeArguments as $scrapeArgument) {
            $value = $scrapeProduct->$scrapeArgument;
            if (is_array($value)) {
                $value = json_encode($value, JSON_PRETTY_PRINT);
            }
            $prompt = str_replace("{ scrape.$scrapeArgument }", $value, $prompt);
        }

        foreach ($systemArguments as $systemArgument) {
            $value = $systemProduct->$systemArgument;
            if (is_array($value)) {
                $value = json_encode($value, JSON_PRETTY_PRINT);
            }
            $prompt = str_replace("{ system.$systemArgument }", $value, $prompt);
        }

        return $prompt;
    }

    public function gptresponse(Request $request, $id, $productId)
    {
        try {
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
                ]);

                $d = json_decode($chat);
                // StorageLog::info( print_r($d, true));
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

    public function systemProduct($productId, $code)
    {
        try {

            $productPath = Option::where('key', 'product-url')->first()->value;
            $response = Http::get($productPath, [
                'id' => $productId,
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
            $response = $d->choices[0]->message->content;
            $data = $this->formatJsonContent($response);
            return ['status' => 'success', 'data' => $response];
        } catch (\Exception $e) {
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
}
