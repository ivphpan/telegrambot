<?php

namespace App;

class Answer
{
    private $chatId;
    private $bot;
    private $text='';
    private $keyboard=null;

    public function __construct($bot, $chatId)
    {
        $this->bot = $bot;
        $this->chatId = $chatId;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function setKeyboard($keyboard)
    {
        $this->keyboard = $keyboard;
    }

    public function isReady()
    {
        return !empty($this->text);
    }

    public function send()
    {
        return $this->bot->sendMessage(
            $this->chatId,
            $this->text,
            $this->keyboard
        );
    }
}
