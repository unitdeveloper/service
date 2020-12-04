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
use zetsoft\models\user\UserCompany;
use zetsoft\models\shop\ShopElement;
use zetsoft\models\menu\Menu;
use zetsoft\models\menu\MenuImage;
use zetsoft\models\core\CoreMenuNew;
use zetsoft\models\shop\ShopOption;
use zetsoft\models\shop\ShopOptionType;
use zetsoft\models\shop\ShopOptionBranch;
use zetsoft\models\shop\ShopProduct;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\helpers\ZUrl;
use zetsoft\system\kernels\ZFrame;
use zetsoft\dbitem\wdg\MenuItems;
/**
 *
 * @property string $role
 */
class CategoryUmid extends ZFrame
{
    #region vars
    public $core_categories;
    public $brands;
    
    #region init
    public function init()
    {
        parent::init();
        $this->core_categories = collect(ShopCategory::find()->all());
        $this->brands = collect(ShopBrand::find()->all());
    }
    #endregion

    /**
     * @param ShopCategory $category
     * @return mixed
     */

     public function test()
     {
         vdd($this->getItems(1));

//        $this->generateDBMenuItems();

     }

    public function normalizeExtra($category)
    {
        $brand_ids = $category->shop_brand_ids;
        $brands = [];
        if (is_array($brand_ids))
        {

            /*$brand_ids = array_map(function($id){
                return (int)$id;
            }, $brand_ids);*/

            $brand_ids = $brand_ids->map(function($id){
                return (int)$id;
            });



            $brands = $this->brands->whereIn('id', $brand_ids);
        }
        
        $sample = [];
        if (!empty($brands))
            foreach ($brands as $brand) {
                $menuItem = new MenuItem();
                $menuItem->url = $brand->url;
                $menuItem->image = '/uploaz/' . $this->bootEnv('appTitle') . "/ShopBrand/image/$brand->id/" . ZArrayHelper::getValue($brand->image, 0);
                $menuItem->target = $brand->target;
                $menuItem->tooltip = $brand->name;
                $menuItem->location = $brand->location;
                $sample[] = $menuItem;
            }
        return $sample;
    }

    public function generateDBMenuItems()
    {


        $db_menu_items = [];
        
        $parent_categories = $this->core_categories
            ->where('parent_id', null)
            ->sortBy('sort');

        foreach ($parent_categories as $parent_category) {
            $menuItem = new MenuItem();
            $menuItem->title = $parent_category->name;
            $menuItem->icon = $parent_category->icon;
            $menuItem->url = ZUrl::to([
                '/shop/user/filter-common/main',
                'id' => $parent_category->id,
            ]);
            $menuItem->target = '_self';
            $menuItem->items = $this->getItems($parent_category);
            $menuItem->extra = $this->normalizeExtra($parent_category);

            $db_menu_items[] = $menuItem;
        }
        return $db_menu_items;
    }


    /**
     * @param ShopCategory $parent_category
     */
    public function getItems($parent_category)
    {
        $items = [];

            /*$shop_categories = CoreCategory::find()->where([
                'id' => $parent_category->child
            ])->orderBy(['sort' => SORT_ASC])->all();*/

            $core_categories = $this->core_categories->where('parent_id', $parent_category->id)->sortBy('sort');

        if (!empty($core_categories)) {
            foreach ($core_categories as $core_category) {
                $menuItem = new MenuItem();
                $menuItem->title = $core_category->name;
                $menuItem->icon = $parent_category->icon;
                $menuItem->url = ZUrl::to([
                    '/shop/user/filter-common/main',
                    'id' => $core_category->id,
                ]);
                $menuItem->extra = $this->normalizeExtra($core_category);
                $menuItem->items = $this->getItems($core_category);

                $items[] = $menuItem;
            }
        }

        return $items;
    }



    public function getParent($array, $parent_id)
    {
         //$parent = CoreCategory::findOne($parent_id);
         $parent = $this->core_categories
            ->where('parent_id', $parent_id)
                ->first();
         $item = new MenuItem();
         $item->name = $parent->name;
         $item->id = $parent->id;
         $item->items = $array;
         if ($parent->parent_id == null)
            return $item;
         else
         {
             return $this->getParent([$item], $parent->parent_id);
         }
    }

    public function getMenuItem($id, $with_brothers = true){

        //$category = CoreCategory::findOne($id);
        $category = $this->core_categories
            ->where('id', $id)
                ->first();
        /*$brothers = CoreCategory::find()->where([
            'parent_id' => $category->parent_id
        ])->all();*/
        $brothers = $this->core_categories
            ->where('parent_id', $category->parent_id)
                 ->all();


        $brother_items = [];
        if(\Dash\count($brothers) != null){
            foreach($brothers as  $brother){
               $item = new MenuItem();
               $item->name = $brother->name;
               $item->id = $brother->id;
                if($brother->id == $id)
                    $item->class = "active";

               $brother_items[] = $item;
            }
        }


        if ($with_brothers == false)
        {
             $brother_items = [];
             $self_item = new MenuItem();
             $self_item->name = $category->name;
             $self_item->id = $category->id;
             $brother_items[] = $self_item;
        }
        

        //$a = CoreCategory::findOne($category->parent_id);
        $a = $this->core_categories
            ->where('parent_id', $category->parent_id)
                ->first();
        if($a == null)
            return  $brother_items;
        $bobo = $this->getParent($brother_items, $category->parent_id);
        return $bobo;
    }

    public function getBrands($selectedId){
        if(!empty($selectedId)){
            //$category = CoreCategory::find()->where(['id'=>$selectedId])->one();
            $category = $this->core_categories
                ->where('id', $selectedId)
                     ->first();
            if($category === null){
                return null;
            }
            $data = $category->getCoreBrandsFromCoreBrandIds();
            $brands = [];
            if(empty($data))
                return null;
            foreach ($data as $item){
                $brands[$item->id] = $item->name;
            }
            if($brands === null)
                return [];

            return $brands;



        }

    }





}




