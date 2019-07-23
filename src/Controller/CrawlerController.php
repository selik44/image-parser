<?php

namespace App\Controller;

use mysql_xdevapi\Exception;
use React\EventLoop\Factory;
use React\HttpClient\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DomCrawler;
use Clue\React\Buzz\Browser;
//use GuzzleHttp\Client;


class CrawlerController extends AbstractController
{

    protected $originalUrl;
    protected $maxDepth;
    protected $response;
    protected $client;

    /**
     * CrawlerController constructor.
     * @param string $url
     * @param int $depth
     */
    public function __construct(string $url = '', int $depth = 5)
    {
        $this->originalUrl = $url;
        $this->maxDepth = $depth;
        $this->response = array();
//        $this->client = new Client();
    }

    /**
     * @Route("/crawler", name="app_crawler")
     */
    public function index($parser = null, $link = null)
    {
        $links = [];
        $links_diff = [];
        $test = $this->crawlerAction('http://gadget-it.ru/', $links, $links_diff);
        dd($test);
        $link = 'http://gadget-it.ru/';
// Get html remote text.
        $html = file_get_contents($link);

// Create new instance for parser.
        $crawler = new DomCrawler\Crawler(null, $link);
        $crawler->addHtmlContent($html, 'UTF-8');

// Get title text.
        $links = $crawler->filter('a')->links();
        foreach ($links as $link) {
//            explode('/','https://phpprofi.ru/blogs/post/101')) - 2
            var_dump($link->getUri());
            $html = file_get_contents($link->getUri());
            $crawler = new DomCrawler\Crawler(null, $link->getUri());
            $crawler->addHtmlContent($html, 'UTF-8');
            $links = $crawler->filter('a')->links();
            foreach ($links as $link) {
                var_dump($link->getUri());
            }
//            $links = $crawler->filter('a')->links();

//            dd($link->getUri());
        }
        $title = $crawler->filter('img')->count();

// If exist settings for teaser.
        if (!empty(trim($parser->settings->teaser))) {
            $teaser = $crawler->filter($parser->settings->teaser)->text();
        }

// Get images from page.
        $images = $crawler->filter($parser->settings->image)->each(function (Crawler $node, $i) {
            return $node->image()->getUri();
        });

// Get body text.
        $bodies = $crawler->filter($parser->settings->body)->each(function (Crawler $node, $i) {
            return $node->html();
        });

        $content = [
            'link' => $link,
            'title' => $title,
            'images' => $images,
            'teaser' => strip_tags($teaser),
            'body' => $body
        ];

        return $content;
    }

    public function crawlerAction($link, &$all_links, &$parsedLinks)
    {

        try {
            $context = stream_context_create(
                array(
                    'http' => array(
                        'follow_location' => false
                    )
                )
            );
            if (!is_null($link)) {
                $html = file_get_contents($link, false, $context);
                if (substr($http_response_header[0], 9, 3) == 200) {

                    $crawler = new DomCrawler\Crawler(null, $link);
                    $crawler->addHtmlContent($html, 'UTF-8');
                    $links_count = $crawler->filter('a')->count();

                    $all_links = [];

                    if ($links_count > 0) {
//                        $all_links = $crawler->filter('a')->each(function ($node) {
//                            $href = $node->attr('href');
//                            if (strpos($href, 'gadget-it.ru') !== false) {
//                                return $href;
//                            }
//                        });
                    $links = $crawler->filter('a');
//                    dd($links);
                    foreach ($links as $link) {
                dd($link);
                        $all_links[] = $link->getURI();
                    }
                        $all_links = array_unique($all_links);
//            dd($all_links);
//            $parsedLinks = array_diff(array_unique($all_links), $parsedLinks);
//            var_dump($parsedLinks);
                        foreach ($all_links as $link) {
                            if (!in_array($link, $parsedLinks)) {
                                $parsedLinks[] = $link;
                                var_dump($parsedLinks);
                                if ($parsedLinks != $all_links) {
                                    $this->crawlerAction($link, $all_links, $parsedLinks);
                                } else {
                                    return $parsedLinks;
                                }
                            }
                        }
                    }
                }
            }

        } catch (Exception $exception) {
            return $exception;
        }
    }

}