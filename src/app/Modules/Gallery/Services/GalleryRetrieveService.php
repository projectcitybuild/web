<?php
namespace App\Modules\Gallery\Services;

use App\Modules\Gallery\Services\ImgurApiService;
use Illuminate\Cache\Repository as Cache;

class GalleryRetrieveService {

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var ImgurApiService
     */
    private $imgurApi;

    public function __construct(Cache $cache, ImgurApiService $imgurApi) {
        $this->cache = $cache;
        $this->imgurApi = $imgurApi;
    }

    public function getFeaturedAlbum() {
        $images = $this->cache->remember('featured-images', 180, function() {
            $album = config('gallery.featured-album');
            return $this->imgurApi->getImagesFromAlbum($album);
        });
    }

}