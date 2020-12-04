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


use ReflectionFunction;
use yii\db\Exception;
use yii\helpers\Json;
use zetsoft\dbitem\data\Form;
use zetsoft\dbitem\data\FormDb;
use zetsoft\widgets\inputes\ZKSelect2Widget;
use zetsoft\widgets\values\ZDateFormatWidget;
use zetsoft\models\dyna\DynaConfig;
use zetsoft\service\cores\Cache;
use zetsoft\service\smart\Model;
use zetsoft\system\actives\ZData;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\module\Models;
use zetsoft\widgets\inputes\ZKSwitchInputWidget;

/**
 * Class    zWidget
 * @package zetsoft\service\forms
 */
class WiData extends ZFrame
{

    #region Vars

    public $data;
    public $value;
    public $columns;

    public $fkAttr = 'name';
    public $fkTable;

    public $fkQuery;
    public $fkAndQuery;
    public $fkOrQuery;

    public $orderBy = 'id';
    public $isFilter = false;

    #endregion

    #region Data

    public function run()
    {

        $this->modelColumns = $this->model->columns;
        $this->modelColumn = $this->modelColumns[$this->attribute];
        $this->modelConfigs = $this->model->configs;

        $this->fkTable = $this->modelColumn->fkTable;
        $this->fkAttr = $this->modelColumn->fkAttr;

        $this->fkQuery = $this->modelColumn->fkQuery;
        $this->fkAndQuery = $this->modelColumn->fkAndQuery;
        $this->fkOrQuery = $this->modelColumn->fkOrQuery;

    }


    public function data()
    {

        $this->run();

        /**
         *
         * Get Data from DB
         */

        $data = [];
        if (ZArrayHelper::keyExists($this->attribute, $this->modelColumns))
            $data = $this->modelColumn->data;

    /*    if ($this->modelColumn->widget === ZKSelect2Widget::class)
            vd($data);*/

        switch (true) {

            /** @var ZData $class */

            case !empty($data):
                switch (true) {
                    case is_array($data):
                        $this->data = $data;
                        break;

                    case is_callable($data):
                        $this->data = $data($this->model);
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

            case !empty($this->modelColumn->fkTable):
                $this->data = $this->related();
                break;

            case  ZStringHelper::endsWith($this->attribute, '_id'):
            case  ZStringHelper::endsWith($this->attribute, '_ids'):

                $this->fkTable = str_replace(['_ids', '_id'], '', $this->attribute);
                $this->data = $this->related();

                break;

        }

        return $this->data;
    }



    #endregion

    #region Related

    public function relatedClass()
    {

        if (class_exists($this->fkTable))
            return $this->fkTable;

        $relatedClassName = ZInflector::camelize($this->fkTable);

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

        $classes = Az::$app->smart->migra->scan();

        if (!ZArrayHelper::isIn($relatedClass, $classes))
             return null;

        return $relatedClass;

    }

    public function related()
    {

        $return = $this->cache('related' . "{$this->fkTable}{$this->fkAttr}{$this->orderBy}{$this->attribute}" . ZVarDumper::export([$this->fkQuery, $this->fkOrQuery, $this->fkAndQuery]), Cache::type['array'], function () {

            $relatedClass = $this->relatedClass();

            if ($relatedClass === null) {
                Az::warning($relatedClass, 'Class Not Exists');
                return [];
            }


            $Q = $relatedClass::find($relatedClass);

            if ($this->fkQuery !== null)
                $Q->where($this->fkQuery);

            if ($this->fkAndQuery !== null)
                $Q->andWhere($this->fkAndQuery);

            if ($this->fkOrQuery !== null)
                $Q->orWhere($this->fkOrQuery);

            $Q->orderBy($this->orderBy);

            $all = $Q
                ->asArray()
                ->all();

            // vd($Q->sql());
            //     vd($all);
            if ($this->isFilter)
                return ZArrayHelper::map($all, $this->attribute, $this->attribute);

            return ZArrayHelper::map($all, 'id', $this->fkAttr);

        });

        return $return;
    }


    #endregion

    #region Service


    #endregion

    #region Value

    public function clean()
    {

        $this->model = null;
        $this->value = null;
        $this->attribute = null;
        $this->data = [];

        $this->fkTable = null;

        $this->fkQuery = null;
        $this->fkAndQuery = null;
        $this->fkOrQuery = null;

    }


    public function value(bool $edit = true, $index = null, $key = null)
    {
        //  $this->clean();
        $this->data();
        if (!empty($this->modelAttrs))
            $value = ZArrayHelper::getValue($this->modelAttrs, $this->attribute);
        else
            $value = $this->model->getAttribute($this->attribute);

        /** @var FormDb $column */

        $value = $this->cleanValue($value);
        //return ZVarDumper::beauty($value);


        switch (true) {

            case $this->modelColumn->dbType === dbTypeDateTime:

                if (!empty($value))
                    /*$this->value = date('d-m-Y h:i:s', strtotime($value));*/
                    //start: Daho
                    $this->value = date('d-m-Y H:i:s', strtotime($value));
                //end

                break;

            case $this->modelColumn->dbType === dbTypeDate:

                if ($this->model->configs->reverseDate && !empty($value))
                    $this->value = date('d-m-Y', strtotime($value));
                else
                    $this->value = $value;

                break;

            case !empty($this->modelColumn->valueWidget):

                if (empty($this->modelColumn->valueOptions))
                    $this->modelColumn->valueOptions = $this->modelColumn->options;

                $options = ZArrayHelper::merge($this->modelColumn->valueOptions, [
                    'model' => $this->model,
                    'modelAttrs' => $this->modelAttrs,
                    'attribute' => $this->attribute,
                    'id' => 'value-' . $this->attribute . '-' . $index . '-' . $key,
                    'value' => $value,
                ]);

                $this->value = $this->modelColumn->valueWidget::widget($options);

                break;

            case $this->modelColumn->dbType === dbTypeBoolean:
                $this->value = $this->valueBool($value, $edit);
                break;

            case $this->modelColumn->dbType === dbTypeJson:
            case $this->modelColumn->dbType === dbTypeJsonb:

                $search = [];
                $names = [];

                if (is_array($value))
                    foreach ($value as $item) {

                        //start|DavlatovRavshan|2020.10.11
                        if (is_array($item)) {
                            if (ZArrayHelper::keyExists('name', $item))
                                $names[] = $item['name'];
                        } else {
                            if (ZArrayHelper::keyExists($item, $this->data))
                                $search[] = $this->data[$item];
                        }
                        //end|DavlatovRavshan|2020.10.11

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
                        break;
                }

                if (empty($value))
                    $this->value = null;


                break;

            case $this->modelColumn->dbType === dbTypeInteger:
            case $this->modelColumn->dbType === dbTypeString:

                if (ZArrayHelper::keyExists($value, $this->data))
                    $release = ZArrayHelper::getValue($this->data, $value);
                else
                    $release = $value;

                $this->value = ZStringHelper::truncate($release, $this->modelColumn->cropLength);

                break;

            case !empty($this->modelColumn->value):
                $this->value = $this->modelColumn->value;
                break;

            case is_callable($value):
                $this->value = $value($this->model);
                break;

            default:

                if (!empty($value))
                    $this->value = ZStringHelper::truncate($value, $this->modelColumn->cropLength);

                break;
        }

        /*
                if (empty($this->value) && !empty($value))
                    $this->value = $value;*/
        /* if ($this->modelColumn->pageSummary && empty($this->value))
             $this->value = 0;
         else */
        //todo:start Daho
        if ($this->isEmpty($this->value))
            $this->value = $this->emtyValue($this->modelColumn, $this->attribute, $edit);
        //todo:end 2lines

        return $this->value;
    }

    /**
     * @param $type
     * @param Form|FormDb $edit
     * @return int|mixed|string|null
     * @author Daho
     * @since 16.10.2020
     */
    public function emtyValue(Form $column, $attribute, $edit = true)
    {
        switch ($column->dbType) {
            case dbTypeBigInteger:
            case dbTypeDecimal:
            case dbTypeDouble:
            case dbTypeFloat:
            case dbTypeBinary:
            case dbTypeInteger:
            case dbTypeSmallInteger:
            case dbTypeTinyInteger:
                if ($this->tableRelated($attribute, $column))
                    break;

                return 0;
                break;
        }

        if ($edit)
            return Az::l('Не задано');

        return null;
    }   //8lines


    #endregion

    #region Utilities

    public function cleanValue($value)
    {

        $return = $value;
        if ($this->modelColumn->dbType === 'jsonb') {

            if (!is_array($value) && !empty($value)) {
                try {

                    $return = Json::decode($value, true);
                } catch (\Exception $exception) {
                    return $return;
                }

            }

        }

        return $return;
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


    public function valueBool($value, $edit = true)
    {

        if (!$edit)
            return $value ? Az::l('Да') : Az::l('Нет');

        $icon = 'times';
        $color = 'danger';

        if ($value) {
            $icon = 'check';
            $color = 'success';
        }

        return "<span class='fas fa-$icon text-$color'></span>";

    }


}

