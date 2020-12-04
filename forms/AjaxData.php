<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * Date:    9/20/2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\forms;


use zetsoft\dbitem\data\FormDb;
use zetsoft\models\dyna\DynaConfig;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\service\cores\Cache;
use zetsoft\service\smart\Model;
use zetsoft\system\actives\ZData;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\helpers\ZJsonHelper;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\module\Models;
use zetsoft\widgets\inputes\ZKSwitchInputWidget;
use function False\true;

/**
 * Class    zWidget
 * @package zetsoft\service\forms
 */
class AjaxData extends ZFrame
{

    #region Vars

    public $data;
    public $value;
    public $limit;
    public $page;
    public $searchs;


    /* @var FormDb[] $columns */
    public $columns;

    #endregion


    #region Data

    public function data()
    {

        $this->columns = $this->model->columns;

        /**
         *
         * Get Data from DB
         */

        $data = [];
        if (ZArrayHelper::keyExists($this->attribute, $this->columns))
            $data = $this->columns[$this->attribute]->data;


        $query = null;
        if (!empty($this->columns[$this->attribute]->fkQuery))
            $query = $this->columns[$this->attribute]->fkQuery;

        switch (true) {

            /** @var ZData $class */

            case !empty($data):
                switch (true) {
                    case is_array($data):
                        $this->data = $data;
                        break;

                    case is_string($data):
                        $class = $data;
                        if (class_exists($class)) {

                            /** @var ZData $obj */
                            $obj = new $class();
                            $this->data = $obj->lang();
                        }
                        break;
                }
                break;

            case !empty($this->columns[$this->attribute]->fkTable):
                $relatedTable = $this->columns[$this->attribute]->fkTable;

                $this->data = $this->related($relatedTable, $query);
                break;

            case  ZStringHelper::endsWith($this->attribute, '_id'):
            case  ZStringHelper::endsWith($this->attribute, '_ids'):

                $relatedTable = str_replace(['_ids', '_id'], '', $this->attribute);
                $this->data = $this->related($relatedTable, $query);

                break;

        }


// vd($this->data);
        return $this->data;
    }


    public function related($relatedTable, $query = null)
    {

        $return = $this->cache('related' . $relatedTable . ZVarDumper::export($query), Cache::type['array'], function () use ($relatedTable, $query) {
            $relatedClassName = ZInflector::camelize($relatedTable);

            /** @var Models $relatedClassApp */
            /** @var Models $relatedClassALL */

            $relatedClassApp = $this->bootFull($relatedClassName);
            $relatedClassALL = Model::Path['namespaceModelAll'] . $relatedClassName;

            switch (true) {

                case class_exists($relatedClassALL):
                    $relatedClass = $relatedClassALL;
                    break;

                case class_exists($relatedClassApp):
                    $relatedClass = $relatedClassApp;
                    break;

                default:
                    $relatedClass = null;

            }

            if ($relatedClass === null) {
                Az::warning($relatedClass, 'Class Not Exists');
                return [];
            }

            $Q = $relatedClass::find();
            $name = (new $relatedClass)->configs->name;

            if ($query !== null)
                $Q->where($query);

            if (!empty($this->searchs))
                $Q->where(['like', $name, $this->searchs]);

            $all = $Q->orderBy('id')
                ->asArray()
                ->all();

            $count = $Q->orderBy('id')
                ->count();

            $page = $this->page;
            $limit = $this->getLimit($count);

            $return = [];
            for ($i = $page; $i <= $page + $limit; $i++) {

                if ($i >= $count) {
                    return [
                        'results' => [
                            [
                                'id' => 'end',
                                'text' => Az::l('Данные закончились'),
                                'children' => []
                            ]
                        ]
                    ];
                }

                $return[$i] = [
                    'id' => $all[$i]['id'],
                    'text' => $all[$i]['name'],
                ];

            }

            return $return;

        });

        return $return;
    }


    public function getLimit($count)
    {


        switch (true) {

            case !empty($this->limit):
                $limit = $this->limit;
                break;

            default:
                $limit = 50;
                break;

        }

        if ($count < $limit)
            $limit = $count - 1;

        return $limit;

    }

    #endregion

    #region MultiRemove


    public function multiRemoveTest()
    {
        return $this->multiRemove(1, 'shop_option', 9, 'ShopProductNew');
    }

    public function multiRemove($index, $parentAttr, $parentId, $parentClass)
    {

        /**
         *
         * ModelClass
         */
        if (empty($parentClass))
            return [
                'parentClass Is Empty',
            ];

        $modelClass = $this->bootFull($parentClass);


        /** @var Models $model */
        $model = $modelClass::findOne($parentId);

        if ($model === null)
            return [
                'model Is Empty',
                $modelClass,
                $parentId
            ];

        $value = $model->$parentAttr;

        ZArrayHelper::remove($value, $index);

        $model->$parentAttr = $value;
        return $model->save();

    }

    #endregion


    #region AutoSave

    public function autoSaveTest()
    {
        $id = null;
        $value = random_int(1, 666);
        $modelClassName = 'ShopOptionTypeFormNew';
        $parentClass = 'PlaceAdressThree';

        $attr = 'text';
        $parentAttr = 'home';
        $parentId = 7;
        $index = 1;

        $data = Az::$app->forms->ajaxData->autoSave($id, $attr, $index, $value, $parentAttr, $parentId, $parentClass, $modelClassName);

        vd($data);
    }


    public function autoSave($id, $attr, $index, $value, $parentAttr, $parentId, $parentClass, $modelClassName)
    {

        /**
         *
         * Fileinput
         */

        if (ZStringHelper::find($value, '[{'))
            $value = ZJsonHelper::decode($value);

        if (is_array($value))
            if (!empty(ZArrayHelper::getValue($value, '0.name')))
                $value = ZArrayHelper::getColumn($value, 'name');


        /**
         *
         * ModelClass
         */
        if (!empty($parentClass))
            $modelClassName = $parentClass;

        $modelClass = $this->bootFull($modelClassName);

        /*              var_dump($id, $attr, $index, $value, $parentAttr, $parentId, $parentClass, $modelClassName);*/
        /**
         *
         * ParentId
         */
        if (!empty($parentId))
            $id = $parentId;


        /** @var Models $model */
        $model = $modelClass::findOne($id);

        if ($model === null)
            return [
                'modelIsEmpty',
                $modelClass,
                $id
            ];

        /**
         *
         * Attribute
         */

        $myAttr = null;

        if (!empty($parentAttr)) {
            $myAttr = $attr;
            $attr = $parentAttr;
        }
        $data = $model->$attr;


        if (!empty($parentAttr)) {
            if ($index !== null)
                $data[$index][$myAttr] = $value;
            else
                $data[$myAttr] = $value;

            $model->$attr = $data;
        } else
            $model->$attr = $value;

        $model->columns();

        $rename = Az::$app->forms->modelz->uploadFile($model, $attr);


        if ($saved = $model->save()) {

            Az::$app->forms->wiData->clean();
            Az::$app->forms->wiData->model = $model;
            Az::$app->forms->wiData->attribute = $attr;
            $value = Az::$app->forms->wiData->value($id);

            return [
                '$saved' => $saved,
                '$id' => $id,
                '$attr' => $attr,
                '$index' => $index,
                '$myAttr' => $myAttr,
                '$rename' => $rename,
                '$model::className()' => $model::className(),
                '$value' => $value,
                '$parentAttr' => $parentAttr,
                '$parentId' => $parentId,
                '$parentClass' => $parentClass,
                '$modelClassName' => $modelClassName,
            ];

        }

        return [
            'Cannot Save',
            $model->errors
        ];

    }

    #endregion

    #region Service

    public function clean()
    {
        $this->model = null;
        $this->attribute = null;
        $this->data = [];
        $this->columns = [];
    }

    #endregion

    #region Value

    public function value(bool $edit = true, $index = null, $key = null)
    {

        $this->data();

        $attr = $this->attribute;

        $value = $this->model->$attr;

        /** @var FormDb $column */

        $column = $this->columns[$this->attribute];

        switch (true) {

            case !empty($this->columns[$this->attribute]->valueWidget):

                $options = ZArrayHelper::merge($this->columns[$this->attribute]->valueOptions, [
                    'model' => $this->model,
                    'attribute' => $this->attribute,
                    'id' => 'value-' . $this->attribute . '-' . $index . '-' . $key,
                ]);
                $this->value = $this->columns[$this->attribute]->valueWidget::widget($options);
                break;


            case $column->dbType === dbTypeBoolean:
                $this->value = $this->valueBool($value, $edit);
                break;


            case $column->dbType === dbTypeJson:
            case $column->dbType === dbTypeJsonb:

                $search = [];
                $names = [];
                if (is_array($value))
                    foreach ($value as $item) {
                        if (ZArrayHelper::keyExists($item, $this->data))
                            $search[] = $this->data[$item];

                        if (is_array($item))
                            if (ZArrayHelper::keyExists('name', $item))
                                $names[] = $item['name'];

                    }

                switch (true) {
                    case !empty($search):
                        $this->value = ZVarDumper::beauty($search);
                        break;

                    case !empty($names):
                        $this->value = ZVarDumper::beauty($names);
                        break;

                    default:
                        $this->value = ZVarDumper::beauty($value);
                }

                break;


            case $column->dbType === dbTypeInteger:
            case $column->dbType === dbTypeString:

                if (ZArrayHelper::keyExists($value, $this->data))
                    $this->value = $this->data[$value];
                else
                    $this->value = $value;
                break;


            case !empty($this->columns[$this->attribute]->value):

                $this->value = $this->columns[$this->attribute]->value;
                break;

            default:

                if (!empty($value))
                    $this->value = $value;

        }

        if (empty($this->value) || $this->isEmpty($this->value))
            $this->value = Az::l('Не задано');

        return $this->value;
    }

    public function valueBool($value, $edit = true)
    {

        if (!$edit)
            return $value ? Az::l('Да') : Az::l('Нет');

        $answer = 'Нет';
        $switch = 'off';
        $type = 'danger';
        $style = 'margin-left: 40px';

        if ($value) {
            $answer = 'Да';
            $switch = 'on';
            $type = 'success';
            $style = 'margin-right: 40px';
        }

        $column = $this->columns[$this->attribute];

        if ($column->readonly)
            $style = '';

        return '<div style="width: auto" class="bootstrap-switch bootstrap-switch-mini ro-switcher">
                    <div class="bootstrap-switch-container" style="' . $style . '">
                        <span style="width: 40px;" class="bootstrap-switch-handle-' . $switch . ' bootstrap-switch-' . $type . '">' . $answer . '</span>
                    </div>
                </div>
          ';

    }


    public function isEmpty($value)
    {

        $value = strtr($value, [
            "\r" => '',
            "\n" => '',
            "\r\n" => '',
            '  ' => ' '
        ]);

        if (empty($value))
            return true;

        return false;

    }


}

