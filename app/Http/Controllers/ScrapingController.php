<?php

namespace App\Http\Controllers;

use App\Services\WebScraperService;
use Illuminate\Http\Request;

class ScrapingController extends Controller
{
    protected $scraper;

    public function __construct(WebScraperService $scraper)
    {
        $this->scraper = $scraper;
    }

    public function scrape(Request $request)
    {
        $url = 'https://www.amazon.com/dp/'.$request->input('asin');
        $data = $this->scraper->scrapeData($url);

        return response()->json($data);
    }
}
