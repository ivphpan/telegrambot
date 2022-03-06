<?php

namespace app;

class Task
{
    private $userId;
    const DataPath =  './task';
    private $items;
    private $pendingCount = 0;

    public function __construct($userId)
    {
        $this->userId = $userId;

        if (!$this->exists())
            $this->createUserFile();

        $this->load();
    }

    public function create($name)
    {
        $this->items[] = (object)['id'=>'task_'.time(),'name'=>$name, 'done'=>false];
        $this->save();
    }

    public function pendingList()
    {
        return array_filter($this->items, function($item){
           if ($item->done == false) return $item;
        });
    }

    public function pendingCount()
    {
        return sizeof($this->pendingList());
    }

    public function done($taskId)
    {
       foreach($this->items as &$item) {
           if ($item->id == $taskId) {
               $item->done = true;
           }
       }
       $this->save();
    }

    public function doneList()
    {
        return array_filter($this->items, function($item){
           if ($item->done) return $item;
        });
    }

    public function doneCount()
    {
        return sizeof($this->doneList());
    }

    public function doneRemove()
    {
        $this->items = $this->pendingList();
        $this->save();
    }

    private function save()
    {
        file_put_contents($this->filename(), json_encode($this->items, JSON_UNESCAPED_UNICODE));
    }

    private function exists()
    {
        return file_exists($this->filename());
    }

    private function filename()
    {
        return self::DataPath . '/' . $this->userId . '.json';
    }

    private function createUserFile()
    {
        file_put_contents($this->filename(), json_encode([]));
    }

    private function load()
    {
        $this->items = json_decode(file_get_contents($this->filename()));
    }
}