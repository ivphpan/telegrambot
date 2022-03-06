<?php

namespace App;

class User
{
    private $from;
    private $user;

    const DataPath = './user';

    public function __construct($from)
    {
        $this->from = $from;
        $this->loadFromFile();
    }

    public function id()
    {
        return $this->user->id;
    }

    public function firstName()
    {
        return $this->user->firstName;
    }

    public function loadFromFile()
    {

        $filename = $this->filename();

        if (!file_exists($filename)) {
            $this->user = $this->create();
            return;
        }

        $this->user = json_decode(file_get_contents($filename));
    }

    private function filename()
    {
        return self::DataPath . '/' . $this->from->id . '.json';
    }

    private function create()
    {
        return (object)[
            'id' => $this->from->id,
            'firstName' => $this->from->first_name,
            'state' => 'default',
        ];
    }

    public function setState($state)
    {
        $this->user->state = $state;
    }

    public function state()
    {
        return $this->user->state;
    }

    public function save()
    {
        $filename = $this->filename();
        file_put_contents($filename, json_encode($this->user, JSON_UNESCAPED_UNICODE));
    }
}

