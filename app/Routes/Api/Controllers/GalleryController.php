<?php

namespace App\Routes\Api\Controllers;

use Illuminate\Http\Request;
use App\Modules\Gallery\Services\ImgurFetcher;
use Illuminate\Cache\Repository as Cache;

class GalleryController extends Controller {

    /**
     * Fetches and caches a list of all images from the featured imgur album
     *
     * @param Request $request
     * @return array
     */
    public function getFeaturedImages(Request $request) {
        $cache = resolve(Cache::class);
        $images = $cache->remember('featured-images', 180, function() {
            $album = config('gallery.featured-album');
        
            $fetcher = resolve(ImgurFetcher::class);
            return $fetcher->getImagesFromAlbum($album);
        });

        return [
            'status_code' => 200,
            'data' => $images,
        ];
    }
}
