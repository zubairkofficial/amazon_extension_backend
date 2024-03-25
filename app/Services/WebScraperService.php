<?php

namespace App\Services;

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\Selector;

class WebScraperService
{
    public function scrapeData($url)
    {
        $browser = new HttpBrowser(HttpClient::create());
        $crawler = $browser->request('GET', $url);
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

        return ['productList' => $productDetails ];
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
