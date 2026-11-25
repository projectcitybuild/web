<?php

namespace App\Http\Controllers\Front;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InstagramImageController
{
    public function __invoke(string $imageKey): StreamedResponse
    {
        $diskName = config('filesystems.default');

        if (! is_string($diskName) || $diskName === '') {
            abort(500);
        }

        $disk = Storage::disk($diskName);
        $path = 'instagram-images/'.$imageKey.'.jpg';

        if (! $disk->exists($path)) {
            abort(404);
        }

        $stream = $disk->readStream($path);

        if (! is_resource($stream)) {
            abort(404);
        }

        return response()->stream(static function () use ($stream): void {
            try {
                fpassthru($stream);
            } finally {
                fclose($stream);
            }
        }, headers: [
            'Cache-Control' => 'public, max-age=3600',
            'Content-Disposition' => 'inline',
            'Content-Type' => 'image/jpeg',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
