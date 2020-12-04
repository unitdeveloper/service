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

use zetsoft\dbitem\core\WebItem;
use zetsoft\models\page\PageView;
use zetsoft\models\page\PageViewType;
use zetsoft\service\ALL\App;
use zetsoft\system\actives\ZActiveRecord;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\module\Models;

class BuildWeb extends ZFrame
{

    #region Vars

    public $pathView;
    public $pathApp;
    public $pathAll;

    public $control;

    public $ignoreList = [
        'block',
        'apps',
        'thm',
        'lays',
        'test',
        '@'
    ];

    public $whiteList = [
        'core',
    ];
    #endregion

    #region Main

    public function init()
    {

        parent::init();

        $this->pathView = Root . '/webhtm';

        $this->paramSet(paramNoEvent, true);
    }

    public function run()
    {
        $this->folder();
        $this->action();
    }

    #endregion

    #region Folder

    public function folder()
    {

        Az::start(__FUNCTION__);

        $this->update(PageViewType::class);

        $this->folderSave();
        $this->folderParent();

        $this->delete(PageViewType::class);

        Az::end();

        return false;
    }

    public function folderSave()
    {

        $folders = $this->folderScan();
        //   vdd( $folders);
        foreach ($folders as $name) {

            $model = PageViewType::findOne([
                'name' => $name,
            ]);

            if ($model === null) {
                Az::debug($name, 'Create new CoreModule');
                $model = new PageViewType();
            }

            $model->configs->nameAuto = false;
            $model->configs->rules = validatorSafe;
            $model->columns();

            $model->name = $name;
            $model->check = true;

            $model->save();
        }

    }

    public function folderScan()
    {
        $folders = ZFileHelper::scanFolder($this->pathView, true);

        $return = [];
        $lists = $this->bootEnv('webHtm');
        $lists = explode('|', $lists);

        if (empty($lists))
            return Az::error('webHtm not found on ' . App . '.env');

        $this->whiteList = ZArrayHelper::merge($this->whiteList, $lists);

        foreach ($folders as $folder) {
            $continue = true;

            foreach ($this->whiteList as $list) {
                if (ZStringHelper::find($folder, $list)) {
                    $continue = false;
                    break;
                }
            }

            if (!$continue)
                foreach ($this->ignoreList as $list) {
                    if (ZStringHelper::find($folder, $list, false)) {
                        $continue = true;
                        break;
                    }
                }

            if ($continue)
                continue;

            Az::info($folder, 'Processing Folder');

            $folder = str_replace([$this->pathView, '\\'], ['', '/'], $folder);

            $return[] = $folder;

        }

        return $return;

    }

    public function folderParent()
    {

        /** @var PageViewType[] $models */
        $models = PageViewType::find()->all();

        foreach ($models as $model) {

            $parentName = $model->name . '\\';
            $parentName = str_replace('/' . bname($model->name) . '\\', '', $parentName);

            $parent = PageViewType::findOne([
                'name' => $parentName
            ]);

            if (!$parent)
                continue;

            $model->configs->rules = validatorSafe;
            $model->columns();
            $model->page_view_type_id = $parent->id;
            $model->save();

        }

    }


    ##endregion

    #region Action

    public function action()
    {

        Az::start(__FUNCTION__);

        $this->update(PageView::class);
        $this->actionScans();
        $this->delete(PageView::class);

        Az::end();
    }

    public function actionScans()
    {
        $pageViewTypes = PageViewType::find()->all();

        foreach ($pageViewTypes as $pageViewType) {

            $path = $this->pathView . $pageViewType->name;

            if (!file_exists($path)) {
                Az::warning($path, 'Control Path not Exists');
                continue;
            }

            $files = ZFileHelper::scanFilesPHP($path);

            foreach ($files as $file) {
                $name = str_replace([$this->pathView, '\\', '.php'], ['', '/', ''], $file);
                if (ZStringHelper::find($file, 'App', false)) {
                    if (!ZStringHelper::endsWith($file, '_' . mb_strtolower(App) . 'php')) {
                        continue;
                    }
                }

                Az::info($file, 'Processing File');
                $this->actionAdder($name, $pageViewType, $file);
            }

        }
    }

    private function actionAdder($name, $pageViewType, $file)
    {

        $action = PageView::findOne([
            'name' => $name
        ]);

        if ($action === null)
            $action = new PageView();

        $content = file_get_contents($file);
        $service = Az::$app->utility->pregs;

        $action->name = $name;
        $action->check = true;
        $action->page_view_type_id = $pageViewType->id;

        $title = $service->pregMatch($content, "action->title = Azl\.'(.*)'");

        if ($title === null)
            $title = $service->pregMatch($content, "action->title = Azl \. '(.*)'");

        if ($title === null)
            $title = $service->pregMatch($content, "action->title = Az::l\('(.*)'\)");

        if ($title === null)
            $title = $service->pregMatch($content, "action->title = Az::l\(\"(.*)\"\)", bname($name));

        if ($title === null)
            $title = $service->pregMatch($content, "action->title = *'(.*)'");

        $action->title = $title;

        $action->icon = $service->pregMatch($content, "action->icon ?= ?'(.*)'", Az::$app->utility->mains->icon());
        $action->type = $service->pregMatch($content, "action->type = WebItem::type\['(.*)'\]", WebItem::type['html']);

        if ($action->save())
            Az::debug($name, 'Save PageView');

    }

    #endregion

    #region Service

    public function update($class)
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
            $model->configs->rules = validatorSafe;
            $model->columns();
            $model->check = false;
            $model->save();
        }

        Az::count($models, 'Updated Rows');

    }

    public function delete($class)
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
        Az::$app->db->createCommand('TRUNCATE TABLE "page_view" CASCADE')->execute();
        Az::$app->db->createCommand('TRUNCATE TABLE "page_view_type" CASCADE')->execute();
    }

    #endregion

}
