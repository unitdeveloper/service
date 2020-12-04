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


use kartik\base\WidgetTrait;
use kartik\dynagrid\DynaGridTrait;
use kartik\grid\DataColumn;
use yii\grid\Column;
use yii\helpers\Html;
use zetsoft\dbitem\data\Config;
use zetsoft\dbitem\data\ConfigDB;
use zetsoft\models\shop\ShopOrder;
use zetsoft\system\actives\ZActiveRecord;
use zetsoft\system\actives\ZModel;
use zetsoft\system\Az;
use zetsoft\system\column\ZKEditableColumn;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZHTML;
use zetsoft\system\kernels\ZFrame;


class DataTable extends ZFrame
{
 

    /**
     * @var ZModel $model
     */
    public $model;
    public $columns;
    public $type = 'model';
    public $id = null;
    public $provider = null;
    public $data = [];


    public $layout = [
        'main' => <<<HTML
    <div class="table-responsive">
        <table id="{id}-dataTable" class="dataTable uk-table uk-table-hover uk-table-striped  dt-responsive display nowrap" cellspacing="0" width="100%">
            <thead class="{headClass}">
                {sub}
            </thead>
            <tbody>
                {items}
            </tbody>
            <tfoot>
            </tfoot>
        </table>
    </div>
HTML,
    ];

    public function test()
    {
        $this->testModel();
    }

    public function testModel()
    {
        $this->model = new ShopOrder();
        $this->model->configs->editClass = ALLData::editClass['popover'];
        $this->model->columns();
        $this->type = 'model';
        $this->run();

        vdd($this->generateRow(ShopOrder::findOne('97'), 1, 1));
    }

    public function run()
    {
        switch ($this->type) {
            case 'model':
                $this->generateFromModel();
                $this->provider = $this->model->search();
                break;


            case 'form':
                $this->generateFromForm();
                $this->provider = $this->model->searchForm($this->data);
                break;

            case 'item':
                $this->generateFromItem();
                /*
                 *
                 *
                 */
                break;
        }


        //vdd($this->provider);

    }

    public function generateFromModel()
    {
        $columns = $this->model->columns;
        $configs = $this->model->configs;
        $system = $this->model->configs->showSystemColumn;

        foreach ($columns as $key => $column) {
            $editClass = $this->model->configs->editClass;
            if ($column->editClass !== null)
                $editClass = $column->editClass;
            $col = [
                'class' => $editClass,
                'editableOptions' => $editClass::run($configs, $column, $key),
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
                'value' => static function ($model, $key, $index, DataColumn $dataColumn) use ($system) {

                    Az::$app->forms->wiData->clean();
                    Az::$app->forms->wiData->model = $model;
                    Az::$app->forms->wiData->attribute = $dataColumn->attribute;
                    Az::$app->forms->wiData->systemColumn = $system;
                    if ($dataColumn::className() === ZKEditableColumn::class)
                        $edit = true;
                    else
                        $edit = false;
                    return Az::$app->forms->wiData->value($edit, $index, $key);

                },
                //'options' => $this->options,
            ];

            $this->columns[$key] = $col;
        }
    }

    public function generateFromForm()
    {

    }

    public function generateFromItem()
    {

    }

    public function clean()
    {
        $this->model = null;
        $this->columns = null;
        $this->type = null;
        $this->provider = null;

    }

    public function generate($model, $id)
    {

        $this->model = $model;
        $this->id = $id;
        return $this->run();
    }

    public function run_shahzod()
    {
        $model = new $this->model;

        switch ($this->type) {
            case 'model':
                $this->generateFromModel();
                break;
        }

        $code = null;
        $head = null;
        //vdd($this->columns);
        foreach ($this->columns as $column) {


            $items = $model::find()->asArray()->all();

            foreach ($items as $item) {
                $code .= '<tr>';
                $head = '<tr>';

                foreach ($item as $key => $value) {
                    $head .= '<th>' . $key . '</th>';
                    $code .= '<td>' . $value . '</td>';
                }

                $code .= '</tr>';
                $head .= '</tr>';
            }
            //vdd($head);
        }


        $main = <<<HTML
    <div class="table-responsive">
        <table id="{$this->id}-dataTable" class="dataTable uk-table uk-table-hover uk-table-striped  dt-responsive display nowrap" cellspacing="0" width="100%">
            <thead class="{headClass}">
                {$head}
            </thead>
            <tbody>
                {$code}
            </tbody>
            <tfoot>
            </tfoot>
        </table>
    </div>
HTML;

//        $this->htm = strtr($this->layout['main'], [
////            '{sub}' => $head,
////            '{items}' => $code,
////        ]);
///
///


        return $main;


    }

    private function generateHead()
    {
        $options = [
            'classs' => 'p-5',
        ];

        $result = '<tr>';
        foreach ($this->columns as $key => $column) {
            $result .= ZHTML::tag('th', $key, $options);
        }
        $result .= '</tr>';
        return $result;
    }

    private function generateRow($model, $key, $index)
    {


        $result = null;
        $cells = [];
        /* @var $column Column */
        foreach ($this->columns as $column) {
            $cells[] = $column->renderDataCell($model, $key, $index);
        }
        if ($this->rowOptions instanceof Closure) {
            $options = call_user_func($this->rowOptions, $model, $key, $index, $this);
        } else {
            $options = $this->rowOptions;
        }
        $options['data-key'] = is_array($key) ? json_encode($key) : (string) $key;

        return Html::tag('tr', implode('', $cells), $options);

    }


}
