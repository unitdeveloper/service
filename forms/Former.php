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


use kartik\builder\Form;
use Symfony\Component\CssSelector\Exception\ExpressionErrorException;
use yii\base\DynamicModel;
use zetsoft\dbitem\data\ALLApp;

//use zetsoft\dbitem\data\Form;
use zetsoft\dbitem\data\Config;
use zetsoft\dbitem\data\ConfigDB;
use zetsoft\dbitem\data\FormDb;
use zetsoft\former\stat\StatHistoryForm;
use zetsoft\former\test\TestLaptopForm;
use zetsoft\former\test\TestForm;
use zetsoft\models\test\Test;
use zetsoft\system\actives\ZDynamicModel;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\module\Forms;
use zetsoft\system\module\Models;
use zetsoft\widgets\former\ZFormBuildWidget;
use zetsoft\widgets\former\ZFormWidget;
use zetsoft\widgets\former\ZFormWidget_2;
use zetsoft\widgets\former\ZMultiWidget;
use zetsoft\widgets\former\ZMultiWidgetNorm;
use zetsoft\widgets\inputes\ZHTextareaWidget;

class Former extends ZFrame
{


    /* @var Models $model */
    public $model;

    public $isCnt = false;
    public $count = 1;

    public $ident;

    public $type;
    public $widget;
    public $options;
    public $form;
    public $readonly;
    public $readonlyWidget;
    public $wrap;
    public $rows;
    private $card;

    public const widgets = [
        ZFormWidget::class,
        ZFormBuildWidget::class,
        ZMultiWidget::class,
    ];

    #region Core

    public $_layout = [

        'changeReload' => <<<JS

    $.Empty = function () {
        console.log('No Reload');
    }
    
    //start|DavlatovRavshan|2020.10.12
    $.sendAjax = function() {
           
        var parent = $('#{formId}').parent()
        
        $.ajax({
            type: 'POST',
            url: '{formUrl}',
            data: {
                fullWebId: '{fullWebId}',
                modelClassName: "{modelClassName}",
                modelId: {modelId},
                formId: '{formId}',
            },
            success: function (response) {
                if ('#{parentId}' !== 'null') {  
                    $('#{parentId}').html(response)
                } else {
                    parent.html(response)
                }
                    
            }
        })
        
    }
    //start|DavlatovRavshan|2020.10.12
     $.sendPjax = function() {
           
        $.pjax.reload({
            container: '#{container}', 
            async:true,
            timeout:false
        });
        
    }
       $.submitForm = function() {
       
          console.log('changeSubmit: ' + event.target.id);
          $('#{formId}').submit();
        
    }
    
JS,

    ];


    public function event()
    {

        $js = null;
        if ($this->model !== null)
            if (!empty($this->paramGet(paramChangeReloadId))) {
                $js = strtr($this->_layout['changeReload'], [
                    '{modelClassName}' => bname($this->model::className()),
                    '{formId}' => $this->form->id,
                    '{container}' => $this->paramGet(paramChangeReloadId),
                    '{parentId}' => $this->paramGet(paramChangeReloadId),
                    '{fullWebId}' => $this->urlMain,
                    '{formUrl}' => '/core/dyna/form.aspx',
                ]);

                if (!empty($this->model->id))
                    $js = strtr($js, [
                        '{modelId}' => $this->model->id,
                    ]);

                Az::$app->view->registerJs($js);

                $this->sessionSet("cards_{$this->model->className}", $this->model->cards);
                $this->sessionSet("configs_{$this->model->className}", $this->model->configs);

            }


    }

    public function clean()
    {
        $this->model = null;
        $this->rows = null;
    }

    public function run()
    {
        switch (true) {
            case $this->type === ZFormWidget::type['block']:
                $this->card = $this->model->itemsByBlock($this->ident, $this->isCnt);
                break;

            case $this->type === ZFormWidget::type['card']:
                $this->card = $this->model->itemsByCard($this->ident, $this->isCnt);
                break;
            case $this->type === ZFormWidget::type['steps']:
                $this->card = $this->model->itemsByStep($this->ident, $this->isCnt);
                break;

            case $this->type === ZFormWidget::type['allbl']:
                $this->card = $this->model->blockByAll($this->isCnt, $this->count);
                break;

            default:
                $this->card = $this->model->blockColumn();
                break;
        }

        // $this->model->columns();
        //vdd($this->card);
        $columns = $this->model->columns;
        $configs = $this->model->configs;


        if (empty($this->card))
            return Az::error($this->model::className(), 'Rows Cannot be Gotten from Model');

        $items = ZArrayHelper::getValue($this->card, 'items');

        if (empty($items))
            $items = $this->card;

        //vd($items);
        foreach ($items as $blocks) {

            $attributes = null;

            foreach ($blocks as $name) {

                /** @var FormDb $column */
                if (!ZArrayHelper::keyExists($name, $columns)) {
                    Az::error($name, 'Block Does Not Exists in Column');
                    continue;
                }

                $column = $columns[$name];
                $readonly = $column->readonlyWidget;
                if ($this->callableCheck($column->readonlyWidget)) {
                    $readonly = $column->readonlyWidget;
                    $readonly = $readonly($this->model);
                } else {
                    if ($this->callableCheck($column->readonly)) {
                        $readonly = $column->readonly;
                        $readonly = $readonly($this->model);
                    }
                }
                    
                if (!$column->showForm)
                    continue;

                if (!Az::$app->forms->dynas->nameShow($column, $configs))
                    continue;

                if (!Az::$app->forms->dynas->roleShow($column))
                    continue;

                if (!Az::$app->forms->dynas->roleEdit($column))
                    $column->readonly = true;

                if (!Az::$app->forms->dynas->roleFilter($column))
                    $column->filter = false;


                $options = [];
                if ($column->options !== null)
                    $options = ZArrayHelper::merge([
                        'form' => $this->form,
                        'attribute' => $this->attributeAll,
                        
                        'opts' => false,
                        'config' => [
                            //'isLabel' => $column->hasLabel,
                            'readonly' => $readonly,
                            'grapes' => false,
                        ]
                    ], $column->options);

                if (empty($column->valueOptions))
                    $column->valueOptions = $options;

                $attributes[$name] = [
                    'type' => Form::INPUT_WIDGET,
                    'widgetClass' => $column->widget,
                    'options' => $options,
                ];

                $this->checkForms($column->widget, $column->options);
                $this->checkForms($column->filterWidget, $column->filterOptions);
                $this->checkForms($column->valueWidget, $column->valueOptions);
                $this->checkForms($column->dynaWidget, $column->dynaOptions);

            }

            if (empty($attributes))
                continue;

            $this->rows[] = [
                'contentBefore' => '',
                'contentAfter' => '',
                'attributes' => $attributes
            ];

        }

        return $this->rows;
    }


    #endregion


    #region Utility

    public function checkFix($configs)
    {

        $formClass = !empty(ZArrayHelper::getValue($configs, 'formClass'));
        $formAttr = !empty(ZArrayHelper::getValue($configs, 'formAttr'));
        $formModel = !empty(ZArrayHelper::getValue($configs, 'formModel'));
        $formObject = ZArrayHelper::getValue($configs, 'formObject') !== null;
        $formSession = !empty(ZArrayHelper::getValue($configs, 'formSession'));

        return $formClass || $formAttr || $formModel || $formObject || $formSession;

    }



    public function checkForms($widget, $options)
    {
                 
        $configs = ZArrayHelper::getValue($options, 'config');

        if (ZArrayHelper::isIn($widget, self::widgets)) {
            $formALL = $this->checkFix($configs);
            if ($formALL)
                return $formALL;
            else {

                throw new \ErrorException('$column->options parametr must be set. If $ column->options is installed, make sure you have inside of config');
            }


        }


        
        
        return null;

    }



    #endregion


    #region Dynamic


    /**
     *
     * Function  getCards
     * @param $app
     * @return  array[]
     * Dyanmic
     */

    public function getCards($app)
    {
        $cards = [];

        $return = [];
        $i = 1;
        foreach ($app->columns as $key => $card) {
            $cards[] = $key;
            if ($i % $app->configs->columnCount === 0) {
                $return[] = $cards;
                $cards = [];
            }
            $i++;
        }
        $return[] = $cards;
        $returns =
            [
                [
                    'title' => Az::l('It is first stape'),
                    'items' => [
                        [
                            'title' => Az::l('Block'),
                            'items' => $return
                        ]
                    ]
                ]
            ];


        return $returns;
    }


    public ?ALLApp $app = null;
    public ?string $formName = null;
    public ?string $mainModel = null;


    /**
     *
     * Function  model
     * @param ALLApp $app
     * @param string|null $formName
     * @return  ZDynamicModel
     *
     * Create Model for DynamicModel
     */

    public function model(ALLApp $app, string $formName = null, $values = null)
    {
        $names = null;

        foreach ($app->columns as $attr => $formDB)
            $names[$attr] = null;

        $model = new ZDynamicModel($names);
        $model->formName = $formName;

        foreach ($app->columns as $attr => $formDB) {

            $form = new  \zetsoft\dbitem\data\Form();

            if (is_callable($formDB))
                $app->columns[$attr] = $formDB($form);
        }

        $app->configs = new Config();
        $app->configs->dynamic = true;
        $app->cards = $this->getCards($app);

        $model->setApp($app);

        /**
         * Set main Attributes
         */

        $model->setAttributes($values);
      //  $model->columns();
        return $model;

    }

    #endregion


}
