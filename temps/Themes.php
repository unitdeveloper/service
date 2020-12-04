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

namespace zetsoft\service\temps;

use zetsoft\models\page\PageTheme;
use zetsoft\models\page\PageThemeType;
use zetsoft\system\actives\ZActiveRecord;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\module\Models;


class Themes extends ZFrame
{

    public $pathView;

    public function init()
    {
        parent::init();
        $this->pathView = Root . '/webhtm/thm';
    }


    #region CATEGORY
    /*
     *  @author DavlatovRavshan
     *  Сканирует директорию thm и сохраняет все папки в модель PageThemeType
     *
     * */


    public function category()
    {

        Az::start(__FUNCTION__);

        $this->update(PageThemeType::class);
        $this->saveCategories();
        $this->delete(PageThemeType::class);

        Az::end();

        return false;

    }


    public function scanCategories()
    {

        $folders = ZFileHelper::scanFolder($this->pathView);

        $categories = [];
        foreach ($folders as $folder) {
            $folder = str_replace('.php', '', $folder);
            $categories[] = bname($folder);
        }

        return $categories;

    }


    public function saveCategories()
    {

        foreach ($this->scanCategories() as $category) {

            $model = PageThemeType::findOne([
                'name' => $category,
            ]);

            if ($model === null) {
                Az::debug($category, 'Create new CoreModule');
                $model = new PageThemeType();
            }

            $model->name = $category;
            $model->check = true;

            $model->save();

        }

    }


    #endregion


    #region TEMPLATES
    /*
     *  @author MurdovMirbosit
     *
     * Сканирует файлы внутри директории thm и сохраняет все папки в модель PageThemeType
     * */

    public function template()
    {

        Az::start(__FUNCTION__);

        $this->update(PageTheme::class);
        $this->templateScans();
        $this->delete(PageTheme::class);

        Az::end();
    }


    public function templateScans()
    {

        $pageThemeTypes = PageThemeType::find()->all();

        foreach ($pageThemeTypes as $pageThemeType) {

            $path = $this->pathView . '/' . $pageThemeType->name;

            if (!file_exists($path)) {
                Az::warning($path, 'Control Path not Exists');
                continue;
            }

            $files = ZFileHelper::scanFilesPHP($path);

            foreach ($files as $file) {
                $name = str_replace([$this->pathView, '\\', '.php'], ['', '/', ''], $file);
                $this->templateAdder($name, $pageThemeType, $file);
            }

        }

    }

    private function templateAdder($name, $pageThemeType, $file)
    {

        $theme = PageTheme::findOne([
            'name' => $name,
        ]);

        if ($theme === null)
            $theme = new PageTheme();

        $content = file_get_contents($file);
        $service = Az::$app->utility->pregs;

        $theme->name = bname($name);
        $theme->path = $name;
        $theme->check = true;
        $theme->page_theme_type_id = $pageThemeType->id;
        $theme->title = $service->pregMatch($content, "action->title = '(.*)'", bname($name)) . $t;
        $theme->icon = $service->pregMatch($content, "action->icon ?= ?'(.*)'", Az::$app->utility->mains->icon());

        if ($theme->save()) {
            Az::debug($name, 'Save PageTheme');
        }

    }

    #endregion


    #region SYSTEM

    /*
     *  @author DavlatovRavshan
     *
     * */


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

        /** @var PageThemeType $model */
        foreach ($models as $model) {
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

    #endregion
}
