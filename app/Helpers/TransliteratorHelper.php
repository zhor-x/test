<?php

namespace App\Helpers;

use Behat\Transliterator\Transliterator;

class TransliteratorHelper
{
    public static function transliterate(string $text): string
    {
        return Transliterator::transliterate($text);
    }
}
