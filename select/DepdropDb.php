<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\select;

use yii\db\ActiveRecord;
use yii\web\Response;
use zetsoft\dbitem\ALL\ZAppItem;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

/**
 * Class Adder
 * @package zetsoft\service\smart
 *
 * yangi proyekt yaratish, klon qilish servisi
 *
 */
class DepdropDb extends ZFrame
{

public $parentParam = 'depdrop_parents';

public $otherParam = 'depdrop_params';

public $allowEmpty = false;


public function run(){
    Az::$app->response->format = Response::FORMAT_JSON;
    $request = Az::$app->getRequest();
    if (($selected = $request->post($this->parentParam)) && is_array($selected) && (!empty($selected[0]) || $this->allowEmpty)) {
        $params = $request->post($this->otherParam, []);

        $id = $selected[0];

        return ['output' => $this->outputCallback($id), 'selected' => $this->getSelected($id, $params)];
    }
    return ['output' => '', 'selected' => ''];
}


protected function getOutput($id, $params = [])
{
    return $this->parseCallback('outputCallback', $id, $params);
}

protected function getSelected($id, $params = [])
{
    return $this->parseCallback('selectedCallback', $id, $params);
}

protected function parseCallback($funcName, $id, $params = [])
{
    if (!isset($this->$funcName)) {
        return '';
    }
    $func = $this->$funcName;
    if (is_callable($func)) {
        return $func($id, $params);
    }
    return '';
}

protected function outputCallback($selectedId)
{
    /*Where select db relations or model attributes*/
    $db_config = Az::$app->request->queryParams['db_config'];
    $parent = Az::$app->request->queryParams['parent'];
    $target = Az::$app->request->queryParams['target'];
    $app = $this->catModel($target);

    /** @var ActiveRecord $sModelName */
    $sModelName = "\zetsoft\models\\" . $app . "\\" . $target;

    if($db_config == 'false') {
        $models = $sModelName::findOne($selectedId)->attributes;
        return Az::$app->App->eyuf->depdrop->getDbData($models);
    }
    else {
        /** @var ActiveRecord $sModelName 's column $parentName */
        $parentName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $parent));
        $models = $sModelName::find()->where([$parentName . '_id' => $selectedId])->all();
        return Az::$app->App->eyuf->depdrop->getData($models);
    }
}


}
