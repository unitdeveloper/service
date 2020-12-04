<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * Date:    05.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\forms;

use kartik\form\ActiveForm;
use kartik\widgets\ActiveField;
use zetsoft\system\actives\ZAjaxForm;

/**
 * Class ZKActiveFormWidget
 * http://demos.krajee.com/widget-details/active-form
 *
 * https://www.yiiframework.com/doc/api/2.0/yii-widgets-activeform
 */
class Ajaxer extends Active
{
    public $enableAjaxSubmit = true;

    public function begin($config = null)
    {

        if ($config === null)
            $config = $this;

        return ZAjaxForm::begin([
            'id' => $config->id,
            'bsVersion' => '4',
            'type' => $config->type,
            'fullSpan' => $config->fullSpan,
            'formConfig' => [
                'labelSpan' => $config->labelSpan,
                'deviceSize' => self::size['md'],
                'showLabels' => $config->showLabels,
                'showErrors' => $config->showErrors,
                'showHints' => true
            ],
            'ajaxSubmitOptions' => $config->ajaxSubmitOptions,
            'staticOnly' => $config->isStaticOnly,
            'event' => [
                'error' => $config->error,
                'success' => $config->success,
                'complete' => $config->complete,
            ],
            'ajaxData' => $config->ajaxData,
            'disabled' => $config->disabled,
            'readonly' => $config->readonly,
            /**
             * yii options
             */
            'action' => $config->formAction,
            'formAction' => $config->formAction,
            'ajaxDataType' => $config->ajaxDataType,
            'ajaxParam' => $config->ajaxParam,
            /**
             *  'attributes' => $this->attributes,
             */
            'enableAjaxValidation' => $config->enableAjaxValidation,
            'enableAjaxSubmit' => $config->enableAjaxSubmit,
            'enableClientScript' => $config->enableClientScript,
            'enableClientValidation' => $config->enableClientValidation,
            'encodeErrorSummary' => $config->encodeErrorSummary,
            'errorCssClass' => 'has-error',
            'errorSummaryCssClass' => 'error-summary',
            'fieldClass' => ActiveField::class,
            'fieldConfig' => [
                'enableLabel' => $config->enableLabel,
                'options' => [
                    'class' => ''
                ]
            ],
            /**
             *  'fieldConfig' => [],
             * public array|Closure $fieldConfig = []
             *
             * id
             *  ID of the widget.
             *
             */

            'method' => $config->method,
            'requiredCssClass' => 'required',
            'scrollToError' => true,
            'scrollToErrorOffset' => 0,
            /**
             * 'stack    yii\base\Widget[],
             * The widgets that are currently being rendered (not ended). This property is maintained by begin() and end() methods.
             */
            'successCssClass' => 'has-success',
            'validateOnBlur' => true,
            'validateOnChange' => true,
            'validateOnSubmit' => true,
            'validateOnType' => true,
            'validatingCssClass' => 'validating',
            'validationDelay' => 100,
            /**
             * Where to render validation state class Could be either "container" or "input". Default is "container".
             */
            'validationStateOn' => ActiveForm::VALIDATION_STATE_ON_CONTAINER,
            'validationUrl' => '',
            'options' => [
                'enctype' => $config->enctype,
                //     'class' => 'md-form',
                'class' => '',
                'data-pjax' => $config->pjax
            ]
            /**
             * 'viewPath    string,
             * 'view    yii\web\View,
             */

        ]);
    }

    public function end()
    {
        ZAjaxForm::end();
    }

}
