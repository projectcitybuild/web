<?php

namespace App\Domains\Crawlers;

use Psr\Http\Message\UriInterface;
use Spatie\Crawler\CrawlProfiles\CrawlProfile;

class SiteMapCrawlProfile extends CrawlProfile
{
    public function shouldCrawl(UriInterface $url): bool
    {
        if (str_contains($url->getQuery(), "page=")) {
            return false;
        }
        if ($url->getHost() !== "projectcitybuild.com") {
            return false;
        }
        return true;
    }
}
