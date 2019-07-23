<?php

namespace App\Services\Contracts;

abstract class AbstractCrawler {


    /**
     * @param string $url
     * @param int $depth
     * @param int $pageCount
     * @return mixed
     */
    abstract protected function crawl(string $url, int $depth, int $pageCount);

    /**
     * @param string $baseLink
     * @param string $url
     * @param int $depth
     * @param int $pageCount
     * @param $allLinks
     * @return mixed
     */
    abstract protected function loop(string $baseLink, string $url, int $depth, int $pageCount,  &$allLinks);

}