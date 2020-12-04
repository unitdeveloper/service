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

namespace zetsoft\service\menu;

use zetsoft\models\menu\Menu;
use zetsoft\models\page\PageView;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZJsonHelper;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\system\kernels\ZFrame;

class Json extends ZFrame
{


    /**
     *
     * Function  json
     * @param Menu $model
     * @throws \Exception
     */

    public function actions()
    {
        $actions = PageView::find()
            ->asArray()
            ->indexBy('title')
            ->all();

        return $actions;
    }

    public function run($model)
    {

        if (empty($model->json))
            return '{}';

        $return = [];
        foreach ($model->json as $item) {
            $return[] = $item;
        }

        return ZJsonHelper::encode($return);

    }

    public function getJson($post)
    {
         $return = [];

         foreach ($post as $key => $value) {
            $return[$key] = ZVarDumper::ajaxValue($value);
         }

        return $return;
    }


}

