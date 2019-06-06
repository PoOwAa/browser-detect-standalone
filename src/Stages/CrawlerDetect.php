<?php

namespace PoOwAa\BrowserDetect\Stages;

use League\Pipeline\StageInterface;
use PoOwAa\BrowserDetect\Contracts\PayloadInterface;

/**
 * Checks if the user agent belongs to bot or crawler.
 *
 * @package PoOwAa\BrowserDetect\Stages
 */
class CrawlerDetect implements StageInterface
{
    /**
     * @param  PayloadInterface $payload
     * @return PayloadInterface
     */
    public function __invoke($payload)
    {
        $crawler = new \Jaybizzle\CrawlerDetect\CrawlerDetect(['HTTP_FAKE_HEADER' => 'Crawler\Detect'], $payload->getAgent());
        $payload->setValue('isBot', $crawler->isCrawler());

        return $payload;
    }
}
