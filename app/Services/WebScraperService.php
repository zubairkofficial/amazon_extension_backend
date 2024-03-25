<?php

namespace App\Services;

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\Selector;

class WebScraperService
{
    // public function scrapeData($url, $tags)
    // {
    //     $browser = new HttpBrowser(HttpClient::create());
    //     $crawler = $browser->request('GET', $url);
    //     $results = [];
    //     $allData = [];

    //     // Define a mapping of tags to their relevant attributes
    //     $tagAttributes = [
    //         'a' => 'href',
    //         'img' => 'src',
    //         // Add more tags and their attributes as needed
    //         // Textual content tags don't need explicit mapping here unless specific attributes are required
    //     ];

    //     $tags = is_array($tags) ? $tags : [$tags];

    //     foreach ($tags as $tag) {
    //         $attribute = $tagAttributes[$tag] ?? 'text';
    //         $results[$tag] = $crawler->filter($tag)->each(function (Crawler $node) use ($attribute) {
    //             // Fetch attribute or text based on the tag type
    //             return $attribute === 'text' ? $node->text() : $node->attr($attribute);
    //         });

    //         // Combine all tag data into a single array for a collective output
    //         $allData = array_merge($allData, $results[$tag]);
    //     }

    //     // Return both individual tag data and combined data
    //     return [
    //         'allData' => $allData,
    //         'tagsData' => $results,
    //     ];
    // }



    public function scrapeData($url, $tags)
{
    $browser = new HttpBrowser(HttpClient::create());
    // $crawler = $browser->request('GET', $url);
    $crawler = $browser->request('GET', 'https://www.amazon.com/BestOffice-Gaming-Executive-Headrest-Computer/dp/B07KJYY9BD/ref=sr_1_1_sspa?_encoding=UTF8&content-id=amzn1.sym.12129333-2117-4490-9c17-6d31baf0582a&dib=eyJ2IjoiMSJ9.sKNMB-JCQ3o4Cgylq0aL3R6xb--O7bPRscegvYC7X3pVuJJAhLKcp3AdVx35vnNxDfN3Q1v7ULVx-lELTJ8rccTAr7vyO5HXliq7tpFxgsdkcuyGDFMgqMQbNttZmuXhO1EI1u4adHEy8jTxk1JtuG2LsCip_dotZ-b8pb4m899lqiZctWo4bqq1H-xs2XJnkd9dFKLU3XkRsZz93SqrUai0Kuq0NZuw-qyauUUzHl7PGRd4MznuPv-CgqpRGVgpOnyTlgS6bClZHP698u6swe0TgylO0WXWH3VxW5qeiCQ.mbcgMzpuQDCghI8uNyv-NJ982LnGSHTo3961GJ1TLuE&dib_tag=se&keywords=gaming+chairs&pd_rd_r=1a176d3d-e717-4bec-a80d-f2b6f4f06454&pd_rd_w=AHsuY&pd_rd_wg=CXGsp&pf_rd_p=12129333-2117-4490-9c17-6d31baf0582a&pf_rd_r=A3JJ8ZMQA23NA83X1BF5&qid=1711345499&sr=8-1-spons&sp_csd=d2lkZ2V0TmFtZT1zcF9hdGY&psc=1');
    $dynamicSelectors = Selector::all();
    $productDetails = [];
    // After requesting the page
    $htmlContent = $crawler->html();
    \Log::info('Page HTML content:', ['html' => $htmlContent]);

    $crawler->filter('div.product-container')->each(function ($node) use ($dynamicSelectors, &$productDetails) {
        $product = [];
        foreach ($dynamicSelectors as $selector) {
            if ($selector['status'] === 'enable') {
                switch ($selector['name']) {
                    case 'title':
                    case 'price':
                    case 'priceUnit':
                    case 'asin':
                    case 'shippingcost':
                        $product[$selector['name']] = $this->scrapeContent($node, $selector['selector']);
                        break;
                    case 'image':
                    case 'description':
                        $product[$selector['name']] = $this->scrapeContentById($node, $selector['selector']);
                        break;
                    case 'about_this_item':
                        $product[$selector['name']] = $node->filter($selector['selector'])->each(function ($node) {
                            return trim($node->text());
                        });
                        break;
                    case 'colorVariationsContainer':
                        $product['colorVariations'] = $this->scrapeColorVariations($node, json_decode($selector['selector'], true));
                        break;
                    case 'detailTableRows':
                        $product['detailInfo'] = $this->scrapeProductDetails($node, json_decode($selector['selector'], true));
                        break;
                    case 'brandDetailsSelectors':
                        $product['brandDetails'] = $this->scrapeBrandDetails($node, json_decode($selector['selector'], true));
                        break;
                }
            }
        }
        $productDetails[] = $product;
    });

    // Assuming you want to return the product details along with a user ID
    // You might need to adjust this based on your actual requirements
    return response()->json([
        'productList' => $productDetails,
        'userId' => '4' // This should be dynamically determined based on your application's logic
    ]);
}


    private function scrapeContent($crawler, $selector)
    {
        try {
            return $crawler->filter($selector)->each(function ($node) {
                return trim($node->text());
            });
        } catch (\Exception $e) {
            return [];
        }
    }

    private function scrapeContentById($crawler, $id)
    {
        // This method might not be directly applicable as IDs are not directly supported for filtering in Goutte/Symfony DomCrawler.
        // Instead, this function demonstrates a workaround by filtering elements with a specific attribute (id).
        try {
            // return $crawler->filter("[id='$id']")->each(function ($node) {
            return $crawler->filter('#'.$id)->each(function ($node) {
                return trim($node->text());
            });
        } catch (\Exception $e) {
            return [];
        }
    }

    private function scrapeColorVariations($crawler, $selectors)
    {
        $colorVariations = [];
        try {
            $crawler->filter($selectors['colorVariationsContainer'])->each(function ($node) use (&$colorVariations, $selectors) {
                $colorName = $node->attr($selectors['colorName']);
                $imageUrl = $node->filter($selectors['colorImageUrl'])->attr('src');
                $price = $node->filter($selectors['colorPrice'])->text();

                $colorVariations[] = [
                    'colorName' => trim($colorName),
                    'imageUrl' => trim($imageUrl),
                    'price' => trim($price),
                ];
            });
        } catch (\Exception $e) {
            // Handle exceptions or log errors as needed
        }

        return $colorVariations;
    }

    private function scrapeProductDetails($crawler, $selectors)
    {
        $details = [];
        try {
            $crawler->filter($selectors['detailTableRows'])->each(function ($row) use (&$details, $selectors) {
                $key = $row->filter($selectors['detailKey'])->text();
                $value = $row->filter($selectors['detailValue'])->text();

                $details[trim($key)] = trim($value);
            });
        } catch (\Exception $e) {
            // Handle exceptions or log errors as needed
        }

        return $details;
    }

    private function scrapeBrandDetails($crawler, $selectors)
    {
        $brandDetails = [];
        try {
            $crawler->filter($selectors['detailRows'])->each(function ($row) use (&$brandDetails, $selectors) {
                $detailName = $row->filter($selectors['detailName'])->text();
                $detailValue = $row->filter($selectors['detailValue'])->text();

                $brandDetails[trim($detailName)] = trim($detailValue);
            });
        } catch (\Exception $e) {
            // Handle exceptions or log errors as needed
        }

        return $brandDetails;
    }

}
