<?php

namespace App\Support;

class VideoIframe
{
    public static function srcFromInput(string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        if (preg_match('/<iframe\b[^>]*\bsrc=(["\'])(.*?)\1/iu', $value, $matches) === 1) {
            $value = html_entity_decode($matches[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        if (! filter_var($value, FILTER_VALIDATE_URL)) {
            return null;
        }

        $scheme = parse_url($value, PHP_URL_SCHEME);

        if (! in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        return $value;
    }
}
