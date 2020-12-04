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
class Detail extends ZFrame
{

    /* @var Models $model */
    public $model;
    public $attribute;


    public $columns;
    public $columnsOne;
    public $columnsRel;


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

        $this->ignoreEdit = ZArrayHelper::merge($this->ignoreALL, $this->ignoreEdit);
        $this->ignoreFilter = ZArrayHelper::merge($this->ignoreALL, $this->ignoreFilter);

        /**
         *
         * Model Columns
         */

        $columns = $this->model->columns;
        $configs = $this->model->configs;

        foreach ($columns as $key => $column) {

            if (ZArrayHelper::isIn($key, $configs->nameShowEx)) {
                continue;
            }

            if (!$column->showDetail) {
                Az::debug($key, 'We Dont show it!');
                continue;
            }

            $this->columnsOne[$key] = $column;

        }


        /*if (!empty($this->model->configs->query))
            $Q = $this->model->configs->query;
        else
            $Q = $this->model::find();

        $models = $Q->all();
        $counts = new $this->model();

        foreach ($models as $value)
            foreach ($value->attributes as $key => $item)
                if (is_int($item))
                    $counts->$key += $value->$key;*/

        /** @var FormDb $column */
        foreach ($this->columnsOne as $key => $column) {


            /**
             *
             * Filter Widget
             */

            if (empty($column->filterWidget)) {
                $column->filterWidget = $column->widget;
                $column->filterOptions = $column->options;
            }

            $widgetOptions = ZArrayHelper::merge([
                'class' => $column->widget,
                
                'config' => [
                    'grapes' => false,
                    'config' => [
                        'readonly' => $column->readonlyWidget
                    ]
                ]
            ], $column->options);

            Az::$app->forms->wiData->clean();
            Az::$app->forms->wiData->model = $this->model;
            Az::$app->forms->wiData->attribute = $key;
            $value = Az::$app->forms->wiData->value();


            /**
             * attribute: string|Closure, the attribute name. This is required if either label or value is not specified.

             * label: string|Closure, the label associated with the attribute. If this is not specified, it will be generated from the attribute name.
             *
             *  value: string|Closure, the value to be displayed. If this is not specified, it will be retrieved from model using the attribute name by calling ArrayHelper::getValue(). Note that this value will be formatted into a displayable text according to the format option.
             *
             * format: string|Closure, the type of the value that determines how the value would be formatted into a displayable text. Please refer to Formatter for supported types.
             *
             * visible: boolean|Closure, whether the attribute is visible. If set to false, the attribute will NOT be displayed.
             *
             * The additional attribute settings that are supported in this enhanced widget are:
             *
             * columns: array|Closure, the configuration of multiple attributes in the same row. Each element, will be configured as an array similar to the attributes setting. When this is set, the label or value or attribute setting is entirely skipped.
             *
             * rowOptions: array|Closure, HTML attributes for the row (if not set, will default to the rowOptions set at the widget level).

             * labelColOptions: array|Closure, HTML attributes for the label column (if not set, will default to the labelColOptions set at the widget level).
             *
             * valueColOptions: array|Closure, HTML attributes for the value column (if not set, will default to the valueColOptions set at the widget level).
             *
             * group: boolean|Closure, whether to group the selection by merging the label and value into a single column. Defaults to false.
             *
             * groupOptions: array|Closure, HTML attributes for the grouped/merged column when group is set to true.
             *
             * type: string|Closure, the input type for rendering the attribute in edit mode. If not provided, this defaults to DetailView::INPUT_TEXT. All common input types are supported including widgets.
             *
             * inputWidth: string|Closure, the width of the container holding the input, should be appended along with the width unit (px or %). NOTE: This property is deprecated since v1.7.1 and will be removed in future releases. Use inputContainer to control HTML attributes for the container enclosing the input.

             * inputType: string|Closure, the HTML 5 input type if type is set to DetailView::INPUT_HTML5_INPUT.
             *
             * fieldConfig: array|Closure, optional, the Active field configuration.
             *
             * options: array|Closure, optional, the HTML attributes for the input.
             *
             * updateAttr: string|Closure, optional, the name of the attribute to be updated, when in edit mode (if you do not want it to be same as attribute. If not provided, this will default to the attribute setting.
             *
             * updateMarkup: string|Closure, the raw markup to render in edit mode. If not set, this normally will be automatically generated based on attribute or updateAttr setting. If this is set it will override the default markup.
             *
             *
             * https://demos.krajee.com/detail-view#settings
             */
            $this->columns[$key] = [
                'attribute' => $key,
                'updateAttr' => $key,

                'value' => $value,
                'type' => DetailView::INPUT_WIDGET,
                'widgetOptions' => $widgetOptions,
                'options' => [],
                'fieldConfig' => null,

                'label' => $column->title,
                'format' => $column->format, // text | raw
                'visible' => $column->showDetail,

                'rowOptions' => [],
                'labelColOptions' => [],
                'valueColOptions' => [],

                'group' => false,
                'groupOptions' => [],

                'items' => null,
                'inputContainer' => [],


                'updateMarkup' => null,
                'inputWidth' => null,
                'inputType' => null,
            ];

        }

        //$this->columns = $this->columnStructure($configs);

        return true;
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
