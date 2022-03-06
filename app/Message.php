<?php

namespace App;

class Message
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function chatId()
    {
        return $this->data->message->chat->id;
    }

    public function nextId()
    {
        return $this->data->update_id + 1;
    }

    public function text()
    {
        return property_exists($this->data->message, 'text') ? $this->data->message->text : '';
    }
}
