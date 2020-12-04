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

namespace zetsoft\service\cores;

use PhpOffice\PhpSpreadsheet\Shared\OLE\PPS\Root;
use yii\caching\TagDependency;
use yii\helpers\FileHelper;
use zetsoft\dbdata\web\ActionWebData;
use zetsoft\dbitem\core\WebItem;
use zetsoft\models\page\PageAction;
use zetsoft\models\page\PageControl;
use zetsoft\models\page\PageModule;
use zetsoft\models\page\PageApi;
use zetsoft\models\page\PageApiType;
use zetsoft\system\actives\ZActiveRecord;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\kernels\ZView;
use zetsoft\system\module\Models;

class BuildApi extends ZFrame
{

    #region Vars

    public $pathView;
    public $pathApp;
    public $pathAll;

    public $control;

    public $whiteList = [
        'core',
        'auth',
    ];
    #endregion


    #region Main

    public function init()
    {

        parent::init();

        $this->pathView = Root . '/webrest/';
        $this->pathApp = $this->pathView . App;
        $this->pathAll = $this->pathView . 'ALL';

        $this->paramSet(paramNoEvent, true);
    }

    public function run()
    {

        $this->folder();
        $this->action();
    }

    ##endregion


    #region Folder


    public function folder()
    {

        Az::start(__FUNCTION__);

        $this->update(PageApiType::class);
        $this->saveFolders();
        $this->saveTypes();
        $this->delete(PageApiType::class);

        Az::end();

        return false;
    }

    public function saveFolders()
    {

        foreach ($this->folderScan() as $name) {

            $model = PageApiType::findOne([
                'name' => $name,
            ]);

            if ($model === null) {
                Az::debug($name, 'Create new CoreModule');
                $model = new PageApiType();
            }

            $model->name = $name;
            $model->check = true;

            $model->save();
        }

    }

    public function folderScan()
    {
        $folders = ZFileHelper::scanFolder($this->pathView, true);

        $return = [];
        $lists = $this->bootEnv('webApi');
        $lists = explode('|',$lists);
        $this->whiteList = ZArrayHelper::merge($this->whiteList,$lists);
        foreach ($folders as $folder) {
            $continue = true;

            foreach ($this->whiteList as $list){
                if (ZStringHelper::find($folder, $list)){
                    $continue = false;
                    break;
                }
            }

            if ($continue)
                continue;

            $folder = str_replace([$this->pathView, '\\'], ['', '/'], $folder);

            $return[] = $folder;

        }

        return $return;

    }

    public function saveTypes()
    {

        $models = PageApiType::find()->all();

        foreach ($models as $model) {

            $parentName = $model->name . '\\';
            $parentName = str_replace('/' . bname($model->name) . '\\', '', $parentName);

            $parent = PageApiType::findOne([
                'name' => $parentName
            ]);

            if (!$parent)
                continue;

            $model->page_api_type_id = $parent->id;
            $model->check = true;
            $model->save();

        }

    }


    ##endregion


    #region Action

    public function action()
    {

        Az::start(__FUNCTION__);

        $this->update(PageApi::class);
        $this->actionScans();
        $this->delete(PageApi::class);

        Az::end();
    }

    public function actionScans()
    {
        $pageApiTypes = PageApiType::find()->all();

        foreach ($pageApiTypes as $pageApiType) {

            $path = $this->pathView . $pageApiType->name;

            if (!file_exists($path)) {
                Az::warning($path, 'Control Path not Exists');
                continue;
            }

            $files = ZFileHelper::scanFilesPHP($path);

            foreach ($files as $file) {
                $name = str_replace([$this->pathView, '\\', '.php'], ['', '/', ''], $file);
                $this->actionAdder($name, $pageApiType, $file);
            }
        }
    }

    private function actionAdder($name, $pageApiType, $file)
    {

        $action = PageApi::findOne([
            'name' => $name
        ]);

        if ($action === null)
            $action = new PageApi();

        $content = file_get_contents($file);
        $service = Az::$app->utility->pregs;

        $action->name = $name;
        $action->check = true;
        $action->page_api_type_id = $pageApiType->id;
        $action->title = Azl . $service->pregMatch($content, "action->title = '(.*)'", bname($name));
        $action->icon = $service->pregMatch($content, "action->icon ?= ?'(.*)'", Az::$app->utility->mains->icon());
        $action->type = $service->pregMatch($content, "action->type = WebItem::type\['(.*)'\]", WebItem::type['html']);

        if ($action->save())
            Az::debug($name, 'Save PageApi');

    }

    #endregion


    #region Service

    private function update($class)
    {

        /** @var Models $class */
        $models = $class::find()
            ->where([
                'check' => [
                    true,
                    null
                ]
            ])
            ->all();


        foreach ($models as $model) {
            $model->check = false;
            $model->save();
        }

        Az::count($models, 'Updated Rows');

    }

    private function delete($class)
    {
        /** @var ZActiveRecord $class */
        $deleted = $class::deleteAll([
            'check' => false
        ]);
        Az::info($deleted, 'Deleted Rows');

    }

    public function clean()
    {
        Az::start(__FUNCTION__);

        Az::$app->db->createCommand('TRUNCATE TABLE "page_api" CASCADE')->execute();
        Az::$app->db->createCommand('TRUNCATE TABLE "page_api_type" CASCADE')->execute();
    }

    #endregion

}
