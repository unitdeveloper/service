<?php

/**
 * Author:  Asror Zakirov
 * Date:    07.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 */

namespace zetsoft\service\market;

use yii\caching\TagDependency;
use zetsoft\dbitem\wdg\MenuItem;
use zetsoft\models\page\PageAction;
use zetsoft\models\shop\ShopBrand;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopCategory;
use zetsoft\models\menu\Menu;
use zetsoft\models\menu\MenuImage;
use zetsoft\models\core\CoreMenuNew;
use zetsoft\models\shop\ShopElement;
use zetsoft\models\shop\ShopProduct;
use zetsoft\service\cores\Cache;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\helpers\ZUrl;
use zetsoft\system\kernels\ZFrame;
use zetsoft\dbitem\wdg\MenuItems;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @property string $role
 */
class CategoryOver extends ZFrame
{
    public $categories;
    public $brands;
    public $defaultCateogryImage = "https://cdn.dribbble.com/users/357797/screenshots/3998541/empty_box.jpg";

    public function init()
    {

        parent::init();
    }

    public function test()
    {
        $this->generateDBMenuItemsJ();
    }

    /**
     * @param ShopCategory $category
     * @return mixed
     */
    public function normalizeExtra($category)
    {
        if ($category === null)
            return null;
        //$brand_ids = $category->shop_brand_ids;
        $brands = [];
        /*if (is_array($brand_ids)) {
            $brand_ids = array_map(function ($id) {
                return (int)$id;
            }, $brand_ids);
            $brands = $this->brands->whereIn('id', $brand_ids);
        }*/

        $sample = [];
        if (!empty($brands))
            foreach ($brands as $brand) {
                //$brand = $this->toObject($brand);

                $menuItem = new MenuItem();
                /*if ($brand != null and !empty($brand) and $brand != "")
                {
                    if (!isset($brand->url))
                        vdd($brand);
                    $menuItem->url = $brand->url;
                    $menuItem->image = '/uploaz/' . $this->bootEnv('appTitle') . "/ShopBrand/image/$brand->id/" . ZArrayHelper::getValue($brand->image, 0);

                    $menuItem->target = $brand->target;
                    $menuItem->tooltip = $brand->name;
                    $menuItem->location = $brand->location;
                }*/

                $sample[] = $menuItem;
            }
        return $sample;
    }


    public function generateDBMenuItems($company_id = null)
    {
        $key = __FUNCTION__ . $company_id;
        /*         if ($this->cacheExists($key))
                     return $this->cacheGet( $key);*/

        $shopCat = ShopCategory::find()
            ->asArray()
            ->indexBy('id')
            ->orderBy('sort')
            ->all();

        $this->categories = collect($shopCat);

        $this->brands = ShopBrand::find()->indexBy('id')
            ->asArray()
            ->all();

        $db_menu_items = [];

        $parent_categories = $this->categories
            ->where('parent_id', null);

        foreach ($parent_categories as $parent_category) {

            $menuItem = new MenuItem();

            $menuItem->title = $parent_category['name'];
            $menuItem->id = $parent_category['id'];
            $menuItem->icon = $parent_category['icon'];
            $id = $parent_category['id'];
            $path = '/uploaz/' . $this->bootEnv('appTitle') . "/ShopBrand/image/$id/" . ZArrayHelper::getValue($parent_category['image'], 0);

            if (file_exists($path))
                $menuItem->image = $path;
            else
                $menuItem->image = $this->defaultCateogryImage;

            if ($company_id !== null) {
                $menuItem->url = ZUrl::to([
                    '/shop/user/filter-catalog/main',
                    'market_id' => $company_id,
                    'category_id' => $parent_category['id'],
                ]);
            } else {
                $menuItem->url = ZUrl::to([
                    '/shop/user/filter-common/main',
                    'id' => $parent_category['id'],
                ]);
            }
            $menuItem->target = '_self';
            $menuItem->items = $this->getItems($parent_category, $company_id);
            //    $menuItem->extra = $this->normalizeExtra($parent_category);

            $db_menu_items[] = $menuItem;
        }


        $this->cacheSet($key, $db_menu_items, Cache::type['cache']);

        return $db_menu_items;
    }


    /**
     * @param ShopCategory $parent_category
     */
    public function getItems($parent_category, $company_id = null)
    {
        $items = [];

        /*$shop_categories = CoreCategory::find()->where([
            'id' => $parent_category->child
        ])->orderBy(['sort' => SORT_ASC])->all();*/


        $core_categories = $this->categories
            ->where('parent_id', $parent_category['id'])
            ->sortBy('sort');

        if (!empty($core_categories)) {
            foreach ($core_categories as $core_category) {

                $aa = $this->paramGet('aaa');
                //$b = ;
                $this->paramSet('aaa', ++$aa);

                $menuItem = new MenuItem();
                $menuItem->title = $core_category['name'];
                $menuItem->id = $core_category['id'];
                $menuItem->icon = $parent_category['icon'];
                if ($company_id !== null) {
                    $menuItem->url = ZUrl::to([
                        '/shop/user/filter-catalog/main',
                        'market_id' => $company_id,
                        'category_id' => $parent_category['id'],
                    ]);
                } else {
                    $menuItem->url = ZUrl::to([
                        '/shop/user/filter-common/main',
                        'id' => ZArrayHelper::getValue($parent_category, 'id'),
                    ]);
                }
                //       $menuItem->extra = $this->normalizeExtra($core_category);
                $menuItem->items = $this->getItems($core_category);

                $items[] = $menuItem;
            }
        }

        return $items;
    }


    public function getParent($array, $parent_id)
    {
        $parent = ShopCategory::findOne($parent_id);
        $item = new MenuItem();
        $item->name = $parent->name;
        $item->id = $parent->id;
        $item->items = $array;
        if ($parent->parent_id == null)
            return $item;
        else {
            return $this->getParent([$item], $parent->parent_id);
        }
    }

    public function getMenuItem($id, $with_brothers = true)
    {

        $category = ShopCategory::findOne($id);
        if ($category === null) {
            return false;
        }
        $brothers = ShopCategory::find()->where([
            'parent_id' => $category->parent_id
        ])
            ->all();


        $brother_items = [];
        if (\Dash\count($brothers) !== null) {
            foreach ($brothers as $brother) {
                $item = new MenuItem();
                $item->name = $brother->name;
                $item->id = $brother->id;
                if ($brother->id === $id)
                    $item->class = "active";

                $brother_items[] = $item;
            }
        }


        if ($with_brothers == false) {
            $brother_items = [];
            $self_item = new MenuItem();
            $self_item->name = $category->name;
            $self_item->id = $category->id;
            $brother_items[] = $self_item;
        }


        $a = ShopCategory::findOne($category->parent_id);
        if ($a == null)
            return $brother_items;
        $bobo = $this->getParent($brother_items, $category->parent_id);
        return $bobo;
    }

    public function getBrands()
    {
        $selectedId = $this->httpGet('depend');

        if (!empty($selectedId)) {
            $category = ShopCategory::find()->where(['id' => $selectedId])->one();
            if ($category === null) {
                return null;
            }
            $data = $category->getCoreBrandsFromCoreBrandIds();
            $brands = [];
            if (empty($data))
                return null;
            foreach ($data as $item) {
                $brands[$item->id] = $item->name;
            }
            if ($brands === null)
                return [];

            return $brands;


        }

    }

    public function getLeafs($item)
    {

        if (\Dash\count($item->items) == 0)
            return $item;
        else {
            foreach ($item->items as $child) {
                $leafs = [];
                $leafs[] = $this->getLeafs($child);
                return $leafs;
            }
        }
    }

    public function getParentAndLeaf()
    {
        $items = $this->generateDBMenuItems();
        $result_items = [];
        foreach ($items as $item) {
            $leafs = $this->getLeafs($item);

            $item->items = $leafs;
            $result_items[] = $leafs;
        }
    }


}




