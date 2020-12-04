<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * Date:    11.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\cores;

use PHPHtmlParser\Dom\Tag;
use yii\caching\TagDependency;
use zetsoft\dbitem\core\WebItem;
use zetsoft\models\test\TestAsror;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

class Cache extends ZFrame
{

    #region Vars
    public const duration = 24 * 60 * 60;
    public const type = [
        'file' => 'file',
        'redis' => 'redis',
        'apcu' => 'apcu',
        'array' => 'array',
        'cache' => 'cache',
    ];

    #endregion



    public function setGetTest()
    {

        $key = 'terrabaytKey1221';

        if ($this->cacheGet($key))
            return $this->cacheGet($key);
//        $model = TestAsror1::findOne(8);
        $model = new WebItem();
        $model->icon = 'it is icon1212312';

        $this->cacheSet($key, $model, self::type['cache'], new TagDependency(['tags' => TestAsror::class]));
        return $model;
    }
    /**
     *
     * Function  cacheSet
     * @param string $name
     * @param string $type
     * @return  bool
     */
    public function set($name, $value, $type, $dependency)
    {
        if (is_array($name)) {
            return Az::$app->$type->multiSet($name, $dependency);
        }
        return Az::$app->$type->set($name, $value, 0 , $dependency);
    }

    public function get($name, $type)
    {
        if (is_array($name))
            return Az::$app->$type->multiGet($name);
        return Az::$app->$type->get($name);
    }

    public function exists($name, $type)
    {
        return Az::$app->$type->exists($name);
    }


    public function delete($name, $type)
    {
        return Az::$app->$type->delete($name);
    }

    public function flush($type)
    {
        return Az::$app->$type->flush();
    }

    public function getOrSet($name, $type, $callable, $dependency)
    {
        global $boot;
        if (!$boot->env('renderCache'))
            return $callable();
        
        return Az::$app->$type->getOrSet($name, $callable, null, $dependency);
    }

    public function add($name, $value, $type, $dependency)
    {
        if (is_array($name)) {
            return Az::$app->$type->multiAdd($name, $dependency);
        }
        return Az::$app->$type->add($name, $value, $dependency);
    }
}
