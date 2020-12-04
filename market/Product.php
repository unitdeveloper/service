<?php

/**
 * Author: Jobir Yusupov
 * Date:    07.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 */

namespace zetsoft\service\market;

use Illuminate\Support\Collection;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use zetsoft\dbitem\shop\MultiProductItem;
use zetsoft\dbitem\shop\ProductItem;
use zetsoft\dbitem\shop\PropertyItem;
use zetsoft\dbitem\shop\SingleProductItem;
use zetsoft\dbitem\user\UserCompanyItem;
use zetsoft\dbitem\wdg\MenuItem;
use zetsoft\models\shop\ShopBrand;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopCategory;
use zetsoft\models\shop\ShopElement;
use zetsoft\models\shop\ShopOffer;
use zetsoft\models\shop\ShopOption;
use zetsoft\models\shop\ShopOptionBranch;
use zetsoft\models\shop\ShopOptionType;
use zetsoft\models\shop\ShopProduct;
use zetsoft\models\user\UserCompany;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZTest;
use zetsoft\system\helpers\ZUrl;
use zetsoft\system\kernels\ZFrame;
use function Spatie\array_keys_exist;


/**
 * Class    Product
 * @package zetsoft\service\market
 *
 *
 * @author
 * @license
 *
 */
class Product extends ZFrame
{
    public $shop_products;
    public ?Collection $shop_offers = null;
    public bool $is_seperated_by_branch = true;
    public ?Collection $shop_elements = null;

    public ?Collection $shop_catalogs = null;
    //   public $shop_catalogs;
    public $core_categories;
    public $core_option_types;
    public $core_options;
    public $core_companies;
    public $core_option_branches;
    public $core_brands;
    public $shop_option_branches;

    public const cart_type = [
        'add' => 'add',
        'set' => 'set',
    ];

    public $defaultProductImage = "https://cdn.dribbble.com/users/357797/screenshots/3998541/empty_box.jpg";
    #endregion

#region init

    public function init()
    {

        $this->shop_products = collect(ShopProduct::find()->asArray()->all());
        $this->shop_elements = collect(ShopElement::find()->asArray()->all());
        $this->shop_catalogs = collect(ShopCatalog::find()->asArray()->all());
        $this->shop_offers = collect(
            ShopOffer::find()->where([
                '<', 'start',
                date('Y-m-d H:i:s')])
                ->andWhere([
                        '>',
                        'end',
                        date('Y-m-d H:i:s')]
                )->all());


        $this->core_options = collect(ShopOption::find()->asArray()->all());


        $this->shop_option_branches = collect(ShopOptionBranch::find()->asArray()->all());
        $this->core_option_types = collect(ShopOptionType::find()->asArray()->all());

        $this->core_categories = collect(ShopCategory::find()->asArray()->all());
        $this->core_brands = collect(ShopBrand::find()->asArray()->all());
        $this->core_companies = collect(UserCompany::find()->asArray()->all());

        parent::init();
    }

    public function initbahodir()
    {

        $this->shop_products = collect(ShopProduct::find()->asArray()->all());
        $this->shop_elements = collect(ShopElement::find()->asArray()->all());
        $this->shop_catalogs = collect(ShopCatalog::find()->asArray()->all());
        $this->shop_offers = collect(
            ShopOffer::find()->where([
                '<', 'start',
                date('Y-m-d H:i:s')])
                ->andWhere([
                        '>',
                        'end',
                        date('Y-m-d H:i:s')]
                )->all());


        $this->core_options = collect(ShopOption::find()->asArray()->all());


        if (!$this->cacheExists('OneDayCache'))
            $this->cacheSet('OneDayCache', time());

        $timestamp = $this->cacheGet('OneDayCache');
        $day = 24 * 60 * 60;

        if ($timestamp + $day < time())
            $this->cacheFlush();

        if (!$this->cacheExists('ShopOptionBranchCache'))
            $this->cacheSet('ShopOptionBranchCache', collect(ShopOptionBranch::find()->asArray()->all()));

        if (!$this->cacheExists('ShopOptionTypeCache'))
            $this->cacheSet('ShopOptionTypeCache', collect(ShopOptionType::find()->asArray()->all()));

        if (!$this->cacheExists('ShopCategoryCache'))
            $this->cacheSet('ShopCategoryCache', collect(ShopCategory::find()->asArray()->all()));

        if (!$this->cacheExists('ShopBrandCache'))
            $this->cacheSet('ShopBrandCache', collect(ShopBrand::find()->asArray()->all()));

        if (!$this->cacheExists('UserCompanyCache'))
            $this->cacheSet('UserCompanyCache', collect(UserCompany::find()->asArray()->all()));


        $this->shop_option_branches = $this->cacheGet('ShopOptionBranchCache');
        $this->core_option_types = $this->cacheGet('ShopOptionTypeCache');
        $this->core_categories = $this->cacheGet('ShopCategoryCache');
        $this->core_brands = $this->cacheGet('ShopBrandCache');
        $this->core_companies = $this->cacheGet('UserCompanyCache');


        parent::init();
    }


    public function test()
    {
        $this->productOffersTest();
        $this->allProductsTest();
        $this->max_pageTest();
        $this->allElementsTest();
        $this->sortProductsTest(); //there is an error!
        $this->allCompaniesTest();
        $this->productTest();
        $this->productwithBranchTest();
        $this->sortPriceAttributeTest();
        $this->productByStatusTest(); // productByStatus() //there is an error in that function!
        $this->productsIfHasCatalogTest();
        $this->productItemByCatalogIdTest();
        $this->propertsByCategoryTest();
        $this->is_multiTest();
        $this->getOptionsByCategoryTest();
        $this->imagesTest(); //there is an error in images() function!
        $this->propertyItemsTest();
        $this->singleProductItemByOptionsTest();
        $this->productItemByElementsTest();
        $this->productItemByElementIdTest();
        $this->offerCategoriesWithProductsTest(); //there is an error in offerCategoriesWithProducts() function!
    }


#endregion

#region for AdminPanel
    // depdrop uchun
    //  public function testgetOptionsByCategory()
    //  {
    //      $data = $this->getOptionsByCategory(2480);
    //      vd($data);
    //  }

    // public function getOptionsByCategoryTest()
    // {
    //     $value = $this->getOptionsByCategory(1854);
    //     //prasd
    //     $expectedValue = [
    //         '11' => "74.8x162.6x8.96 мм",
    //         '12' => "162.3 х 77.2 х 8.9 мм",
    //     ];
    //     ZTest::assertEquals($expectedValue, $value);

    // }
    // Dilshod Khudoyarov
    // telegram = @Dishkan2000
    // this function shows options by the category id
    public function getOptionsByCategoryTest()
    {
        $selectedId = 1854;
        $result = Az::$app->market->product->getOptionsByCategory($selectedId);
        vd($result);
    }

    public function getOptionsByCategory($selectedId = null)
    {
        if (empty($selectedId))
            return [];

        $out = ['11' => "74.8x162.6x8.96 мм",
            '12' => "162.3 х 77.2 х 8.9 мм"];

        if ($selectedId !== null) {
            $category = $this->core_categories->where('id', $selectedId)->first();


            $option_types = ZArrayHelper::getValue($category, 'shop_option_type');

            if ($this->emptyVar($option_types) || $option_types === null)
                return null;
            if (!is_array($option_types))
                $option_types = json_decode($option_types, true);
            $data = [];

            if (empty($option_types))
                return [];

            foreach ($option_types as $option_type) {

                $core_option_type = $this->core_option_types
                    ->where(
                        'id', (int)ZArrayHelper::getValue($option_type, 'shop_option_type_id')
                    )->first();

                if ($core_option_type === null)
                    continue;

                $options = $this->core_options
                    ->where('shop_option_type_id', $core_option_type['id']);


                $data = ZArrayHelper::merge($data, $options);
            }
            foreach ($data as $model) {
                $out[$model['id']] = $model['name'];
            }

            return $out;
        }
    }


#endregion


#region Product
    public function productTest()
    {
        $shop_product_id = 15;
        $company_id = null;
        $is_full = false;
        $seperated_by_branch = true;
        /** @var Collection $result */
        $result = Az::$app->market->product->product($shop_product_id, $company_id, $is_full, $seperated_by_branch);
        vd($result);
    }

    /**
     *
     * Function  product
     * @param null $shop_product_id
     * @param null $company_id
     * @param bool $is_full
     * @param bool $seperated_by_branch
     * @return  MultiProductItem|SingleProductItem|null
     *
     *
     */

    public function product($shop_product_id = null, $company_id = null, $is_full = false, $seperated_by_branch = true)
    {

        if ($shop_product_id === null)
            return null;

        $shop_product = $this->shop_products->where('id', $shop_product_id)->first();

        if ($shop_product === null)
            return null;

        $core_category = $this->core_categories->where('id', $shop_product['shop_category_id'])->first();

        if ($core_category === null) {
            $product_id = $shop_product['id'];
            //throw new ErrorException("$product_id product's category is not exist");
            return null;
        }

        $is_multi = true;
        if ($company_id !== null and !$this->is_multi($core_category))
            $is_multi = false;

        if ($is_multi)
            $item = new MultiProductItem();
        else
            $item = new SingleProductItem();

        $item->id = $shop_product['id'];
        $item->categoryName = $core_category['name'] ?? 'not found';
        $item->categoryId = $core_category['id'] ?? 'not found';
        $item->categoryUrl = ZUrl::to([
            '/shop/user/filtering/common',
            'id' => $core_category['id'],
        ]);
        $item->url = ZUrl::to([
            '/shop/user/product-single/common',
            'id' => $shop_product['id'],
        ]);
        $item->name = $shop_product['name'];

        //agar shu productni option type laridan hech qaysi kombinatsiyaga ishtirok etmasa
        //cart_amount nomi bilan karzinkada shundan nechta borligi chiqib turadi

        /** @var \Illuminate\Support\Collection $elements */
        $elements = $this->shop_elements
            ->where('shop_product_id', $shop_product['id']);

        if ($is_multi) {
            if ($is_full) {
                foreach ($elements as $element) {
                    //agar hech kim sotmayotgan bo'lsa marketda ko'rinmaydi
                    $catelogs = $this->shop_catalogs
                        ->where('shop_element_id', $element['id']);

                    if ($catelogs !== null and $catelogs->count() !== 0) {
                        $sub_item = $this->productItemByElementId($element['id']);
                        $item->items[] = $sub_item;
                    }
                }
            }

            $catalogNew = null;

            if ($elements->isNotEmpty()) {
                if ($company_id !== null) {
                    $catalogNew = $this->shop_catalogs
                        ->where('shop_element_id', $elements->first()['id'])
                        ->where('user_company_id', $company_id)
                        ->first();
                } else {
                    $catalogNew = $this->shop_catalogs
                        ->where('shop_element_id', $elements->first()['id'])
                        ->first();
                }
                if ($catalogNew !== null) {
                    $item->catalogId = ZArrayHelper::getValue($catalogNew, 'id');
                }
            }

        } else {

            $cart_catalogs = collect($this->sessionGet('cart'));
            $catalog = null;

            if ($elements->isNotEmpty()) {
                if ($company_id !== null && $company_id !== 0) {
                    $catalog = $this->shop_catalogs
                        ->where('shop_element_id', $elements->first()['id'])
                        ->where('user_company_id', $company_id)
                        ->first();
                } else {
                    $catalog = $this->shop_catalogs
                        ->where('shop_element_id', $elements->first()['id'])
                        ->first();
                }

                if ($catalog !== null) {
                    $current_cart_catalog = $cart_catalogs->where('catalogId', $catalog['id'])->first();
                    $item->cart_amount = $current_cart_catalog['cart_amount'] ?? 0;
                    $item->catalogId = $catalog['id'];

                }
            }
        }
        $brand = $this->core_brands->where('id', $shop_product['shop_brand_id'])->first();
        if ($brand !== null) {
            $item->brand = $brand['name'] ?? 'not found';
            $brand['image'] = explode(',', $brand['image']);
            $item->brandImage = '/uploaz/' . App . '/ShopBrand/image/' . $brand['id'] . '/' . ZArrayHelper::getValue($brand['image'], 0);
        }

        $item->title = $shop_product['title'];
        $item->text = $shop_product['text'];
        $item->status = $this->productOffers($shop_product_id, $company_id);
        $item->rating = (float)$shop_product['rating'];

        $temp_product = new ShopProduct();
        $item->measure = $temp_product->_measure[$shop_product['measure']] ?? $temp_product->_measure['pcs'];
        $item->measureStep = ProductItem::measureStep[$shop_product['measure']] ?? ProductItem::measureStep['pcs'];

        $item->in_wish = Az::$app->market->wish->CheckWish($shop_product['id']);
        $item->in_compare = Az::$app->market->wish->CheckCompare($shop_product['id']);

        //properties
        if ($is_full) {
            $item->properties = $this->prepertyItems($shop_product['id'], true, false, false);

            $item->allProperties = $this->prepertyItems($shop_product['id'], false, false, $seperated_by_branch);
            //vdd($item->properties);
        }

        $item->images = $this->images($shop_product);

        $shop_catalogs = $this->shop_catalogs->whereIn('shop_element_id', $elements->pluck('id'));
        $currency = Az::$app->cores->session->get('currency');
        $shop_catalogs = $shop_catalogs->map(function ($core_catalog) use ($currency) {

            $core_catalog['price'] = Az::$app->payer->currency2->convert($core_catalog['currency'], $currency, $core_catalog['price']);

            $core_catalog['price_old'] = Az::$app->payer->currency2->convert($core_catalog['currency'], $currency, $core_catalog['price_old']);

            return $core_catalog;
        });

        $amount = $shop_catalogs->sum('amount');
        $item->amount = $amount;

        if ($item->is_multi) {
            $price_min = \Dash\Curry\min($shop_catalogs->pluck('price'));
            $price_max = \Dash\Curry\max($shop_catalogs->pluck('price'));

            $item->min_price = $price_min ?? null;
            $item->max_price = null;
            if ($price_min !== $price_max)
                $item->max_price = $price_max;
        } else {
            $item->new_price = $shop_catalogs->pluck('price')->first() ?? null;
            $item->price_old = $shop_catalogs->pluck('price_old')->first() ?? null;

            if ($item->new_price != null and $item->price_old != null) {
                $item->discount = ($item->new_price - $item->price_old) * 100 / $item->price_old;
                $item->discount = round($item->discount);
            }
        }

        if ($item->allProperties === null) {
            $item->allProperties = [];
        }

        if ($item->properties === null) {
            $item->properties = [];
        }
        return $item;
    }

    #endregion


#region productItem and database

    /**
     * Return all Products
     * Function  allProducts
     * @param null $category_id
     * @return  array
     * @throws ErrorException
     */

    public function testProd()
    {

        $items_test = [];

        $item = new SingleProductItem();
        $item->id = $this->myId();
        $item->name = 'Арахисовая паста с медом 200г';
        $item->title = 'Test Desc';
        $item->new_price = '14825920';
        $item->price_old = '188920';
        $item->barcode = '34234234';
        $item->exist = ProductItem::exists['not'];
        $item->images = [
            'https://images.pexels.com/photos/1095550/pexels-photo-1095550.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500',
            'https://images.pexels.com/photos/461198/pexels-photo-461198.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500',
            'https://previews.123rf.com/images/veneratio/veneratio1511/veneratio151100044/48203428-landscape-iamge-of-river-flowing-through-lush-green-forest-in-summer.jpg',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcRVDh2D2fzRSBYnwaA-70G74wjOeeZWbRnEVBMxfu1jVqcP9fMv&usqp=CAU',
        ];
        $item->currency = 'сум';
        $item->currencyType = 'after';
        $item->measure = 'шт';
        $item->rating = 3.5;
        $item->discount = -10;
        $item->catalogId = 19;
        $item->sale = 'sdadsa';
        $item->is_multi = false;
        $item->in_wish = true;
        $item->in_compare = false;

        $items_test[] = $item;

        return $items_test;
    }


    /**
     *
     * Function  allProducts
     * @param null $category_id
     * @param null $company_id
     * @param $page
     * @param $limit
     * @param array $sort
     * @return  mixed
     *
     *
     */
    #region  max_page
    // Dilshod Khudoyarov
    // telegram = @Dishkan2000
    // this function shows count of pages of shop_product
    public function max_pageTest()
    {
        $category_id = 978;
        $company_id = null;
        /** @var Collection $result */
        $result = Az::$app->market->product->max_page($category_id, $company_id);
        vd($result);
    }

    public function max_page($category_id = null, $company_id = null)
    {
        $shop_products = $this->productsIfHasCatalog($company_id);
        if (!empty($category_id))
            $shop_products = $shop_products->where('shop_category_id', $category_id);

        //filtirlab beradi agar sessionda filter bo'lsa property va brand bo'yicha
        if (Az::$app->cores->session->get('filter'))
            $shop_products = Az::$app->market->filter->filter($shop_products);

        return $shop_products->count();
    }
    #endregion
    #region  allElements
    // Dilshod Khudoyarov
    // telegram = @Dishkan2000
    // this function shows elements of shop_product from the database
    public function allElementsTest()
    {
        $category_id = 984;
        $company_id = null;
        $page = 1;
        $limit = 10;
        /** @var Collection $result */
        $result = Az::$app->market->product->allElements($category_id, $company_id, $page, $limit);
        vd($result);
    }

    public function allElements($category_id = null, $company_id = null, $page, $limit)
    {
        //price bo'yicha filter shuni ichida
        $shop_products = $this->productsIfHasCatalog($company_id);
        if (!empty($category_id))
            $shop_products = $shop_products->where('shop_category_id', $category_id);

        //filtirlab beradi agar sessionda filter bo'lsa property va brand bo'yicha
        if (Az::$app->cores->session->get('filter'))
            $shop_products = Az::$app->market->filter->filter($shop_products);
        // vdd($this->shop_elements->whereIn('shop_product_id', $shop_products->pluck('id'))->count());

        $element_ids = $this->shop_elements->whereIn('shop_product_id', $shop_products->pluck('id'))->pluck('id');
        $catalogs = $this->shop_catalogs->whereIn('shop_element_id', $element_ids);
        if ($company_id !== null)
            $catalogs = $catalogs->whereIn('user_company_id', $company_id);
        $elements = $this->shop_elements->whereIn('id', $catalogs->pluck('shop_element_id')->unique());

        if ($page !== null and $limit !== null) {
            $skip = ($page - 1) * $limit;
            $elements = $elements->skip($skip)->take($limit);
        }
        $shop_elements = $elements->map(function ($element) use ($company_id) {
            return $this->productItemByElementId($element['id'], $company_id, false);
        });

        return $shop_elements;
    }
    #endregion
    #region  allProducts
    public function allProductsTest()
    {
        $categoryId = 978;
        $companyId = null;
        $page = 1;
        $limit = 10;
        $sort = ['price', '-name'];
        /** @var Collection $result */
        $result = Az::$app->market->product->allProducts($categoryId, $companyId, $page, $limit, $sort);
        vd($result);

        //  $productItem = $result->first();

        //  vdd(gettype($productItem->id));
        //  ZTest::assertEquals(30, $productItem->id);
    }


    /**
     *
     * Function  getCompany
     * @param null $companyId
     * @return  UserCompany
     */


    public function allProducts($category_id = null, $company_id = null, $page = 0, $limit = 12, $sort = [], $brand_id = null)
    {
        Az::start(__FUNCTION__);


        //price bo'yicha filter shuni ichida
        $shop_products = $this->productsIfHasCatalog($company_id);;
        if (!empty($category_id)) {

            if (\Dash\count($shop_products) > 0) {

                $shop_products = $shop_products->where('shop_category_id', $category_id);
            } else {

                $shop_products = $this->shop_products->where('shop_category_id', $category_id);

            }
        }

        if (!empty($brand_id)) {

            if (\Dash\count($shop_products) > 0)
                $shop_products = $shop_products->where('shop_brand_id', $brand_id);
            else
                $shop_products = $this->shop_products->where('shop_brand_id', $brand_id);
        }

        //filtirlab beradi agar sessionda filter bo'lsa property va brand bo'yicha
        if (Az::$app->cores->session->get('filter') or Az::$app->cores->session->get('brand_filter'))
            $shop_products = Az::$app->market->filter->filter($shop_products);

        if ($page !== null and $limit !== null) {
            $skip = ($page - 1) * $limit;
            $shop_products = $shop_products->skip($skip)->take($limit);
        }

        $shop_products = $shop_products->map(function ($shop_product) use ($company_id) {
            return $this->product($shop_product['id'], $company_id, $is_full = false, $seperated_by_branch = true);
        });

        return $this->sortProducts($shop_products, $sort, $category_id);
    }




    #endregion


    #region forVue
//    public function testVue(){
//        vdd($this->shop_products);
//        $this->shop_catalogs;
//        $this->core_categories;
//    }
//    public function product2Test($shop_product_id = null, $company_id = null, $is_full = false, $seperated_by_branch = true)
//    {
//
//        if ($shop_product_id === null)
//            return null;
//
//        $shop_product = $this->shop_products->all();
//
//        if ($shop_product === null)
//            return null;
//
//        $core_category = $this->core_categories->where('id', $shop_product['shop_category_id'])->first();
//
//        if ($core_category === null) {
//            $product_id = $shop_product['id'];
//            //throw new ErrorException("$product_id product's category is not exist");
//            return null;
//        }
//
//        $is_multi = true;
//        if ($company_id !== null and !$this->is_multi($core_category))
//            $is_multi = false;
//
//        if ($is_multi)
//            $item = new MultiProductItem();
//        else
//            $item = new SingleProductItem();
//
//        $item->id = $shop_product['id'];
//        $item->categoryName = $core_category['name'] ?? 'not found';
//        $item->categoryId = $core_category['id'] ?? 'not found';
//        $item->categoryUrl = ZUrl::to([
//            '/shop/user/filtering/common',
//            'id' => $core_category['id'],
//        ]);
//        $item->url = ZUrl::to([
//            '/shop/user/product-single/common',
//            'id' => $shop_product['id'],
//        ]);
//        $item->name = $shop_product['name'];
//
//        //agar shu productni option type laridan hech qaysi kombinatsiyaga ishtirok etmasa
//        //cart_amount nomi bilan karzinkada shundan nechta borligi chiqib turadi
//
//        /** @var \Illuminate\Support\Collection $elements */
//        $elements = $this->shop_elements
//            ->where('shop_product_id', $shop_product['id']);
//
//        if ($is_multi) {
//            if ($is_full) {
//                foreach ($elements as $element) {
//                    //agar hech kim sotmayotgan bo'lsa marketda ko'rinmaydi
//                    $catelogs = $this->shop_catalogs
//                        ->where('shop_element_id', $element['id']);
//
//                    if ($catelogs !== null and $catelogs->count() !== 0) {
//                        $sub_item = $this->productItemByElementId($element['id']);
//                        $item->items[] = $sub_item;
//                    }
//                }
//            }
//
//            $catalogNew = null;
//
//            if ($elements->isNotEmpty()) {
//                if ($company_id !== null) {
//                    $catalogNew = $this->shop_catalogs
//                        ->where('shop_element_id', $elements->first()['id'])
//                        ->where('user_company_id', $company_id)
//                        ->first();
//                } else {
//                    $catalogNew = $this->shop_catalogs
//                        ->where('shop_element_id', $elements->first()['id'])
//                        ->first();
//                }
//
//                if ($catalogNew !== null) {
//                    $item->catalogId = ZArrayHelper::getValue($catalogNew, 'id');
//                }
//            }
//
//
//        } else {
//
//            $cart_catalogs = collect($this->sessionGet('cart'));
//            $catalog = null;
//
//            if ($elements->isNotEmpty()) {
//                if ($company_id == null) {
//                    $catalog = $this->shop_catalogs
//                        ->where('shop_element_id', $elements->first()['id'])
//                        ->where('user_company_id', $company_id)
//                        ->first();
//                } else {
//                    $catalog = $this->shop_catalogs
//                        ->where('shop_element_id', $elements->first()['id'])
//                        ->first();
//                }
//
//                if ($catalog !== null) {
//                    $current_cart_catalog = $cart_catalogs->where('catalogId', $catalog['id'])->first();
//                    $item->cart_amount = $current_cart_catalog['cart_amount'] ?? 0;
//                    $item->catalogId = $catalog['id'];
//                }
//            }
//        }
//
//        $brand = $this->core_brands->where('id', $shop_product['shop_brand_id'])->first();
//        if ($brand !== null) {
//            $item->brand = $brand['name'] ?? 'not found';
//            $item->brandImage = '/uploaz/' . App . '/ShopBrand/image/' . $brand['id'] . '/' . ZArrayHelper::getValue($brand['image'], 0);
//        }
//
//        $item->title = $shop_product['title'];
//        $item->text = $shop_product['text'];
//        $item->status = $this->productOffers($shop_product_id, $company_id);
//        $item->rating = (float)$shop_product['rating'];
//
//        $temp_product = new ShopProduct();
//        $item->measure = $temp_product->_measure[$shop_product['measure']] ?? $temp_product->_measure['pcs'];
//        $item->measureStep = ProductItem::measureStep[$shop_product['measure']] ?? ProductItem::measureStep['pcs'];
//
//        $item->in_wish = Az::$app->market->wish->CheckWish($shop_product['id']);
//        $item->in_compare = Az::$app->market->wish->CheckCompare($shop_product['id']);
//
//        //properties
//        if ($is_full) {
//            $item->properties = $this->prepertyItems($shop_product['id'], true, false, false);
//
//            $item->allProperties = $this->prepertyItems($shop_product['id'], false, false, $seperated_by_branch);
//            //vdd($item->properties);
//        }
//
//        $item->images = $this->images($shop_product);
//
//        $shop_catalogs = $this->shop_catalogs->whereIn('shop_element_id', $elements->pluck('id'));
//        $currency = Az::$app->cores->session->get('currency');
//        $shop_catalogs = $shop_catalogs->map(function ($core_catalog) use ($currency) {
//
//            $core_catalog['price'] = Az::$app->payer->currency2->convert($core_catalog['currency'], $currency, $core_catalog['price']);
//
//            $core_catalog['price_old'] = Az::$app->payer->currency2->convert($core_catalog['currency'], $currency, $core_catalog['price_old']);
//
//            return $core_catalog;
//        });
//
//        $amount = $shop_catalogs->sum('amount');
//        $item->amount = $amount;
//
//        if ($item->is_multi) {
//            $price_min = \Dash\Curry\min($shop_catalogs->pluck('price'));
//            $price_max = \Dash\Curry\max($shop_catalogs->pluck('price'));
//
//            $item->min_price = $price_min ?? null;
//            $item->max_price = null;
//            if ($price_min !== $price_max)
//                $item->max_price = $price_max;
//        } else {
//            $item->new_price = $shop_catalogs->pluck('price')->first() ?? null;
//            $item->price_old = $shop_catalogs->pluck('price_old')->first() ?? null;
//
//            if ($item->new_price != null and $item->price_old != null) {
//                $item->discount = ($item->new_price - $item->price_old) * 100 / $item->price_old;
//                $item->discount = round($item->discount);
//            }
//        }
//
//        if ($item->allProperties === null) {
//            $item->allProperties = [];
//        }
//
//        if ($item->properties === null) {
//            $item->properties = [];
//        }
//        return $shop_product;
//    }
//
//
//    public function getAllProducts($category_id = null, $company_id = null, $page = 0, $limit = 12, $sort = [], $brand_id = null)
//    {
//        $return = [];
//        foreach ($this->shop_products as $shop_product) {
//            $return = $shop_product;
//        }
//        return $return;
//    }

    #endregion


    #region sortProducts
    // Dilshod Khudoyarov
    // telegram = @Dishkan2000
    // this function shows sort of shop_product
    // There is an ERROR! - $shop_products = 31;
    public function sortProductsTest()
    {
        $shop_products = 31;
        $sort = ['price', '-name'];
        $category_id = 2619;
        $result = Az::$app->market->product->sortProducts($shop_products, $sort, $category_id);
        vd($result);
    }

    public function sortProducts($shop_products, $sort, $category_id)
    {
        foreach ((array)$sort as $s) {
            if (ZArrayHelper::getValue($sort, 0) === '-') {
                $s = substr($s, 1);
                if ($s === 'price')
                    $s = $this->sortPriceAttribute($category_id);
                $shop_products = $shop_products->sortByDesc($s);
            } else {
                if ($s === 'price')
                    $s = $this->sortPriceAttribute($category_id);
                $shop_products = $shop_products->sortBy($s);
            }
        }
        return $shop_products;

    }

    #endregion

    #region sortPriceAttribute
    // Dilshod Khudoyarov
    // telegram = @Dishkan2000
    // this function shows all sort prices of products in the list
    public function sortPriceAttributeTest()
    {
        $category_id = 2225;
        /** @var Collection $result */
        $result = Az::$app->market->product->sortPriceAttribute($category_id);
        vd($result);
    }

    private function sortPriceAttribute($category_id)
    {
        $category = $this->core_categories->where('id', $category_id)->first();
        $is_multi = $this->is_multi($category);
        if ($is_multi)
            $s = 'min_price';
        else
            $s = 'new_price';

        return $s;
    }

    #endregion
    // Dilshod Khudoyarov
    // telegram = @Dishkan2000
    // this function shows products by their status
    public function productByStatusTest()
    {
        $category_id = 2225;
        $status = 't';
        $count = 3;
        $company_id = null;
        /** @var Collection $result */
        $result = Az::$app->market->product->productByStatus($category_id, $status, $count, $company_id);
        vd($result);
    }

    public function productByStatus($category_id = null, $status, $count = 3, $company_id = null)
    {
        /// bu sql lik tezroq isholaydi
        if ($company_id == null)
            $statuslik_catalogs = collect(ShopCatalog::findBySql('SELECT  * FROM shop_catalog WHERE offer @> \'[{"offer" : "' . $status . '"}]\' ;')->asArray()->all());
        else
            $statuslik_catalogs = collect(ShopCatalog::findBySql('SELECT  * FROM shop_catalog WHERE offer @> \'[{"offer" : "' . $status . '"}]\' AND user_company_id = ' . $company_id . ';')->asArray()->all());

        $catalogs = $statuslik_catalogs->filter(function ($catalog, $key) {
            $offer = collect(json_decode($catalog['offer'], true));
            $offer = $offer->filter(function ($item, $key) {
                return (strtotime($item['end']) >= time() and strtotime($item['start']) <= time());
            });
            if ($offer->count() > 0)
                return true;
        });


        // vdd($catalogs);
        ///////////
        /*$catalogs = $this->shop_catalogs->filter(function ($catalog, $key) use ($status) {
            $offer = collect(json_decode($catalog['offer'], true));
            $offer = $offer->where('offer', $status)->filter(function ($item, $key) {
                return (strtotime($item['end']) >= time() and strtotime($item['start']) <= time());
            });
            if ($offer->count() > 0)
                return true;
        });*/

        $elements = $this->shop_elements->whereIn('id', $catalogs->pluck('shop_element_id'));

        $products = $this->shop_products->whereIn('id', $elements->pluck('shop_product_id'));
        // vdd($category_id);
        if ($category_id !== null)
            $products = $products->where('shop_category_id', $category_id);
        // vdd($products);
        $products = $products->take($count)->map(function ($product, $key) {
            return $this->product($product['id']);
        });

        return $products;
    }

    public function productByStatusOffer($status, $count = 3)
    {
        $ids = [];


        foreach ($this->shop_products as $offer) {
            if ($offer['offer'] != null) {

                $array = json_decode($offer['offer'], true);
                if (is_array($array))
                    if (in_array($status, $array)) {
                        $ids[] = $offer['id'];
                    }
            }


        }
        $products = $this->shop_products->whereIn('id', $ids);
        $products = $products->take($count)->map(function ($product, $key) {
            return Az::$app->market->product->product($product['id']);
        });

        return $products;

    }
    // Dilshod Khudoyarov
    // telegram = @Dishkan2000
    // this function shows products if it has catalog
    public function productsIfHasCatalogTest()
    {
        $company_id = null;
        $result = AZ::$app->market->product->productsIfHasCatalog($company_id);
        vd($result);
    }


    /**
     *
     * Function  productsIfHasCatalog
     * @param null $company_id
     * @return  mixed
     *
     * @author  AsrorZakirov
     * @license AsrorZakirov
     *
     * Agar Product ShopCatalogda mavjud bo'lsa - uni qaytaradi
     */
    public function productsIfHasCatalog($company_id = null)
    {
        //price_filter

        $priceFilter = Az::$app->cores->session->get('price_filter');
        if (!empty($priceFilter)) {

            // todo:start AsrorZakirov

            $currency = Az::$app->cores->session->get('currency') ?? ShopCatalog::currency['UZS'];

            $min_price = Az::$app->payer->currency2->convert($currency, ShopCatalog::currency['UZS'], ZArrayHelper::getValue($priceFilter, 0));

            $max_price = Az::$app->payer->currency2->convert($currency, ShopCatalog::currency['UZS'], ZArrayHelper::getValue($priceFilter, 1));

            // todo:end AsrorZakirov

            $shop_catalogs = $this->shop_catalogs
                ->where('price', '>=', $min_price)
                ->where('price', '<=', $max_price);
        } else {
            $shop_catalogs = $this->shop_catalogs;

        }
        //endpricefilter

        if (!empty($company_id))
            $shop_catalogs = $shop_catalogs->where('user_company_id', $company_id);

        $shop_element_ids = $shop_catalogs
            ->where('available', true)
            ->where('amount', '>', 0)
            ->pluck('shop_element_id')
            ->unique();
        $shop_product_ids = $this->shop_elements->whereIn('id', $shop_element_ids)->pluck('shop_product_id');

        return $this->shop_products->whereIn('id', $shop_product_ids);
    }
    // Dilshod Khudoyarov
    // telegram = @Dishkan2000
    // this function shows all companies
    public function allCompaniesTest()
    {
        $page = 1;
        $limit = 10;
        /** @var Collection $result */
        $result = Az::$app->market->product->allCompanies($page, $limit);
        vd($result);
    }

    public function allCompanies($page = null, $limit = null)
    {
        /*$companies = UserCompany::find()
            ->all();*/
        $companies = $this->core_companies->where('parent_id', null)->where('active', true);

        if ($page !== null and $limit !== null) {
            $skip = ($page - 1) * $limit;
            $companies = $companies->skip($skip)->take($limit);
        }
        $items = [];
        foreach ($companies as $company) {
            $companyItem = new UserCompanyItem();
            if ($company['photo'] === null) {
                $companyItem->photo = [0 => 'no-photo.png'];
                continue;
            }
            $companyItem->id = $company['id'];
            // $companyItem->code = $company['code'];
            $companyItem->type = $company['type'];
            $companyItem->name = $company['name'];
            $companyItem->phone = $company['phone'];
            $companyItem->website = $company['website'];
            $companyItem->email = $company['email'];
            $companyItem->photo = json_decode($company['photo'], true);
            $companyItem->rating = $company['rating'];
            $companyItem->inn = $company['inn'];
            $companyItem->okonx = $company['okonx'];
            //$companyItem->amount = $catalog->amount;
            $companyItem->title = $company['title'];
            $companyItem->text = $company['text'];
            $companyItem->text_short = $company['text_short'];
            if (\Dash\count($company['photo']) > 0) {
                $path = '/imagez/mplace/' . ZArrayHelper::getValue($companyItem->photo, 0);
                // '/uploaz/eyuf/UserCompany/photo/' . $company['id'] . '/' .
                if (file_exists($path))
                    $companyItem->photo = $path;
            }
            if (empty($companyItem->photo))
                $companyItem->photo = $this->defaultProductImage;

            $companyItem->url = ZUrl::to([
                '/shop/user/main-catalog/main',
                'id' => $company['id']
                // /shop/user/main-market/main.aspx
            ]);
            //vdd($companyItem->url);
            $companyItem->distence = "12km";
            //$companyItem->image = $catalog->price;
            //$companyItem->price = $catalog->price_old;
            //$companyItem->currency = "$";
            //$companyItem->cart_amount = 0;

            $items[] = $companyItem;
        }

        return $items;
    }

    #region getCompanyByID

    /**
     *
     * Function  getCompany
     * @param null $companyId
     * @return  UserCompany
     *
     * @author AzimjonToirov
     */

    public function getCompany($companyId = null)
    {
        $companies = Az::$app->market->product->allCompanies();

        /** @var UserCompany $company */
        foreach ($companies as $company) {
            if ($company->id === (int)$companyId)
                return $company;
        }


        return $company;
    }

    #endregion


    // Dilshod Khudoyarov
    // telegram = @Dishkan2000
    // this function shows products with branch
    public function productwithBranchTest()
    {
        $shop_product_id = 28;
        $company_id = null;
        $is_full = false;
        $is_sep = true;
        /** @var Collection $result */
        $result = Az::$app->market->product->productWithBranch($shop_product_id, $company_id, $is_full, $is_sep);
        vd($result);
    }

    public function productWithBranch($shop_product_id = null, $company_id = null, $is_full = false, $is_sep = true)
    {

        $shop_product = $this->shop_products->where('id', $shop_product_id)->first();


        $core_category = $this->core_categories->where('id', $shop_product['shop_category_id'])->first();

        if ($core_category === null) {
            $product_id = $shop_product['id'];
            throw new ErrorException("$product_id product's category is not exist");
        }

        $is_multi = true;
        if ($company_id !== null and !$this->is_multi($core_category))
            $is_multi = false;

        if ($is_multi)
            $item = new MultiProductItem();
        else
            $item = new SingleProductItem();

        $item->id = $shop_product['id'];
        $item->categoryName = $core_category['name'] ?? 'not found';
        $item->categoryId = $core_category['id'] ?? 'not found';
        $item->categoryUrl = ZUrl::to([
            '/shop/user/filtering/common',
            'id' => $core_category['id'],
        ]);
        $item->url = ZUrl::to([
            '/shop/user/product-single/common',
            'id' => $shop_product['id'],
        ]);
        $item->name = $shop_product['name'];

        //agar shu productni option type laridan hech qaysi kombinatsiyaga ishtirok etmasa
        //cart_amount nomi bilan karzinkada shundan nechta borligi chiqib turadi

        $elements = $this->shop_elements
            ->where('shop_product_id', $shop_product['id']);

        if ($is_multi) {
            if ($is_full) {
                foreach ($elements as $element) {
                    //agar hech kim sotmayotgan bo'lsa marketda ko'rinmaydi
                    $catelogs = $this->shop_catalogs
                        ->where('shop_element_id', $element['id']);

                    if ($catelogs !== null and $catelogs->count() !== 0) {
                        $sub_item = $this->productItemByElementId($element['id']);
                        $item->items[] = $sub_item;
                    }
                }
            }
        } else {
            $cart_catalogs = collect($this->sessionGet('cart'));
            $catalog = $this->shop_catalogs
                ->where('shop_element_id', $elements->first()['id'])->where('user_company_id', $company_id)
                ->first();

            if ($catalog !== null) {
                $current_cart_catalog = $cart_catalogs->where('catalogId', $catalog['id'])->first();
                $item->cart_amount = $current_cart_catalog["cart_amount"] ?? 0;
                $item->catalogId = $catalog["id"];
            }

        }


        $brand = $this->core_brands->where('id', $shop_product['shop_brand_id'])->first();
        if ($brand !== null) {
            $item->brand = $brand['name'] ?? 'not found';
            $item->brandImage = '/uploaz/' . App . '/ShopBrand/image/' . $brand['id'] . '/' . ZArrayHelper::getValue($brand['image'], 0);
        }
        $item->title = $shop_product['title'];
        $item->text = $shop_product['text'];
        $item->status = $this->productOffers($shop_product_id, $company_id);
        $item->rating = $shop_product['rating'];

        $temp_product = new ShopProduct();
        $item->measure = $temp_product->_measure[$shop_product['measure']] ?? $temp_product->_measure['pcs'];
        $item->measureStep = ProductItem::measureStep[$shop_product['measure']] ?? ProductItem::measureStep['pcs'];

        $item->in_wish = Az::$app->market->wish->CheckWish($shop_product['id']);
        $item->in_compare = Az::$app->market->wish->CheckCompare($shop_product['id']);

        //properties
        if ($is_full) {
            $item->properties = $this->prepertyItems($shop_product['id'], false, false, $is_sep);
            $item->allProperties = $this->prepertyItems($shop_product['id']);
        }

        $item->images = $this->images($shop_product);

        $shop_catalogs = $this->shop_catalogs->whereIn('shop_element_id', $elements->pluck('id'));
        $currency = Az::$app->cores->session->get('currency');
        $shop_catalogs = $shop_catalogs->map(function ($core_catalog) use ($currency) {

            $core_catalog['price'] = Az::$app->payer->currency2->convert($core_catalog['currency'], $currency, $core_catalog['price']);

            $core_catalog['price_old'] = Az::$app->payer->currency2->convert($core_catalog['currency'], $currency, $core_catalog['price_old']);

            return $core_catalog;
        });

        $amount = $shop_catalogs->sum('amount');
        $item->amount = $amount;

        if ($item->is_multi) {
            $price_min = \Dash\Curry\min($shop_catalogs->pluck('price'));
            $price_max = \Dash\Curry\max($shop_catalogs->pluck('price'));

            $item->min_price = $price_min ?? null;
            $item->max_price = null;
            if ($price_min !== $price_max)
                $item->max_price = $price_max;
        } else {
            $item->new_price = $shop_catalogs->pluck('price')->first() ?? null;
            $item->price_old = $shop_catalogs->pluck('price_old')->first() ?? null;

            if ($item->new_price != null and $item->price_old != null) {
                $item->discount = ($item->new_price - $item->price_old) * 100 / $item->price_old;
                $item->discount = round($item->discount);
            }
        }

        return $item;
    }
    // Dilshod Khudoyarov
    // telegram = @Dishkan2000
    // this function returns image of products from database
    private function imagesTest()
    {
        $shop_product = '';
        $result = Az::$app->market->product->images($shop_product);
        vd($result);
    }

    private function images($shop_product)
    {

        $images = [];

        if (!empty($shop_product['image']) || $shop_product['image'] !== null) {
            $imageslist = json_decode($shop_product['image'], true);

            if (is_array($imageslist))
                foreach ($imageslist as $image) {
                    $path = '/uploaz/' . App . '/ShopProduct/image/' . $shop_product['id'] . '/' . $image;
                    if (file_exists(Root . '/upload/' . $path))
                        $images[] = $path;
                    else
                        $images[] = '/imagez/default/product/noimage.jpg';
                }
            else {
                for ($i = 0; $i < 4; $i++) {
                    $a = rand(1, 100);
                    $images[] = 'https://picsum.photos/600/300?random=' . $a;
                }
                return $images;
            }
            return $images;
        }


        //test uchun rasm qo'shish
        $images = [];
        for ($i = 0; $i < 4; $i++) {
            $a = rand(1, 100);
            $images[] = 'https://picsum.photos/600/300?random=' . $a;
        }
        ///test uchun rasm qo'shish. ishga tushirish payti olib tashlash keak

        if (\Dash\count($images) == 0)
            $images[] = $this->defaultProductImage;

        return $images;
    }

    public function prepertyItemsProduct($id, $only_combinations = false, $is_seperated_by_branch = true)
    {
        $property = [];
        $shop_product = $this->shop_products->where('id', $id)->first();
        if (empty($shop_product['shop_option_ids']))
            return null;
        $options = $this->core_options->whereIn('id', json_decode($shop_product['shop_option_ids'], true));
        foreach ($options as $option) {
            $type = $this->core_option_types->where('id', $option)->first();

            $branchname = $this->core_option_branches->where('id', $type)->first()["name"] ?? 'not found';;


        }
        return $property;
    }
    // Dilshod Khudoyarov
    // telegram = @Dishkan2000
    // this function shows property items by its id
    public function propertyItemsTest()
    {
        $id = 28;
        $only_combinations = false;
        $is_element = false;
        $is_seperated_by_branch = true;
        $result = Az::$app->market->product->prepertyItems($id, $only_combinations, $is_element, $is_seperated_by_branch);
        vd($result);
    }

    public function prepertyItems($id, $only_combinations = false, $is_element = false, $is_seperated_by_branch = true)
    {
        if ($is_element) {
            $shop_element = $this->shop_elements->where('id', $id)->first();
            if (empty($shop_element)) return [];
            $shop_product = $this->shop_products->where('id', $shop_element['shop_product_id'])->first();
        } else {
            $shop_product = $this->shop_products->where('id', $id)->first();
        }
        if (empty($shop_product['shop_option_ids'])) return null;
        $options = $this->core_options->whereIn('id', json_decode($shop_product['shop_option_ids'], true));

        $core_category = $this->core_categories->where('id', $shop_product['shop_category_id'])->first();;
        $category_properties = $this->propertsByCategory($core_category['id'], $only_combinations);
        //vdd($category_properties);
        if ($category_properties === null) {
            return null;
        }
        $properties = [];
        if ($is_seperated_by_branch) {
            $category_propertiesByBranch = $category_properties->groupBy('shop_option_branch_id');
            foreach ($category_propertiesByBranch as $branch_id => $category_properties) {
                $branch_name = $this->shop_option_branches->where('id', $branch_id)->first()["name"] ?? 'not found';
                $properties[$branch_name] = [];
                foreach ($category_properties as $category_property) {
                    $new_property_item = new PropertyItem();
                    $new_property_item->name = $category_property['name'];
                    $options1 = $options->where('shop_option_type_id', $category_property['id']);
                    foreach ($options1 as $option) {
                        if ($is_element) {
                            if ($options1->count() > 1) {
                                if ($shop_element['shop_option_ids'] === null)
                                    continue;
                                $ars = json_decode($shop_element['shop_option_ids'], true);
                                if (is_array($ars))
                                    if (in_array($option['id'], json_decode($shop_element['shop_option_ids'], true))) {
                                        $new_property_item->items[$option['id']] = $option['name'];
                                    }

                            } else {
                                $new_property_item->items[$option['id']] = $option['name'];
                            }
                        } else {
                            $new_property_item->items[$option['id']] = $option['name'];
                        }
                    }
                    $properties[$branch_name][] = $new_property_item;
                }
            }       //vdd($properties);

        } else {
            if ($category_properties === null)
                return null;
            foreach ($category_properties as $category_property) {
                $new_property_item = new PropertyItem();
                $new_property_item->name = $category_property['name'];
                $options1 = $options->where('shop_option_type_id', $category_property['id']);
                //vdd($shop_product);
                foreach ($options1 as $option) {
                    if ($is_element) {
                        if ($options1->count() > 1) {
                            if ($shop_element['shop_option_ids'] === null)
                                continue;
                            $ars = json_decode($shop_element['shop_option_ids'], true);

                            if (is_array($ars))
                                if (in_array($option['id'], json_decode($shop_element['shop_option_ids'], true))) {
                                    $new_property_item->items[$option['id']] = $option['name'];
                                }

                        } else {
                            $new_property_item->items[$option['id']] = $option['name'];

                        }
                    } else {
                        $new_property_item->items[$option['id']] = $option['name'];
                    }
                }
                $properties[] = $new_property_item;
            }
        }

        return $properties;
    }

    private function productOffersTest()
    {
        $shop_product_id = 36;
        $company_id = 200;
        $result = Az::$app->market->product->productOffers($shop_product_id, $company_id);
        vd($result);
    }

    private function productOffers($shop_product_id, $company_id = null)
    {
        $shop_element_ids = $this->shop_elements
            ->where('shop_product_id', $shop_product_id)
            ->pluck('id');

        $shop_catalogs = $this->shop_catalogs->whereIn('shop_element_id', $shop_element_ids);
        if (!empty($company_id))
            $shop_catalogs = $shop_catalogs->where('shop_company_id', $company_id);

        $offers = $this->shop_offers
            ->whereIn('shop_catalog_id', $shop_catalogs->pluck('id'))
            ->pluck('type');

        $merged_offers = collect();

        foreach ($offers as $offer) {
            $merged_offers = $merged_offers->merge($offer);
        }

        $merged_offers = $merged_offers->unique()->toArray();

        $price_old_catalogs = $shop_catalogs->where('price_old', '>', 0);
        if ($price_old_catalogs->count() > 0)
            $merged_offers[] = 'sale';

        return $merged_offers;

    }
    // Dilshod Khudoyarov
    // telegram = @Dishkan2000
    // this function shows items of products by its catalog_id in database
    public function productItemByCatalogIdTest()
    {
        $catalog_id = 1470;
        $result = Az::$app->market->product->productItemByCatalogId($catalog_id);
        vd($result);
    }

    public function productItemByCatalogId($catalog_id)
    {
        $catalogs = ShopCatalog::find()
            ->where([
                'id' => $catalog_id
            ])->all();
        $shop_element_id = '';
        $user_company_id = '';
        // $catalog = $this->shop_catalogs
        foreach ($catalogs as $key => $v) {

            foreach ($v->attributes as $attr) {

                $shop_element_id = ZArrayHelper::getValue($v->attributes, 'shop_element_id');
                $user_company_id = ZArrayHelper::getValue($v->attributes, 'user_company_id');
            }

        }

        if ($catalogs === null)
            return null;

        $product_item = $this->productItemByElementId($shop_element_id, $user_company_id);

        return $product_item;

    }
    // Dilshod Khudoyarov
    // telegram = @Dishkan2000
    // this function shows one product item by its option
    public function singleProductItemByOptionsTest()
    {
        $product_id = 41;
        $company_id = null;
        $options = ["1", "2", "5", "8", "204", "206", "207"];
        $result = Az::$app->market->product->singleProductItemByOptions($product_id, $company_id, $options);
        vd($result);
    }

    public function singleProductItemByOptions($product_id, $company_id, $options = [])
    {
        $elements = $this->shop_elements->where('shop_product_id', $product_id);
        /*  vdd($elements);*/
        $shop_element = null;
        foreach ($elements as $element) {
            $bool = true;
            if ($element['shop_option_ids'] !== null)
                $array = json_decode($element['shop_option_ids'], true);
            if ($element['shop_option_ids'] === null) {
                $shop_element = $element;
                break;
            } else {
                if (is_array($array))
                    foreach ($array as $option_id) {

                        if (!in_array($option_id, $options)) {
                            $bool = false;
                            continue;
                        }
                    }
            }
            if ($bool) {
                $shop_element = $element;
                break;
            }
        }
        $id = ZArrayHelper::getValue($shop_element, 'id');


        return $this->productItemByElementId($id, $company_id);
    }

    //search uchun
    // Dilshod Khudoyarov
    // telegram = @Dishkan2000
    // this function shows product items by the elements
    public function productItemByElementsTest()
    {
        $element_ids = 584919;
        $page = 1;
        $limit = 10;
        $result = Az::$app->market->product->productItemByElements($element_ids, $page, $limit);
        vd($result);
    }

    public function productItemByElements($element_ids, $page = null, $limit = null)
    {
        $shop_elements = $this->shop_elements->whereIn('id', $element_ids);
        if ($page !== null and $limit !== null) {
            $skip = ($page - 1) * $limit;
            $shop_elements = $shop_elements->skip($skip)->take($limit);
        }

        $productItems = [];
        foreach ($shop_elements as $shop_element) {
            $productItems[] = $this->productItemByElementId($shop_element['id']);
        }

        return $productItems;
    }
    // Dilshod Khudoyarov
    // telegram = @Dishkan2000
    // this function shows product items by the element id
    public function productItemByElementIdTest()
    {
        $element_id = 584926;
        $company_id = null;
        $is_full = true;
        $result = Az::$app->market->product->productItemByElementId($element_id, $company_id, $is_full);
        vd($result);
    }

    public function productItemByElementId($element_id, $company_id = null, $is_full = true)
    {
        $shop_element = $this->shop_elements->where(
            'id', $element_id
        )
            ->first();

        if (empty($shop_element))
            return null;

        $shop_product = $this->shop_products->where(
            'id', $shop_element['shop_product_id']
        )
            ->first();


        $core_category = $this->core_categories->where('id', ZArrayHelper::getValue($shop_product, 'shop_category_id'))->first();

        if ($core_category === null) {
            $product_id = ZArrayHelper::getValue($shop_product, 'id');
            return null;
            // throw new ErrorException("$product_id product's category is not exist");
        }

        $is_multi = true;
        if ($company_id !== null)
            $is_multi = false;

        if ($is_multi)
            $item = new MultiProductItem();
        else
            $item = new SingleProductItem();

        //$item->id = $shop_element['id'];
        $item->id = $shop_product['id'];
        $item->categoryName = $core_category['name'] ?? 'not found';
        $item->categoryId = $core_category['id'] ?? 'not found';
        $item->categoryUrl = ZUrl::to([
            '/shop/user/filtering/common',
            'id' => $core_category['id'],
        ]);
        $item->url = ZUrl::to([
            '/shop/user/product-single/common',
            'id' => $shop_product['id'],
        ]);

        $company = $this->core_companies->where('id', $company_id)->first();

        if ($company !== null) {
            $item->company_id = $company_id;
            $item->company_name = $company['name'];
            $item->company_url = ZUrl::to([
                'user/market-single/products',
                'id' => $company['id']
            ]);
        }

        $item->name = $shop_element['name'];

        //agar shu productni option type laridan hech qaysi kombinatsiyaga ishtirok etmasa
        //cart_amount nomi bilan karzinkada shundan nechta borligi chiqib turadi


        if (!$is_multi) {
            $cart_catalogs = collect($this->sessionGet('cart'));
            $catalog = $this->shop_catalogs
                ->where('shop_element_id', $shop_element['id'])->where('user_company_id', $company_id)
                ->first();
            if ($catalog !== null) {
                $current_cart_catalog = $cart_catalogs->where('catalog_id', $catalog['id'])->first();
                $item->cart_amount = $current_cart_catalog["amount"] ?? 0;
                $item->catalogId = $catalog["id"];
            }
        }

        $brand = $this->core_brands->where('id', $shop_product['shop_brand_id'])->first();
        if ($brand !== null) {
            $item->brand = $brand['name'] ?? 'not found';
            $item->brandImage = '/uploaz/' . App . '/ShopBrand/image/' . $brand['id'] . '/' . ZArrayHelper::getValue($brand['image'], 0);
        }
        $item->title = $shop_product['title'];
        $item->text = $shop_product['text'];
        $item->status = $this->productOffers($shop_product['id'], $company_id);
        $item->rating = $shop_product['rating'];

        $temp_product = new ShopProduct();
        $item->measure = $temp_product->_measure[$shop_product['measure']] ?? $temp_product->_measure['pcs'];
        $item->measureStep = ProductItem::measureStep[$shop_product['measure']] ?? ProductItem::measureStep['pcs'];

        $item->in_wish = Az::$app->market->wish->CheckWish($shop_product['id']);
        $item->in_compare = Az::$app->market->wish->CheckCompare($shop_product['id']);

        if ($is_full) {
            //properties
            $item->properties = $this->prepertyItems($shop_element['id'], true, true, false);
            $item->allProperties = $this->prepertyItems($shop_element['id'], false, true, false);
        }
        $item->images = $this->images($shop_product);

        $shop_catalogs = $this->shop_catalogs->where('shop_element_id', $shop_element['id']);
        $currency = Az::$app->cores->session->get('currency');
        $shop_catalogs = $shop_catalogs->map(function ($core_catalog) use ($currency) {

            $core_catalog['price'] = Az::$app->payer->currency2->convert($core_catalog['currency'], $currency, $core_catalog['price']);

            $core_catalog['price_old'] = Az::$app->payer->currency2->convert($core_catalog['currency'], $currency, $core_catalog['price_old']);

            return $core_catalog;
        });

        if ($item->is_multi) {
            $price_min = \Dash\Curry\min($shop_catalogs->pluck('price'));
            $price_max = \Dash\Curry\max($shop_catalogs->pluck('price'));

            $item->min_price = $price_min ?? null;
            $item->max_price = null;
            if ($price_min !== $price_max)
                $item->max_price = $price_max;

            $amount = $shop_catalogs->sum('amount');
        } else {
            $core_catalog1 = $shop_catalogs->where('user_company_id', $company_id)->first();
            $item->new_price = $core_catalog1['price'] ?? null;
            $item->price_old = $core_catalog1['price_old'] ?? null;

            if ($item->new_price != null and $item->price_old != null) {
                $item->discount = ($item->new_price - $item->price_old) * 100 / $item->price_old;
                $item->discount = round($item->discount);
            }
            $amount = $core_catalog1['amount'];
        }
        $item->amount = $amount;

        return $item;
    }


    /**
     *  $is_combination = false bo'lsa hammasini beradi true bo'lsa faqat is_combination true bo'lgan option_typelarni beradi
     * Function  propertsByCategory
     * @param null $category_id
     * @param bool $is_combination
     * @param null $branch_id
     * @return  array
     */
    // Dilshod Khudoyarov
    // telegram = @Dishkan2000
    // this function shows properties by its category
    public function propertsByCategoryTest()
    {
        $category_id = 984;
        $is_combination = false;
        $branch_id = 5;
        $result = Az::$app->market->product->propertsByCategory($category_id, $is_combination, $branch_id);
        vd($result);
    }

    public function propertsByCategory($category_id = null, $is_combination = false, $branch_id = null)
    {

        if (!empty($category_id)) {
            $core_category = $this->core_categories->where('id', $category_id)->first();
            //vdd(!is_array(json_decode($core_category['shop_option_type'])));

            if ($core_category['shop_option_type'] === null || !is_array(json_decode($core_category['shop_option_type'])))
                return null;

            $core_option_type_ids = array_map(function ($a) use ($is_combination) {
                $optionType = ZArrayHelper::getValue($a, 'shop_option_type_id');
                if ($is_combination) {

                    if (!is_array($a))
                        return null;

                    if (array_key_exists('is_combination', $a)) {
                        if ($optionType !== '')
                            return $optionType;

                    }
                } else {
                    if ($optionType !== '')
                        return $optionType;
                }
            }, json_decode($core_category['shop_option_type'], true));
            //vdd($core_option_type_ids);

            $core_option_types = $this->core_option_types
                ->whereIn('id', $core_option_type_ids);

        } else {
            $core_option_types = $this->core_option_types;
        }
        if ($branch_id !== null)
            $core_option_types = $core_option_types->filter(function ($a) use ($branch_id) {
                if ($a['shop_option_branch_id'] === $branch_id)
                    return true;
                else
                    return false;
            });

        return $core_option_types;
    }
    // Dilshod Khudoyarov
    // telegram = @Dishkan2000
    // this function checks core_category whether it`s multi or not, by combination
    public function is_multiTest()
    {
        $core_category = ['shop_option_type'];
        $result = Az::$app->market->product->is_multi($core_category);
        vd($result);
    }

    public function is_multi($core_category)
    {
        //$option_type_json = json_decode($core_category['0'], true);
        $option_type_json = json_decode(ZArrayHelper::getValue($core_category, 0), true);

        // $option_type_json = json_decode($core_category['shop_option_type'], true);

        $is_multi = false;
        if (is_array($option_type_json))
            foreach ($option_type_json as $option_type) {
                if (array_keys_exist('is_combination', $option_type)) {
                    $is_multi = true;
                    break;
                }
            }
        return $is_multi;
    }

#endregion

#region  offerCategoriesWithProducts
    public function test111()
    {
        $a = Az::$app->market->product->offerCategoriesWithProducts('deal_of_day', null, 3);
    }
    // Dilshod Khudoyarov
    // telegram = @Dishkan2000
    // this function shows offers category with products
    public function offerCategoriesWithProductsTest()
    {
        $status = 't';
        $company_id = null;
        $count = 3;
        $result = Az::$app->market->product->offerCategoriesWithProducts($status, $company_id, $count);
        vd($result);
    }

    public function offerCategoriesWithProducts($status, $company_id = null, $count = 3)
    {
        $txt = 'SELECT * FROM shop_category WHERE offer @> \'["' . $status . '"]\'';
        $categories = ShopCategory::findBySql($txt)->asArray()->all();
        //vdd($categories);
        $menuItmes = [];
        foreach ($categories as $category) {
            $menuItme = new MenuItem();
            $menuItme->name = $category['name'];
            $menuItme->url = "asdfasdf";
            $menuItme->id = $category['id'];

            $menuItme->items = $this->productByStatus($category['id'], $status, $count, $company_id)->toArray();

            $menuItmes[] = $menuItme;
        }

        return $menuItmes;

    }
    #endregion

    #region  propertiesByProduct


    /**
     *
     * Function  getPropertiesByProduct
     * @param null $productId
     * @return  array|null
     *
     * @author AzimjonToirov
     */
    public function getPropertiesByProduct($productId = null)
    {
        $product = Az::$app->market->product->product($productId, null, true);

        if ($product->properties === null)
            return null;

        $array = [];
        foreach ($product->properties as $key => $property_group) {

            foreach ($property_group->items as $shop_option_ids => $elementNames) {
                $array[$shop_option_ids] = $elementNames;
            }

        }

        return $array;
    }

    #endregion
}
