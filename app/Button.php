<?php

namespace App;

class Button
{
    public $text;

    public function __construct($text)
    {
        $this->text = $text;
    }

    public function get()
    {
        return [
            'text' => $this->text,
        ];
    }

    public function isPress($text)
    {
        return preg_match('#'.$this->text.'#', $text);
    }
}
