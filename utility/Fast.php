<?php

/**
 *
 *
 * Author:  Asror Zakirov
 *
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\utility;


use Phpfastcache\CacheManager;
use Phpfastcache\Drivers\Redis\Config;
use Phpfastcache\Helper\Psr16Adapter;
use zetsoft\dbitem\core\CpasTrackerItem;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\kernels\ZFrame;

/**
 *  Class    Fast
 *  @package zetsoft\service\utility
 *  https://packagist.org/packages/phpfastcache/phpfastcache
 *  https://www.phpfastcache.com/
 *  https://github.com/PHPSocialNetwork/phpfastcache
 */
class Fast extends ZFrame
{
    public $instance;
    public $type =  self::type['redis'];
    public const type = [
        'redis' => 'Redis',
        'files' => 'Files',
        'apcu' => 'Apcu',
    ];

    public function init()
    {
        $this->setType($this->type);
        parent::init();
    }


    private function setType($type)
    {
        switch ($type) {
            case self::type['files']:
                $config = new \Phpfastcache\Drivers\Files\Config();
                $config->setPath(Root . '/storing/loggers/cmd/' . App . '/fastCache');
                break;
            case self::type['redis']:
                $config = new Config();
                $config->setHost(Az::$app->rediscon->hostname);
                $config->setPort(Az::$app->rediscon->port);
                $config->setPassword(Az::$app->rediscon->password);
                $config->setDatabase(Az::$app->rediscon->database);
                $config->setTimeout(Az::$app->rediscon->dataTimeout);
                break;
            case self::type['apcu']:
                $config = new \Phpfastcache\Drivers\Apcu\Config();
                break;
        }
        $this->instance = CacheManager::getInstance($type, $config);

    }

    public function set($key, $value, $duration = \zetsoft\service\cores\Cache::duration)
    {
        $CachedString = $this->instance->getItem($key);
        if (!$CachedString->isHit()) {
            $CachedString->set($value)->expiresAfter($duration);//in seconds, also accepts Datetime
            return $this->instance->save($CachedString); // Save the cache item just like you do with doctrine and entities
        }
    }

    public function get($key)
    {
        $CachedString = $this->instance->getItem($key);
        if ($CachedString->isHit()) {
            return $CachedString->get();
        }
    }
    
    public function delete($key)
    {
        return $this->instance->deleteItem($key);
    }
}

