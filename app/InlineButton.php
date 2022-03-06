<?php

namespace app;

class InlineButton
{
    private $text;
    private $data;

    public function __construct($text, $data)
    {
        $this->text = $text;
        $this->data = $data;
    }

    public function get()
    {
        return [
            'text' => $this->text,
            'callback_data'=>$this->data,
        ];
    }

    public function isPress($text)
    {
        return preg_match('#'.$this->text.'#', $text);
    }
}
