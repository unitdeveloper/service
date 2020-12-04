<?php
/**
 *
 * CreatedBy: Jaxongir Maxamadjonov
 *
 */
namespace zetsoft\service\utility;
use yii\helpers\ArrayHelper;
use zetsoft\dbitem\wdg\MenuItem;
use zetsoft\system\assets\ZMenu;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\module\Models;
class Menu extends ZMenu
{

    public function run()
    {

        Az::start(__METHOD__);

        // $this->widgets();
        $this->develop();
        //$this->library();

        return $this->options;

    }



    private function develop()
    {

        global $boot;

        $notUserDev = !$boot->userDev();
        $notAdmin = !$this->hasRole('admin');

        if ($notAdmin && $notUserDev)
            return false;

        $this->models();

        return false;
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

    private function models()
    {

        $classes = Az::$app->smart->migra->scan();

        $excludes = [
            'CoreInput',
            'ScholarNew',
            'ScholarTest'
        ];

        $systems = [];
        $elements = [];

        foreach ($classes as $class) {
            if (ZArrayHelper::isIn(bname($class), $excludes))
                continue;

            $className = bname($class);


            $controller = ZInflector::camel2id($className);

            /** @var Models $model */
            $model = new $class();
            $title = $model->configs->title;

            $menuItem = new MenuItem();

            $menuItem->icon = Az::$app->utility->mains->icon();
            $menuItem->title = $title;

            $lang = Az::$app->language;
            $menuItem->url = "/$lang/admin/{$controller}.aspx";
            $mapp = $this->catModel($className);

            if ($model->configs->makeMenu)
                if ($mapp === 'ALL')
                    $systems[] = $menuItem;
                else
                    $elements[] = $menuItem;

            Az::debug($model->configs->title, 'Added to Menu');
        }


        $item = new MenuItem();
        $item->title = Az::l('Система');
        $item->items = $systems;

        $this->options[] = $item;


        $item = new MenuItem();
        $item->title = Az::l('Элементы');
        $item->items = $elements;

        $this->options[] = $item;

    }

}

