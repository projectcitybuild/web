<?php

namespace App\Domains\Instagram;

use UnexpectedValueException;

final class InstagramImageWatermarker
{
    private const WATERMARK_TEXT = 'https://projectcitybuild.com';
    private const MARGIN = 8;
    private const PADDING = 8;

    public function watermark(string $contents): string
    {
        $image = imagecreatefromstring($contents);

        if ($image === false) {
            throw new UnexpectedValueException('Instagram returned an invalid JPEG image.');
        }

        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);
        $font = 1;
        $fontFits = false;

        for ($candidateFont = 5; $candidateFont >= 1; $candidateFont--) {
            $textWidth = imagefontwidth($candidateFont) * strlen(self::WATERMARK_TEXT);
            $textHeight = imagefontheight($candidateFont);

            if (
                $textWidth + self::PADDING * 2 + self::MARGIN <= $imageWidth
                && $textHeight + self::PADDING * 2 + self::MARGIN <= $imageHeight
            ) {
                $font = $candidateFont;
                $fontFits = true;

                break;
            }
        }

        if (! $fontFits) {
            throw new UnexpectedValueException('Instagram image is too small to watermark.');
        }

        $textWidth = imagefontwidth($font) * strlen(self::WATERMARK_TEXT);
        $textHeight = imagefontheight($font);
        $left = $imageWidth - $textWidth - self::PADDING * 2 - self::MARGIN;
        $top = $imageHeight - $textHeight - self::PADDING * 2 - self::MARGIN;

        if (! imagealphablending($image, true)) {
            throw new UnexpectedValueException('Instagram image could not be watermarked.');
        }

        $backgroundColor = imagecolorallocatealpha($image, 0, 0, 0, 64);
        $textColor = imagecolorallocate($image, 255, 255, 255);

        if (
            $backgroundColor === false
            || $textColor === false
            || ! imagefilledrectangle(
                $image,
                $left,
                $top,
                $imageWidth - self::MARGIN,
                $imageHeight - self::MARGIN,
                $backgroundColor,
            )
            || ! imagestring(
                $image,
                $font,
                $left + self::PADDING,
                $top + self::PADDING,
                self::WATERMARK_TEXT,
                $textColor,
            )
        ) {
            throw new UnexpectedValueException('Instagram image could not be watermarked.');
        }

        $output = fopen('php://temp', 'w+b');

        if ($output === false) {
            throw new UnexpectedValueException('Watermarked Instagram image could not be encoded.');
        }

        try {
            if (! imagejpeg($image, $output, 90) || ! rewind($output)) {
                throw new UnexpectedValueException('Watermarked Instagram image could not be encoded.');
            }

            $watermarkedContents = stream_get_contents($output);
        } finally {
            fclose($output);
        }

        if (! is_string($watermarkedContents) || $watermarkedContents === '') {
            throw new UnexpectedValueException('Watermarked Instagram image could not be encoded.');
        }

        return $watermarkedContents;
    }
}
