<?php

/**
 *
 *
 * Author:  Asror Zakirov
 *
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\menu;


use Ratchet\App;
use yii\helpers\ArrayHelper;
use zetsoft\actions\crud\test;
use zetsoft\dbitem\wdg\MenuItem;
use zetsoft\dbitem\core\CpasTrackerItem;
use zetsoft\models\drag\DragConfig;
use zetsoft\models\drag\DragConfigDb;
use zetsoft\models\drag\DragForm;
use zetsoft\models\drag\DragFormDb;
use zetsoft\models\shop\ShopOrder;
use zetsoft\system\assets\ZMenu;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\helpers\ZUrl;
use zetsoft\system\module\Models;

class ALL extends ZMenu
{

    public function run()
    {

        Az::start(__METHOD__);

        // $this->widgets();
        $this->develop();
//        $this->library();
//        $this->elfind();
//        $this->zoft();
        //   $this->appMenu();
        /*  vdd($this->options);*/
        return $this->options;

    }

    private function appMenu()
    {
        $path = Root . '\models\\App\\' . App;
        //vdd($path);
        $models = ZFileHelper::scanFiles($path);
        if (empty($models)) return null;
        $items = [];

        foreach ($models as $class) {


            $className = strtr(bname($class), ['.php' => '']);


            $controller = ZInflector::camel2id($className);
            $modelClass = 'zetsoft\\models\\App\\' . App . '\\' . $className;
            /** @var Models $model */
            $model = new $modelClass();

            $menuItem = new MenuItem();

            $menuItem->icon = Az::$app->utility->mains->icon();
            $menuItem->title = $model->configs->title;

            $lang = Az::$app->language;
            $menuItem->url = "/$lang/cruds/{$controller}.aspx";

            $items[] = $menuItem;


            Az::debug($model->configs->title, 'Added to Menu');
        }

        $appMenu = new MenuItem();
        $appMenu->title = Az::l('Приложение');
        $appMenu->items = $items;

        $this->options[] = $appMenu;
    }

    private function develop()
    {

        global $boot;
        
        $this->models();

        return false;
    }

    private function editors()
    {
        $main = new MenuItem();
        $main->icon = Az::$app->utility->mains->icon();
        $main->label = Az::l('Editors');

        $items = [];

        $item1 = new MenuItem();
        $item1->icon = Az::$app->utility->mains->icon();
        $item1->label = Az::l("Grapes");
        //$item->items = $this->allChilds($path);
        $item1->url = '/core/widget/sampleWidgetNorm.aspx';

        $items[] = $item1;

        $item2 = new MenuItem();
        $item2->icon = Az::$app->utility->mains->icon();
        $item2->label = Az::l("Menu editor");
        //$item->items = $this->allChilds($path);
        $item2->url = "http://idrive.zoft.uz:5050/";

        $items[] = $item2;

        $main->items = $items;
        $this->options[] = $main;
        return true;
    }

    private function zoft()
    {
        $main = new MenuItem();
        $main->icon = Az::$app->utility->mains->icon();
        $main->label = Az::l('Zoft');

        $items = [];

        $item1 = new MenuItem();
        $item1->icon = Az::$app->utility->mains->icon();
        $item1->label = Az::l("Learning");
        //$item->items = $this->allChilds($path);
        $item1->url = 'http://learning.zoft.uz:5050/';

        $items[] = $item1;

        $item2 = new MenuItem();
        $item2->icon = Az::$app->utility->mains->icon();
        $item2->label = Az::l("IDrive");
        //$item->items = $this->allChilds($path);
        $item2->url = "http://idrive.zoft.uz:5050/";

        $items[] = $item2;

        $item3 = new MenuItem();
        $item3->icon = Az::$app->utility->mains->icon();
        $item3->label = Az::l("Proccess");
        //$item->items = $this->allChilds($path);
        $item3->url = "http://process.zoft.uz/";

        $items[] = $item3;

        $item4 = new MenuItem();
        $item4->icon = Az::$app->utility->mains->icon();
        $item4->label = Az::l("Testing");
        //$item->items = $this->allChilds($path);
        $item4->url = "http://testing.zoft.uz/";

        $items[] = $item4;

        $item5 = new MenuItem();
        $item5->icon = Az::$app->utility->mains->icon();
        $item5->label = Az::l("Platform");
        //$item->items = $this->allChilds($path);
        $item5->url = "http://learning.zoft.uz:5050/Platform/";

        $items[] = $item5;

        $main->items = $items;
        $this->options[] = $main;
        return true;
    }

    private function elfind()
    {
        $main = new MenuItem();
        $main->icon = Az::$app->utility->mains->icon();
        $main->label = Az::l('Elfind');

        $items = [];

        $item1 = new MenuItem();
        $item1->icon = Az::$app->utility->mains->icon();
        $item1->label = Az::l("all");
        //$item->items = $this->allChilds($path);
        $item1->url = ZUrl::to(["/core/elfind/all"]);

        $items[] = $item1;

        $item2 = new MenuItem();
        $item2->icon = Az::$app->utility->mains->icon();
        $item2->label = Az::l("php");
        //$item->items = $this->allChilds($path);
        $item2->url = ZUrl::to(["/core/elfind/php"]);

        $items[] = $item2;

        $item3 = new MenuItem();
        $item3->icon = Az::$app->utility->mains->icon();
        $item3->label = Az::l("htm");
        //$item->items = $this->allChilds($path);
        $item3->url = ZUrl::to(["/core/elfind/htm"]);

        $items[] = $item3;

        $main->items = $items;
        $this->options[] = $main;
        return true;
    }

    private function widgets()
    {

        global $boot;

        if (!$boot->userDev())
            return false;

        $noWidget = $this->cookieGet('noWidget', false);

        if ($noWidget === 1)
            return true;

        $widget = $this->cookieGet('widget');

        if (empty($widget))
            $widget = $boot->env('widget');

        if (empty($widget))
            return Az::warning($widget, 'widget is empty');

        $folders = explode('|', $widget);

        if (!is_array($folders) || empty($folders))
            return Az::warning($folders, 'folders is empty');

        $main = new MenuItem();
        $main->icon = Az::$app->utility->mains->icon();
        $main->label = Az::l('Компоненты');

        $items = [];
        foreach ($folders as $folder) {

            $pathAlias = '@zetsoft/render/' . $folder;
            $path = Az::getAlias($pathAlias);

            if (!file_exists($path))
                return false;

            $item = new MenuItem();
            $item->icon = Az::$app->utility->mains->icon();
            $item->title = Az::l($folder);
            $item->items = $this->allChilds($path);

            $items[] = $item;
        }

        $main->items = $items;
        $this->options[] = $main;

        return true;
    }

    public const exclude = [
        DragConfig::class,
        DragConfigDb::class,
        DragForm::class,
        DragFormDb::class,
    ];

    private function itemCreator($classes, $folder)
    {

        $items = [];

        foreach ($classes as $class) {

            $className = bname($class, '.php');

            $controller = ZInflector::camel2id($className);

            /** @var ShopOrder $modelClass */
            $modelClass = 'zetsoft\\models\\' . $folder . '\\' . $className;
            /** @var Models $model */

            if (!class_exists($modelClass))
                continue;

            if (ZArrayHelper::isIn($modelClass, self::exclude))
                continue;

            if (!$modelClass::$menu)
                continue;

            $menuItem = new MenuItem();

            $lang = Az::$app->language;
            $menuItem->url = "/crud/{$controller}/index.aspx";
            $menuItem->title = $modelClass::$title;

            if (!empty($modelClass::$icon))
                $modelClass::$icon = Az::$app->utility->mains->icon();

            $menuItem->icon = $modelClass::$icon;

            $this->items[] = $menuItem;

            $menuItemCreate = clone $menuItem;
            $menuItemCreate->title = Az::l('Создание ') . $modelClass::$title;
            $menuItemCreate->url = str_replace('index', 'create', $menuItem->url);

            $this->items[] = $menuItemCreate;


            $items[] = $menuItem;

            Az::debug($modelClass::$title, 'Added to Menu');
        }

        return $items;
    }


    public function models()
    {
        $modules = Az::$app->smart->migra->category(true);
        $modulesApp = Az::$app->smart->migra->categoryApp(true);

        foreach ($modules as $folder => $title) {

            if ($folder === '@')
                continue;

            $path = Root . '\models\\' . $folder . '\\';
            $path = ZFileHelper::normalizePath($path);

            $models = ZFileHelper::scanFilesPHP($path, true);
            //  vdd($models);
            if (empty($models))
                continue;

            $item = new MenuItem();
            $item->title = $title;
            $item->items = $this->itemCreator($models, $folder);

            $this->options[] = $item;

        }

    }

}




