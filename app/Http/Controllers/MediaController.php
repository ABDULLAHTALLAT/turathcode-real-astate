<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\MediaVariant;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MediaController extends Controller
{
    public function show(Request $request, Media $media, string $variant): Response
    {
        abort_unless(array_key_exists($variant, \App\Services\ImageProcessor::VARIANTS), 404);

        $etag = '"'.substr($media->sha256, 0, 24).'-'.$variant.'"';
        $headers = [
            'ETag' => $etag,
            // الصورة مش بتتغير (البصمة جزء من الهوية) فنخليها تتكاشّى سنة
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; sandbox",
            'Cross-Origin-Resource-Policy' => 'same-origin',
        ];

        if ($request->headers->get('If-None-Match') === $etag) {
            return response('', 304, $headers);
        }

        $data = MediaVariant::where('media_id', $media->id)->where('name', $variant)->value('data');
        abort_if($data === null, 404);
        if (is_resource($data)) {
            $data = stream_get_contents($data);
        }

        return response($data, 200, $headers + ['Content-Type' => 'image/webp', 'Content-Disposition' => 'inline']);
    }
}
