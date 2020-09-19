<?php

use Zanzara\Context;
use Zanzara\Zanzara;
use App\Components\Translator;
use App\Components\RandomWord;

require __DIR__ . '/vendor/autoload.php';
$token = require __DIR__ . '/config/token.php';

$bot = new Zanzara($token);

$bot->onCommand('start', function (Context $ctx) {
    $kb = ['reply_markup' =>
        ['keyboard' => [[
            ['text' => 'Переводчик'],
            ['text' => 'Добавить слово'],
            ['text' => 'Изучить слова'],
        ]], 'resize_keyboard' => true]
    ];
    $firstName = $ctx->getEffectiveUser()->getFirstName();
    $welcomeText = require __DIR__ . '/protected/welcomeText.php';
    $ctx->sendMessage( $firstName . ', ' . $welcomeText, $kb);
});


// Переводчик
$bot->onText('Переводчик', function (Context $ctx) {
    $ctx->sendMessage('Введите слово, которое хотите перевести:');
    $ctx->nextStep('Translator');
});
function Translator(Context $ctx)
{
    $enteredWord = $ctx->getMessage()->getText();
    try {
        $word = Translator::engRusTranslator($enteredWord);
        $message = $word->eng_word . ' - ' . $word->rus_word;
        $ctx->sendMessage($message);
    } catch (Exception $errorTranslator) {
        $ctx->sendMessage($errorTranslator->getMessage());
    }
}

// Добавить слово в словарь
$bot->onText('Добавить слово', function (Context $ctx) {
    $ctx->sendMessage('Введите слово, которое хотите добавить в словарь:');
    $ctx->nextStep('addNewWord');
});
function addNewWord(Context $ctx)
{
    $enteredWord = $ctx->getMessage()->getText();
    try {
        $word = Translator::engRusTranslator($enteredWord);
        $word->user_id = $ctx->getEffectiveUser()->getId();

        if ($word->saveNewWord()) {
            $message = 'Слово \'' . $word->rus_word . '\'-\'' .
                $word->eng_word . '\' добавлен в словарь для заучивания';
            $ctx->sendMessage($message);
        }
    } catch (Exception $e) {
        $ctx->sendMessage($e->getMessage());
    }
}

// Изучение слов
$bot->onText('Изучить слова', function (Context $ctx) {
    $kb = ['reply_markup' =>
        ['inline_keyboard' => [[
            ['callback_data' => 'next', 'text' => 'Я знаю! Следующее!'],
            ['callback_data' => 'translate', 'text' => 'Я не знаю! Перевод!'],
        ]], 'resize_keyboard' => true],
    ];
    $userId = $ctx->getEffectiveUser()->getId();
    try {
        $randomWord = RandomWord::randomWord($userId);
        $ctx->setChatData('word', $randomWord, );
        $ctx->sendMessage($randomWord->eng_word, $kb);
    } catch (Exception $e) {
        $ctx->sendMessage($e->getMessage());
    }
});
$bot->onCbQueryData(['translate'], function (Context $ctx) {
    $ctx->getChatDataItem('word')->then(function ($word) use ($ctx) {
        $message = $word->eng_word . ' - ' . $word->rus_word;
        $kb = ['reply_markup' =>
            ['inline_keyboard' => [[
                ['callback_data' => 'next', 'text' => 'Следующее слово!'],
            ]], 'resize_keyboard' => true],
        ];
        $ctx->sendMessage($message, $kb);
    });
    $ctx->deleteChatDataItem('word');
});
$bot->onCbQueryData(['next'], function (Context $ctx) {
    $kb = ['reply_markup' =>
        ['inline_keyboard' => [[
            ['callback_data' => 'next', 'text' => 'Я знаю! Следующее!'],
            ['callback_data' => 'translate', 'text' => 'Я не знаю! Перевод!'],
        ]], 'resize_keyboard' => true],
    ];
    $userId = $ctx->getEffectiveUser()->getId();
    try {
        $randomWord = RandomWord::randomWord($userId);
        $ctx->setChatData('word', $randomWord, );
        $ctx->sendMessage($randomWord->eng_word, $kb);
    } catch (Exception $e) {
        $ctx->sendMessage($e->getMessage());
    }
});

$bot->run();