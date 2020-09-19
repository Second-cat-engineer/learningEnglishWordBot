<?php

namespace App\Components;

use App\Models\Word;

class RandomWord
{
    public static function randomWord(int $user_id) : object
    {
        $words = Word::findAllByUser($user_id);
        if (!empty($word)) {
            throw new \Exception('Ваш словарь пуст! Добавьте слова для заучивания');
        }
        $max = count($words) - 1;
        $min = 0;

        $randomNumber = random_int($min, $max);
        return $words[$randomNumber];
    }
}