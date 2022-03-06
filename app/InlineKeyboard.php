<?php

namespace app;

class InlineKeyboard
{
    private $keyboard = [];

    public function button($row, InlineButton $button)
    {
        $systemRow = $row - 1;
        if (!array_key_exists($systemRow, $this->keyboard)) {
            $this->keyboard[$systemRow] = [];
        }
        $this->keyboard[$systemRow][] = $button->get();
    }

    public function get()
    {
        ksort($this->keyboard);
        $keyboard = [];

        foreach ($this->keyboard as $row) {
            $keyboard[] = $row;
        }
        print_r($keyboard);
        return json_encode([
            'inline_keyboard' => $keyboard,
        ]);
    }
}
