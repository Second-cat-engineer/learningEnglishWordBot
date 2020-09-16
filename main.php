<?php

use Zanzara\Context;
use Zanzara\Zanzara;

require __DIR__ . '/vendor/autoload.php';
$token = require __DIR__ . '/config/token.php';

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
    $welcomeText = require __DIR__ . '/protected/welcomeText.php';
    $ctx->sendMessage( $firstName . ', ' . $welcomeText, $kb);
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
$bot->onText('Добавить слова', function (Context $ctx) {
    $kb = ['reply_markup' =>
        ['inline_keyboard' => [[
            ['callback_data' => 'eng', 'text' => 'eng'],
            ['callback_data' => 'rus', 'text' => 'rus'],
        ]], 'resize_keyboard' => true]
    ];
    $ctx->sendMessage('Выберете, на каком языке вы хотите добавить слово в словарь', $kb);
});
$bot->onText('Изучить слова', function (Context $ctx) {
    $ctx->sendMessage('Раздел в данный момент находится в разработке');
});

// Выбор режима переводчика
$bot->onCbQueryData(['eng-rus'], function (Context $ctx) {
    $ctx->sendMessage('Перевод с английского на русский. Введите слово:');
    $ctx->nextStep('Translator');
});
$bot->onCbQueryData(['rus-eng'], function (Context $ctx) {
    $ctx->sendMessage('Перевод с русского на английский. Введите слово:');
    $ctx->nextStep('Translator');
});

// Выбор добавления слов в словарь
$bot->onCbQueryData(['rus'], function (Context $ctx) {
    $ctx->sendMessage('Введите слово которое хотите добавить в свой словарь');
    $ctx->nextStep('addNewRusWord');
});
$bot->onCbQueryData(['eng'], function (Context $ctx) {
    $ctx->sendMessage('Введите слово которое хотите добавить в свой словарь');
    $ctx->nextStep('addNewEngWord');
});

// Функции добавления слов в словарь
function addNewRusWord(Context $ctx)
{
    $word = $ctx->getMessage()->getText();
    $translateWord = \App\Components\Translator::rusEngTranslator($word);
    $user = $ctx->getEffectiveUser();

}
function addNewEngWord(Context $ctx)
{
    $word = new \App\Models\Word($ctx);
    $word->save();
}



// Функция переводчик
function Translator(Context $ctx)
{
    $enteredWord = $ctx->getMessage()->getText();
    $word = \App\Components\Translator::engRusTranslator($enteredWord);
    $ctx->sendMessage($word->eng_word . ' - ' . $word->rus_word);
}

$bot->run();