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

        if (!property_exists($this->user, 'data'))
            $this->user->data = [];

        $this->user->data = (array) $this->user->data;
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
            'data' => [],
        ];
    }

    public function setData($key, $val)
    {
        $this->user->data[$key] = $val;
    }

    public function deleteData($key)
    {
        if (array_key_exists($key, $this->user->data))
            unset($this->user->data[$key]);
    }

    public function getData($key)
    {
        if (array_key_exists($key, $this->user->data))
            return $this->user->data[$key];

        return null;
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

