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


use BotMan\BotMan\Interfaces\StorageInterface;
use Illuminate\Support\Collection;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Cache\SymfonyCache;
use BotMan\BotMan\Storages\Storage;
use BotMan\Drivers\Telegram\TelegramAudioDriver;
use BotMan\Drivers\Telegram\TelegramLocationDriver;
use BotMan\Drivers\Telegram\TelegramPhotoDriver;
use BotMan\Drivers\Telegram\TelegramVideoDriver;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use zetsoft\dbitem\chat\BotmanItem;
use zetsoft\system\kernels\ZFrame;

class UserStorage extends ZFrame
{
  public function testCase(string $string){
    return $string;
  }

}
