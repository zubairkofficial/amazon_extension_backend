<?php

namespace App\Http\Controllers;

use App\Models\Option;
use Illuminate\Http\Request;
use App\Models\SystemProduct;
use Illuminate\Support\Facades\Http;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
        ]);

        $productPath = Option::where('key', 'product-url')->first()->value;

        $response = Http::get($productPath, [
            'userId' => $request->product_id,
            'json' => true,
        ]);

        // Check if the request was successful
        if ($response->successful()) {
            // Get the JSON response body
            $productData = $response->json();
            $ipaddress = $request->ip();
            $systemProduct = new SystemProduct();
            $systemProduct->product_id = $request->product_id;
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
            $systemProduct->ip_address = $ipaddress;
            $systemProduct->save();
            return response()->json(['message' => 'Webhook received and processed successfully'], 200);
        } else {
            return response()->json(['error' => 'Failed to fetch product data'], $response->status());
        }
    }
}
