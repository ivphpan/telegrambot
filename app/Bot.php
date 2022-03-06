<?php

namespace App;

use Grpc\Call;

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
                print_r($update);
                if (property_exists($update, 'callback_query')) {
                    $message = new CallbackQueryMessage($update);
                } else {
                    $message = new Message($update);
                }

                $this->editMessageText($message->chatId(), $message->id(), 'Подождите, идёт загрузка...');
                $this->deleteMessage($message->chatId(), $message->id());

                $user = new User($message->from());
                $answer = new Answer($this, $message->chatId());

                if ($message instanceof CallbackQueryMessage) {
                    $this->answerCallbackQuery($update->callback_query->id);
                }

                $offset = $message->nextId();
                $cb($user, $message->text(), $answer);

                if ($answer->isReady()) {
                    $sendResponse = $answer->send();
                    print_r($sendResponse);
                }

                $user->save();
            }
            usleep(500000);
        }
    }

    private function answerCallbackQuery($id)
    {
        $this->callApi('answerCallbackQuery', [
            'callback_query_id' => $id,
        ]);
    }

    public function getUpdates($offset = 0)
    {
        return $this->callApi('getUpdates', [
            'offset' => $offset,
            'allowed_updates' => json_encode(['message', 'callback_query']),
        ]);
    }

    private function deleteMessage($chatId, $messageId)
    {
        return $this->callApi('deleteMessage', [
            'chat_id' => $chatId,
            'message_id' => $messageId
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

    public function editMessageText($chatId, $messageId, $text, $keyboard = null)
    {
        $reply_markup = '';
        if ($keyboard != null) {
            $reply_markup = $keyboard->get();
        }

        return $this->callApi('editMessageText', [
            'chat_id' => $chatId,
            'message_id' => $messageId,
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
