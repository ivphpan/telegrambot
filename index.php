<?php

namespace App;

require_once './vendor/autoload.php';

date_default_timezone_set('Asia/Tashkent');

const TelegramApiKey = '5098947282:AAHO2Y4pbER__mlEWMXInFB3Pa1aydvFZQ8';

/*
 У нас есть бот
- Бот для получения и отправки сообщений использует ключ
- Бот умеет:
    Устанавливать ключ
    Получать сообщения
    Отвечать на сообщения
- Сообщения
    Получить ключ от чата
    Получить ключ следующего обновления
    Получить текст сообщения

Бот при получении сообщения /date - Отправляет ответ с датой и временем
Нам необходимо, чтобы бот добавлял кнопочки в сообщении

При получении сообщения /buttons- Бот отправляет сообщение с тестом
    Вот тебе кнопочки
    Снизу кнопочки
    Кнопочка 01
    Кнопочка 02
    Кнопочка 03

При создании получении сообщения создавать пользователя

Возможности пользователя
    Получить ключ
    Получить имя
    Получить состояние
    Изменить состояние
    Сохранить данные

После обработки сообщения, сохранять данные пользователя

У нас поменялись условия, теперь в зависимости от условий
Нам нужно создать ответ
Заполнить его
Проверить и отправить

Условия и кнопки не имееют целостности - необходимо исправить
 */

//Создаём бот
$telegramBot = new Bot();

//Устанавливаем ключ для работы с телеграмом
$telegramBot->setKey(TelegramApiKey);

//Бот при получении сообщения, проверяет по условию и заполняет ответ
$telegramBot->onGetMessages(function ($user, $text, Answer $answer) {
    $startBtn = new Button('/start'); //Создаем кнопочку /start
    $inlineBtn = new Button('/inline-keyboard');
    $dateBtn = new Button('/date');//Создаем кнопочку /date
    $userBtn = new Button('/user');//Создаем кнопочку /user
    $userChangeStateBtn = new Button('/user-change-state');//Создаем кнопочку /user-change-state

    $inlineBtn01 = new InlineButton('Кнопка 01', 'button-1');
    $inlineBtn02 = new InlineButton('Кнопка 02', 'button-2');
    $inlineBtn03 = new InlineButton('Кнопка 03', 'button-3');

    if ($inlineBtn01->isPress($text)) {
        $answer->setText('Вы нажали на кнопку01 встроенной клавиатуры');
    }

    if ($inlineBtn->isPress($text)) {
        $keyboard = new InlineKeyboard();
        $keyboard->button(1, $inlineBtn01);
        $keyboard->button(2, $inlineBtn02);
        $keyboard->button(3, $inlineBtn03);
        $answer->setText('Встроенная клавиатура');
        $answer->setKeyboard($keyboard);
    }

    if ($dateBtn->isPress($text)) { // Если нажали на кнопочку /date
        $answer->setText(date('d.m.Y')); //заполняем ответ текущей датой
    }

    if ($userBtn->isPress($text)) {
        $answer->setText(json_encode([
            'id' => $user->id(),
            'firstName' => $user->firstName(),
            'state' => $user->state(),
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    if ($userChangeStateBtn->isPress($text)) {
        $user->setState('customState '.mt_rand(1, 1000));
        $answer->setText('Состояние изменено');
    }

    if ($startBtn->isPress($text)) {
        $keyboard = new Keyboard();
        $keyboard->button(0, $inlineBtn);
        $keyboard->button(1, $startBtn);
        $keyboard->button(2, $dateBtn);
        $keyboard->button(3, $userBtn);
        $keyboard->button(3, $userChangeStateBtn);

        $answer->setText('Вот тебе кнопочки');
        $answer->setKeyboard($keyboard);
    }
});