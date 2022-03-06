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
$telegramBot->onGetMessages(function (User $user, $text, Answer $answer) {
    $task = new Task($user->id());

    $startBtn = new Button('/start');
    $pendingBtn = new InlineButton('Невыполненные (' . $task->pendingCount() . ')', 'pending');
    $doneBtn = new InlineButton('Выполненные ('.$task->doneCount().')', 'completed');
    $addBtn = new InlineButton('Добавить задачу', 'add');
    $cancelBtn = new InlineButton('Отменить', 'cancel');
    $mainBtn = new InlineButton('На главную', 'main');
    $cleanBtn = new InlineButton('Очистить', 'clean');

    if ($startBtn->isPress($text) || $mainBtn->isPress($text)) {
        $user->setState('default');
        $answer->setText('Я ваш личный помощник по задачам');
        $keyboard = new InlineKeyboard();
        $keyboard->button(1, $pendingBtn);
        $keyboard->button(2, $doneBtn);
        $keyboard->button(3, $addBtn);
        $answer->setKeyboard($keyboard);
    }

    if ($user->isState('taskAdd')) {

        if ($cancelBtn->isPress($text)) {
            $answer->setText('Вы отменили добавление задачи');
            $user->setState('default');
            $keyboard = new InlineKeyboard();
            $keyboard->button(1, $pendingBtn);
            $keyboard->button(2, $doneBtn);
            $keyboard->button(3, $addBtn);
            $answer->setKeyboard($keyboard);
            return;
        }

        $task->create($text);

        $answer->setText('Список невыполненных задач');
        $user->setState('pendingList');
        $keyboard = new InlineKeyboard();
        foreach ($task->pendingList() as $i => $task) {
            $keyboard->button($i, new InlineButton($task->name, $task->id));
        }
        $keyboard->button(98, $addBtn);
        $keyboard->button(99, $mainBtn);
        $answer->setKeyboard($keyboard);
        return;
    }

    if ($addBtn->isPress($text)) {
        $user->setState('taskAdd');
        $answer->setText('Введите название задачи');
        $keyboard = new InlineKeyboard();
        $keyboard->button(1, $cancelBtn);
        $answer->setKeyboard($keyboard);
    }

    if ($user->isState('pendingList')) {
        $task->done($text);
        $answer->setText('Задача перемещена в выполненные, осталось (' . $task
                ->pendingCount() . ')');
        $keyboard = new InlineKeyboard();
        foreach ($task->pendingList() as $i => $task) {
            $keyboard->button($i, new InlineButton($task->name, $task->id));
        }
        $keyboard->button(98, $addBtn);
        $keyboard->button(99, $mainBtn);
        $answer->setKeyboard($keyboard);
        return;
    }

    if ($pendingBtn->isPress($text)) {
        $answer->setText('Список невыполненных задач (' . $task->pendingCount() . ')');
        $user->setState('pendingList');

        $keyboard = new InlineKeyboard();
        foreach ($task->pendingList() as $i => $task) {
            $keyboard->button($i, new InlineButton($task->name, $task->id));
        }
        $keyboard->button(98, $addBtn);
        $keyboard->button(99, $mainBtn);
        $answer->setKeyboard($keyboard);
        return;
    }

    if ($user->isState('doneList') && $cleanBtn->isPress($text)) {
        $task->doneRemove();
        $keyboard = new InlineKeyboard();
        $keyboard->button(1, new InlineButton('Невыполненные ('.$task->pendingCount().')', 'pendingList'));
        $keyboard->button(2, new InlineButton('Выполненные ('.$task->doneCount().')', 'doneList'));
        $keyboard->button(3, $addBtn);
        $answer->setText('Список выполненных задача успешно очищен');
        $answer->setKeyboard($keyboard);
        return;
    }

    if ($doneBtn->isPress($text) || $user->isState('doneList')) {
        $answer->setText('Список выполненных задач ('.$task->doneCount().')');
        $user->setState('doneList');

        $keyboard = new InlineKeyboard();
        foreach ($task->doneList() as $i => $task) {
            $keyboard->button($i, new InlineButton($task->name, $task->id));
        }
        $keyboard->button(98, $cleanBtn);
        $keyboard->button(99, $mainBtn);
        $answer->setKeyboard($keyboard);
        return;
    }
});