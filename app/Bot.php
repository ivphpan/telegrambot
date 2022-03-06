<?php

namespace App;

class Bot
{
    private $key;

    public function setKey($key)
    {
        $this->key = $key;
    }

    public function onGetMessages($cb)
    {
        $offset = 0;
        while (true) {
            $updates = $this->getUpdates($offset);

            if (!$updates) continue;

            foreach ($updates as $update) {
                $message = new Message($update);
                $user = new User($update);
                $answer = new Answer($this, $message);

                $offset = $message->nextId();
                $cb($user, $message->text(), $answer);

                if ($answer->isReady()) {
                    $answer->send();
                }

                $user->save();
            }
            usleep(500000);
        }
    }

    public function getUpdates($offset = 0)
    {
        return $this->callApi('getUpdates', [
            'offset' => $offset,
            'allowed_updates' => json_encode(['message']),
        ]);
    }

    public function sendMessage($chatId, $text, $keyboard = null)
    {
        $reply_markup = '';
        if ($keyboard != null) {
            $reply_markup = $keyboard->get();
        }

        return $this->callApi('sendMessage', [
            'chat_id' => $chatId,
            'text' => $text,
            'reply_markup' => $reply_markup,
        ]);
    }

    private function callApi($method, $params = [])
    {
        $url = 'https://api.telegram.org/bot' . $this->key . '/' . $method;
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 5,
        ]);

        if (!empty($params)) {
            curl_setopt_array($curl, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($params)
            ]);
        }

        $result = curl_exec($curl);
        $debug = curl_getinfo($curl);
        curl_close($curl);

        if ($debug['http_code'] !== 200) {
            print_r($result);
            return null;
        }
        $data = json_decode($result);
        return $data->result;
    }
}
