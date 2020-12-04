<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * Date:    11.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\forms;


use kartik\form\ActiveForm;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\web\Response;
use zetsoft\dbitem\data\ALLApp;
use zetsoft\dbitem\data\Config;
use zetsoft\dbitem\data\ConfigDB;
use zetsoft\dbitem\data\Form;
use zetsoft\former\core\CoreServiceForm;
use zetsoft\models\ware\WareEnterItem;
use zetsoft\models\ware\WareSeries;
use zetsoft\system\actives\ZActiveRecord;
use zetsoft\system\actives\ZData;
use zetsoft\system\actives\ZModel;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\module\Models;
use zetsoft\widgets\themes\ZCardWidget;


class Modelz extends ZFrame
{

    /**
     *
     *
     * Sample App
     */

    public $sample = self::sample['Select2'];

    public $modelId;

    public const sample = [
        'Select2' => 'Select2',
    ];


    public const fileSuffix = '_XFile';

    /**
     *
     *
     * File Upload Operation Type
     */

    #region Utils

    /**
     *
     * Methods
     * @param $sampleType
     * @return array
     */

    public function data($sampleType = self::sample['Select2'])
    {

        switch ($sampleType) {
            case self::sample['Select2']:
                $return = [
                    1 => 'Item1',
                    2 => 'Item2',
                    3 => 'Item3',
                    4 => 'Item4',
                    5 => 'Item5',
                    6 => 'Item6'
                ];
                break;

            default:
                $return = [];
        }

        return $return;
    }


    /**
     *
     * Function  info
     * @param Models $model
     *
     * @return string
     */
    public function info($model)
    {
        $class = get_class($model);
        $className = bname($class);
        $id = $model->id;
        $title = $model->configs->title;

        $nameAttr = $model->configs->name;

        if (ZArrayHelper::isIn($nameAttr, $model->columnsList())) {
            $name = $model->$nameAttr;
            $all = "$className | $title | $id | $name";
        } else
            $all = "$className | $title | $id";

        return $all;
    }


    public function post($die = false)
    {

        if (!empty($this->httpPost())) {
            $text = ZVarDumper::export($this->httpPost());

            ZCardWidget::begin([

            ]);
            echo "<pre>$text</pre>";
            ZCardWidget::end();

            if ($die)
                die;

        }
    }


    #endregion

    #region Related

    /**
     *
     * Function  save
     * @param Models $model
     * @return bool|Models
     */

    public function saveRelatedTest()
    {


        $this->paramSet('myTest', true);
        $model = new WareEnterItem();
        $model->name = 'sadf';
        $model->shop_element_id = 131;
        $model->ware_series_id = 'gfjhfdhfdh';
        $model->ware_enter_id = 24;
        $model->currency = 'UZS';
        $model->configs->relatedSave = true;
        $model->modelSave($model);


        $series = WareSeries::findOne([
            'name' => 'gfjhfdhfdh'
        ]);

        vdd($series);

    }


    public function saveRelated(&$model)
    {


        if (!$this->isCLI())
            if (!$this->httpIsPost())
                return null;


        /** @var Models $model */
        foreach ($model->columns as $attribute => $column) {

            switch (true) {

                case  ZStringHelper::endsWith($attribute, '_id'):
                case  ZStringHelper::endsWith($attribute, '_ids'):
                    Az::$app->forms->wiData->fkTable = str_replace(['_ids', '_id'], '', $attribute);
                    $data = Az::$app->forms->wiData->related();

                    break;

                case !empty($column->fkTable):
                    Az::$app->forms->wiData->fkTable = $column->fkTable;
                    $data = Az::$app->forms->wiData->related();

                    break;

                default:
                    $relatedTable = null;
                    $data = null;
                    break;
            }

            $attrValue = $model->$attribute;

            $b1 = $data === null;
            $b2 = $relatedTable === null;
            $b3 = is_array($attrValue);

            if (($b1 && $b2) || $b3)
                continue;

            $class = ZInflector::camelize($relatedTable);
            $class = $this->bootFull($class);

            if (!class_exists($class))
                return null;

            $bool1 = !ZArrayHelper::isIn($attrValue, $data);
            $bool2 = !ZArrayHelper::keyExists($attrValue, $data);
            $bool3 = !empty($attrValue);

            if ($bool1 && $bool2 && $bool3) {


                $obj = new $class();
                $nameAttr = $obj->configs->name;
                $obj->$nameAttr = $attrValue;
                $obj->configs->rules = [
                    [
                        validatorSafe
                    ]
                ];

                $obj->save();
                $model->$attribute = $obj->id;

            }

        }

    }

    #endregion

    #region Save

    /**
     *
     * Function  save
     * @param Models $model
     * @return  bool
     */
    public function save(&$model)
    {

        //  $model->columns();

        if ($this->form($model) === true) {

            if ($model->configs->relatedSave) {
                $this->saveRelated($model);
            }


            //      vd($model->isNewRecord);


            if ($model->save()) {
                $this->upload($model);
                return true;
            }

            $this->notifyError(Az::l('Ошибка сохранения {item}', [
                'item' => ZVarDumper::export($model->errors)
            ]), $model->id);

            $problem = true;
        } else
            $problem = true;

        if (Az::$app->request->isAjax || Az::$app->request->isPjax)
            if ($problem) {
                vd($model->errors);

                return false;
            } else
                return true;

    }


    /**
     *
     * Function  saveForm
     * @param ZModel $model
     * @return  bool
     *
     */
    public function saveForm(&$model)
    {
        $key = $this->formKey($model);

        if ($this->form($model) === true)
            $this->sessionSet($key, $model->attributes);

    }


    public function changeSave($model)
    {
        /* @var Models $model */
        $b2 = $model->validate();
        $b3 = $model->configs->changeSave;

        if ($b2 && $b3) {
            if ($model->isModel())
                $model->save();
            else
                $this->sessionSet($this->formKey($model), $model->attributes);
        }

        return $model;

    }


    public function formKey($model)
    {

        $modelClass = $model::className();
        $key = "{$modelClass}_{$this->urlArrayStr}";

        return $key;
    }

    #endregion

    #region Form


    public function form($model)
    {
        $request = Az::$app->request;

        if ($this->isCLI())
            return true;

        $post = $this->httpPost();

        if ($request->isPost) {

            if ($request->isAjax && !$request->isPjax && $model->load($post)) {
                Az::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }

            if ($model->load($post)) {

                /** @var ZActiveRecord $model */
                if ($model->validate())
                    return true;
                else
                    if ($request->isAjax)
                        vdd($model->errors);

                $this->notifyError('Ошибка валидации', $model->errors);
                return false;
            }

        }

        return false;
    }


    #endregion

    #region Clone

    public function lastID($modelClass)
    {
        $lastID = $modelClass::find()
            ->max('id');

        return $lastID;
    }


    public function clone($modelClass, $id)
    {
        $oldModel = $modelClass::findOne($id);

        $lastID = $this->lastID($modelClass);
        if ($oldModel === null)
            return null;

        /** @var Models $model */
        $model = new $modelClass();
        $model->attributes = $oldModel->attributes;
        $model->id = $lastID + 1;
        $attribute = $model->configs->name;
        //  vdd($attribute);
        if (!ZArrayHelper::isIn($attribute, $model->columnsList()))
            $attribute = $model->columnsList();


        //if (!ZArrayHelper::getValue(Az::$app->params, 'is_clone'))
        //  vdd($attribute);
        $model->$attribute = ZStringHelper::cleanName($model->$attribute) . '_' . $model->id;

        $model->isNewRecord = true;

        /** @var ZActiveRecord $model */

        if ($model->configs->addDel) {
            $model->deleted_at = null;
            $model->deleted_by = null;
        }

        if ($model->configs->addBy) {
            $model->created_at = null;
            $model->created_by = null;
            $model->modified_at = null;
            $model->modified_by = null;
        }

        $model->configs->rules = validatorSafe;
        $model->save();

        return $model;

    }

    #endregion

    #region formObject

    final public function formObject($config, $model, $attributes = [])
    {

        if (empty($config)) {
            Az::error($this::className(), 'Форма параметр formClass для формы!');
            return null;
        }

        switch (true) {

            case !empty($config['formClass']):
                $formName = $config['formClass'];
                break;

            case !empty($config['formAttr']) && !empty($config['formModel']):
                $attr = $config['formAttr'];
                $formName = $config['formModel']->$attr;
                break;

            case !empty($config['formAttr']):
                $attr = $config['formAttr'];

                $value = $model->$attr;
                if (!empty($attributes))
                    $value = ZArrayHelper::getValue($attributes, $attr);

                $formName = $value;

                break;

            default:
                $formName = null;
                break;

        }

        switch (true) {

            case !empty($config['formSession']):

                $sessionItem = $this->sessionGet($config['formSession']);
                $appArray = ZArrayHelper::getValue($sessionItem, 'data');
                $app = $this->checkFormSession($appArray);

                $return = Az::$app->forms->former->model($app);

                break;

            case !empty($config['formObject']):
                $return = $config['formObject'];
                break;

            default:
                $return = null;

        }

        if ($return !== null)
            return $return;

        if (empty($formName)) {
            Az::error($this::className(), 'Форма отсутствует');
            return null;
        }


        /**
         *
         * Unnecessary for iframe actions
         */

        //   vdd($formName);

        if (!class_exists($formName)) {
            Az::error($this::className(), 'Класс формы некорректная');
            return null;
        }

        $return = new $formName();

        /*      vd($formName);
              vd($return->configs);
              vd($return->columns['namespace']->ajax);*/

        return $return;
    }


    final public function checkFormSession($app)
    {

        $returnApp = new AllApp();

        $columns = ZArrayHelper::getValue($app, 'columns');
        $configs = ZArrayHelper::getValue($app, 'configs');
        $cards = ZArrayHelper::getValue($app, 'cards');

        $returnApp->configs = $this->getAllAppConfigs($configs);
        $returnApp->columns = $this->getAllAppColumns($columns);
        $returnApp->cards = $cards;

        return $returnApp;
    }

    #endregion

    #region AllApp


    public function getAllAppConfigs($array)
    {

        $configDb = new Config();

        if (!$array)
            return null;

        foreach ($array as $key => $value) {
            $configDb->$key = $value;
        }

        return $configDb;

    }

    public function getAllAppColumns($array)
    {
        $columns = [];

        if (!$array)
            return null;

        foreach ($array as $attribute => $items) {

            $formDb = new Form();
            foreach ($items as $key => $value) {
                $formDb->$key = $value;
            }

            $columns[$attribute] = $formDb;
        }

        return $columns;
    }


    #endregion

    #region Upload

    public function upload(ZActiveRecord $model)
    {

        if ($this->isCLI())
            return true;

        if (!$model)
            return null;

        $request = $this->httpPost();
        $index = $this->httpPost('editableIndex');
        $attribute = $this->httpPost('editableAttribute');

        $userId = $this->userIdentity()->id;
        $modelName = bname($model::className());

        $fileSuffix = self::fileSuffix;

        if (!ZArrayHelper::keyExists($modelName, $request))
            return null;

        $fileAttributes = array_keys($request[$modelName]);

        if (isset($index)) {

            $editableAttr = $request[$modelName][$index][$attribute];

            if (is_array($editableAttr)) {

                $fileTmpPath = Az::getAlias('@root/upload/tempz/' . App . "/{$modelName}/{$attribute}/{$userId}");
                $fileNewPath = Az::getAlias('@root/upload/uploaz/' . App . "/{$modelName}/{$attribute}/{$model->id}");

                if (file_exists($fileTmpPath)) {
                    $this->resize($fileTmpPath, $modelName, $attribute, $model->id);

                    ZFileHelper::copyDirectory($fileTmpPath, $fileNewPath);
                    ZFileHelper::removeDir($fileTmpPath);
                }

            }
        } else {
            foreach ($fileAttributes as $attribute) {

                $attribute = str_replace($fileSuffix, '', $attribute);

                $fileTmpPath = Az::getAlias("@root/upload/tempz/" . App . "/{$modelName}/{$attribute}/{$userId}/");
                $fileNewPath = Az::getAlias("@root/upload/uploaz/" . App . "/{$modelName}/{$attribute}/{$model->id}");

                $this->resize($fileTmpPath, $modelName, $attribute, $model->id);

                if (file_exists($fileTmpPath)) {
                    ZFileHelper::copyDirectory($fileTmpPath, $fileNewPath);
                    ZFileHelper::removeDir($fileTmpPath);
                }
            }
        }

        return true;
    }
    public function uploadFile(ZActiveRecord $model, string $attr)
    {

        if (!$model)
            return null;

        $userId = $this->userIdentity()->id;
        $modelName = bname($model::className());

        $fileTmpPath = Az::getAlias('@root/upload/tempz/' . App . "/{$modelName}/{$attr}/{$userId}");
        $fileNewPath = Az::getAlias('@root/upload/uploaz/' . App . "/{$modelName}/{$attr}/{$model->id}");

        if (file_exists($fileTmpPath)) {
            $this->resize($fileTmpPath, $modelName, $attr, $model->id);

            ZFileHelper::copyDirectory($fileTmpPath, $fileNewPath);
            ZFileHelper::removeDir($fileTmpPath);
        }

        return true;
    }


    public function removeFile($model)
    {
        $modelColumns = $model->columns;
        $className = $model::className();
        $id = $model->id;

        foreach ($modelColumns as $columnName => $column) {

            if ($column->dbType === 'jsonb') {

                $nowPath = Az::getAlias('@root/upload/uploaz/' . App . "/{$className}/{$columnName}");

                $trashPath = Az::getAlias('@root/upload/trashz/' . App . "/{$className}/{$columnName}");

                if (!file_exists($nowPath))
                    continue;

                $nowPath = "$nowPath/{$id}/";
                $trashPath = "$trashPath/{$id}/";

                ZFileHelper::createDirectory($trashPath);

                if (!is_dir($nowPath))
                    continue;

                ZFileHelper::copyDirectory($nowPath, $trashPath);
                ZFileHelper::removeDir($nowPath);
            }
        }
    }

    public function resize($filePath, $modelClassName, $attribute, $id)
    {
        Az::$app->image->intervent->modelClassName = $modelClassName;
        Az::$app->image->intervent->attribute = $attribute;
        Az::$app->image->intervent->id = $id;
        Az::$app->image->intervent->resize($filePath);
    }

    #endregion

    #endregion

    #region Table


    public function get(string $modelClass, int $id = 0): ?ZActiveRecord
    {

        /** @var ZActiveRecord $modelClass */
        /** @var Models $model */

        if ($id === 0) {
            $model = new $modelClass();
            $model->id = $this->lastID($modelClass) + 1;

        } else {

            $model = $modelClass::findOne($id);

            /**
             * If model with such id not exists
             */

            if ($model === null) {

                $model = new $modelClass();
                $model->id = $id;
                $model->save();
            }
        }

        return $model;
    }

    public function getForm(string $modelClass): ?ZModel
    {

        /** @var ZActiveRecord $modelClass */
        /** @var Models $model */

        $key = "{$modelClass}_{$this->urlArrayStr}";
        $attributes = $this->sessionGet($key);

        $model = new $modelClass();

        if ($attributes !== null)
            $model->setAttributes($attributes);

        return $model;
    }


    public function exists($table)
    {
        return \Yii::$app->db->getTableSchema($table, true) !== null;
    }


    #endregion

}
