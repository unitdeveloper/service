<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 * https://github.com/stonemax/acme2
 */

namespace zetsoft\service\inputs;

use Ramsey\Uuid\Exception\UnsupportedOperationException;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\web\UploadedFile;
use zetsoft\system\actives\ZActiveRecord;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\kernels\ZFrame;

class Fileinput extends ZFrame
{

    public $id;
    public ?string $modelClassName = null;
    public $attribute;
    public $key;
    public const blocked = [
        'exe',
        'php',
        'aspx',
        'asp',
        'php4',
        'php5',
        'elf',
    ];

    private $attrIdx; // required attribute index
    private $isComplex; // complex attribute


    #region Test
    public function test()
    {
        $this->testDelete();
    }

    public function testDelete()
    {
        $this->key = 'Screenshot_2.png';
        $this->id = 3;
        $this->modelClassName = 'TestFile2';
        $this->attribute = 'single';
        $this->delete();
    }

    #endregion
    public function delete()
    {
        $id = $this->id;
        $modelClassName = $this->modelClassName;
        $fileName = $this->key;
        $match = Az::$app->utility->pregs->pregMatchAll($this->attribute, '\[(.*?)\]');
        $attribute = ZArrayHelper::getValue($match, 1);
        $modelClass = $this->bootFull($modelClassName);   //get classname of model

        $model = $modelClass::findOne($id);
        //get attribute name

        if (is_numeric($attribute[0])) {   //If InputFile in DynoWidget
            $column = $attribute[1];
            $attr = $model->$column;
            switch (count($attribute)) {

                case 4:
                    $itemKey = $attribute[2];
                    $multiKey = $attribute[3];

                    foreach ($attr as $key => $item) {
                        if ($key === (int)$itemKey) {
                            foreach ($item as $imageKey => $images) {
                                if ($imageKey === $multiKey) {
                                    foreach ($images as $n => $image)
                                        if ($image === $fileName) {

                                            $trashPath = Az::getAlias('@root/upload/trashz/' . App . "/$modelClassName/$column/$model->id/$itemKey/$multiKey/");
                                            $nowPath = Az::getAlias('@root/upload/uploaz/' . App . "/$modelClassName/$column/$model->id/$itemKey/$multiKey/");

                                            $oldPath = $nowPath . $fileName;
                                            $newPath = $trashPath . $fileName;
                                            if (!file_exists($trashPath))
                                                FileHelper::createDirectory($trashPath, $mode = 775, $recursive = true);
                                            if (file_exists($oldPath)) {
                                                rename($oldPath, $newPath);
                                            }
                                            unset($attr[$key][$imageKey][$n]);
                                        }
                                }
                            }
                        }
                    }
                    break;
                case 3:
                    $key = $attribute[2];
                    $trashPath = Az::getAlias('@root/upload/trashz/' . App . "/$modelClassName/$column/$model->id/$key/");
                    $nowPath = Az::getAlias('@root/upload/uploaz/' . App . "/$modelClassName/$column/$model->id/$key/");
                    foreach ($attr as $n => $item) {
                        if ($n === $key) {
                            foreach ($item as $i => $image) {
                                if ($image === $fileName) {
                                    $oldPath = $nowPath . $fileName;
                                    $newPath = $trashPath . $fileName;
                                    if (!file_exists($trashPath))
                                        FileHelper::createDirectory($trashPath, $mode = 775, $recursive = true);
                                    if (file_exists($oldPath)) {
                                        rename($oldPath, $newPath);
                                    }
                                    unset($attr[$key][$i]);
                                }
                            }
                        }
                    }
                    break;
                default:
                    $trashPath = Az::getAlias('@root/upload/trashz/' . App . "/$modelClassName/$column/$model->id/");
                    $nowPath = Az::getAlias('@root/upload/uploaz/' . App . "/$modelClassName/$column/$model->id/");
                    foreach ($attr as $n => $item) {
                        if ($item === $fileName) {
                            $oldPath = $nowPath . $fileName;
                            $newPath = $trashPath . $fileName;
                            if (!file_exists($trashPath))
                                FileHelper::createDirectory($trashPath, $mode = 775, $recursive = true);
                            if (file_exists($oldPath)) {
                                rename($oldPath, $newPath);
                            }
                            unset($attr[$n]);
                        }
                    }

                    break;
            }
        } else {   //for common case
            $column = $attribute[0];
            $attr = $model->$column;
            switch (count($attribute)) {    //For multiWidget
                case 3:
                    $itemKey = $attribute[1];
                    $multiKey = $attribute[2];
                    foreach ($attr as $key => $item) {
                        if ($key === (int)$itemKey) {
                            foreach ($item as $imageKey => $images) {
                                if ($imageKey === $multiKey) {
                                    foreach ($images as $n => $image)
                                        if ($image === $fileName) {
                                            $trashPath = Az::getAlias('@root/upload/trashz/' . App . "/$modelClassName/$column/$model->id/$itemKey/$multiKey/");
                                            $nowPath = Az::getAlias('@root/upload/uploaz/' . App . "/$modelClassName/$column/$model->id/$itemKey/$multiKey/");

                                            $oldPath = $nowPath . $fileName;
                                            $newPath = $trashPath . $fileName;
                                            if (!file_exists($trashPath))
                                                FileHelper::createDirectory($trashPath, $mode = 775, $recursive = true);
                                            if (file_exists($oldPath)) {
                                                rename($oldPath, $newPath);
                                            }
                                            unset($attr[$key][$imageKey][$n]);
                                        }
                                }
                            }
                        }
                    }
                    break;
                case 2:                //For FormWidget
                    $key = $attribute[1];
                    $trashPath = Az::getAlias('@root/upload/trashz/' . App . "/$modelClassName/$column/$model->id/$key/");
                    $nowPath = Az::getAlias('@root/upload/uploaz/' . App . "/$modelClassName/$column/$model->id/$key/");
                    foreach ($attr as $n => $item) {
                        if ($n === $key) {
                            foreach ($item as $i => $image) {
                                if ($image === $fileName) {
                                    $oldPath = $nowPath . $fileName;
                                    $newPath = $trashPath . $fileName;
                                    if (!file_exists($trashPath))
                                        FileHelper::createDirectory($trashPath, $mode = 775, $recursive = true);
                                    if (file_exists($oldPath)) {
                                        rename($oldPath, $newPath);
                                    }
                                    unset($attr[$key][$i]);
                                }
                            }
                        }
                    }
                    break;
                default:            //For Single
                    $trashPath = Az::getAlias('@root/upload/trashz/' . App . "/$modelClassName/$column/$model->id/");
                    $nowPath = Az::getAlias('@root/upload/uploaz/' . App . "/$modelClassName/$column/$model->id/");
                    foreach ($attr as $n => $item) {
                        if ($item === $fileName) {
                            $oldPath = $nowPath . $fileName;
                            $newPath = $trashPath . $fileName;
                            if (!file_exists($trashPath))
                                FileHelper::createDirectory($trashPath, $mode = 775, $recursive = true);
                            if (file_exists($oldPath)) {
                                rename($oldPath, $newPath);
                            }
                            unset($attr[$n]);
                        }
                    }
                    break;
            }
        }
        $model->$column = array_values($attr);
        return $model->save();
    }

    public function upload($modelClassName, $attribute)
    {
        $match = Az::$app->utility->pregs->pregMatchAll($attribute, '\[(.*?)\]');
        $match = ZArrayHelper::getValue($match, 1);
        $files = UploadedFile::getInstancesByName($attribute);

        if (is_numeric($match[0]))
            switch (count($match)) {
                case 4:
                    $pregedModel = Az::$app->utility->pregs->pregMatchAll($modelClassName, '(.*\w)\[');
                    if (\Dash\count($pregedModel[1]) > 0)
                        $modelClassName = $pregedModel[1][0];

                    $pathTmp = Az::getAlias("@root/upload/tempz/" . App . "/{$modelClassName}/{$match[1]}/{$this->userIdentity()->id}/{$match[2]}/{$match[3]}/");
                    break;
                case 3:
                    $pathTmp = Az::getAlias("@root/upload/tempz/" . App . "/{$modelClassName}/{$match[1]}/{$this->userIdentity()->id}/{$match[2]}/");
                    break;
                default:
                    $pathTmp = Az::getAlias("@root/upload/tempz/" . App . "/{$modelClassName}/{$match[1]}/{$this->userIdentity()->id}/");
                    break;
            }        //If InputFile in DynoWidget
        else
            switch (count($match)) {
                case 3:
                    $pregedModel = Az::$app->utility->pregs->pregMatchAll($modelClassName, '(.*\w)\[');
                    if (\Dash\count($pregedModel[1]) > 0)
                        $modelClassName = $pregedModel[1][0];

                    $pathTmp = Az::getAlias("@root/upload/tempz/" . App . "/{$modelClassName}/{$match[0]}/{$this->userIdentity()->id}/{$match[1]}/{$match[2]}/");
                    break;
                case 2:
                    $pathTmp = Az::getAlias("@root/upload/tempz/" . App . "/{$modelClassName}/{$match[0]}/{$this->userIdentity()->id}/{$match[1]}/");
                    break;
                default:
                    $pathTmp = Az::getAlias("@root/upload/tempz/" . App . "/{$modelClassName}/{$match[0]}/{$this->userIdentity()->id}/");
                    break;
            }        //For common case
        if (!empty($files)) {

            $fileList = [];
            FileHelper::createDirectory($pathTmp, $mode = 775, $recursive = true);

            foreach ($files as $file) {

                if (ZArrayHelper::isIn($file->extension, self::blocked))
                    throw new UnsupportedOperationException('File type is not allowed');

                $fileName = $file->basename . '.' . $file->extension;

                if ($file->saveAs($pathTmp . $fileName)) {
                    $fileList[] = $fileName;
                } else {
                    return false;
                }
            }

        }// END IF

        return true;
    }
}
