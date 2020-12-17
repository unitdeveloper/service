<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\bot;

require Root . '/vendors/botapi/vendor/autoload.php';

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Cache\SymfonyCache;
use BotMan\Drivers\Telegram\TelegramAudioDriver;
use BotMan\Drivers\Telegram\TelegramLocationDriver;
use BotMan\Drivers\Telegram\TelegramPhotoDriver;
use BotMan\Drivers\Telegram\TelegramVideoDriver;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use zetsoft\dbitem\chat\BotmanItem;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\targets\ZTelegramDriver;

class TgBotman extends ZFrame
{
    #region Vars
    public $token;

    /* @var Phpwebdirver $botman*/
    public $botman;

    #endregion

    /**
     *
     * Function  botman
     * @param $token
     * @return Phpwebdirver
     */
    public function botman($token)
    {
        $this->token = $token;

        $config = [
            "telegram" => [
                "token" => $this->token
            ] ,
            'botman' => [
                'conversation_cache_time' => 30, // bot saval bergandan keyin 30 minut javob kutadi. 30 minutdan keyin javob bersangiz qabul qilmaydi
                'user_cache_time' => 30 // user javoblarini saqlab qo'ygan bo'lsangiz 30 minut turadi botmanni esida. keyin unutadi. shu vaqtni ichida database ga saqlab qo'yish kerak.
            ],
        ];
        DriverManager::loadDriver(ZTelegramDriver::class);
        DriverManager::loadDriver(TelegramLocationDriver::class);
        DriverManager::loadDriver(TelegramAudioDriver::class);
        DriverManager::loadDriver(TelegramPhotoDriver::class);
        DriverManager::loadDriver(TelegramVideoDriver::class);
        $adapter = new FilesystemAdapter();

        $this->botman = BotManFactory::create($config, new SymfonyCache($adapter));

        return $this->botman;
    }

    
    
}
