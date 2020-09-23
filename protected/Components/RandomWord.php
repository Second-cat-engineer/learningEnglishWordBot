<?php

namespace App\Components;

use App\Models\Word;

class RandomWord
{
    public static function randomWord(int $user_id)
    {
        $words = Word::findAllByUser($user_id);
        if (empty($words)) {
            throw new \Exception('Ваш словарь пуст! Добавьте слова для заучивания');
        }
        $max = count($words)-1;
        $min = 0;

        $randomNumber = random_int($min, $max);
        return $words[$randomNumber];
    }
}