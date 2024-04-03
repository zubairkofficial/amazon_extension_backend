<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\services\AmazonScraper;

class AmazonScraperController extends Controller
{
    public function scrapeAmazon(Request $request)
    {
        $apiToken = 'apify_api_dgIjmkd1P6JaG3gOCxxcQBJh50e2DD0M7CZU';
        $asins = $request->input('asin');
        $actor = 'ZhSGsaq9MHRnWtStl';

        $scraper = new AmazonScraper($apiToken, $asins, $actor);
        $data = $scraper->runApifyActor();

        return response()->json($data);
    }

}
