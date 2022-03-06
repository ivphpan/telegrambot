<?php

namespace App;

class Answer
{
    private $message;
    private $bot;
    private $text='';
    private $keyboard=null;

    public function __construct($bot, $message)
    {
        $this->bot = $bot;
        $this->message = $message;
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
            $this->message->chatId(),
            $this->text,
            $this->keyboard
        );
    }
}
