<?php
/**
 * Created by: Xolmat Ravshanov
 * Date: 27.05.2019
 * Time: 14:26
 */

namespace zetsoft\service\utility;


use Telegram\Bot\Api;





class TelegramAsync {
       private $token;
       private $chatId;

     public function sendTelegram($token, $chatId, $text)
     {
            $this->token = $token;
            $this->chatId = $chatId;

            $telegram = new Api($this->token, true);
            $telegram
                 ->setAsyncRequest(true);

             $response = $telegram->sendMessage([
                'chat_id' => $this->chatId,
                 'text' => $text
             ]);

            
     }


}


