<?php

namespace App\Components;

use App\Exceptions\ErrorTranslator;

class Translator
{
    const BASE_URL = 'https://dictionary.skyeng.ru/api/public/v1/words/search';
    protected array $params =[];

    public function __construct($word)
    {
        $this->params['search'] = $word;
    }

    protected function getResponse()
    {
        $url = self::BASE_URL . '?' . http_build_query($this->params);

        $response = json_decode(
            file_get_contents($url),
            JSON_OBJECT_AS_ARRAY
        );

        if (!$response) {
            throw new ErrorTranslator('Не удалось перевести слово');
        }
        return $response[0];
    }

    public function engRusTranslator()
    {
        try {
            $data = $this->getResponse();
        } catch (ErrorTranslator $e) {
            return $message = $e->getMessage();
        }
        return $translation = $data['meanings'][0]['translation']['text'];
    }

    public function rusEngTranslator()
    {
        try {
            $data = $this->getResponse();
        } catch (ErrorTranslator $e) {
            return $message = $e->getMessage();
        }
        return $translation = $data['text'];
    }

}