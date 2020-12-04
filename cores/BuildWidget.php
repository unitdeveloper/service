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
use zetsoft\models\page\PageWidget;
use zetsoft\models\page\PageWidgetType;
use zetsoft\system\actives\ZActiveRecord;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\kernels\ZWidget;
use zetsoft\system\module\Models;

class BuildWidget extends ZFrame
{

    #region Vars

    public $pathWidget;
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

        $this->pathWidget = Root . '/widgets';

        $this->paramSet(paramNoEvent, true);
    }

    public function run()
    {
        $this->widgetType();
        $this->widget();
    }

    ##endregion

    #region Folder


    public function widgetType()
    {

        Az::start(__FUNCTION__);

        $this->update(PageWidgetType::class);
        $this->saveWidgetType();
        $this->delete(PageWidgetType::class);

        Az::end();

        return false;
    }

    public function saveWidgetType()
    {

        foreach ($this->scanWidgetType() as $name) {

            $model = PageWidgetType::findOne([
                'name' => $name,
            ]);

            if ($model === null) {
                Az::debug($name, 'Create new WidgetType');
                $model = new PageWidgetType();
            }

            $category = ZArrayHelper::getValue(ZWidget::categories, $name);

            $title = $name;
            if (!empty(ZArrayHelper::getValue($category, 'title')))
                $title = ZArrayHelper::getValue($category, 'title');

            $model->name = $name;
            $model->title = $title;
            $model->check = true;

            $model->save();
        }

    }

    public function scanWidgetType()
    {

        $folders = ZFileHelper::scanFolder($this->pathWidget);

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

    public function widget()
    {

        Az::start(__FUNCTION__);

        $this->update(PageBlocks::class);
        $this->widgetScans();
        $this->delete(PageBlocks::class);

        Az::end();
    }

    public function widgetScans()
    {
        $names = [
            'columns',
            'blocks',
            'inputes',
        ];

        $pageWidgetTypes = PageWidgetType::find()->where([
            'name' => $names
        ])->all();

        foreach ($pageWidgetTypes as $pageWidgetType) {

            $path = $this->pathWidget . '/' . $pageWidgetType->name;

            if (!file_exists($path)) {
                Az::warning($path, 'Control Path not Exists');
                continue;
            }

            $files = $this->getFileWidgets($path);

            foreach ($files as $file) {
                $name = str_replace(['/', '.php', Az::getAlias('@zetsoft')], ['\\', '', 'zetsoft'], $file);

                if (!class_exists($name))
                    continue;

                $widget = PageWidget::findOne([
                    'name' => $name
                ]);

                if ($widget === null)
                    $widget = new PageWidget();

                $grapes = null;
                if (property_exists($name, 'grapes'))
                    $grapes = $name::$grapes;

                if (!$grapes)
                    continue;

                $widget->name = $name;
                $widget->check = true;
                $widget->page_widget_type_id = $pageWidgetType->id;
                $widget->title = ZArrayHelper::getValue($grapes, 'title');
                $widget->icon = ZArrayHelper::getValue($grapes, 'icon');
                $widget->image = ZArrayHelper::getValue($grapes, 'image');

                if ($widget->save())
                    Az::debug($name, 'Save PageBlocks');

            }

        }
    }


    private function getFileWidgets($path)
    {

        $files = ZFileHelper::scanFilesPHP($path);

        $return = [];
        foreach ($files as $file) {
            $return[] = $file;
        }

        return $return;

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
