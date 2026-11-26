<?php

namespace App\Domains\Instagram;

use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use UnexpectedValueException;

final class InstagramImageDownloader
{
    private const MAX_IMAGE_BYTES = 10485760;
    private const MAX_CONCURRENT_DOWNLOADS = 4;
    private const DOWNLOAD_CHUNK_BYTES = 8192;

    /**
     * @param  array<string, array{imageUrl: string, imageKey: string, altText: string}>  $images
     * @return array<string, string>
     */
    public function download(array $images): array
    {
        foreach ($images as $image) {
            if (! self::isAllowedUrl($image['imageUrl'])) {
                throw new UnexpectedValueException('Instagram returned an unauthorized image URL.');
            }
        }

        $downloadedImages = [];

        foreach (array_chunk($images, self::MAX_CONCURRENT_DOWNLOADS, true) as $imageBatch) {
            $responses = Http::pool(static function (Pool $pool) use ($imageBatch): array {
                $requests = [];

                foreach ($imageBatch as $imageKey => $image) {
                    $requests[$imageKey] = $pool->as($imageKey)
                        ->connectTimeout(3)
                        ->timeout(10)
                        ->withOptions([
                            'allow_redirects' => false,
                            'stream' => true,
                            'on_headers' => static function (ResponseInterface $response, RequestInterface $request): void {
                                $contentLength = $response->getHeaderLine('Content-Length');

                                if (ctype_digit($contentLength) && (int) $contentLength > self::MAX_IMAGE_BYTES) {
                                    throw new UnexpectedValueException('Instagram image exceeds the size limit.');
                                }
                            },
                            'progress' => static function (
                                int $downloadTotal,
                                int $downloadedBytes,
                                int $uploadTotal,
                                int $uploadedBytes,
                            ): void {
                                if ($downloadedBytes > self::MAX_IMAGE_BYTES) {
                                    throw new UnexpectedValueException('Instagram image exceeds the size limit.');
                                }
                            },
                        ])
                        ->get($image['imageUrl']);
                }

                return $requests;
            }, self::MAX_CONCURRENT_DOWNLOADS);

            foreach ($responses as $imageKey => $response) {
                if ($response instanceof Throwable || ! $response instanceof Response) {
                    throw new UnexpectedValueException('Instagram image download failed.');
                }

                $downloadedImages[$imageKey] = $this->validatedImageContents($response);
            }
        }

        return $downloadedImages;
    }

    public static function isAllowedUrl(mixed $url): bool
    {
        if (! is_string($url) || filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        $parts = parse_url($url);

        if (! is_array($parts)) {
            return false;
        }

        $host = is_string($parts['host'] ?? null) ? strtolower($parts['host']) : '';
        $isInstagramCdn = $host === 'cdninstagram.com'
            || str_ends_with($host, '.cdninstagram.com');
        $isFacebookCdn = $host === 'fbcdn.net'
            || str_ends_with($host, '.fbcdn.net');

        return ($parts['scheme'] ?? null) === 'https'
            && ! isset($parts['user'])
            && ! isset($parts['pass'])
            && ! isset($parts['port'])
            && ($isInstagramCdn || $isFacebookCdn);
    }

    private function validatedImageContents(Response $response): string
    {
        if (! $response->successful()) {
            throw new UnexpectedValueException('Instagram image download failed.');
        }

        $contentLength = $response->header('Content-Length');

        if (is_string($contentLength) && ctype_digit($contentLength) && (int) $contentLength > self::MAX_IMAGE_BYTES) {
            throw new UnexpectedValueException('Instagram image exceeds the size limit.');
        }

        $contentType = $response->header('Content-Type');
        $mimeType = is_string($contentType)
            ? strtolower(trim(explode(';', $contentType, 2)[0]))
            : '';

        if ($mimeType !== 'image/jpeg') {
            throw new UnexpectedValueException('Instagram returned an invalid image.');
        }

        $body = $response->toPsrResponse()->getBody();

        if ($body->isSeekable()) {
            $body->rewind();
        }

        $contents = '';

        while (! $body->eof()) {
            $remainingBytes = self::MAX_IMAGE_BYTES + 1 - strlen($contents);
            $chunk = $body->read(min(self::DOWNLOAD_CHUNK_BYTES, $remainingBytes));

            if ($chunk === '') {
                if (! $body->eof()) {
                    throw new UnexpectedValueException('Instagram image could not be read.');
                }

                break;
            }

            $contents .= $chunk;

            if (strlen($contents) > self::MAX_IMAGE_BYTES) {
                throw new UnexpectedValueException('Instagram image exceeds the size limit.');
            }
        }

        if (! str_starts_with($contents, "\xFF\xD8\xFF")) {
            throw new UnexpectedValueException('Instagram returned an invalid JPEG image.');
        }

        return $contents;
    }
}
