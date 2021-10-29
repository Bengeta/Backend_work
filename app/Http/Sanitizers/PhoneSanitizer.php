<?php

namespace App\Http\Sanitizers;

class PhoneSanitizer
{
    public static function sanitize(string $value): string
    {
        return preg_replace('/^8/', '7', preg_replace('/\D+/', '', $value));
    }
}
