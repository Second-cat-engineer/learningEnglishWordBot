<?php

use Zanzara\Context;
use Zanzara\Zanzara;

require __DIR__ . '/vendor/autoload.php';
$token = require __DIR__ . '/token.php';

$bot = new Zanzara($token);

$bot->onCommand('start', function (Context $ctx) {
    $kb = ['reply_markup' =>
        ['keyboard' => [[
            ['text' => 'Переводчик'],
            ['text' => 'Добавить слова'],
            ['text' => 'Изучить слова'],
        ]], 'resize_keyboard' => true]
    ];
    $firstName = $ctx->getEffectiveUser()->getFirstName();
    $ctx->sendMessage("Привет, $firstName! Я бот, который поможет тебе запоминать слова. У меня есть 3 режима:\n
        1. Простой переводчик.\n
        2. Возможность добавлять слова в свой список для заучивания.\n
        3. Упражнение. Я буду показывать тебе слово из твоего списка, а ты должен сказать помнишь ли ты перевод этого слова.\n
        Поехали!", $kb);
});


// ответ на команды Меню
$bot->onText('Переводчик', function (Context $ctx) {
    $kb = ['reply_markup' =>
        ['inline_keyboard' => [[
            ['callback_data' => 'eng-rus', 'text' => 'eng-rus'],
            ['callback_data' => 'rus-eng', 'text' => 'rus-eng'],
        ]], 'resize_keyboard' => true]
    ];
    $ctx->sendMessage('Выберете режим перевода', $kb);
});
$bot->onText('Изучить слова', function (Context $ctx) {
    $ctx->sendMessage('Раздел в данный момент находится в разработке');
});
$bot->onText('Добавить слова', function (Context $ctx) {
    $ctx->sendMessage('Раздел в данный момент находится в разработке');
});

// Выбор режима переводчика
$bot->onCbQueryData(['eng-rus'], function (Context $ctx) {
    $ctx->sendMessage('Перевод с английского на русский. Введите слово:');
    $ctx->nextStep('engRusTranslator');
});
$bot->onCbQueryData(['rus-eng'], function (Context $ctx) {
    $ctx->sendMessage('Перевод с русского на английский. Введите слово:');
    $ctx->nextStep('rusEngTranslator');
});


//Функции переводчики
function engRusTranslator(Context $ctx)
{
    $word = $ctx->getMessage()->getText();
    $translator = new \App\Components\Translator($word);
    $message = $translator->engRusTranslator();
    $ctx->sendMessage($message);
}

function rusEngTranslator(Context $ctx)
{
    $word = $ctx->getMessage()->getText();
    $translator = new \App\Components\Translator($word);
    $message = $translator->rusEngTranslator();
    $ctx->sendMessage($message);
}

$bot->run();