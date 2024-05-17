<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ScrapeProduct;
use App\Models\Setting;
use App\Models\Option;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

            $scrapeProductResponse = $this->saveScrapeProduct($product, $code);
            if ($scrapeProductResponse['status'] === 'error') {
                DB::rollBack();
                return response()->json(["message" => $scrapeProductResponse['message']]);
            }
            $Id = $scrapeProductResponse['id'];
            $setting = Setting::firstOrFail();
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
}
