<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 * https://github.com/stonemax/acme2
 */

namespace zetsoft\service\bot;

use Longman\TelegramBot\Exception\TelegramException;
use Longman\bot\TelegramBot\Telegram;
use zetsoft\system\kernels\ZFrame;
//include Root . '/ventest/telegram-bot/vendor/autoload.php';

class Telegrambot extends ZFrame
{

    #region Vars
    public $bot_api_key;
    public $bot_username;
    public $hook_url;

    #endregion
     public function setWethook($bot_api_key, $bot_username, $hook_url)
     {
         $this->bot_api_key = $bot_api_key;
         $this->bot_username = $bot_username;
         $this->hook_url = $hook_url;
         
         $bot_api_key  = '1210680423:AAGqATmg4p3tqKpl1eoyd0wUR2gjJQxdU2k';
         $bot_username = 'zetSoftTestBot';
         
         $hook_url  = '/core/tester/telegrambotj/hook.aspx';

         try {
             // Create Telegram API object
             $telegram = new Telegram($bot_api_key, $bot_username);

             // Set webhook
             $result = $telegram->setWebhook($hook_url);

             // To use a self-signed certificate, use this line instead
             //$result = $telegram->setWebhook($hook_url, ['certificate' => $certificate_path]);

             if ($result->isOk()) {
                 echo $result->getDescription();
             }
         } catch (TelegramException $e) {
             echo $e->getMessage();
         }

     }
}
