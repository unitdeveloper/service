<?php

namespace zetsoft\service\forms;

use kartik\builder\TabularForm;
use zetsoft\dbitem\data\FormDb;
use zetsoft\system\actives\ZModel;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;
use zetsoft\widgets\former\ZFormWidget;

class Tabular extends ZFrame
{
    public $names = [];
    public $nameOff = [];
    public $nameHide = [];
    public $items = [];

    public function generate($model, $attributes = [], $columnsWidth = [])
    {
        //vdd($model->configs->nameOn);

        if ($model === null)
            return Az::warning('Model is Null');


        /** @var ZModel $model */

        $this->nameOn = $model->configs->nameOn;
        $this->nameOff = $model->configs->nameOff;
        $this->nameHide = $model->configs->nameShowEx;
        $cols = $model->columnsList();

        switch (true) {
            case $this->nameOn !== []:
                $cols = $this->nameOn;
                break;
            case $this->nameOff !== []:
                foreach ($cols as $key => $name) {
                    if (in_array($name, $this->nameOff)) {
                        unset($cols[$key]);
                    }
                }
                break;
            case $this->nameHide !== []:

                foreach ($cols as $key => $name) {
                    if (in_array($name, $this->nameHide))
                        unset($cols[$key]);
                }
                foreach ($cols as $key) {
                    /** @var FormDb $column */
                    $column = $model->columns[$key];


                    $options = [];
                    if ($column->widget === ZFormWidget::class) {
                        $options = [
                            'config' => [
                                'topBtn' => false,
                                'botBtn' => false,
                            ]
                        ];
                    }

                    $columnOptions = [];
                    if (isset( $columnsWidth[$key] )){
                        $columnOptions = [
                            'width' => $columnsWidth[$key],
                        ];
                    }

                    /* if ($key === 'id'){
                         $options = [
                             'label'=>'book_id',
                             'type' => TabularForm::INPUT_HIDDEN_STATIC,
                             'columnOptions' => ['vAlign' => GridView::ALIGN_MIDDLE],
                         ]
                     }*/

                    /**
                     *
                     * For more informations about column options:
                     * https://demos.krajee.com/builder-details/tabular-form#settings
                     */

                    $this->items[$key] = [
                        'attribute' => $key,
                        'type' => $key === "id" ? TabularForm::INPUT_HIDDEN_STATIC : 'widget', //  TabularForm::INPUT_WIDGET
                        'widgetClass' => $column->widget,
                        'options' => $options,
                        'visible' => true,
                        'value ' => $column->value,
                        'label' => $column->title,
                        'labelOptions' => [],
                        'prepend' => "",
                        'append' => "",
                        'hint' => '',

                        'fieldConfig' => [],
                        'data' => $column->data,
                        'staticValue' => function ($model, $key, $index, $widget) {

                        },

                        'format' => 'html',
                        'container' => [],
                        'inputContainer' => [],
                        'items' => [],
                        'enclosedByLabel' => true,
                        'columnOptions' => [
                            'width' => $column->width,
                        ],
//                        'columnOptions' => $columnOptions,
                    ];
                }
        }

//        vdd($this->items);

        if ($attributes != []) {

            $items = [];

            foreach ($attributes as $attribute) {
                $items[$attribute] = $this->items[$attribute];
            }
            return $items;
        }

        return $this->items;


    }
}

