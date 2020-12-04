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
use zetsoft\dbitem\data\ALLData;
use zetsoft\system\actives\ZActiveForm;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\module\Models;
use zetsoft\widgets\former\ZActiveField;
use zetsoft\widgets\former\ZAjaxForm;


/**
 * Class ZKActiveFormWidget
 * http://demos.krajee.com/widget-details/active-form
 *
 * https://www.yiiframework.com/doc/api/2.0/yii-widgets-activeform
 */
class Active extends ZFrame
{

    public const encType = [
        'application/x-www-form-urlencoded' => 'application/x-www-form-urlencoded',
        'multipart/form-data' => 'multipart/form-data',
        'text/plain' => 'text/plain'
    ];

    public const method = [
        'get' => 'get',
        'post' => 'post',
    ];


    public $class = 'zoftactive';

    public $enctype = self::encType['multipart/form-data'];
    public $method = self::method['post'];

    public $target = self::target['main'];
    public $validationUrl = '/api/core/form/validate.aspx';

    public $enableLabel = false;

    public $pjax = false;

    public $config;

    public ?bool $pjaxInclude = null;
    public ?ZPjax $pjaxOptions = null;

    public $childClass = 'zoft my-3';
    public $id;

    public const target = [
        'main' => ActiveForm::VALIDATION_STATE_ON_CONTAINER,
        'input' => ActiveForm::VALIDATION_STATE_ON_INPUT,
    ];


    public ?string  $change = null;

    /**
     * $type
     *
     * 'vertical' or ActiveForm::TYPE_VERTICAL
     * 'horizontal' or ActiveForm::TYPE_HORIZONTAL
     * 'inline' or ActiveForm::TYPE_INLINE
     */
    public $type = self::type['vertical'];

    public const type = [
        'horizontal' => ActiveForm::TYPE_HORIZONTAL,
        'vertical' => ActiveForm::TYPE_VERTICAL,
        'inline' => ActiveForm::TYPE_INLINE,
    ];

    public $_layout = [

        'main' => <<<HTML
        {submitTop}
        {formGrid}
        {submitBottom}
HTML,

        'submitTop' => <<<HTML
    <div class="d-flex justify-content-end topBtn mt-0 mb-3">{topBtn}</div>
HTML,

        'submitBottom' => <<<HTML
     <div class="d-flex justify-content-end bottomBtn">{bottomBtn}</div>
HTML,

        'css' => <<<CSS
            .btnCursor {
                    cursor: default;
             }

CSS,


    ];

    public $fullSpan = 12;
    public $options;


    /**
     * @var int labelSpan
     * Should be a number between 1 and fullSpan. Defaults to 1
     */
    public $labelSpan = 2;

    public $showLabels = true;

    /**
     * @var
     * Defaults to true, except for INLINE forms where it defaults to false
     */
    public $showErrors = true;
    public $isStaticOnly = false;

    public $success = "''";
    public $error = "''";
    public $complete = "''";

    public $ajaxSubmitOptions = [];
    public $disabled = false;
    public $readonly = false;


    /**
     * yii options
     */
    public $formAction = '#';
    public $ajaxData = '{}';
    public $ajaxDataType = 'json';
    public $ajaxParam = 'ajax';

    /**
     * @var bool
     *
     * Validation
     */
    public $enableAjaxValidation = null;

    public $enableClientValidation = true;
    public $enableClientScript = true;
    public $encodeErrorSummary = true;

    public const size = [
        'lg' => 'lg',
        'xl' => 'xl',
        'sm' => 'sm',
        'md' => 'md',
    ];


    /**
     *
     * Function  begin
     * @param null $config
     * @return  ActiveForm|\yii\base\Widget
     */
    public function begin($config = null)
    {

        global $boot;

        if ($config === null)
            $config = $this;

        if (is_callable($config))
            $config = $config($this);

        $config->pjaxInclude = $this->pjaxEnable();

        if ($config->pjaxInclude)
            $this->pjaxBegin($this->pjaxOptions);

        $config->enableAjaxValidation = $boot->env('enableAjaxValidation');

        $this->config = $config;


        $form = ZActiveForm::begin([
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
            'staticOnly' => $config->isStaticOnly,
            'disabled' => $config->disabled,
            'readonly' => $config->readonly,
            /**
             * yii options
             */
            'action' => $config->formAction,
            'ajaxDataType' => $config->ajaxDataType,
            'ajaxParam' => $config->ajaxParam,
            /**
             *  'attributes' => $this->attributes,
             */
            'enableAjaxValidation' => $config->enableAjaxValidation,
            'enableClientScript' => $config->enableClientScript,
            'enableClientValidation' => $config->enableClientValidation,
            'encodeErrorSummary' => $config->encodeErrorSummary,
            'errorCssClass' => 'has-error',
            'errorSummaryCssClass' => 'error-summary',
            'fieldClass' => ZActiveField::class,
            'fieldConfig' => [],
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
            'validateOnType' => false,

            'validatingCssClass' => 'validating',
            'validationDelay' => 100,
            /**
             * Where to render validation state class Could be either "container" or "input". Default is "container".
             */
            'validationStateOn' => $config->target,
            'validationUrl' => $config->validationUrl,
            'options' => [
                'enctype' => $config->enctype,
                'class' => $config->class,
                //  'id' => 'test',
                'data-pjax' => $config->pjax
            ]

        ]);

        return $form;
    }

    public function end()
    {

        ZActiveForm::end();

        if ($this->config->pjaxInclude)
            $this->pjaxEnd();

    }

}
