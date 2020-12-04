<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * Date:    05.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\forms;

use yii\widgets\Pjax;
use zetsoft\dbitem\data\ALLData;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;
use kartik\form\ActiveForm;
use kartik\widgets\ActiveField;

/**
 * Class    ZPjax
 * @package zetsoft\service\forms
 *
 * http://blog.neattutorials.com/yii2-pjax-tutorial/
 * https://www.yiiframework.com/doc/api/2.0/yii-widgets-pjax
 * https://github.com/yiisoft/jquery-pjax
 */
class ZPjax extends ZFrame
{

    public $url;
    public $timeout = 1000;
    public $push = true;
    public $replace = false;
    public $scrollTo;


    public $id;
    public $class;
    public $formSelector = null;
    public $linkSelector = null;

    public $container;
    public $options;

    public $type = self::type['POST'];
    public $dataType = self::dataType['html'];

    public const type = [
        'POST' => 'POST',
        'GET' => 'GET',
        'PUT' => 'PUT',
    ];

    public const dataType = [
        'xml' => 'xml',
        'html' => 'html',
        'script' => 'script',
        'json' => 'json',
        'jsonp' => 'jsonp',
        'text' => 'text',
    ];


    /**
     *
     * Function  begin
     * @param  $config
     * @return  Pjax
     */
    public function begin($config = null)
    {
        if ($config === null)
            $config = new ZPjax();

        if (is_callable($config))
            $config = $config($this);

        if (empty($config->id))
            if (!empty($this->paramGet(paramChangeReloadId)))
                $config->id = $this->paramGet(paramChangeReloadId);

        return Pjax::begin([

            'options' => [
                'id' => $config->id,
                'class' => $config->class
            ],

            'clientOptions' => [
                'type' => $config->type,
                'dataType' => $config->dataType,

                'scrollOffset' => 0,
                'maxCacheLength' => 20,

                'pushRedirect' => true,
                'replaceRedirect' => true,
                'skipOuterContainers' => true,
                'ieRedirectCompatibility' => true,

//                'url' => $config->url,
//                'timeout' => $config->timeout, //650,
//                'push' => $config->push, //true,       // enablePushState
//                'replace' => $config->replace, //false,   // enableReplaceState
//                'scrollTo' => $config->scrollTo,//0,
//                'container' => $config->container,//null,
//                'target' => $config->target, //null,
//                'fragment' => $config->fragment //null,
            ],
            'enablePushState' => $config->push,
            'enableReplaceState' => $config->replace,
            'formSelector' => $config->formSelector,
            'linkSelector' => $config->linkSelector,

            'scrollTo' => $config->scrollTo,
            'submitEvent' => 'submit',
            'timeout' => $config->timeout,
        ]);
    }


    public function end()
    {
        Pjax::end();
    }


}
