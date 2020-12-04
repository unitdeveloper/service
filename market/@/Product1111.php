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
use zetsoft\dbitem\data\ALLApp;
use zetsoft\dbitem\data\Config;
use zetsoft\dbitem\data\Form;
use zetsoft\dbitem\shop\CompanyCardItem;
use zetsoft\dbitem\shop\MultiProductItem;
use zetsoft\dbitem\shop\ProductItem;
use zetsoft\dbitem\shop\PropertyItem;
use zetsoft\dbitem\shop\SingleProductItem;
use zetsoft\models\shop\ShopBrand;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopCategory;
use zetsoft\models\shop\ShopElement;
use zetsoft\models\shop\ShopOption;
use zetsoft\models\shop\ShopOptionBranch;
use zetsoft\models\shop\ShopOptionType;
use zetsoft\models\shop\ShopProduct;
use zetsoft\models\shop\ShopShipment;
use zetsoft\models\user\UserCompany;
use zetsoft\system\assets\ZColor;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZUrl;
use zetsoft\system\kernels\ZFrame;
use zetsoft\widgets\actions\ZEasySelectableWidget;
use zetsoft\widgets\former\ZFormWidget;
use zetsoft\widgets\images\ZImageWidget;
use zetsoft\widgets\incores\ZIRadioGroupWidget;
use zetsoft\widgets\incores\ZMCheckboxGroupWidget;
use zetsoft\widgets\incores\ZMRadioWidget;
use zetsoft\widgets\inputes\ZHInputWidget;
use zetsoft\widgets\inputes\ZKSliderIonWidget;
use zetsoft\widgets\inputes\ZSelect2Widget;
use zetsoft\widgets\navigat\ZGAccordionWidget;
use zetsoft\widgets\navigat\ZMarketDropdownWidget;
use zetsoft\widgets\values\ZFormViewWidget;
use function Spatie\array_keys_exist;

class Product1111 extends ZFrame
{
    /**
     * @var Collection $shop_products ;
     */
    public $shop_products;
    /**
     * @var Collection $shop_elements ;
     */
    public $shop_elements;
    public $shop_catalogs;
    public $shop_categories;
    public $shop_option_types;
    public $shop_options;
    public $core_companies;
    public $core_option_branches;
    public $core_brands;
    public const cart_type = [
        'add' => 'add',
        'set' => 'set',
    ];

    public $defaultProductImage = "https://cdn.dribbble.com/users/357797/screenshots/3998541/empty_box.jpg";
    #endregion

#region test collection
    public function test()
    {
        return $this->test1();
    }

    private function test1()
    {


        // vdd($this->productItemByCatalogId(16));
        //  vdd($this->getOptionsByCategory(540));
        // vdd($this->allProducts(49));
        // vdd($this->getPropertsByCategory2(549));
        //   vdd($this->getCatelogsByProductId(2743));
        //  vdd($this->getAllProductsByCompany(40));
        //  vdd($this->filterFormItems(40));

        // vdd($this->productItemByElementId(1225));
    }


#endregion
#region init

    public function init()
    {

        $this->shop_products = collect(ShopProduct::find()->all());
        $this->shop_elements = collect(ShopElement::find()->all());
        $this->shop_catalogs = collect(ShopCatalog::find()->all());

        $this->core_brands = $this->cacheRedis('core_brands', fn() => collect(ShopBrand::find()->all()), [
            ShopBrand::class
        ]);

        $this->shop_categories = collect(ShopCategory::find()->all());
        $this->shop_option_types = collect(ShopOptionType::find()->all());
        $this->shop_options = collect(ShopOption::find()->all());
        $this->core_companies = collect(UserCompany::find()->all());
        $this->core_option_branches = collect(ShopOptionBranch::find()->all());
        parent::init();
    }


    public function getTestProducts()
    {
        $data = [];
        for ($i = 0; $i < 10; $i++) {
            $data[] = $this->getproducttest();
        }

        return $data;
    }

    public function getproducttest()
    {
        $property = new PropertyItem();
        $property->name = '182sad';
        $property->branch = '00421sa';
        $property->items = [
            'huihuih',
            'okjio',
            'juijiio'
        ];

        $item = new MultiProductItem();

        $item->id = 18;
        $item->catalogId = 19;
        $item->name = 'Product Name';
        $item->amount = 0;
        $item->properties = [
            $property,
            $property,
            $property,
        ];
        $item->allProperties = [
            $property
        ];
        $item->status = ['new', 'hot_)', 'sale'];
        $item->categoryId = 20;
        $item->categoryName = 'catalogName';
        $item->categoryUrl = '';
        $item->title = '';
        $item->text = '';
        $item->brand = '';
        $item->brandImage = '';
        $item->rating = '';
        $item->reviewCount = 8;
        $item->url = '';
        $item->visible = true;
        $item->images = [];
        $item->is_multi = true;
        $item->in_wish = '';
        $item->in_compare = '';
        $item->cart_amount = '';
        $item->items = [];
        $item->barcode = '';
        $item->max_price = 5200;
        $item->min_price = 4500;


        return $item;
    }

    public function getproducttestsingle()
    {
        $property = new PropertyItem();
        $property->name = '182sad';
        $property->branch = '00421sa';
        $property->items = [
            1 => 'huihuih',
            2 => 'okjio',
            3 => 'juijiio'
        ];
        $item = new SingleProductItem();

        $item->id = 18;
        $item->catalogId = 19;
        $item->name = 'Product Name';
        $item->amount = 0;
        $item->status = ['new', 'sale'];
        $item->categoryId = 18;
        $item->categoryName = 'catalogName';
        $item->categoryUrl = '';
        $item->title = '';
        $item->currency = '$';
        $item->currencyType = 'before';
        $item->text = '';
        $item->brand = '';
        $item->brandImage = '';
        $item->rating = (string)3.0;
        $item->reviewCount = 8;
        $item->url = '';
        $item->visible = true;
        $item->images = [];
        $item->is_multi = '';
        $item->in_wish = '';
        $item->in_compare = '';
        $item->cart_amount = '';
        $item->items = [];
        $item->barcode = '';
        $item->new_price = 120;
        $item->price_old = 120;


        return $item;
    }


    public function product()
    {

    }

    public function allElement($where)
    {

        $Q = ShopElement::find()
            ->where([

            ])
            ->all();

        $data = $this->cacheRedis('core_brands', collect(
            $Q), [
            ShopBrand::class
        ]);
    }

    public function allCatalog()
    {

    }

#endregion

#region for AdminPanel
    // depdrop uchun


    /**
     *
     * Function  getOptionsByCategory
     * @param null $selectedId
     * @return  array|null
     */
    public function getOptionsByCategory($selectedId = null)
    {


        if ($selectedId === null)
            return null;


        $out = [];
        if (isset($selectedId)) {
            if (!empty($selectedId)) {

                //$category = $this->shop_categories->where('id', $selectedId)->first();

                $category = $this->shop_categories->where('id', $selectedId)->first();
                $option_types = $category->shop_option_type;
                if (empty($option_types))
                    return null;
                $data = [];
                foreach ($option_types as $option_type) {

                    $shop_option_types = $this->shop_option_types
                        ->where(
                            'id', (int)$option_type['shop_option_type']
                        );
                    if (count($shop_option_types) == 0) {
                        return null;
                    }

                    $options = $this->shop_options
                        ->where('shop_option_types_id', $shop_option_types->id);
                    $data = $data->merge($options);
                }
                foreach ($data as $model) {
                    $out[$model->id] = $model->name;
                }

                return $out;
            }
        }
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
    public function allProducts($category_id = null, $status = null, $page = null, $limit = null)
    {

        //  return [];
        if ($page !== null and $limit !== null) {
            $skip = $page * $limit;
            $shop_products = $this->shop_products->skip($skip)->take($limit);
        } else {
            $shop_products = $this->shop_products;
        }
        if (!empty($category_id))
            $shop_products = $shop_products
                ->where('shop_category_id', $category_id);

        //$shop_products = $this->productsIfHasCatalog($shop_products);
        //  filtirlab beradi agar sessionda filter bo'lsa
        $shop_products = $this->filter($shop_products);
        $shop_products = $this->priceFilter($shop_products);

        $result = [];
        foreach ($shop_products as $shop_product) {
            $item = $this->product($shop_product->id);
            /*$item->id = */
            if (!empty($item)) {
                if ($status === null) {
                    $result[] = $item;
                } else {
                    if (in_array($status, $item->status))
                        $result[] = $item;
                }
            }
        }
        //vdd($result);
        return $result;

    }

    public function getAllProductsTest($id = 4)
    {

        $productItems = [];

        $item = new ProductItem();
        $item->id = 2;
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
        $item->max_price = 2155632;
        $item->sale = 'sdadsa';
        $item->is_multi = false;
        $item->min_price = 'adssad';
        $item->in_wish = true;
        $item->in_compare = false;

        $productItems[] = $item;

        $item = new ProductItem();
        $item->id = 2;
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
        $item->max_price = 2155632;
        $item->sale = 'sdadsa';
        $item->is_multi = false;
        $item->min_price = 'adssad';
        $item->in_wish = true;
        $item->in_compare = false;

        $productItems[] = $item;

        $item = new ProductItem();
        $item->id = 2;
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
        $item->max_price = 2155632;
        $item->sale = 'sdadsa';
        $item->is_multi = false;
        $item->min_price = 'adssad';
        $item->in_wish = true;
        $item->in_compare = false;

        $productItems[] = $item;

        $item = new ProductItem();
        $item->id = 2;
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
        $item->max_price = 2155632;
        $item->sale = 'sdadsa';
        $item->is_multi = false;
        $item->min_price = 'adssad';
        $item->in_wish = true;
        $item->in_compare = false;

        $productItems[] = $item;

        $item = new ProductItem();
        $item->id = 2;
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
        $item->max_price = 2155632;
        $item->sale = 'sdadsa';
        $item->is_multi = false;
        $item->min_price = 'adssad';
        $item->in_wish = true;
        $item->in_compare = false;

        $productItems[] = $item;

        $newProduct = [];
        for ($i = 0; $i < $id; $i++) {
            $newProduct[] = $productItems[$i];
        }

        return $newProduct;

    }


    public function productsIfHasCatalog($shop_products)
    {
        $shop_products = $shop_products->filter(function ($shop_product) {

            $shop_elements = $this->shop_elements->where(
                'shop_elements', $shop_product->id
            );

            if (count($shop_elements) === 0)
                return false;

            $shop_element_ids = $shop_elements->pluck('id');
            $shop_catalogs = $this->shop_catalogs->whereIn(
                'shop_element_id', $shop_element_ids
            );

            if (count($shop_catalogs) === 0)
                return false;

            return true;
        });

        return $shop_products;
    }

    public function getAllProductsByBrand($brand_id = null)
    {
        $result = [];
        if ($brand_id === null)
            $shop_products = $this->shop_products;
        else
            $shop_products = $this->shop_products
                ->where(
                    'shop_brand_id', $brand_id
                );

        //filtirlab beradi agar sessionda filter bo'lsa
        //brand bo'yicha hozircha filterlamaymiz
        $shop_products = $this->filter($shop_products);

        foreach ($shop_products as $shop_product) {
            $item = $this->product($shop_product->id);
            $result[] = $item;
        }

        return $result;
    }

    public function getAllProductsByCompany($company_id = null, $category_id = null)
    {
        $result = [];
        if ($company_id === null) {
            $shop_products = $this->shop_products;
        } else {
            //shu yerda join ishlatish kerak!!!
            $catalogs = $this->shop_catalogs
                ->where(
                    'user_company_id', $company_id
                );

            /*  $element_ids = array_map(function ($a) {
                  return $a->shop_element_id;
              }, $catalogs);*/


            $element_ids = $catalogs->pluck("shop_element_id");


            $shop_elements = $this->shop_elements
                ->whereIn(
                    'id', $element_ids
                );

            $product_ids = $shop_elements->pluck('shop_elements');
            $product_ids = $product_ids->unique();

            if ($category_id == null) {
                $shop_products = $this->shop_products
                    ->whereIn(
                        'id', $product_ids
                    );
            } else {
                /* $shop_products = CoreProduct::find()
                     ->where([
                         'id' => $product_ids,
                         'shop_category_id' => $category_id
                     ])->all();*/
                $shop_products = $this->shop_products->whereIn('id', $product_ids)
                    ->where('shop_category_id', $category_id);
            }
            //shu yerda join ishlatish kerak!!!
        }


        //$shop_products = $this->filter($shop_products);
        foreach ($shop_products as $shop_product) {
            $item = $this->product($shop_product->id, $company_id);
            $result[] = $item;
        }

        return $result;
    }


    public function allCompanies()
    {
        /*$companies = UserCompany::find()
            ->all();*/

        $companies = $this->core_companies;

        $items = [];
        foreach ($companies as $company) {

            $companyItem = new CompanyCardItem();
            $companyItem->id = $company->id;
            $companyItem->name = $company->name;
            //$companyItem->amount = $catalog->amount;
            $companyItem->title = $company->title;
            if (\Dash\count($company->photo)) {
                $path = '/uploaz/eyuf/UserCompany/photo/' . $company->id . '/' . $company->photo[0];
                if (file_exists($path)) {
                    $companyItem->image = $path;
                } else {
                    $companyItem->image = $this->defaultProductImage;
                }
            } else {
                $companyItem->image = $this->defaultProductImage;
            }
            $companyItem->url = ZUrl::to([
                'customer/markets/show',
                'id' => $company->id
            ]);
            $companyItem->distence = "12km";
            //$companyItem->image = $catalog->price;
            //$companyItem->price = $catalog->price_old;
            //$companyItem->currency = "$";
            //$companyItem->cart_amount = 0;

            $items[] = $companyItem;
        }
        return $items;
    }

    public function product($shop_elements = null, $company_id = null)
    {
        if (!$shop_elements) return null;

        //  $shop_product = CoreProduct::findOne($shop_elements);
        $shop_product = $this->shop_products->where('id', $shop_elements)->first();

        if (!$shop_product) return null;

        // $core_category = CoreCategory::findOne($shop_product->shop_category_id);
        $core_category = $this->shop_categories->where('id', $shop_product->shop_category_id)->first();

        if ($core_category === null)
            throw new ErrorException("$shop_product->id product's category is not exist");

        $item = new MultiProductItem();
        if ($company_id !== null)
            if (!$this->is_multi($core_category))
                $item = new SingleProductItem();
        $item->id = $shop_product->id;

        $item->categoryName = $core_category->name ?? 'not found';
        $item->categoryId = $core_category->id ?? 'not found';
        $item->categoryUrl = ZUrl::to([
            '/shop/user/filter-common/main',
            'id' => $core_category->id,
        ]);
        $item->url = ZUrl::to([
            '/shop/user/product/single-product',
            'id' => $shop_product->id,
        ]);
        $item->name = $shop_product->name;

        //agar shu productni option type laridan hech qaysi kombinatsiyaga ishtirok etmasa
        //cart_amount nomi bilan karzinkada shundan nechta borligi chiqib turadi

        $cart_elements = $this->cartOrders();
        /*   $elements = CoreElement::find()
               ->where([
                   'shop_elements' => $shop_product->id,
               ])
               ->all();*/

        $elements = $this->shop_elements
            ->where('shop_elements', $shop_product->id);


        if ($company_id === null) {
            foreach ($elements as $element) {
                //agar hech kim sotmayotgan bo'lsa marketda ko'rinmaydi
                /*
                    $catelogs = ShopCatalog::find()
                        ->where([
                            'shop_element_id' => $element->id
                        ])->all();*/

                $catelogs = $this->shop_catalogs
                    ->where('shop_element_id', $element->id);


                if ($catelogs !== null and count($catelogs) !== 0) {
                    $sub_item = $this->productItemByElementId($element->id);
                    $item->items[] = $sub_item;
                }
            }
        } else {
            if ($company_id !== null) {
                if (!$this->is_multi($core_category)) {

                    /* $catalog = ShopCatalog::find()
                         ->where([
                             'shop_element_id' => $elements[0]->id,
                             'user_company_id' => $company_id,
                         ])
                         ->one();*/


                    $catalog = $this->shop_catalogs
                        ->where('shop_element_id', $elements->first()->id)->where('user_company_id', $company_id)
                        ->first();

                    $item->cart_amount = 0;

                    foreach ($cart_elements as $cart_element)
                        if ($cart_element->catalogId == $catalog->id)
                            $item->cart_amount = $cart_element->cart_amount;
                    $item->catalogId = $catalog->id;
                }
            }
        }

        /*if (count($elements) < 2) {
            if ($company_id !== null) {
                $catalog = ShopCatalog::find()
                    ->where([
                        'shop_element_id' => $elements[0]->id,
                        'user_company_id' => $company_id,
                    ])
                    ->one();

                $item->cart_amount = 0;
                foreach ($cart_elements as $cart_element)
                    if ($cart_element->id === $catalog->id)
                        $item->cart_amount = $cart_element->cart_amount;

                $item->catalogId = $catalog->id;
            }
        } else {
            foreach ($elements as $element) {
                //agar hech kim sotmayotgan bo'lsa marketda ko'rinmaydi
                $catelogs = ShopCatalog::find()
                    ->where([
                        'shop_element_id' => $element->id
                    ])->all();
                if ($catelogs !== null) {
                    $sub_item = $this->productItemByElementId($element->id);
                    $item->items[] = $sub_item;
                }
            }
        }*/

        /*$item->brand = ShopBrand::findOne($shop_product->shop_brand_id)->name ?? 'not found';*/


        $brand = $this->core_brands->where('id', $shop_product->shop_brand_id)->first();
        if ($brand !== null) {
            $item->brand = $brand->name ?? 'not found';
            $item->brandImage = '/uploaz/' . App . '/ShopBrand/image/' . $brand->id . '/' . ZArrayHelper::getValue($brand->image, 0);
        }
        $item->title = $shop_product->title;
        $item->text = $shop_product->text;
        $item->status = $this->productOffers($shop_elements, $company_id);
        $item->rating = $shop_product->rating;
        $item->measure = $shop_product->_measure[$shop_product->measure] ?? $shop_product->_measure['pcs'];
        $item->measureStep = ProductItem::measureStep[$shop_product->measure] ?? ProductItem::measureStep['pcs'];

        $item->in_wish = $this->inWish($shop_elements);
        $item->in_compare = $this->inCompare($shop_elements);

        //properties
        $category_properties = $this->propertsByCategory($core_category->id, true);
        //vdd($category_properties);
        $properties = [];
        foreach ($category_properties as $key1 => $category_property) {
            $items = [];
            foreach ($category_property->items as $key => $value) {
                if ($shop_product->shop_option_ids !== null && $key !== null && $shop_product->shop_option_ids !== '')
                    if (in_array($key, $shop_product->shop_option_ids))
                        $items[$key] = $value;
            }
            $category_property->items = $items;
            if (!empty($category_property->items))
                $properties[] = $category_property;
        }
        //vdd($properties);
        $item->properties = $properties;

        //All properties
        $category_properties = $this->propertsByCategory($core_category->id);
        $all_properties = [];
        foreach ($category_properties as $key1 => $category_property) {
            $items = [];
            foreach ($category_property->items as $key => $value) {
                if ($key !== null && $shop_product->shop_option_ids !== null && $shop_product->shop_option_ids !== '')
                    if (in_array($key, $shop_product->shop_option_ids))
                        $items[$key] = $value;
            }
            $category_property->items = $items;
            if (!empty($category_property->items))
                $all_properties[] = $category_property;
        }
        $item->allProperties = $all_properties;
        //vdd($item->allProperties);

        $item->images = [];
        if (is_array($shop_product->image))
            foreach ($shop_product->image as $image) {
                $path = '/uploaz/eyuf/CoreProduct/images/' . $shop_product->id . '/' . $image;
                if (file_exists(Root . '/upload/' . $path))
                    $item->images[] = $path;
            }


        //test uchun rasm qo'shish

        $item->images = [];
        for ($i = 0; $i < 4; $i++) {
            $a = rand(1, 100);
            $item->images[] = 'https://picsum.photos/600/300?random=' . $a;
        }

        //TODO: test uchun rasm qo'shish. ishga tushirish payti olib tashlash keak


        if (\Dash\count($item->images) == 0)
            $item->images[] = $this->defaultProductImage;


        $amount = 0;
        $prices = [];
        $price_olds = [];
        foreach ($elements as $element) {
            /*  $catalogs = ShopCatalog::find()
                  ->where([
                      'shop_element_id' => $element->id
                  ])
                  ->all();*/


            $catalogs = $this->shop_catalogs->
            where('shop_element_id', $element->id);


            foreach ($catalogs as $catalog) {
                $currency = Az::$app->cores->session->get('currency');
                $amount += $catalog->amount;
                //$item->price[] = $catalog->price;
                $prices[] = Az::$app->payer->currency2->convert($catalog->currency, $currency, $catalog->price);
                $price_olds[] = Az::$app->payer->currency2->convert($catalog->currency, $currency, $catalog->price_old);
            }
        }

        if ($item->is_multi) {
            $price_min = \Dash\Curry\min($prices);
            $price_max = \Dash\Curry\max($prices);

            $item->min_price = $price_min ?? null;
            $item->max_price = null;
            if ($price_min !== $price_max)
                $item->max_price = $price_max;
        } else {
            $item->new_price = $prices[0] ?? null;
            $item->price_old = $price_olds[0] ?? null;

            if ($item->new_price != null and $item->price_old != null) {
                $item->discount = ($item->new_price - $item->price_old) * 100 / $item->price_old;
                $item->discount = round($item->discount);
            }
        }
        //vdd($item->allProperties);
        $item->amount = $amount;
        //  vdd($item);
        return $item;
    }

    public function productOffers($shop_elements, $company_id = null)
    {
        $offers = [];

        /*$shop_elements = CoreElement::find()
            ->where([
                'shop_elements' => $shop_elements
            ])
            ->all();*/

        $shop_elements = $this->shop_elements
            ->where('shop_elements', $shop_elements);


        $shop_element_ids = $shop_elements->pluck("id");

        if ($company_id === null) {

            /*$shop_catalogs = ShopCatalog::find()
                ->where([
                    'shop_element_id' => $shop_element_ids
                ])->all();*/


            $shop_catalogs = $this->shop_catalogs
                ->whereIn('shop_element_id', $shop_element_ids->toArray());


        } else {
            /*$shop_catalogs = ShopCatalog::find()
                ->where([
                    'shop_element_id' => $shop_element_ids,
                    'user_company_id' => $company_id
                ])->all();*/


            $shop_catalogs = $this->shop_catalogs
                ->whereIn('shop_element_id', $shop_element_ids)
                ->where('user_company_id', $company_id);

        }

        $price_old = null;
        /** @var ShopCatalog[] $shop_catalogs */
        foreach ($shop_catalogs as $core_catalog) {
            $price_old = $core_catalog->price_old;

            $catalog_offers = $core_catalog->offer;
            if (is_array($catalog_offers)) {
                foreach ($catalog_offers as $catalog_offer) {
                    $end = strtotime($catalog_offer["end"]);
                    if (time() < $end)
                        $offers[] = ProductItem::statuses[$catalog_offer['offer']];
                }
            }
        }

        if ($price_old !== null)
            $offers[] = 'sale';

        $offers = array_unique($offers);

        return $offers;

    }

    public function productItemByCatalogId($catalog_id)
    {
        // $catalog = ShopCatalog::findOne($catalog_id);
        $catalog = $this->shop_catalogs
            ->where('id', $catalog_id)
            ->first();

        if ($catalog === null)
            return null;
        $product_item = $this->productItemByElementId($catalog->shop_element_id, $catalog->user_company_id);
        return $product_item;

    }

    public function singleProductItemByOptions($product_id, $company_id, $options = [])
    {
        $elements = $this->shop_elements->where('shop_elements', $product_id);
        $shop_element = null;
        foreach ($elements as $element) {
            $bool = true;
            if ($element->shop_option_ids == null) {
                $shop_element = $element;
                break;
            } else {
                foreach ($element->shop_option_ids as $option_id) {
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

        return $this->productItemByElementId($shop_element->id, $company_id);

    }

    public function productItemByElementId($element_id, $user_company_id = null)
    {
        //$shop_element = CoreElement::findOne($element_id);
        $shop_element = $this->shop_elements->where('id', $element_id)->first();

        if ($shop_element === null)
            return Az::error(__FUNCTION__, '$shop_element is Null');
        // $shop_product = CoreProduct::findOne($shop_element->shop_elements);
        $shop_product = $this->shop_products
            ->where('id', $shop_element->shop_elements)
            ->first();
        if ($shop_product === null)
            return Az::error(__FUNCTION__, '$shop_product is Null');

        //$core_category = CoreCategory::findOne($shop_product->shop_category_id);
        $core_category = $this->shop_categories->where('id', $shop_product->shop_category_id)->first();

        if ($core_category === null)
            return Az::error(__FUNCTION__, '$core_category is Null');

        //$core_brand = ShopBrand::findOne($shop_product->shop_brand_id);

        $core_brand = $this->core_brands
            ->where('id', $shop_product->shop_brand_id)
            ->first();

        /*if ($core_brand == null)
        {
            throw new ErrorException("$shop_product->shop_brand_id li brand bazadan ochib ketgan. shunaqa yozmasam bo'lmaydidaxay");
        }*/

        $productItem = new MultiProductItem();
        if ($user_company_id !== null)
            $productItem = new SingleProductItem();

        $productItem->id = $shop_element->id;
        $productItem->name = $shop_element->name;
        $productItem->categoryName = $core_category->name;
        $productItem->categoryUrl = ZUrl::to([
            '/customer/filtering-common/common',
            'id' => $core_category->id,
        ]);
        $productItem->title = $shop_product->title;
        $productItem->text = $shop_product->text;
        $productItem->brand = $core_brand->name ?? '';
        $productItem->url = "";
        $productItem->images = [];

        /*if ($shop_product->image !== null)
            foreach ($shop_product->image as $image)
                $productItem->images[] = '/uploaz/eyuf/CoreProduct/images/' . $shop_product->id . '/' . $image;*/
        if (\Dash\count($productItem->images) == 0)
            $productItem->images[] = $this->defaultProductImage;

        $productItem->properties = [];
        // vdd($shop_element->shop_option_ids);
        if (\Dash\count($shop_element->shop_option_ids))
            foreach ($shop_element->shop_option_ids as $option_id) {
                //$option = CoreOption::findOne($option_id);
                $option = $this->shop_options->where('id', $option_id)->first();
                if (empty($option))
                    return null;

                //$option_type = CoreOptionType::findOne($option->shop_option_types_id);

                $option_type = $this->shop_option_types
                    ->where('id', $option->shop_option_types_id)
                    ->first();

                $propertyItem = new PropertyItem();
                $propertyItem->name = $option_type->name;
                $propertyItem->branch = $option_type->getCoreOptionBranch()->name;
                $propertyItem->items = [$option->id => $option->name];

                $productItem->properties[] = $propertyItem;
            }

        if (!is_array($shop_product->shop_option_ids)) {
            $not_combination_option_ids = [];
        } else {

            /*
                   $not_combination_option_ids = array_filter($shop_product->shop_option_ids, function ($a) use ($core_category) {

                       //$core_option = CoreOption::findOne($a);
                       $core_option = $this->shop_options
                       ->where('id', $a)
                       ->first();

                      /* $shop_option_types = CoreOptionType::find()
                           ->where([
                               'id' => $core_option->shop_option_types_id
                           ])->one();


                       $shop_option_types = $this->shop_option_types
                           ->where('id', $core_option->shop_option_types_id)    ->first();




                       $option_type_json = $core_category->shop_option_type;
                       $a = false;
                       foreach ($option_type_json as $option_type) {
                           if ($option_type["shop_option_type"] == $shop_option_types->id)
                               if (!array_keys_exist('is_combination', $option_type))
                                   $a = true;
                       }
                       return $a;

                   });*/


            $not_combination_option_ids = array_filter($shop_product->shop_option_ids, function ($a) use ($core_category) {

                //$core_option = CoreOption::findOne($a);
                $core_option = $this->shop_options
                    ->where('id', $a)
                    ->first();

                /* $shop_option_types = CoreOptionType::find()
                     ->where([
                         'id' => $core_option->shop_option_types_id
                     ])->one();*/
                if (empty($core_option))
                    return false;

                $shop_option_types = $this->shop_option_types
                    ->where('id', $core_option->shop_option_types_id)->first();

                $option_type_json = $core_category->shop_option_type;
                $a = false;
                foreach ($option_type_json as $option_type) {
                    if ($option_type["shop_option_type"] == $shop_option_types->id)
                        if (!array_keys_exist('is_combination', $option_type))
                            $a = true;
                }
                return $a;

            });


        }


        //vdd($not_combination_option_ids);
        foreach ($not_combination_option_ids as $option_id) {

            //$option = CoreOption::findOne($option_id);
            $option = $this->shop_options
                ->where('id', $option_id)
                ->first();

            //$option_type = CoreOptionType::findOne($option->shop_option_types_id);


            $option_type = $this->shop_option_types->
            where('id', $option->shop_option_types_id)
                ->first();


            $propertyItem = new PropertyItem();
            $propertyItem->name = $option_type->name;
            $propertyItem->branch = $option_type->getCoreOptionBranch()->name;
            $propertyItem->items = [$option->id => $option->name];

            $productItem->allProperties[] = $propertyItem;
        }
        $productItem->allProperties = array_merge($productItem->allProperties, $productItem->properties);

        if (!$productItem->is_multi) {
            /* $catalog = ShopCatalog::find()->where([
                 'shop_element_id' => $element_id,
                 'user_company_id' => $user_company_id,
             ])->one();*/

            $catalog = $this->shop_catalogs
                ->where('shop_element_id', $element_id)
                ->where('user_company_id', $user_company_id)
                ->first();


            if ($catalog !== null) {
                $currency = Az::$app->cores->session->get('currency');
                $productItem->catalogId = $catalog->id;
                if ($catalog->price !== null)
                    $productItem->new_price = Az::$app->payer->currency2->convert($catalog->currency, $currency, $catalog->price);
                if ($catalog->price_old !== null)
                    $productItem->price_old = Az::$app->payer->currency2->convert($catalog->currency, $currency, $catalog->price_old);

                if ($productItem->new_price !== null and $productItem->price_old !== null) {
                    $productItem->discount = ($productItem->new_price - $productItem->price_old) * 100 / $productItem->price_old;
                }
            }
        }
        return $productItem;
    }

    public function getPropertsByCategory2($category_id = null, $is_combination = false)
    {

        //$shop_option_types = CoreOptionType::find()->all();
        $shop_option_types = $this->shop_option_types;
        if ($category_id) {

            //$core_category = CoreCategory::findOne($category_id);
            $core_category = $this->shop_categories->
            where('id', $category_id)
                ->first();

            /*
                        $shop_option_type_ids =$core_category->shop_option_type->map(function ($a) use ($is_combination) {
                            $b = ZArrayHelper::getValue($a, 'shop_option_type');
                            if ($is_combination && ZArrayHelper::keyExists('is_combination', $a))
                                return $b;

                            return $b;

                        });
            */

            $shop_option_type_ids = array_map(function ($a) use ($is_combination) {
                $b = ZArrayHelper::getValue($a, 'shop_option_type');
                if ($is_combination && ZArrayHelper::keyExists('is_combination', $a))
                    return $b;

                return $b;

            }, $core_category->shop_option_type);


            /*$shop_option_types = CoreOptionType::find()
                ->where([
                    'id' => $shop_option_type_ids
                ])
                ->all();*/


            $shop_option_types = $this->shop_option_types
                ->whereIn('id', $shop_option_type_ids);

        }

        $result = [];
        foreach ($shop_option_types as $shop_option_types) {

            $property_item = new PropertyItem();
            $property_item->name = $shop_option_types->name;

            /*  $core_option = CoreOption::find()
                  ->where([
                      'shop_option_types_id' => $shop_option_types->id
                  ])
                  ->all();*/


            $core_option = $this->shop_options
                ->where('shop_option_types_id', $shop_option_types->id);

            foreach ($core_option as $option)
                $property_item->items[$option->id] = $option->name;

            $result[] = $property_item;

        }

        return $result;
    }

    /**
     *
     * Function  getBranchesByCategory
     * @param null $category_id
     * @return  mixed
     */
    public function getBranchesByCategory($category_id = null)
    {
        if ($category_id == null) {
            //$branches = CoreOptionTypeBranch::find()->all();
            $branches = $this->core_option_branches;

        } else {
            //$category = CoreCategory::findOne($category_id);
            $category = $this->shop_categories
                ->where('id', $category_id)
                ->first();


            $branch_ids = array_map(function ($a) {
                if (array_key_exists('shop_option_type', $a))
                    if ($a["shop_option_type"] !== "")
                        //$shop_option_types = CoreOptionType::findOne($a["shop_option_type"]);
                        $shop_option_types = $this->shop_option_types
                            ->where('id', $a["shop_option_type"])
                            ->first();

                if (isset($shop_option_types))
                    return $shop_option_types->shop_option_branch_id;
            }, $category->shop_option_type);


            /*  $branch_ids = $category->shop_option_type->map(function ($a) {
                  if (array_key_exists('shop_option_type', $a))
                      if ($a["shop_option_type"] !== "")
                          //$shop_option_types = CoreOptionType::findOne($a["shop_option_type"]);
                          $shop_option_types = $this->shop_option_types
                              ->where('id', $a["shop_option_type"])
                              ->first();

                  if (isset($shop_option_types))
                      return $shop_option_types->shop_option_branch_id;
              });*/

            $branch_ids = array_unique($branch_ids);


            /*$branches = CoreOptionTypeBranch::find()
                ->where([
                    'id' => $branch_ids
                ])->all();*/


            $branches = $this->core_option_branches
                ->whereIn('id', $branch_ids);


        }
        return $branches;
    }


    /**
     *  $is_combination = false bo'lsa hammasini beradi true bo'lsa faqat is_combination true bo'lgan option_typelarni beradi
     * Function  propertsByCategory
     * @param null $category_id
     * @param bool $is_combination
     * @param null $branch_id
     * @return  array
     */
    public function propertsByCategory($category_id = null, $is_combination = false, $branch_id = null)
    {
        $result = [];
        /** @var ShopOptionType[] $shop_option_types */

        /*  $shop_option_types = CoreOptionType::find()
              ->all();*/

        $shop_option_types = $this->shop_option_types;

        if (!empty($category_id)) {

            //$core_category = CoreCategory::findOne($category_id);
            $core_category = $this->shop_categories->where('id', $category_id)->first();


            $shop_option_type_ids = array_map(function ($a) use ($is_combination) {
                if ($is_combination) {
                    if (array_key_exists('is_combination', $a)) {
                        if ($a['shop_option_type'] !== "")
                            return $a['shop_option_type'];
                    }
                } else {
                    if ($a['shop_option_type'] !== "")
                        return $a['shop_option_type'];
                }
            }, $core_category->shop_option_type);

            /*
                        $shop_option_types = collect($core_category->shop_option_type);
                        $shop_option_type_ids = $shop_option_types->map(function ($a) use ($is_combination) {
                            if ($is_combination) {
                                if (array_key_exists('is_combination', $a)) {
                                    if ($a['shop_option_type'] !== "")
                                        return $a['shop_option_type'];
                                }
                            } else {
                                if ($a['shop_option_type'] !== "")
                                    return $a['shop_option_type'];
                            }
                        });*/


            //vdd($shop_option_type_ids);


            /* $shop_option_types = CoreOptionType::find()
                 ->where([
                     'id' => $shop_option_type_ids
                 ])
                 ->all();*/


            $shop_option_types = $this->shop_option_types
                ->whereIn('id', $shop_option_type_ids);

        }
        //vdd($shop_option_types);
        if ($branch_id !== null)

            /* $shop_option_types = array_filter($shop_option_types, function ($a) use ($branch_id) {
                   if ($a->shop_option_branch_id === $branch_id)
                     return true;
                 else
                     return false;
             });*/

            $shop_option_types = $shop_option_types->filter(function ($a) use ($branch_id) {
                if ($a->shop_option_branch_id === $branch_id)
                    return true;
                else
                    return false;
            });


        //vdd($shop_option_types);
        foreach ($shop_option_types as $shop_option_types) {
            $property_item = new PropertyItem();
            $property_item->name = $shop_option_types->name;

            //$branch = CoreOptionTypeBranch::findOne($shop_option_types->shop_option_branch_id);
            $branch = $this->core_option_branches->where('id', $shop_option_types->shop_option_branch_id)->first();


            $property_item->branch = $branch->name;

            /*
                $core_option = CoreOption::find()->where([
                    'shop_option_types_id' => $shop_option_types->id
                ])->all();*/


            $core_option = $this->shop_options
                ->where('shop_option_types_id', $shop_option_types->id);

            foreach ($core_option as $option) {
                $property_item->items[$option->id] = $option->name;

            }

            $result[] = $property_item;
        }
        return $result;
    }

    public function is_multi($core_category)
    {
        $option_type_json = $core_category->shop_option_type;
        $a = false;

        foreach ($option_type_json as $option_type) {
            if (array_keys_exist('is_combination', $option_type))
                $a = true;
        }
        return $a;
    }

#endregion

#region filter
    /*
    *** filter: ***
    * [
    * "color" => [
    *  '0' => 12,
    *  '1' => 22,
    * ],
    * "memory" => [
    *  '0' => 12,
    *  '1' => 22,
    * ]
    * ]
    *
    */

    public function setFilter($properties)
    {
        if (!empty($properties)) {
            Az::$app->cores->session->set('filter', $properties);
        } else {
            Az::$app->cores->session->set('filter', []);
        }
    }

    public function setPriceFilter($price_filter)
    {
        if (!empty($price_filter)) {
            Az::$app->cores->session->set('price_filter', $price_filter);
        } else {
            Az::$app->cores->session->set('price_filter', []);
        }
    }

    public function filter($shop_products)
    {
        $filter = Az::$app->cores->session->get('filter');

        /*if ($this->isCLI())
        {

        }*/
//        AZ::$app->utility->monolog->log('asdf');
        if (!empty($filter)) {
            $proItems = $this->session2propertyItems($filter);
            //vdd($proItems);
            $result_products = [];
            foreach ($shop_products as $product) {
                $bors = [];
                foreach ($proItems as $key => $proItem) {
                    $bors[$key] = false;
                    if (!empty($proItem->items)) {
                        foreach ($proItem->items as $key1 => $items) {
                            if ($product->shop_option_ids !== null)
                                if (in_array($key1, $product->shop_option_ids))
                                    $bors[$key] = true;
                        }
                    }
                }
                $exist = true;
                foreach ($bors as $bor) {
                    $exist = ($exist and $bor);

                }
                if ($exist)
                    $result_products[] = $product;
            }
            return collect($result_products);
        } else {
            return $shop_products;
        }
    }

    public function priceFilter($shop_products)
    {
        $priceFilter = Az::$app->cores->session->get('price_filter');

        /* if (isset($priceFilter) and !empty($priceFilter)) {
             $shop_products = array_filter($shop_products, function ($shop_product) use ($priceFilter) {
                 $shop_catalogs = $this->getCatelogsByProductId($shop_product->id);
                 if ($shop_catalogs == null)
                     return false;

                 foreach ($shop_catalogs as $core_catalog) {
                     if ($priceFilter[0] <= $core_catalog->price and $core_catalog->price <= $priceFilter[1])
                         return true;
                 }

                 return false;
             });

            /* $shop_categories = $shop_products->map(function ($shop_product) use($priceFilter){
                     $core_category = $shop_product->shop_category;

                     return $core_category;

             });

             $shop_categories = $shop_categories->unique();

         } */


        if (isset($priceFilter) and !empty($priceFilter)) {

            $shop_products = $shop_products->filter(function ($shop_product) use ($priceFilter) {

                $shop_catalogs = $this->getCatelogsByProductId($shop_product->id);

                if ($shop_catalogs == null)
                    return false;

                foreach ($shop_catalogs as $core_catalog) {
                    if ($priceFilter[0] <= $core_catalog->price and $core_catalog->price <= $priceFilter[1])
                        return true;
                }

                return false;
            });

            /* $shop_categories = $shop_products->map(function ($shop_product) use($priceFilter){
                     $core_category = $shop_product->shop_category;

                     return $core_category;

             });

             $shop_categories = $shop_categories->unique();*/

        }

        return $shop_products;
    }

    public function getCatelogsByProductId($shop_elements)
    {
        /* $shop_elements = CoreElement::find()
             ->where([
                 'shop_elements' => $shop_elements
             ])->all();*/

        $shop_elements = $this->shop_elements
            ->where('shop_elements', $shop_elements);


        /*    if ($shop_elements == null)
                  return false;
              $shop_element_ids = array_map(function ($a) {
                  return $a->id;
              }, $shop_elements);*/

        if ($shop_elements == null)
            return false;
        $shop_element_ids = $shop_elements->map(function ($a) {
            return $a->id;
        });


        /*        $shop_catalogs = ShopCatalog::find()
                    ->where([
                        'shop_element_id' => $shop_element_ids
                    ])->all();*/


        $shop_catalogs = $this->shop_catalogs
            ->whereIn('shop_element_id', $shop_element_ids);

        // vdd($shop_catalogs);
        return $shop_catalogs;
    }

    public function session2propertyItems($properties)
    {

        //vdd($properties);
        $proItems = [];
        if ($properties != null) {

            foreach ($properties as $key => $property) {
                $proItem = new PropertyItem();
                $proItem->name = $key;
                if (is_array($property)) {
                    foreach ($property as $option_id) {
                        $proItem->items[$option_id] = '';
                    }
                    $proItems[] = $proItem;
                }
            }
        }
        return $proItems;
    }

    public function filterFormItems($category_id = null)
    {

        //$products = CoreProduct::find()->all();
        $products = $this->shop_products;

        if ($category_id)
            /*$products = CoreProduct::find()
                ->where([
                    'shop_category_id' => $category_id
                ])->all();*/

            $products = $this->shop_products
                ->where('shop_category_id', $category_id);

        if (!empty($products)) {

            /* $product_ids = array_unique(array_map(function ($a) {
                 return $a->id;
             }, $products));*/

            $product_ids = $products->map(function ($a) {
                return $a->id;
            })->unique();

            /*$elements = CoreElement::find()
                ->where([
                    'shop_elements' => $product_ids
                ])->all();
                */

            $elements = $this->shop_elements
                ->whereIn('shop_elements', $product_ids);


            /*
             $element_ids = array_unique(array_map(function ($a) {
                return $a->id;
            }, $elements));*/


            $element_ids = $elements->map(function ($a) {
                return $a->id;
            })->unique();


            /*$shop_catalogs = ShopCatalog::find()
                ->where([
                    'shop_element_id' => $element_ids
                ])->all();*/

            $shop_catalogs = $this->shop_catalogs
                ->whereIn('shop_element_id', $element_ids);


            $currency = Az::$app->cores->session->get('currency');


            /*$catalog_prices = array_map(function ($a) use ($currency) {
                return Az::$app->payer->currency2->convert($a->currency, $currency, $a->price);
            }, $shop_catalogs);   */

            $catalog_prices = $shop_catalogs->map(function ($a) use ($currency) {
                return Az::$app->payer->currency2->convert($a->currency, $currency, $a->price);
            });


            //join ishlatish kerak gavna kod
            $min_price = \Dash\Curry\min($catalog_prices) ?? 0;
            $max_price = \Dash\Curry\max($catalog_prices) ?? 0;
        }


        $config = new Config();
        $app = new ALLApp();
        $app->configs = $config;


        //brand
        //ishlaydi faqat checkboxgroupdagi xato tuzatilsa ochib qo'yish kerak. hali filter qilmaydi.
        $column = new Form();
        $brand_data = [];

        /** @var ShopBrand[] $allBrands */
        //$allBrands = ShopBrand::find()->all();
        $allBrands = $this->core_brands;


        foreach ($allBrands as $brand) {
            $a = ZArrayHelper::getValue($brand->image, 0);
            //vdd('/uploaz/' . App . '/ShopBrand/image/' . $brand->id . '/' . $a);
            //$brand_data[$brand->id] = "<img  src='/uploaz/".App."/ShopBrand/image/".$brand->id."/$a' alt=' '>";
            $brand_data[$brand->id] = ZImageWidget::widget([
                'config' => [
                    'url' => '/uploaz/' /*. App*/ . '/ShopBrand/image/' /*. $brand->id . '/' */ . $a,
                    'class' => "ml-20",
                    'width' => '90%',
                ]
            ]);
        }
        //vdd($brand_data);
        $column->widget = ZMarketDropdownWidget::class;
        $column->options = [
            'config' => [
                'accordion' => true,
                'content' => ZEasySelectableWidget::widget([
                    'data' => $brand_data,
                    'config' => [
                        'class' => "d-flex",
                        'cols' => ZEasySelectableWidget::cols['3'],
                    ],
                    'name' => 'brand',

                    //'value' => ['19'],
                    /*  'config' => [
                          'container' => 'brandCheckBox'
                      ]*/
                ]),
                'title' => Az::l('Марка')
            ]
        ];
        //$column->data = $property->items;
        $app->columns['brand'] = $column;

        //price
        if (null !== $products) {
            //man qoshdim buni min price bomasa chiqmidi slider widget Azimjon
            if (!empty($min_price && $max_price)) {

                $item = new ProductItem();
                //$currency = ZProductItem::
                $column = new Form();
                $column->widget = ZKSliderIonWidget::class;
                $column->options = [
                    'name' => 'price_filter',
                    'config' => [
                        'type' => 'double',
                        'skin' => 'modern',
                        'min' => $min_price,
                        'max' => $max_price,
                        'from' => $min_price,
                        'to' => $max_price,
                        //'postfix' => " $",
                        'inputs_show' => true,
                        'title' => "Цена в ($item->currency)",
                    ],
                ];
                $app->columns['price'] = $column;
            }
        }
        //end price

        //properties
        $branches = $this->getBranchesByCategory($category_id);
        $proporties = Az::$app->market->product->propertsByCategory($category_id);


        $checked_ids = [];
        $cart_array = [];
        if (Az::$app->cores->session->get('filter'))
            $cart_array = Az::$app->cores->session->get('filter');

        foreach ($cart_array as $sub_arr)
            if (is_array($sub_arr))
                $checked_ids = ZArrayHelper::merge($checked_ids, $sub_arr);

        //vdd($checked_ids);
        foreach ($branches as $key1 => $branch) {

            $category = $this->propertsByCategory($category_id, false, $branch->id);

            //vdd($category);


            $content = '';
            foreach ($category as $key => $property) {
                //vdd($property->items);
                $content .= ZGAccordionWidget::widget([
                    'config' => [
                        'content' => ZMCheckboxGroupWidget::widget([
                            'data' => $property->items,
                            'name' => "ZDynamicModel[$key1.$key]",
                            'value' => $checked_ids,
                            'config' => [
                                'class' => 'optionCheckBoxes',
                                'textColor' => ZColor::color['black'],
                            ]
                        ]),
                        'title' => $property->name,
                        'class' => ''
                    ]
                ]);
            }
            $column = new Form();
            $column->widget = ZMarketDropdownWidget::class;
            $column->options = [
                'config' => [
                    'content' => $content,
                    'title' => $branch->name,
                    'class' => '',
                    'onlyOneActive' => 0,
                    'initFirstActive' => 0,
                    'hideControl' => true,
                    'openNextOnClose' => true
                ]
            ];
            //$column->data = $property->items;
            $app->columns['branches' . $key1] = $column;
        }

        $column = new Form();
        $column->widget = ZHInputWidget::class;
        $column->options = [
            'name' => 'category_id',
            'value' => $category_id,
            'config' => [
                'type' => ZHInputWidget::type['hidden'],
            ]
        ];
        $app->columns['hidden_input'] = $column;

        $app->cards = [];

        return Az::$app->forms->former->model($app);
    }

    public function singleProductForm($product_id = null)
    {
        $app = new ALLApp();

        $proporties = Az::$app->market->product->propertsByCategory($product_id);

        $config = new Config();

        $app->configs = $config;
        foreach ($proporties as $key => $property) {

            $column = new Form();
            //$column->title = $property->name;
            $column->widget = ZGAccordionWidget::class;
            $column->options = [
                'config' => [
                    'content' => ZMCheckboxGroupWidget::widget([

                        'data' => $property->items,
                        'name' => "ZDynamicModel[$key]",
                        'value' => ['4']
                    ]),
                    'title' => $property->name
                ]
            ];
            //$column->data = $property->items;
            $app->columns[] = $column;
        }

        $app->cards = [];
        return Az::$app->forms->former->model($app);
    }

    public function sortProduct($data, $filter, $sort)
    {
        ZArrayHelper::multisort($data, $filter, $sort);
        return $data;
    }
#endregion
#region compare
    public function getCompareProductItems()
    {

        $compares = Az::$app->cores->session->get('compare');

        //testlash uchun
        //$compares = [18, 19];
        $productItems = [];
        if ($compares) {
            foreach ($compares as $product_id) {
                $productItem = $this->product($product_id);
                $productItems[] = $productItem;
            }
        }
        return $productItems;
    }

    public function inCompare($product_id)
    {

        $compares = $this->sessionGet('compare');
        $isCompare = false;
        if ($compares) {
            foreach ($compares as $compare) {
                if ($compare == $product_id)
                    $isCompare = true;
            }
        }

        return $isCompare;
    }
#endregion
#region history
    public function getViewedProductItems()
    {

        $compares = Az::$app->cores->session->get('viewed');
        $productItems = [];
        if ($compares) {
            foreach ($compares as $product_id) {
                $productItem = $this->product($product_id);
                $productItems[] = $productItem;
            }
        }
        return $productItems;
    }
#endregion

#region wish
    public function getWishProductItems()
    {
        $wish_list = Az::$app->cores->session->get('wishList');
        //if ($this->isCLI())

        $productItems = [];
        if ($wish_list) {
            foreach ($wish_list as $product_id) {
                $productItem = $this->product($product_id);
                $productItems[] = $productItem;
            }
        }
        return $productItems;
    }

    /**
     *
     * Function  inWish
     * @param $product_id
     * @return  bool
     */
    public function inWish($product_id)
    {

        $wishes = $this->sessionGet('wishList');
        $isWish = false;
        if ($wishes) {
            foreach ($wishes as $wish) {
                if ($wish == $product_id) {
                    $isWish = true;
                }
            }
        }

        return $isWish;
    }
#endregion
#region cart

    /*
     *** cart: ***
     * [
     *   [
     *      'catalog_id' => 3,
     *      'amount' => 1,
     *   ],
     *   [
     *      'catalog_id' => 3,
     *      'amount' => 1,
     *   ]
     * ]
     *
     */


    /**
     *
     * Function  catalogsByElement
     * @param $productId
     * @param array $options
     * @return  array|bool|void
     */
    public function catalogsByElement($productId, $options = [])
    {
        if ($productId === null) return [];
        $shop_product = $this->shop_products->where('id', $productId)->first();

        //$shop_product = CoreProduct::findOne($productId);


        /*        $elements = CoreElement::find()
                    ->where([
                        'shop_elements' => $productId,
                        //'shop_option_ids' => json_encode($options)
                    ])
                    ->all();
        */


        $elements = $this->shop_elements
            ->where(
                'shop_elements', $productId);
        //'shop_option_ids' => json_encode($options))


        $shop_element = null;
        foreach ($elements as $element) {
            $bool = true;
            foreach ($element->shop_option_ids as $option_id) {
                if (!in_array($option_id, $options)) {
                    $bool = false;
                    continue;
                }
            }
            if ($bool) {
                $shop_element = $element;
                break;
            }
        }


        if ($shop_element === null)
            return Az::error(__FUNCTION__ . '$element not found');
        /* return $elements;*/
        /*foreach ($elements as $element) {
            if ($element->shop_option_ids === $options)
                return $element->id;
        }*/


        /** @var ShopCatalog[] $catalogs */

        /*$catalogs = ShopCatalog::find()
            ->where([
                'shop_element_id' => $shop_element->id
            ])
            ->all();*/


        $catalogs = $this->shop_catalogs
            ->where('shop_element_id', $shop_element->id);


        $items = [];
        foreach ($catalogs as $catalog) {

            //$company = UserCompany::findOne($catalog->user_company_id);
            $company = $this->core_companies->where('id', $catalog->user_company_id)->first();

            /** @var CompanyCardItem $companyItem */
            $companyItem = new CompanyCardItem();
            $companyItem->id = $catalog->id;
            $companyItem->name = $company->name ?? null;
            $companyItem->amount = $catalog->amount;
            $companyItem->title = $company->title ?? null;
            $companyItem->measure = $shop_product->_measure[$shop_product->measure] ?? $shop_product->_measure['pcs'];
            $companyItem->measureStep = ProductItem::measureStep[$shop_product->measure] ?? ProductItem::measureStep['pcs'];
            $companyItem->url = ZUrl::to([
                'customer/markets/index',
                'id' => $company->id ?? null
            ]);
            if (\Dash\count($company->photo))
                $companyItem->image = '/uploaz/eyuf/UserCompany/photo/' . $company->id . '/' . $company->photo[0];

            $item = new ProductItem();
            $companyItem->currency = $item->currency;
            $companyItem->currencyType = $item->currencyType;

            $currency = Az::$app->cores->session->get('currency');
            $companyItem->new_price = Az::$app->payer->currency2->convert($catalog->currency, $currency, $catalog->price);
            $companyItem->price_old = Az::$app->payer->currency2->convert($catalog->currency, $currency, $catalog->price_old);


            $cart = Az::$app->cores->session->get('cart');
            if ($cart)
                foreach ($cart as $item) {
                    if ($item['catalog_id'] == $catalog->id)
                        $companyItem->cart_amount = $item['amount'];
                }

            $items[] = $companyItem;
        }

        return $items;
    }

    /**
     * @param $product_id
     * @param int $amount
     * @param ShopOption[] $options
     * @return int
     * @throws \Exception
     */

    public function addToCart($catalog_id, $amount = 1)
    {
        return $this->addOrSetCart($catalog_id, $amount, $this::cart_type['add']);
    }

    public function setToCart($catalog_id, $amount = 1)
    {
        return $this->addOrSetCart($catalog_id, $amount, $this::cart_type['set']);
    }

    public function addOrSetCart($catalog_id, $amount, $type)
    {
        $cart = Az::$app->cores->session->get('cart');
//        if(!isset($element))
        $new_cart = [];
        $total_amount = 0;
        if ($cart) {
            $bor = false;
            foreach ($cart as $cart_item) {
                if ($cart_item['catalog_id'] === $catalog_id) {
                    $bor = true;
                    switch ($type) {
                        case 'add':
                            $cart_item['amount'] += $amount;
                            break;
                        case 'set':
                            $cart_item['amount'] = $amount;
                    }
                }
                if ($cart_item['amount'] > 0)
                    $new_cart[] = [
                        'catalog_id' => $cart_item['catalog_id'],
                        'amount' => $cart_item['amount']
                    ];
                $total_amount += $cart_item['amount'];
            }
            if (!$bor) {
                $new_cart[] = [
                    'catalog_id' => $catalog_id,
                    'amount' => $amount
                ];
                $total_amount += $amount;
            }
        } else {
            if ($amount > 0)
                $new_cart[] = [
                    'catalog_id' => $catalog_id,
                    'amount' => $amount
                ];
            $total_amount += $amount;
        }

        Az::$app->cores->session->set('cart', $new_cart);


        $nechta_catalog_borligi = count($new_cart);


        return $nechta_catalog_borligi;
    }

    //cart ni productitemlarga aylantirib beradi
    public function cartOrders()
    {
        $cart = $this->sessionGet('cart');
        //$cart = Az::$app->cores->session->get('cart');

        //vdd($cart);
        $productItems = [];

        if ($cart) {
            foreach ($cart as $item) {
                $productItem = $this->productItemByCatalogId($item['catalog_id']);
                if ($productItem === null)
                    continue;
                $productItem->cart_amount = $item['amount'];
                $productItems[] = $productItem;
            }
        }
        return $productItems;
    }

    #endregion

    #region combinate element

    //category edit bo'lganda element name lari boshqatdan generatsiya bo'lishi kerak
    public function afterEditCorecategory($model_id)
    {

        ZArrayHelper::setValue(Az::$app->params, 'paramIsUpdate', true);

        /*$shop_products = CoreProduct::find()
            ->where([
                'shop_category_id' => $model_id
            ])
            ->all();*/


        $shop_products = $this->shop_products
            ->where('shop_category_id', $model_id);

        foreach ($shop_products as $shop_product) {
            Az::$app->market->product->saveElements($shop_product);
        }

        return "ok";
    }

    public function saveElements($model)
    {
        $category = $this->shop_categories->where('id', $model->shop_category_id)->first();

        $shop_option_type_ids = [];
        if ($category !== null) {
            $shop_option_type = json_decode($category['shop_option_type'], true);
            foreach ($shop_option_type as $item) {
                if (array_keys_exist('is_combination', $item)) {
                    $shop_option_type_ids[] = (int)$item['shop_option_type'];
                }
            }
        }

        if (empty($shop_option_type_ids)) {
            $element = new ShopElement();
            if (ZArrayHelper::getValue(Az::$app->params, 'paramIsUpdate'))
                $element = $this->shop_elements
                    ->where('id', $model->id)
                    ->first();

            $element->id = $model->id;
            $element->active = true;

            $element->name = '';

            $element->name .= $category['name'] . ', ' . $model->name . ', ';

            $element->save();

            return 0;
        }

        $combination_shop_option_types = $this->shop_option_types
            ->whereIn('id', $shop_option_type_ids);

        $initial_arr = [];
        foreach ($combination_shop_option_types as $shop_option_types) {
            $options = $this->shop_options
                ->where('shop_option_type_id', $shop_option_types['id']);

            $sub_arr = [];
            foreach ($options as $option)
                $sub_arr[] = $option['id'];

            $initial_arr[] = $sub_arr;
        }

        $result_arr = $this->combinations($initial_arr);

        foreach ($result_arr as $item) {
            $element = new ShopElement();

            if (ZArrayHelper::getValue(Az::$app->params, 'paramIsUpdate')) {
                $elements = $this->shop_elements
                    ->where('id', $model->id);

                $element = null;
                foreach ($elements as $e) {
                    $bool = true;
                    foreach (json_decode($e['shop_option_ids'], true) as $option_id) {
                        if (!in_array($option_id, $item)) {
                            $bool = false;
                            continue;
                        }
                    }
                    if ($bool) {
                        $element = $e;
                        break;
                    }

                }
                if ($element == null) {
                    vdd($item['id'] . "li shunaqa element topilmadi bazadan");
                }
            }

            $element->shop_option_ids = $item;
            $element->shop_product_id = $model->id;
            $element->active = true;

            $element->name = '';

            /*$ops = CoreOption::find()
                ->where([
                    'id' => $item
                ])
                ->all();*/

            $ops = $this->shop_options
                ->whereIn('id', $item);


            $element->name .= $category['name'] . ', ' . $model->name . ' ';
            foreach ($ops as $key => $op) {
                $element->name .= $op['name'];
                if (\Dash\count($ops) - 1 !== $key)
                    $element->name .= '/';
            }
            $element->save();
        }
    }

    public function combinations($arrays, $i = 0)
    {
        if (!isset($arrays[$i])) {
            return array();
        }
        if ($i == count($arrays) - 1) {
            return $arrays[$i];
        }

        // get combinations from subsequent arrays
        $tmp = $this->combinations($arrays, $i + 1);
        $result = [];

        // concat each array from tmp with each element from $arrays[$i]
        foreach ($arrays[$i] as $v) {
            foreach ($tmp as $t) {
                $result[] = is_array($t) ?
                    array_merge(array($v), $t) :
                    array($v, $t);
            }
        }

        return $result;
    }

#endregion

#region order and shipment
    public function OrderForm()
    {
        $config = new Config();

        $app = new ALLApp();
        $app->configs = $config;

        //address
        $column = new Form();
        $column->dbType = dbTypeInteger;
        $column->widget = ZSelect2Widget::class;
        $column->options = [
            'data' => Az::$app->market->address->getUserAddresses(11),
            'name' => 'address_id',
            'label' => ShopShipment::shipment_type
        ];
        $app->columns['address_id'] = $column;
        //address

        //shipment_type_id
        $column = new Form();
        $column->dbType = dbTypeString;
        $column->widget = ZMRadioWidget::class;
        $column->options = [
            'config' => [
                'hasPlace' => true
            ],
            'data' => ShopShipment::shipment_type,
            'name' => 'shipment_type',
        ];
        $column->rules = [
            ['zetsoft\\system\\validate\\ZRequiredValidator']
        ];
        $app->columns['shipment_type'] = $column;
        //shipment_type_id

        //contact
        $column = new Form();
        $column->dbType = dbTypeJsonb;
        $column->widget = ZFormWidget::class;
        $column->valueWidget = ZFormViewWidget::class;
        $column->options = [
            'config' => [
                'formClass' => 'zetsoft\\former\\auth\\AuthContactForm',
                'title' => Az::l("Ваши контакты"),
                'titleClass' => ZColor::color['success-color']
            ],
        ];
        $column->valueOptions = [
            'config' => [
                'formClass' => 'zetsoft\\former\\auth\\AuthContactForm',

            ],
        ];
        $app->columns['contact_info'] = $column;
        //contact

        $app->cards = [];
        return Az::$app->forms->former->model($app);
    }

    public function getOwnShipments($operator_id)
    {
        $model = new ShopShipment();

        $model->configs->query = ShopShipment::find()
            ->select('core_shipment.*, shop_order.*')
            ->leftJoin('shop_order', 'shop_order.id = core_shipment.order_id')
            ->where(['shop_order.status' => 'checking'])
            ->andWhere(['shop_order.operator_id' => $operator_id])
            ->with('core_shipment');

        return $model;
    }

#endregion

    /**
     *
     * Function  getProductItemForm
     * @param null $category_id
     * @return  array
     */


#region History

    /**
     *
     * Function  getActiveOrderItems
     * Statusi new bo'lgan orderItemlarni qaytaradi
     * @param $user_id
     * Orderlari qaytariladigan user id si
     * Agar $user_id berilmasa barcha new statusli orderlarni qaytaradi
     */
    public function getActiveOrderItems($user_id = null)
    {

    }


#endregion
}
