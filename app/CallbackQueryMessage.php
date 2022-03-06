<?php

namespace app;

class CallbackQueryMessage
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function id()
    {
        return $this->data->callback_query->message->message_id;
    }

    public function chatId()
    {
        return $this->data->callback_query->message->chat->id;
    }

    public function from()
    {
        return $this->data->callback_query->from;
    }

    public function nextId()
    {
        return $this->data->update_id + 1;
    }

    public function text()
    {
        return $this->data->callback_query->data;
    }

}
