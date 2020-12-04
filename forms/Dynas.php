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


use kartik\grid\DataColumn;
use yii\data\ArrayDataProvider;
use yii\debug\models\timeline\DataProvider;
use yii\grid\GridView;
use zetsoft\dbitem\data\ALLApp;
use zetsoft\dbitem\data\ALLData;
use zetsoft\dbitem\data\ConfigDB;
use zetsoft\dbitem\data\Form;
use zetsoft\dbitem\data\FormDb;
use zetsoft\models\dyna\DynaConfig;
use zetsoft\models\dyna\DynaMulti;
use zetsoft\models\test\TestOrder;
use zetsoft\system\actives\ZActiveRecord;
use zetsoft\system\actives\ZModel;
use zetsoft\system\Az;

use zetsoft\system\column\ZKDataColumn;
use zetsoft\system\column\ZKEditableColumn;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\module\Models;
use zetsoft\widgets\audios\ZPlyrWidget;
use zetsoft\widgets\former\ZFormWidget;
use zetsoft\widgets\former\ZMultiWidget;
use zetsoft\widgets\inputes\ZCKEditorWidget;
use zetsoft\widgets\inputes\ZFileInputWidget;
use zetsoft\widgets\inputes\ZHCheckboxButtonGroupWidget;
use zetsoft\widgets\inputes\ZHInputWidget;
use zetsoft\widgets\inputes\ZHPasswordInputWidget;
use zetsoft\widgets\inputes\ZHRadioButtonGroupWidget;
use zetsoft\widgets\inputes\ZIconPickerWidget;
use zetsoft\widgets\inputes\ZKDatepickerWidget;
use zetsoft\widgets\inputes\ZKDateRangePickerWidget;
use zetsoft\widgets\inputes\ZKSelect2Widget;
use zetsoft\widgets\inputes\ZKSwitchInputWidget;
use zetsoft\widgets\inputes\ZKTouchSpinWidget;
use zetsoft\widgets\inputes\ZPeriodPickerSingleWidget;
use zetsoft\widgets\inputes\ZPeriodPickerWidget;
use zetsoft\widgets\places\ZGoogleReadyWidget;
use zetsoft\widgets\places\ZGoogleReadyWidgetPlace;
use zetsoft\widgets\values\ZDateFormatWidget;

/*use zetsoft\widgets\inputes\ZMultiWidget;*/

class Dynas extends ZFrame
{
    /** @var DataProvider */
    public $provider;

    public $editableClass;

    public $columns;
    public $columnsOne;
    public $columnsRel;

    public $edit;
    public $filter;
    public $summary;
    public $summaryTotal;

    public $headerOptions;
    public $contentOptions;

    public $widget;
    public $dynaWidget;
    public $options;

    public $ignoreALL = [
    ];

    public $ignoreEdit = [
    ];

    public $ignoreFilter = [
        ZPlyrWidget::class,
        ZFormWidget::class,
        ZMultiWidget::class,
        ZFileInputWidget::class,
        ZFileInputWidget::class,
        ZIconPickerWidget::class,
        ZCKEditorWidget::class,
        ZKSwitchInputWidget::class,
        ZGoogleReadyWidget::class,
        ZGoogleReadyWidgetPlace::class,
        ZHRadioButtonGroupWidget::class,
        ZHCheckboxButtonGroupWidget::class,
        ZKTouchSpinWidget::class,
    ];

    public $dateWidgets = [
        ZKDatepickerWidget::class,
    ];


    public function clean()
    {
        $this->model = null;
        $this->attribute = null;
        $this->columns = null;
        $this->columnsOne = null;
        $this->columnsRel = null;
    }


    #region DynaID


    public function dynaId($modelClassName)
    {
        $userId = $this->userIdentity()->id;
        $url = $this->urlArrayStr;
        return "$modelClassName-$url";
    }


    public function dynaIdGet($modelClassName, $url)
    {
        $userId = $this->userIdentity()->id;
        return "$modelClassName-$url-$userId";
    }


    public function dynaWidgetTest()
    {

        $model = $this->modelGet(TestOrder::class, 3);

        Az::$app->forms->dynas->clean();
        Az::$app->forms->dynas->model = $model;
        Az::$app->forms->dynas->model();

        $cols = Az::$app->forms->dynas->columns;
        vdd($cols);

    }

    #endregion


    #region Names


    /**
     *
     * Function  nameShow
     * @param FormDb $column
     * @param ConfigDB $configs
     * @return  bool
     */
    public function nameShow($column, $configs)
    {

        $return = true;
        if (!empty($configs->nameShowEx))
            if (ZArrayHelper::isIn($column->name, $configs->nameShowEx))
                $return = false;
                
        if (!empty($configs->nameShow))
            if (!ZArrayHelper::isIn($column->name, $configs->nameShow))
                $return = false;

        return $return;
    }


    #endregion


    #region Roles


    /**
     *
     * Function  roleFilter
     * @param FormDb $column
     * @return  bool
     */
    public function roleShow($column)
    {

        $return = true;

        if (!empty($column->roleShowEx))
            if (ZArrayHelper::isIn($this->userRole(), $column->roleShowEx))
                $return = false;

        if (!empty($column->roleShow))
            if (!ZArrayHelper::isIn($this->userRole(), $column->roleShow))
                $return = false;

        return $return;
    }

    /**
     *
     * Function  roleFilter
     * @param FormDb $column
     * @return  bool
     */
    public function roleFilter($column)
    {

        $return = true;

        if (!empty($column->roleFilterEx))
            if (ZArrayHelper::isIn($this->userRole(), $column->roleFilterEx))
                $return = false;

        if (!empty($column->roleFilter))
            if (!ZArrayHelper::isIn($this->userRole(), $column->roleFilter))
                $return = false;

        return $return;
    }

    /**
     *
     * Function  roleFilter
     * @param FormDb $column
     * @return  bool
     */
    public function roleEdit($column)
    {
        $return = true;

        if (!empty($column->roleEditEx))
            if (ZArrayHelper::isIn($this->userRole(), $column->roleEditEx))
                $return = false;
                
        if (!empty($column->roleEdit))
            if (!ZArrayHelper::isIn($this->userRole(), $column->roleEdit))
                $return = false;

        return $return;
    }


    #endregion

    #region Main


    public function model()
    {

        $filterTypes = [
            dbTypeInteger,
            dbTypeString,
            dbTypeBigInteger,
            /* dbTypeTime,
             dbTypeDate,*/
        ];
        // vdd($this->columns);
        $this->ignoreEdit = ZArrayHelper::merge($this->ignoreALL, $this->ignoreEdit);

        $this->ignoreFilter = ZArrayHelper::merge($this->ignoreALL, $this->ignoreFilter);

        // vd($this->model->operator);
        //   $this->model->columns();

        $columns = $this->model->columns;
        $configs = $this->model->configs;

        $dynaId = Az::$app->forms->dynas->dynaId($this->model->className);
        $coreDyna = DynaConfig::findOne([
            'dynaId' => $dynaId,
            'active' => true
        ]);
        $service = Az::$app->smart->dyna;

        /** @var FormDb $column */

        $class = $this->model->clazz;

        if (!empty($this->model->configs->query))
            $Q = $this->model->configs->query;
        else
            $Q = $class::find();

        $sortColumns = [];

        /*if ($coreDyna) {
            $models = DynaMulti::find()
                ->where([
                    'dyna_config_id' => $coreDyna->id,
                    'dynaId' => $dynaId,
                    'active' => true,
                ])
                ->orderBy('id')
                ->all();
            $filters = Az::$app->market->filterForm->getFilters($models);
            foreach ($filters as $filter) {

                $operator = ZArrayHelper::getValue($filter, 'query');

                switch ($operator) {

                    case 'or':
                        ZArrayHelper::remove($filter, 'query');
                        $Q->orWhere($filter);
                        break;

                    default:
                        ZArrayHelper::remove($filter, 'query');
                        $Q->andWhere($filter);
                        break;

                }
            }

            if (!empty($coreDyna->column))
                $columns = $service->columnMerge($coreDyna->column, $columns);

            if (!empty($coreDyna->sort)) {
                $sortColumns = $service->fixSort($coreDyna->sort, $this->model);
            }
        }*/

        $columnsAll = [];
        if (!empty($sortColumns)) {
            foreach ($sortColumns as $value) {
                if (ZArrayHelper::keyExists($value, $columns))
                    $columnsAll[$value] = $columns[$value];
            }
        } else
            $columnsAll = $columns;

        $summary = $this->summary();
        foreach ($columnsAll as $key => $column) {
            //todo:start Daho
            if (!$column->showDyna)
                continue;


            /**
             *
             * Show and Show Ex
             */


            if (!Az::$app->forms->dynas->roleShow($column))
                continue;

            if (!Az::$app->forms->dynas->roleEdit($column))
                $column->readonly = true;

            if (!Az::$app->forms->dynas->roleFilter($column))
                $column->filter = false;


            //todo:end  8lines
            $former = Az::$app->forms->former;
            if (!empty($column->dynaWidget)) {
                $column->widget = $column->dynaWidget;
                $column->options = $column->dynaOptions;
            }

            if (empty($column->filterWidget)) {
                $column->filterWidget = $column->widget;
                $column->filterOptions = $column->options;
            }

            if (empty($column->filterOptions))
                $column->filterOptions = $column->options;

            if (ZArrayHelper::isIn($column->filterWidget, $this->ignoreFilter)) {
                $column->filterWidget = ZHInputWidget::class;
                $column->filterOptions = [];
            }

            if (empty($column->valueOptions))
                $column->valueOptions = $column->options;

            /*if (ZArrayHelper::isIn($column->filterWidget, $this->ignoreFilter))
                $column->fil    terWidget = null;*/

            if ($column->widget === ZKDatepickerWidget::class) {
                $column->valueWidget = ZDateFormatWidget::class;
                $column->valueOptions = [];
                if ($column->dbType = dbTypeDate)
                    $column->valueOptions = [
                        'config' => [
                            'hour' => true,
                        ]
                    ];
            }
            //   vd('aaaa',$column->options);
            $former->checkForms($column->widget, $column->options);
            $former->checkForms($column->filterWidget, $column->filterOptions);
            $former->checkForms($column->dynaWidget, $column->dynaOptions);
            $former->checkForms($column->valueWidget, $column->valueOptions);

            $modelBase = strtolower($this->model->className);

            $editClass = $this->model->configs->editClass;
            if ($column->editClass !== null)
                $editClass = $column->editClass;

            /*
                        $filter = $this->model->configs->filter;
            if ($column->filter !== null)
                            $filter = $column->filter;*/

            $editableOptions = null;
            if ($editClass === ALLData::editClass['popover'])
                $editableOptions = $editClass::run($configs, $column, $key);

            /*
        if ($column->filterWidget === ZKSelect2Widget::class) {

               $column->filterOptions = ZArrayHelper::merge([
                   'config' => [
                       'multiple' => true,
                   ]
               ], $column->filterOptions);

           }*/
            /*   $integers = [
                   dbTypeInteger,
                   dbTypeBigInteger
               ];

               $summary = false;
               if (ZArrayHelper::isIn($column->dbType, $integers))
                   $summary = true;*/
            //todo:start Daho
            $sum_val = false;
            if ($column->pageSummary)
                $sum_val = (string)ZArrayHelper::getValue($summary, $key);
            //todo:end 3 lines


            $columnALL = [
                'class' => $editClass,
                'group' => $column->group,
                'model' => $this->model,
                'header' => $column->title,
                'enableSorting' => true,
                //   'defaultSort' => $this->model->configs->defaultOrder,
                //  'defaultSort' => $this->model->configs->defaultOrder,
                'defaultSort' => [
                    'id' => SORT_ASC
                ],
                //  'sort' => $this->model->configs->sort,
                'sort' => true,
                'groupedRow' => $column->groupedRow,
                'subGroupOf' => $column->subGroupOf,
                'groupFooter' => $column->groupFooter,
                'groupOddCssClass' => $column->groupOddCssClass,
                'groupEvenCssClass' => $column->groupEvenCssClass,
                'editableOptions' => $editableOptions,
                'widgetClass' => $column->widget,
                'widgetOptions' => ZArrayHelper::merge($column->options, [

                    'config' => [
                        'readonly' => $column->readonlyWidget,
                        'isDepend' => true,
                        'grapes' => false,
                    ]
                ]),
                'dbType' => $column->dbType,
                'readonly' => $column->readonly,
                'attribute' => $key,
                'filter' => $column->filter,
                'filterType' => $column->filterWidget,
                'format' => $column->format,
                'width' => $column->width,
                'mergeHeader' => $column->mergeHeader,
                'hiddenFromExport' => $column->hiddenFromExport,
                'pageSummary' => $sum_val,

                'filterWidgetOptions' => ZArrayHelper::merge([

                    'id' => $modelBase . '-' . $key,
                    'model' => $this->model,
                    // 'value' => '',
                    'attribute' => $this->attribute,
                    'config' => [
                        //'ajax' => true,
                        //'readonly' => $column->readonlyWidget,
                        'grapes' => false,
                        'hasIcon' => false,
                        'hasPlace' => false,
                        'isFilter' => true
                    ],

                ], $column->filterOptions),

                /*
                                'value' => static function ($model, $key, $index, DataColumn $dataColumn) use ($configs) {

                                    $model->configs = $configs;
                                    $model->columns();

                                    Az::$app->forms->wiData->clean();
                                    Az::$app->forms->wiData->model = $model;
                                    Az::$app->forms->wiData->attribute = $dataColumn->attribute;

                                    if ($dataColumn::className() === ZKEditableColumn::class)
                                        $edit = true;
                                    else
                                        $edit = false;

                                    return Az::$app->forms->wiData->value($edit, $index, $key);

                                },*/


                'contentOptions' => $this->contentOptions,
                'headerOptions' => $this->headerOptions,
            ];
            $this->columns[$key] = $columnALL;

        }

        $this->columns = $this->column($configs);

        return true;
    }

    /**
     *
     * Function  formColumns
     * @return  bool
     * @todo generate columns in Dyna form mode
     * @author Daho
     */
    public function form()
    {

        $configs = $this->model->configs;
        $columns = [];
        $summary = $this->summary();
        foreach ($this->model->columns as $key => $column) {
            if (!ZArrayHelper::isIn($key, $configs->nameOn) && ZArrayHelper::isIn($key, $configs->nameShowEx))
                continue;


            if (!Az::$app->forms->dynas->roleShow($column))
                continue;

            if (!Az::$app->forms->dynas->roleEdit($column))
                $column->readonly = true;

            if (!Az::$app->forms->dynas->roleFilter($column))
                $column->filter = false;


            if (!$column->showDyna)
                continue;

            if (empty($column->filterWidget)) {
                $column->filterWidget = $column->widget;
                $column->filterOptions = $column->options;
            }

            if (ZArrayHelper::isIn($column->filterWidget, $this->ignoreFilter)) {
                $column->filterWidget = ZHInputWidget::class;
                $column->filterOptions = [];
            }

            //todo:start Daho
            $sum_val = false;
            if ($column->pageSummary)
                $sum_val = ZArrayHelper::getValue($summary, $key);
            //todo:end 3 lines

            $this->columns[$key] = [
                'class' => ZKDataColumn::class,
                'header' => $column->title,
                'group' => $column->group,
                'groupedRow' => $column->groupedRow,
                'subGroupOf' => $column->subGroupOf,
                'groupFooter' => $column->groupFooter,
                'attribute' => $key,
                'filter' => $column->filter,
                'filterType' => $column->filterWidget,
                'format' => $column->format,
                'width' => $column->width,
                'mergeHeader' => $column->mergeHeader,
                'hiddenFromExport' => $column->hiddenFromExport,
                'pageSummary' => (string)$sum_val,
                'pageSummaryFunc' => $column->pageSummaryFunc,
                'filterWidgetOptions' => [
                    'config' => [
                        'hasIcon' => false
                    ]
                ],
                'value' => function ($model, $key, $index, DataColumn $dataColumn) {


                    $attribute = $dataColumn->attribute;
                    //  $data = [];
                    $value = isset($model->$attribute) ? $model->$attribute : null;

                    //    vd($model->columns[$attribute]->valueShow);
                    //return "<a>$value</a>";

                    switch (true) {
                        //todo:start Daho
                        case !empty($model->configs->valueShow[$attribute]):
                            $value = $model->configs->valueShow[$attribute];
                            break;
                        //todo:end 3

                        case !empty($model->columns[$attribute]->valueWidget):

                            $options = ZArrayHelper::merge($model->columns[$attribute]->valueOptions, [
                                'model' => $model,
                                'attribute' => $attribute,
                                'id' => 'value-' . $attribute . '-' . $index . '-' . $key,
                            ]);

                            $value = $model->columns[$attribute]->valueWidget::widget($options);
                            break;

                        case !empty($model->columns[$attribute]->data):
                            $value = ZArrayHelper::getValue($model->columns[$attribute]->data, $model->$attribute);
                            break;


                        case !empty($model->columns[$attribute]->value):
                            $value = $model->columns[$attribute]->value;
                            break;

                        case is_array($value):
                            $value = ZVarDumper::beauty($value);
                            break;
                        default:

                            if (!empty($value))
                                $value = ZStringHelper::truncate($value, 200);
                            break;
                    }

                    if ($model->columns[$attribute]->pageSummary && empty($value))
                        $value = 0;
                    else
                        //todo:start Daho
                        if (Az::$app->forms->wiData->isEmpty($value)) {

                            $value = Az::$app->forms->wiData->emtyValue($model->columns[$attribute]->dbType, $attribute, false);
                        }
                    //todo:end
                    return $value;
                },
                'contentOptions' => $this->contentOptions,
                'headerOptions' => $this->headerOptions,
                // 'options' => $this->options,
            ];
        }

        $this->columns = $this->column($configs);

        return true;
    }


    #endregion


    #region Utils

    protected
    function column($configs)
    {

        foreach ($this->columns as $key => $column) {

            if (!empty($configs->before))
                if (ZArrayHelper::keyExists($key, $configs->before))
                    foreach ($configs->before[$key] as $inKey => $inItem) {
                        $this->columnsRel[] = $inItem;
                    }

            $this->columnsRel[] = $column;

            if (!empty($configs->after))
                if (ZArrayHelper::keyExists($key, $configs->after)) {
                    foreach ($configs->after[$key] as $inKey => $inItem) {
                        $this->columnsRel[] = $inItem;
                    }
                }

        }


        return $this->columnsRel;

    }


    #endregion


    #region Item


    /**
     *
     * Function  itemColumnGen
     * @return  ArrayDataProvider
     * @todo generate provider in Dyna item mode
     * @author Daho
     */
    public function item($data)
    {
        $app = new ALLApp();

        $item = ZArrayHelper::getValue($data, 0);

        $columns = [];
        foreach (get_object_vars($item) as $var => $value) {
            $column = new Form();
            $column->title = $var;
            if (is_array($value))
                $column->dbType = dbTypeJsonb;

            $app->columns[$var] = $column;

            $columns[] = [
                'class' => ZKDataColumn::class,
                'attribute' => $var,
            ];

        }

        $this->columns = $columns;

        $app->cards = [];

        $d = [];
        foreach ($data as $item) {
            $elem = Az::$app->forms->former->model($app);
            //  vdd($elem);
            foreach (get_object_vars($item) as $var => $value) {
                $elem->$var = $value;
            }
            $d[] = $item;
        }
        $this->model = $app;

        return new ArrayDataProvider([
            'allModels' => $d
        ]);

    }

    #endregion


    #region Test

    public function dynamicModel()
    {
        $app = new ALLApp();

        $column = new Form();
        $column->title = 'rg';
        $app->columns['name'] = $column;

        $column = new Form();
        $column->title = 'wddddd';
        $app->columns['title'] = $column;

        $model = Az::$app->forms->former->model($app);

        return $model;
    }

    public function dynamicTest($model)
    {

        $data = [];

        /** @var Models $item */
        $item = clone $model;
        $item->name = 'fds';
        $item->title = 1;
        $item->configs->valueShow['name'] = '1111111111111';
        $data[] = $item;

        $items = clone $model;
        $items->name = 'trfuy';
        $items->title = 2;
        $items->configs->valueShow['name'] = '222222222';
        $data[] = $items;

        return $data;
    }

    #endregion

    #region Summary
    /**
     * @return array
     * @throws \Exception
     * @author Daho
     * @since 16.10.2020
     */
    public function summary()
    {

        $code = null;
        /** @var Form $column */
        $models = $this->provider->getModels();
        $count = \Dash\count($models);
        $res = [];
        /** @var ZModel $model */
        foreach ($this->model->columns as $attribute => $column) {
            if (!$column->pageSummary)
                continue;

            if (!$this->showSummary($column, $attribute))
                continue;
            $current = ZArrayHelper::getValue($res, $attribute);
            foreach ($models as $model) {
                $value = ZArrayHelper::getValue($model, $attribute);


                switch ($column->pageSummaryFunc) {
                    case \kartik\grid\GridView::F_SUM:
                        if ($current === null)
                            $current = (int)($value);
                        else
                            $current += (int)($value);
                        break;
                    case \kartik\grid\GridView::F_AVG:
                        if ($current === null)
                            $current = (int)($value) / $count;
                        else
                            $current = ((int)($value) + $current * $count) / $count;
                        break;
                    case \kartik\grid\GridView::F_MAX:
                        if ($current === null)
                            $current = (int)($value);
                        if ($current < $value)
                            $current = (int)($value);
                        break;
                    case \kartik\grid\GridView::F_MIN:
                        if ($current === null)
                            $current = (int)($value);
                        if ($current > $value)
                            $current = (int)($value);
                        break;
                    case \kartik\grid\GridView::F_COUNT:
                        if ($current === null)
                            $current = 1;
                        else
                            $current++;
                        break;
                }

            }
            $res[$attribute] = $current;
        }
        return $res;
    }     // 35lines

    /**
     * @param Form|FormDb $column
     * @param $attribute
     * @return bool
     * @author Daho
     */
    private
    function showSummary($column, $attribute)
    {
        $arr = [
            dbTypeBigInteger,
            dbTypeDecimal,
            dbTypeDouble,
            dbTypeFloat,
            dbTypeBinary,
            dbTypeInteger,
            dbTypeSmallInteger,
            dbTypeTinyInteger,
        ];
        $b1 = ZArrayHelper::isIn($column->dbType, $arr);
        $b2 = !ZStringHelper::endsWith($attribute, 'id');
        $b3 = empty($column->fkTable);

        return $b1 && $b2 && $b3;
    }  //5lines
    #endregion

}

