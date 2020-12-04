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


use rmrevin\yii\fontawesome\FAS;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use zetsoft\dbitem\wdg\MenuItem;
use zetsoft\service\smart\Cruds;
use zetsoft\service\smart\Model;
use zetsoft\system\assets\ZMenu;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\module\Models;

class ALLNew extends ZMenu
{

    public function run()
    {

        Az::start(__METHOD__);

        //  $this->widgets();
        $this->develop();
        //   $this->library();

        return $this->options;

    }


    public function add(string $label)
    {
        if (!empty($this->options))
            $this->optionsALL[] = [
                'label' => $label,
                'items' => $this->options,
            ];


        return true;
    }

    private function develop()
    {

        /*   $admin = Az::$app->cores->auth->role('admin');
           $userDev = $boot->userDev();

           if ($admin || $userDev)
               return true;*/

        $this->models();
        //$this->add('title');

        return false;
    }

    private function widgets()
    {
        if (!$boot->userDev())
            return false;

        $scanWidget = ArrayHelper::getValue($_COOKIE, 'ScanWidget', '0') === '1';

        if ($scanWidget)
            $this->render('@zetsoft/webhtm/ALL/render/');

        /*$this->add('');*/

        return true;
    }

    private function models()
    {

        $classes = Az::$app->smart->migra->scan();
        $systems = [];
        $elements = [];


        foreach ($classes as $class) {


            $className = bname($class);
            $controller = ZInflector::camel2id($className);

            /** @var Models $model */
            $model = new $class();
            $title = $model->configs->title;
            $menuItem = new MenuItem();

            $menuItem->icon = Az::$app->utility->mains->icon();
            $menuItem->title = $title;
            $menuItem->url = "/admin/{$controller}.aspx";
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




