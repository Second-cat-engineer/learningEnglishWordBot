<?php

namespace App\Components;

use App\Exceptions\ErrorTranslator;
use App\Models\Word;
use Zanzara\Context;

class Translator
{
    const BASE_URL = 'https://dictionary.skyeng.ru/api/public/v1/words/search';

    protected static function getTranslate($word)
    {
        $params['search'] = $word;
        $url = self::BASE_URL . '?' . http_build_query($params);

        $response = json_decode(
            file_get_contents($url),
            JSON_OBJECT_AS_ARRAY
        );

        if (!$response) {
            throw new ErrorTranslator('Не удалось перевести слово');
        }
        return $response[0];
    }

    public static function engRusTranslator(string $enteredWord)
    {
        $res = self::getTranslate($enteredWord);

        $word = new Word();
        $word->rus_word = $res['meanings'][0]['translation']['text'];
        $word->eng_word = $res['text'];
        $word->imageUrl = $res['meanings'][0]['imageUrl'];
        $word->soundUrl = $res['meanings'][0]['soundUrl'];

        return $word;
    }
}