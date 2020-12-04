<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\forms;


use kartik\detail\DetailView;
use kartik\grid\DataColumn;
use zetsoft\dbitem\data\FormDb;
use zetsoft\system\actives\ZActiveRecord;
use zetsoft\system\Az;
use zetsoft\system\column\ZKEditableColumn;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\module\Models;
use zetsoft\widgets\former\ZFormWidget;
use zetsoft\widgets\former\ZMultiWidget;
use zetsoft\widgets\inputes\ZFileInputWidget;
use zetsoft\widgets\inputes\ZHCheckboxButtonGroupWidget;
use zetsoft\widgets\inputes\ZHRadioButtonGroupWidget;
use zetsoft\widgets\inputes\ZKSwitchInputWidget;
use zetsoft\widgets\inputes\ZKTouchSpinWidget;

/**
 * Class    DtGrid
 * @package zetsoft\service\forms
 *
 *
 * https://demos.krajee.com/detail-view
 *
 */
class Multi extends ZFrame
{

    /* @var Models $model */
    public $model;
    public $attribute;
    public $widget;


    public $columns;
    public $columnsOne;
    public $columnsRel;
    public $config;


    public $edit;
    public $filter;
    public $summary;
    public $summaryTotal;
    public $readonly;


    public $ignoreALL = [
        ZMultiWidget::class,
        ZFormWidget::class,
        ZFileInputWidget::class,
    ];

    public $ignoreEdit = [
    ];
    public $ignoreFilter = [
        ZKTouchSpinWidget::class,
        ZKSwitchInputWidget::class,
        ZHRadioButtonGroupWidget::class,
        ZHCheckboxButtonGroupWidget::class,
    ];


    public function clean()
    {
        $this->model = null;
        $this->attribute = null;
        $this->columns = null;
        $this->columnsOne = null;
        $this->columnsRel = null;
    }


    public function run()
    {

        /** @var FormDb $column */
        $return = [];

        $configs = $this->model->configs;
        $columns = $this->model->columns;

        foreach ($columns as $key => $column) {

            Az::$app->forms->wiData->clean();
            Az::$app->forms->wiData->model = $this->model;
            Az::$app->forms->wiData->attribute = $key;
            $column->data = Az::$app->forms->wiData->data();

            $return[] = [

                'name' => $key,
                'type' => $column->widget,

                'title' => $column->title,
                'value' => '',
                'defaultValue' => $column->value,
                'items' => $column->data,
                'options' => ZArrayHelper::merge($column->options, [
                    'data' => $column->data,
                    'id' => $this->config['dependAttr'],
                ]),

                'enableError' => false,
                'errorOptions' => [
                    'class' => 'help-block help-block-error'
                ],
                'attributeOptions' => [],

                'columnOptions' => [],
                'inputTemplate' => '{input}',
                
            ];

        }

        return $return;
    }


    protected function columnStructure($configs)
    {

        foreach ($this->columns as $key => $column) {

            if (!empty($configs->before))
                if (ZArrayHelper::keyExists($key, $configs->before))
                    foreach ($configs->before[$key] as $inKey => $inItem) {
                        $this->columnsRel[] = $inItem;
                    }

            $this->columnsRel[] = $column;

            if (!empty($configs->after))
                if (ZArrayHelper::keyExists($key, $configs->after))
                    foreach ($configs->after[$key] as $inKey => $inItem) {
                        $this->columnsRel[] = $inItem;
                    }

        }

        return $this->columnsRel;

    }

}
