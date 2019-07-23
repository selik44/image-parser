<?php

namespace App\Services;

use App\Services\Contracts\AbstractCrawler;
use mysql_xdevapi\Exception;
use Symfony\Component\DomCrawler;
use Goutte\Client;
use App\Repository\ParsePagesRepository;


class ParseService extends AbstractCrawler
{

    /**
     * @var ParsePagesRepository
     */
    protected $parseRepository;

    /**
     * ParseService constructor.
     * @param ParsePagesRepository $parseRepository
     */
    public function __construct(ParsePagesRepository $parseRepository)
    {
        $this->parseRepository = $parseRepository;
    }

    /**
     * @param string $link
     * @param int $depth
     * @param int $pageCount
     * @return bool|mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function crawl(string $link, int $depth, int $pageCount)
    {
        $allLinks = [];
        $data = $this->loop($link, $link, $depth, $pageCount, $allLinks);
//        dd($data);
        $this->parseRepository->saveOrUpdateParsePages($data, $pageCount);
        return true;
    }

    /**
     * @param string $baseLink
     * @param string $link
     * @param int $depth
     * @param int $pageCount
     * @param array $allLinks
     * @return array|mixed|string
     */
    public function loop(string $baseLink, string $link, int $depth, int $pageCount, array &$allLinks)
    {

        try {
            $start_count = count($allLinks);
            if ($pageCount != 0 && $start_count >= $pageCount) {
                return $allLinks;
            }

            $regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
            $client = new Client();
            $client->request('GET', $link);
            $status_code = $client->getResponse()->getStatus();

            if ($status_code == 200) {
                $client->followRedirects();

                if (preg_match_all("/$regexp/siU", $client->getResponse(), $matches)) {
                    foreach (array_unique($matches[2]) as $item) {
                        if (strpos($item, $baseLink) !== false) {
                            if (isset($allLinks[$item]['visited']) && $allLinks[$item]['visited'] === true) {
                                continue;
                            } else {
                                if (count(explode('/', (parse_url($item, PHP_URL_PATH)))) <= $depth || $depth == 0) {
                                    $allLinks[$item] = [
                                        'link' => $item,
                                        'visited' => false,
                                        'count_images' => 0,
                                        'processing_speed' => 0
                                    ];
                                    var_dump($allLinks);
                                }
                            }
                        }
                    }
                    foreach ($allLinks as $key => $value) {
                        if (isset($allLinks[$key]['visited']) && $allLinks[$key]['visited'] === false) {
                            $time_start = microtime(true);
                            $crawler = new DomCrawler\Crawler(null, $key);
                            $crawler->addHtmlContent($client->getResponse(), 'UTF-8');
                            $time_end = microtime(true);

                            $allLinks[$key]['visited'] = true;
                            $allLinks[$key]['count_images'] = $crawler->filter('img')->count();
                            $allLinks[$key]['processing_speed'] = number_format(($time_end - $time_start), 3);

                            $this->loop($baseLink, $key, $depth, $pageCount, $allLinks);
                        }
                        if ($start_count == count($allLinks)) {
                            return $allLinks;
                        }
                    }
                    return $allLinks;
                }
            }
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

}