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
        // Retrieve the 'tags' parameter from the request, ensuring it has a default value if not provided
        $tags = $request->input('tags', []); // Assuming 'tags' is expected as an array. Adjust the default value as necessary

        // Now pass both $url and $tags to scrapeData
        $data = $this->scraper->scrapeData($url, $tags);

        return response()->json($data);
    }
}
