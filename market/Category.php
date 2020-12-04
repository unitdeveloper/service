<?php

/**
 * Author:  Asror Zakirov
 * Date:    07.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 */

namespace zetsoft\service\market;

use Illuminate\Support\Collection;
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
use zetsoft\models\shop\ShopOption;
use zetsoft\models\shop\ShopOptionBranch;
use zetsoft\models\shop\ShopProduct;
use zetsoft\models\test\TestAsror;
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
class Category extends ZFrame
{

    /**
     *
     * @var Collection $categories
     */

    public $categories;

    /**
     *
     * @var Collection $brands
     */
    public $brands;
    public $defaultCateogryImage = "https://cdn.dribbble.com/users/357797/screenshots/3998541/empty_box.jpg";

    public function init()
    {
        $categories = ShopCategory::find()
            ->asArray()
            ->indexBy('id')
            ->all();

        $this->categories = collect($categories);

        parent::init();
    }

    public function test()
    {
        // $this->generateDBMenuItemsJ();
        $this->testNormalizeExtra();
    }

    public function NormalizeExtraTest()
    {
        $category = 457;
        $var = $this->normalizeExtra($category);
        vd($var);
    }


    /**
     * @param ShopCategory $category
     * @return mixed
     */


    public function normalizeExtra(ShopCategory $category)
    {
        if ($category === null)
            return null;

        $brands = $this->brands->whereIn('id', $category->shop_brand_ids)->toArray();

        $sample = [];
        if (!empty($brands))
            foreach ($brands as $brand) {

                $brand = $this->toObject(ShopBrand::class, $brand);

                $menuItem = new MenuItem();
                if ($brand != null and !empty($brand) and $brand != "") {

                    $id = $brand['id'];

                    $menuItem->image = '/uploaz/' . $this->bootEnv('appTitle') . "/ShopBrand/image/$id/" . ZArrayHelper::getValue($brand['image'], 0);

                    if (!file_exists($menuItem->image))
                        $menuItem->image = $this->defaultCateogryImage;

                    //$menuItem->target = $brand['target'];
                    $menuItem->tooltip = $brand['name'];
                    $menuItem->location = $brand['location'];
                }

                $sample[] = $menuItem;
            }
        return $sample;
    }


    public function testGenerateDBMenuItems()
    {
        $data = $this->generateDBMenuItems();
        vd($data);
    }

    public function generateDBMenuItems($company_id = null)
    {

        $key = __FUNCTION__ . $company_id . PHP_SAPI;

        if ($this->cacheGet($key))
            return $this->cacheGet($key);

        $brand = ShopBrand::find()
            ->asArray()
            ->all();
        $this->brands = collect($brand);

        $db_menu_items = [];

        if ($company_id !== null) {
            $shopCatalogs = collect(
            ShopCatalog::find()->where(['user_company_id' => $company_id])->asArray()->all());

            $shop_element_ids = ZArrayHelper::getColumn($shopCatalogs, 'shop_element_id');

            $shopElements = collect(
                ShopElement::find()->where(['id' => $shop_element_ids])->asArray()->all()
            );

            $shop_product_ids = ZArrayHelper::getColumn($shopElements, 'shop_product_id');

            $shopProducts = ShopProduct::find()->where(['id' => $shop_product_ids])->asArray()->all();
            $shop_categories_id = ZArrayHelper::getColumn($shopProducts, 'shop_category_id');
            $shopCategories = collect(ShopCategory::find()->where(['id' => $shop_categories_id])->asArray()->all());
            $parent_categories = collect(ShopCategory::find()->where(['id' => $shopCategories->pluck('parent_id')->unique()->toArray()])->asArray()->all())->sortBy("sort");

            //$parent_categories = $shopCategories->where('parent_id', null)->sortBy('sort');
        } else
            $parent_categories = $this->categories
                ->where('parent_id', null)
                ->sortBy('sort');


        foreach ($parent_categories as $parent_category) {

            /** @var ShopCategory $parentCategory */
            $parentCategory = $this->toObject(ShopCategory::class, $parent_category);

            $menuItem = new MenuItem();
            $menuItem->title = $parentCategory->name;
            $menuItem->id = $parentCategory->id;
            $menuItem->icon = $parentCategory->icon;
            $id = $parentCategory->id;
            $image = $parentCategory->image;

            $path = '/uploaz/' . $this->bootEnv('appTitle') . "/ShopBrand/image/$id/" . ZArrayHelper::getValue($image, 0);

            if (file_exists($path))
                $menuItem->image = $path;
            else
                $menuItem->image = $this->defaultCateogryImage;

            if ($company_id !== null) {
                $menuItem->url = ZUrl::to([
                    '/shop/user/filter-catalog/main',
                    'market_id' => $company_id,
                    'category_id' => $parentCategory->id,
                ]);
            } else {
                $menuItem->url = ZUrl::to([
                    '/shop/user/filter-common/main',
                    'id' => $parentCategory->id,
                ]);
            }
            $menuItem->target = '_self';
            $menuItem->items = $this->getItems($parentCategory, $company_id);
            $menuItem->extra = $this->normalizeExtra($parentCategory);

            $db_menu_items[] = $menuItem;
        }


        $this->cacheSet($key, $db_menu_items, Cache::type['cache'], new TagDependency(['tags' => ShopCategory::class]));

        return $db_menu_items;
    }

    #region Api Quasar menuItems

    /**
     *
     * Function  menuItemsVue
     * @param null $company_id
     * @return  array|bool
     * @throws \Exception
     * @author AzimjonToirov
     */
    public function menuItemsVue($company_id = null)
    {

        $key = __FUNCTION__ . $company_id . PHP_SAPI;

        if ($this->cacheGet($key))
            return $this->cacheGet($key);

        $this->paramSet('aaa', 0);
        $brand = ShopBrand::find()
            ->asArray()
            ->all();
        $this->brands = collect($brand);

        $db_menu_items = [];

        if ($company_id !== null) {
            $shopCatalogs = collect(ShopCatalog::find()->where(['user_company_id' => $company_id])->asArray()->all());

            $shop_element_ids = ZArrayHelper::getColumn($shopCatalogs, 'shop_element_id');

            $shopElements = collect(
                ShopElement::find()->where(['id' => $shop_element_ids])->asArray()->all()
            );

            $shop_product_ids = ZArrayHelper::getColumn($shopElements, 'shop_product_id');

            $shopProducts = ShopProduct::find()->where(['id' => $shop_product_ids])->asArray()->all();
            $shop_categories_id = ZArrayHelper::getColumn($shopProducts, 'shop_category_id');
            $shopCategories = collect(ShopCategory::find()->where(['id' => $shop_categories_id])->asArray()->all());
            $parent_categories = collect(ShopCategory::find()->where(['id' => $shopCategories->pluck('parent_id')->unique()->toArray()])->asArray()->all())->sortBy("sort");

            //$parent_categories = $shopCategories->where('parent_id', null)->sortBy('sort');
        } else
            $parent_categories = $this->categories
                ->where('parent_id', null)
                ->sortBy('sort');


        foreach ($parent_categories as $parent_category) {

            //$parent_category = $this->toObject(ShopCategory::class, );

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
                $menuItem->href = '/filter-common/' . $company_id /*. '/' . $parent_category['id']*/
                ;
            } else {
                $menuItem->href = '/filter-common/' . $parent_category['id'];

            }
            $menuItem->target = '_self';
            $menuItem->child = $this->getItemsVue($parent_category, $company_id);
            $menuItem->extra = $this->normalizeExtra($parent_category);

            $db_menu_items[] = $menuItem;
        }


        $this->cacheSet($key, $db_menu_items, Cache::type['cache'], new TagDependency(['tags' => ShopCategory::class]));

        return $db_menu_items;
    }

    #endregion

    #region getItemsVue

    /**
     *
     * Function  getItemsVue
     * @param $parent_category
     * @param null $company_id
     * @return  array|null
     * @author  AzimjonToirov
     */
    public function getItemsVue($parent_category, $company_id = null)
    {
        $items = [];

        /*$shop_categories = CoreCategory::find()->where([
            'id' => $parent_category->child
        ])->orderBy(['sort' => SORT_ASC])->all();*/

        if (empty($parent_category['id']))
            return null;

        $core_categories = $this->categories
            ->where('parent_id', $parent_category['id'])
            ->sortBy('sort')
            ->toArray();

        if (!empty($core_categories)) {
            foreach ($core_categories as $core_category) {

                $menuItem = new MenuItem();
                $menuItem->title = $core_category['name'];
                $menuItem->id = $core_category['id'];
                $menuItem->icon = $parent_category['icon'];
                if ($company_id !== null) {
                    $menuItem->href = '/filter-common/' . $company_id;
                } else {
                    $menuItem->href = '/filter-common/' . $parent_category['id'];
                }
                //       $menuItem->extra = $this->normalizeExtra($core_category); //
                $menuItem->child = $this->getItemsVue($core_category);

                $items[] = $menuItem;
            }
        }

        return $items;
    }

    #endregion

    /**
     * @param ShopCategory $parent_category
     */

    public function testGetItems()
    {
        $data = $this->getItems();
        vd($data);
    }

    public function getItems($parent_category, $company_id = null)
    {
        $items = [];

        /*$shop_categories = CoreCategory::find()->where([
            'id' => $parent_category->child
        ])->orderBy(['sort' => SORT_ASC])->all();*/

        if (empty($parent_category['id']))
            return null;

        $core_categories = $this->categories
            ->where('parent_id', $parent_category['id'])
            ->sortBy('sort');

        if (!empty($core_categories)) {
            foreach ($core_categories as $core_category) {

                $aa = $this->paramGet('aaa');
                //$b = ;
                $this->paramSet('aaa', ++$aa);

                //    $core_category = $this->toObject(CoreCategory::class, $core_category);


                $menuItem = new MenuItem();
                $menuItem->title = $core_category['name'];
                $menuItem->id = $core_category['id'];
                $menuItem->icon = $parent_category['icon'];
                if ($company_id !== null) {
                    $menuItem->url = ZUrl::to([
                        '/shop/user/filter-catalog/main',
                        'market_id' => $company_id,
                        'category_id' => $core_category['id'],
                    ]);
                } else {
                    $menuItem->url = ZUrl::to([
                        '/shop/user/filter-common/main',
                        'id' => ZArrayHelper::getValue($core_category, 'id'),
                    ]);
                }
                //       $menuItem->extra = $this->normalizeExtra($core_category);
                $menuItem->items = $this->getItems($core_category);

                $items[] = $menuItem;
            }
        }

        return $items;
    }

    public function testGetParent()
    {
        $data = $this->getParent();
        vd($data);
    }

    public function getParent($array, $parent_id)
    {
        $parent = ShopCategory::findOne($parent_id);
        if ($parent === null) return null;
        $item = new MenuItem();
        $item->name = $parent->name;
        $item->id = $parent->id;
        $item->items = $array;
        if ($parent->parent_id === null) {
            return $item;
        } else {
            return $this->getParent([$item], $parent->parent_id);
        }
    }

    public function testGetMenuItem()
    {
        $data = $this->getMenuItem();
        vd($data);
    }

    public function getMenuItem($id, $with_brothers = true)
    {
        $category = ShopCategory::findOne($id);
        if ($category === null) {
            return false;
        }
        if (!empty($category->parent_id))
            $brothers = ShopCategory::find()->where([
                'parent_id' => $category->parent_id
            ])
                ->all();
        else $brothers = ShopCategory::find()->where([
            'parent_id' => $id
        ])
            ->all();

        $brother_items = [];
        if (\Dash\count($brothers) !== null) {
            foreach ($brothers as $brother) {
                $item = new MenuItem();
                $item->name = $brother->name;
                $item->id = $brother->id;
                if ($brother->id === $id)
                    $item->class = 'text-success';

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

    public function testGetBrands()
    {
        $data = $this->getBrands();
        vd($data);
    }

    public function getBrands($selectedId = null)
    {

        if (!$selectedId)
            $selectedId = $this->httpGet('depend');



        if (!empty($selectedId)) {

            /** @var ShopCategory $category */
            $category = ShopCategory::find()
                ->where([
                    'id' => $selectedId
                ])
                ->one();

            if ($category === null) {
                return null;
            }

            $data = $category->getShopBrandsFromShopBrandIdsMulti();
            $brands = [];


          //  vd($data);
            
            if (empty($data))
                return null;

            foreach ($data as $item) {
                $brands[$item->id] = $item->name;
            }

            if ($brands === null)
                return [];


//vd($brands);
            return $brands;

        }

    }


    public function testGetLeafs()
    {
        $data = $this->getLeafs();
        vd($data);
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

    public function testParentAndLeaf()
    {
        $data = $this->getParentAndLeaf();
        vd($data);
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

    #region propertiesByCategory      

    /**
     *
     * Function  propertiesByCategory
     * @param null $categoryId
     * @return  array|null
     * @throws \Exception
     *
     * @author AzimjonToirov
     * @license OtabekNosirov
     * @license SalohiddinJaloliddinov
     */
    public function propertiesByCategory($categoryId = null)
    {
        $core_options = collect(ShopOption::find()->asArray()->all());
        $core_option_branches = collect(ShopOptionBranch::find()->asArray()->all());


        $categoryProperties = Az::$app->market->product->propertsByCategory($categoryId);

        $categoryPropertiesByBranch = $categoryProperties->groupBy('shop_option_branch_id');

        $properties = [];

        foreach ($categoryPropertiesByBranch as $branch_id => $branch) {
            foreach ($branch as $property) {
                $options [] = $core_options->where('shop_option_type_id', $property["id"]);
                $optionBranches[] = $core_option_branches->where('id', $branch_id)->first()['name'] ?? 'not found';
                $properties['branches'] = $optionBranches;
            }
        }

        $shop_category = ShopCategory::findOne($categoryId);

        if ($shop_category === null)
            return null;

        if ($shop_category->shop_brand_ids === [])
            return null;


        $allBrands = ShopBrand::findAll($shop_category->shop_brand_ids);
        $baseUrl = $this->urlGetBase();

        foreach ($allBrands as $brand) {
            $images = [];
            foreach ($brand->image as $key => $img) {
                $images[] = $baseUrl . '/uploaz/' . App . '/ShopBrand/image/' . $img;
            }
            $brand->image = $images;
        }

        $properties ['brands'] = $allBrands;
        $properties['properties'] = $options;

        return $properties;
    }

    #endregion


}




