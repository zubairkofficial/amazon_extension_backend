<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Option;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use App\Models\ScrapeProduct;
use App\Models\SystemProduct;
use App\Models\Setting;
use Orhanerday\OpenAi\OpenAi;

class BaseController extends Controller
{
    protected function substituteValues(string $prompt, ScrapeProduct $scrapeProduct, SystemProduct $systemProduct): string
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
}
