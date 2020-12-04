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
use zetsoft\models\page\PageBlocks;
use zetsoft\models\page\PageBlocksType;
use zetsoft\models\page\PageView;
use zetsoft\models\page\PageViewType;
use zetsoft\system\actives\ZActiveRecord;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\module\Models;

class BuildBlock extends ZFrame
{

    #region Vars

    public $pathBlock;
    public $excludes = [
        '@',
        'other',
        '.idea',
    ];

    #endregion

    #region Main

    public function init()
    {

        parent::init();

        $this->pathBlock = Root . '/webhtm/block';

        $this->paramSet(paramNoEvent, true);
    }

    public function run()
    {
        $this->blockType();
        $this->block();
    }

    ##endregion

    #region Folder


    public function blockType()
    {

        Az::start(__FUNCTION__);

        $this->update(PageBlocksType::class);
        $this->saveBlockType();
        $this->delete(PageBlocksType::class);

        Az::end();

        return false;
    }

    public function saveBlockType()
    {

        foreach ($this->scanBlocksType() as $name) {

            $model = PageBlocksType::findOne([
                'name' => $name,
            ]);

            if ($model === null) {
                Az::debug($name, 'Create new CoreModule');
                $model = new PageBlocksType();
            }

            $model->name = $name;
            $model->check = true;

            $model->save();
        }

    }

    public function scanBlocksType()
    {

        $folders = ZFileHelper::scanFolder($this->pathBlock);
        
        $return = [];
        foreach ($folders as $folder) {
        
            $b1 = ZStringHelper::find($folder, '@');
            $b2 = ZStringHelper::find($folder, '.idea');
            $b3 = ZStringHelper::find($folder, 'other');
            if ($b1 || $b2 || $b3)
                continue;
                
            $return[] = bname($folder);
        }

        return $return;

    }

    #endregion

    #region Action

    public function block()
    {

        Az::start(__FUNCTION__);

        $this->update(PageBlocks::class);
        $this->blockScans();
        $this->delete(PageBlocks::class);

        Az::end();
    }

    public function blockScans()
    {
        $pageBlocksTypes = PageBlocksType::find()->all();

        foreach ($pageBlocksTypes as $pageBlockType) {

            $path = $this->pathBlock . '/' . $pageBlockType->name;

            if (!file_exists($path)) {
                Az::warning($path, 'Control Path not Exists');
                continue;
            }
            
            $files = $this->getFileBlocks($path);

            foreach ($files as $file) {
                $name = str_replace([$this->pathBlock, '\\', '.php'], ['', '/', ''], $file);
                $this->blockAdder($name, $pageBlockType, $file);
            }

        }
    }


    private function getFileBlocks($path) {

        $files = ZFileHelper::scanFilesPHP($path, true);

        $return = [];
        foreach ($files as $file) {

            $b1 = ZStringHelper::find($file, '@');
            $b2 = ZStringHelper::find($file, '.idea');
            $b3 = ZStringHelper::find($file, 'other');
            if ($b1 || $b2 || $b3)
                continue;

            $return[] = $file;
        }

        return $return;

    }


    private function blockAdder($name, $pageBlockType, $file)
    {

        $action = PageBlocks::findOne([
            'name' => $name
        ]);

        if ($action === null)
            $action = new PageBlocks();

        $content = file_get_contents($file);
        $service = Az::$app->utility->pregs;

        $action->name = $name;
        $action->check = true;
        $action->page_blocks_type_id = $pageBlockType->id;
        $action->title = Azl . $service->pregMatch($content, "action->title = '(.*)'", bname($name));
        $action->icon = $service->pregMatch($content, "action->icon ?= ?'(.*)'", Az::$app->utility->mains->icon());

        if ($action->save())
            Az::debug($name, 'Save PageBlocks');

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

        Az::$app->db->createCommand('TRUNCATE TABLE "page_module" CASCADE')->execute();
        Az::$app->db->createCommand('TRUNCATE TABLE "core_control" CASCADE')->execute();
        Az::$app->db->createCommand('TRUNCATE TABLE "page_action" CASCADE')->execute();
    }

    #endregion

}
