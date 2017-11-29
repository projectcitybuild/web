<?php

namespace App\Routes\Api\Controllers;

use Illuminate\Http\Request;
use App\Modules\Gallery\Services\GalleryRetrieveService;

class GalleryController extends Controller {

    /**
     * Fetches and caches a list of all images from the featured imgur album
     *
     * @param Request $request
     * @return array
     */
    public function getFeaturedImages(Request $request, GalleryRetrieveService $galleryService) {
        return [
            'status_code' => 200,
            'data' => $galleryService->getFeaturedAlbum(),
        ];
    }
}
