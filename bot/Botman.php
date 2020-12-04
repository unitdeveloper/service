<?php

/**
 *
 *
 * Author:  Zoirjon Sobirov
 * https://t.me/zoirjon_sobirov
 *
 */

namespace zetsoft\service\bot;


use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use zetsoft\system\kernels\ZFrame;

class Botman extends ZFrame
{

    public $token = '1277348798:AAEXBJt458qC3O8zwbzaYIvNoOdjEDVivXU';

    public function run()
    {
        $config = [
            "telegram" => [
                "token" => $this->token
            ]
        ];
        DriverManager::loadDriver(\BotMan\Drivers\Telegram\TelegramDriver::class);

        $botman = BotManFactory::create($config);

        $botman->hears('hello', function (\zetsoft\service\bot\TgBotman  $bot) {
            $bot->reply('Hello yourself.');
        });

        $botman->listen();
    }
}
