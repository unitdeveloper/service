<?php

/**
 * Author:  Asror Zakirov
 * Date:    07.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 */

namespace zetsoft\service\cores;


use yii\caching\TagDependency;
use zetsoft\dbitem\wdg\MenuDataItem;
use zetsoft\dbitem\wdg\MenuItem;
use zetsoft\models\dyna\DynaChess;
use zetsoft\models\page\PageAction;
use zetsoft\models\menu\Menu;
use zetsoft\models\menu\MenuImage;
use zetsoft\models\core\CoreMenuNew;
use zetsoft\models\page\PageView;
use zetsoft\models\shop\ShopCategory;
use zetsoft\service\menu\ALL;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZCollect;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\helpers\ZUrl;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\system\kernels\ZFrame;


/**
 *
 * @property string $role
 */
class Menus extends ZFrame
{
    public $actions;
    public $cruds;
    /* @var ZCollect $views */

    public $views;
    public $viewsRaw;

    /* @var MenuItem $mainItem */
    public $mainItem;
    public $allMenus = [];


    #region ALL

    public function init()
    {
        parent::init();
        $this->cruds = $this->cruds();

        $this->viewsRaw = PageView::find()
            ->asArray()
            ->all();

        $this->views = zcollect($this->viewsRaw);

    }


    public function create($names = null)
    {


        $key = __FUNCTION__ . '_1333d2' . $this->userRole() . ZVarDumper::export($names) . PHP_SAPI;

        if ($this->cacheGet($key))
            return $this->cacheGet($key);


        /** @var Menus $model */

        $this->dbMenu();


        if ($this->hasRoles(['dev', 'admin'])) {
            $report = $this->reportMenu();

            if (!empty($report->items))
                $this->allMenus[] = $report;

            $appModels = $this->appModels();

            if (!empty($appModels->items))
                $this->allMenus[] = $appModels;

            $runs = (new ALL())->run();

            foreach ($runs as $run)
                $this->allMenus[] = $run;
        }


        $this->cacheSet($key, $this->allMenus, Cache::type['redis'], new TagDependency(['tags' => Menu::class]));

        return $this->allMenus;
    }


    public function dbMenu()
    {

        if (empty($names))
            $models = Menu::findAllData(null, true);
        else
            $models = Menu::find()
                ->where([
                    'name' => $names
                ])
                ->orderBy([
                    'id' => SORT_ASC
                ])
                ->asArray()
                ->all();


        /** @var Menu $model */
        foreach ($models as $model) {
            if (!$this->model($model))
                continue;
        }
    }

    public function model($model)
    {

        $role = $model['role'];

        if (empty($model['json']))
            return false;

        if (!$model['active'])
            return false;

        if ($this->userRole() !== 'dev')
            if (!ZArrayHelper::isIn($this->userRole(), (array)$role))
                return false;

        $item = new MenuItem();
        $item->icon = $model['icon'] ?? Az::$app->utility->mains->icon(true);
        $item->visible = true;
        $item->title = $model['title'];
        $item->name = $model['name'];
        $item->target = $model['target'];

        $this->mainItem = $item;
        $item->items = $this->normalize($model['json']);
        //  $item->extra = $this->normalizeExtra($model['id']);


        if ($this->userRole() === 'dev')
            $model['inline'] = false;

        if ($model['inline']) {
            foreach ($item->items as $childs) {
                $this->allMenus[] = $childs;
            }
        } else
            $this->allMenus[] = $item;

        return true;

    }


    #endregion


    #region Titles

    public function titlesTest()
    {
        $item = $this->titles(16);

        $titles = $item->titles;
        vd($titles);
    }


    public function titles(int $id): ?MenuDataItem
    {

        $model = Menu::findOne($id);

        if ($model === null) {
            $this->alertDanger('Model not found', 'Error');
            return null;
        }
        $dataitem = new MenuDataItem();

        $dataitem->model = $model;
        $dataitem->json = Az::$app->menu->json->run($model);

        $dataitem->views = $this->viewsRaw;

        $dataitem->views = ZArrayHelper::merge($dataitem->views, $this->cruds);

        $dataitem->titles = ZArrayHelper::map($dataitem->views, 'name', 'title');
        $dataitem->action = ZArrayHelper::map($dataitem->views, 'name', 'name');
        $dataitem->icons = ZArrayHelper::map($dataitem->views, 'name', 'icon');

        return $dataitem;


    }


    #endregion

    #region Cruds


    public function crudsTest()
    {
        $cruds = $this->cruds();
        vd($cruds);
    }


    public function cruds()
    {
        Az::$app->menu->aLL->models();

        /** @var MenuItem[] $items */
        $items = Az::$app->menu->aLL->items;
        $views = [];

        foreach ($items as $item) {

            $name = str_replace('.aspx', '', $item->url);

            $views[$name] = [
                'name' => $name,
                'title' => $item->title,
                'icon' => $item->icon,
            ];

        }

        return $views;
    }


    #endregion


    #region Items

    public function appModels()
    {
        global $boot;
        $title = $boot->env('appTitle');
        $item = new MenuItem();
        $item->title = $title;
        $classes = Az::$app->smart->migra->appScan();

        // vdd($classes);
        $data = [];
        foreach ($classes as $class) {
            if (!$class::$menu)
                continue;

            $menu = new MenuItem();
            $menu->title = $class::$title;

            $url = '/crud/' . ZInflector::camel2id(bname($class)) . '/index.aspx';

            $menu->url = $url;
            $data[] = $menu;
        }
        $item->items = $data;
        return $item;

    }


    /**
     *
     * Function  reportMenu
     * @return  MenuItem
     * @author Daho
     */
    public function reportMenu()
    {
        $item = new MenuItem();
        $item->title = Az::l('Универсальный отчет');
        $item->icon = 'fal fa-chart-line';
        $item->items = $this->reportItems();

        return $item;
    }

    /**
     *
     * Function  reportItems
     * @return array
     * @author Daho
     */
    public function reportItems()
    {
        $data = [];

        $chesses = DynaChess::find()->all();
        /** @var DynaChess $chess */
        foreach ($chesses as $chess) {
            if ($this->checkRole($chess)) {
                $item = new MenuItem();
                $item->title = $chess->name;
                $item->url = ZUrl::to([
                    '/core/dynagrid/chess_view',
                    'id' => $chess->id,
                    'modelClass' => $chess->models
                ]);
                $data[] = $item;
            }
        }

        return $data;
    }

    #endregion


    #region Norms

    public function normalize($items)
    {
        if (!is_array($items) || empty($items))
            return [];

        $return = [];
        foreach ($items as $item) {

            $name = ZArrayHelper::getValue($item, 'action');

            switch (true) {
                case ZArrayHelper::keyExists($name, $this->cruds):

                    $action = [];
                    $action['name'] = $name;
                    $action['icon'] = $this->cruds[$name]['icon'];
                    $action['title'] = $this->cruds[$name]['title'];

                    break;

                default:
                    $action = $this->views
                        ->where('name', $name)
                        ->first();

            }


            if ($action === null)
                continue;

            $label = $action['title'] ?? bname($action['name']);

            $url = ZUrl::to([
                $action['name']
            ]);

            $icon = $this->getIcon($item, $action);

            $args = ZArrayHelper::getValue($item, 'args', '');
            $class = ZArrayHelper::getValue($item, 'class', '');
            $target = ZArrayHelper::getValue($item, 'target', '_self');
            $roles = ZArrayHelper::getValue($item, 'role', '');

            $visible = true;
            if (ZStringHelper::find($roles, $this->userRole()))
                $visible = false;


            $children = ZArrayHelper::getValue($item, 'children');

            $childItems = [];

            if (!empty($children))
                $childItems = $this->normalize($children);


            $menuItem = new MenuItem();

            $menuItem->title = $label;
            $menuItem->icon = $icon;
            $menuItem->url = $url;
            $menuItem->args = $args;
            $menuItem->class = $class;
            $menuItem->target = (empty($target)) ? $this->mainItem->target : $target;
            $menuItem->visible = $visible;
            $menuItem->items = $childItems;
            $menuItem->tooltip = $label;

            $return[] = $menuItem;

        }

        return $return;

    }

    public function normalizeExtra($id)
    {
        $images = MenuImage::find()
            ->where([
                'menu_id' => $id
            ])
            ->all();

        $sample = [];

        foreach ($images as $image) {

            $menuItem = new MenuItem();

            $imageApp = ZArrayHelper::getValue($image->image, 0);

            $menuItem->url = $image->url;
            $menuItem->image = '/uploaz/' . $this->bootEnv('appTitle') . "/CoreMenuImage/image/$image->id/" . $imageApp;
            $menuItem->target = $image->target;
            $menuItem->tooltip = $image->title;
            $menuItem->location = $image->location;

            $sample[] = $menuItem;

        }

        return $sample;
    }


    #endregion

    #region Utils


    private function getIcon($item, $action)
    {

        switch (true) {

            case !empty($icon = ZArrayHelper::getValue($item, 'icon')):
                break;

            case !empty($action->icon):
                $icon = $action->icon;
                break;

            default:
                $icon = Az::$app->utility->mains->icon(true);
                break;

        }

        return $icon;

    }


    /**
     *
     * Function  checkRole
     * @param DynaChess $chess
     * @return  bool
     * @author Daho
     */
    public function checkRole(DynaChess $chess)
    {
        $user = $this->userIdentity();
        if ($user === null)
            return false;

        if ($this->userIsGuest())
            return false;

        $role = $user->role;
        $b1 = $chess->allow;
        if (!is_array($chess->rols))
            return $b1;

        $b2 = ZArrayHelper::isIn($role, $chess->rols);

        return $b1 === $b2;
    }

    #endregion


}




