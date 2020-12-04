<?php

/**
 * Author: Jobir Yusupov
 * Date:    07.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 * @license JaloliddinovSalohiddin
 * @license OtabekNosirov
 * @license AkromovAzizjon
 *
 */

namespace zetsoft\service\market;

use zetsoft\dbitem\data\FormDb;
use zetsoft\dbitem\shop\ProductItem;
use zetsoft\dbitem\shop\PropertyItem;
use zetsoft\dbitem\data\ALLApp;
use zetsoft\dbitem\data\Config;
use zetsoft\dbitem\data\Form;
use zetsoft\models\shop\ShopBrand;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopCategory;
use zetsoft\models\shop\ShopElement;
use zetsoft\models\shop\ShopOption;
use zetsoft\models\shop\ShopOptionBranch;
use zetsoft\models\shop\ShopProduct;
use zetsoft\system\assets\ZColor;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\widgets\images\ZImageWidget;
use zetsoft\widgets\incores\ZMCheckboxGroupWidget;
use zetsoft\widgets\inputes\ZBootstrapBorderCheckboxGroupWidget;
use zetsoft\widgets\inputes\ZBootstrapBorderGroupWidgetM;
use zetsoft\widgets\inputes\ZBootstrapImgCheckboxGroupWidgetM;
use zetsoft\widgets\inputes\ZHInputWidget;
use zetsoft\widgets\inputes\ZKSliderIonWidget;
use zetsoft\widgets\navigat\ZGAccordionWidget;
use zetsoft\widgets\navigat\ZMarketDropdownWidget;
use function Dash\where;
use function Spatie\SslCertificate\length;

class Filter extends ZFrame
{

    public $shop_products;
    public $shop_elements;
    public $shop_catalogs;
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
        $this->httpGet('category_id');
        $this->core_brands = collect(ShopBrand::find()->asArray()->all());
        $this->shop_products = collect(ShopProduct::find()->asArray()->all());
        $this->shop_elements = collect(ShopElement::find()->asArray()->all());
        $this->shop_catalogs = collect(ShopCatalog::find()->asArray()->all());
        $this->core_options = collect(ShopOption::find()->asArray()->all());
        $this->core_option_branches = collect(ShopOptionBranch::find()->asArray()->all());
        parent::init();
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

    //[12,10000]
    public function setPriceFilter($price_filter)
    {
        if (!empty($price_filter)) {
            Az::$app->cores->session->set('price_filter', $price_filter);
        } else {
            Az::$app->cores->session->set('price_filter', []);
        }
    }

    //[1,2,3]
    public function setBrandFilter($brand_filter)
    {
        if (!empty($brand_filter)) {
            Az::$app->cores->session->set('brand_filter', $brand_filter);
        } else {
            Az::$app->cores->session->set('brand_filter', []);
        }
    }

    public function filter($products)
    {
        $filter_properties = Az::$app->cores->session->get('filter');

        //vdd($filter_properties);
        /*$filter_properties = [
             0 => [
                 0=> 14
             ]
         ];*/
        /*$filter_properties = $filter;*/

        $filter_brands = Az::$app->cores->session->get('brand_filter');
        //vdd($filter_brands);
        if (!empty($filter_properties)) {
            $proItems = $this->session2propertyItems($filter_properties);
            foreach ($proItems as $proItem) {
                $products = $products->filter(function ($product, $key) use ($proItem) {
                    $option_ids = json_decode($product['shop_option_ids'], true);
                    if (empty($option_ids))
                        return false;
                    if (count(array_intersect(array_keys($proItem->items), $option_ids)) !== 0) {
                        return true;
                    } else {
                        return false;
                    }
                });
            }
        }
        //brand bo'yicha filter
        if (!empty($filter_brands))
            $products = $products->whereIn('shop_brand_id', $filter_brands);

        return $products;
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


    public function testFilterBrands($company_id, $brand_id)
    {

        $branches = $this->getBranches();

        $models = [];
        $allBrands = [
            "TomFord.png",
            "bmw.jpg",
            "huawei.jpg",
            "TommyHilfiger.png",
            "gucci.png",
            "levis.png",
            "mercedesbenz.jpg",
            "lg.png",
            "lg.jpg",
            "HP-logo-2010–2012.jpg",
            "samsung.jpg",
            "asus-6630.svg",
            "apple.jpg",
            "acer.jpg",
            "lenov.png",
            "xiaomi-Mi-red-banner-800x450.jpg",
            "xiaomi-Mi-red-banner-800x450.jpg",
        ];
        $brand_data = [];
        foreach ($allBrands as $brand) {
            $brand_data[] = ZImageWidget::widget([
                'config' => [
                    'url' => '/uploaz/ShopBrand/image/' . $brand,
                    'class' => "ml-20",
                    'width' => '90%',
                ]
            ]);
        }
        $i = 0;
        foreach ($branches as $key => $value) {
            $i++;
            $app = new ALLApp();
            $app->configs = new Config();
            $items = [];
            if ($i == 1) {
                $item = new FormDb();
                $item->title = 'Branch 1';
                $item->widget = ZMarketDropdownWidget::class;
                $item->options = [
                    'config' => [
                        'accordion' => true,
                        'class' => 'd-flex flex-wrap',
                        'content' => ZBootstrapBorderCheckboxGroupWidget::widget([
                            'data' => $brand_data,
                            'config' => [
                                'class' => 'col-md-4',
                                'WHClass' => 'w-70 h-70',
                                'imgSize' => ZBootstrapBorderCheckboxGroupWidget::size['12'],
                            ],
                            'name' => 'brand',]),
                        'title' => Az::l('Марка')
                    ]
                ];
                $items[] = $item;
            }
            if ($i != 1) {
                switch ($i) {
                    case 3:
                        $t1 = 'Размер';
                        $t2 = 'Вес';
                        $data1 = [
                            '120x100',
                            '150x60',
                        ];
                        $data2 = [
                            '200 гр',
                            '500 гр',

                        ];
                        break;

                    case 2:
                        $t1 = 'Оперативная память';
                        $t2 = 'Тип экрана';
                        $data1 = [
                            '4 GB',
                            '8 GB',
                        ];
                        $data2 = [
                            'Led',
                            'OLED',

                        ];
                        break;

                }
                $item = new FormDb();
                $item->title = $t1;
                $item->widget = ZGAccordionWidget::class;
                $item->options = [
                    'config' => [
                        'title' => $t1,
                        'content' => ZMCheckboxGroupWidget::widget([
                            'value' => 2,
                            'name' => 'options',
                            'data' => $data1,
                            'config' => [
                                'type' => ZMCheckboxGroupWidget::type['MaterialCheckbox'],
                            ]
                        ])
                    ]
                ];

                $items[] = $item;
                $item = new FormDb();
                $item->title = $t2;
                $item->widget = ZGAccordionWidget::class;
                $item->options = [
                    'config' => [
                        'title' => $t2,
                        'content' => ZMCheckboxGroupWidget::widget([
                            'value' => 3,
                            'name' => 'options',
                            'data' => $data2
                        ])
                    ]
                ];

                $items[] = $item;
            }


            $app->columns = $items;
            $app->cards = [];
            $model = Az::$app->forms->former->model($app);
            $models[$value] = $model;
        }

        return $models;
    }

    #region getBranches
    public function testgetBranches()
    {
        $data = $this->getBranches();
        vd($data);
    }

    public function getBranches()
    {
        $branches = [];

        $models = ShopOptionBranch::find()->all();

        foreach ($models as $model) {
            $branches[$model->id] = $model->name;
        }

        return $branches;
    }
    #endregion

    #region getCategoryBrandsVue

    /**
     *
     * Function  getCategoryBrandsVue
     * @param null $categoryId
     * @return  ShopBrand[]|null
     * @author AzimjonToirov
     */
    public function getCategoryBrandsVue($categoryId = null)
    {
        $shop_category = ShopCategory::findOne($categoryId);
        if ($shop_category)
            return ShopBrand::findAll($shop_category->shop_brand_ids);

        return null;
    }

    #endregion

    #region filterFormItems

    public function testFilterFormItems()
    {
        $data = $this->filterFormItems(215);
        vd($data);
    }

    /**
     *
     * Function  filterFormItems
     * @param $category_id
     * @param array $filter
     * @param array $price_old_value
     * @param array $brand_checked_ids
     * @param null $company_id
     * @return  \zetsoft\system\actives\ZDynamicModel|null
     * @throws \Exception
     *
     * @license AzimjonToirov
     */
    public function filterFormItems($category_id, $filter = [], $price_old_value = [], $brand_checked_ids = [], $company_id = null)
    {

        $products = null;
        if ($category_id !== null)
            $products = $this->shop_products
                ->where('shop_category_id', $category_id);

        if (!empty($products)) {

            $elements = $this->shop_elements
                ->whereIn('shop_product_id', $products->pluck('id'));

            $shop_catalogs = $this->shop_catalogs
                ->whereIn('shop_element_id', $elements->pluck('id'));

            if ($company_id !== null)
                $shop_catalogs = $shop_catalogs->where('user_company_id', $company_id);

            $catalog_prices = $shop_catalogs->pluck('price');

            $currency = Az::$app->cores->session->get('currency');

            $min_price = Az::$app->payer->currency2->convert(ShopCatalog::currency['UZS'], $currency, $catalog_prices->min());

            $max_price = Az::$app->payer->currency2->convert(ShopCatalog::currency['UZS'], $currency, $catalog_prices->max());
        }


        $config = new Config();
        $app = new ALLApp();
        $app->configs = $config;

        //brand
        $column = new Form();
        $brand_data = [];

        $shop_category = ShopCategory::findOne($category_id);

        //vdd($shop_category);
        if ($shop_category === null)
            return null;
        if ($shop_category->shop_brand_ids == null)
            return null;


        $allBrands = ShopBrand::findAll($shop_category->shop_brand_ids);
        //vdd($allBrands);

        /*$allBrands = $this->core_brands;*/
        foreach ($allBrands as $brand) {
            /*$a = ZArrayHelper::getValue(json_decode($brand['image'],true), 0,);*/

            $a = ZArrayHelper::getValue($brand->image, 0,);
            $brand_data[$brand['id']] = ZImageWidget::widget([
                'config' => [
                    'url' => '/uploaz/' . App . '/ShopBrand/image/' . $brand['id'] . '/' . $a,
                    'class' => "mr-20",
                    'width' => '100%',
                ]
            ]);

        }

        $column->widget = ZMarketDropdownWidget::class;
        $column->options = [
            'config' => [
                'class' => 'd-flex flex-wrap',
                'accordion' => true,
                'content' => ZBootstrapBorderCheckboxGroupWidget::widget([
                    'data' => $brand_data,
                    'config' => [
                        'class' => 'col-md-2',
                        'WHClass' => 'w-100 h-100 mr-2',
                        'imgSize' => ZBootstrapBorderCheckboxGroupWidget::size['3'],
                    ],
                    'name' => 'brand',

                    'value' => $brand_checked_ids,
                    'event' => [
                        'change' => <<<JS
                            function() {
                                $('#activeFormCheck').submit();        
                            }
JS,

                    ]
                ]),
                'title' => Az::l('Марка')
            ]
        ];
        //$column->data = $property->items;
        $app->columns['brand'] = $column;


        //price
        if (null !== $products) {

            //todo: man qoshdim buni min price bomasa chiqmidi slider widget AzimjonToirov
            if (!empty($min_price && $max_price)) {

                $item = new ProductItem();
                //$currency = ProductItem::
                $column = new Form();
                $column->widget = ZKSliderIonWidget::class;
                $column->options = [
                    'event' => [
                        'onfinish' => <<<JS
                            function() {
                                $('#activeFormCheck').submit()        
                            }
JS,
                    ],
                    'name' => 'price_filter',
                    'value' => [$min_price, $max_price], //$price_old_value
                    'config' => [
                        'skin' => ZKSliderIonWidget::skin['modern'],
                        'type' => 'double',
                        'min' => $min_price,
                        'max' => $max_price,
                        'inputs_show' => true,
                        'title' => "Цена в ($item->currency)",
                    ],

                ];
                $app->columns['price'] = $column;
            }
        }
        //end price


        $category_properties = Az::$app->market->product->propertsByCategory($category_id);

        if ($category_properties === null)
            return null;
        $category_propertiesByBranch = $category_properties->groupBy('shop_option_branch_id');
        //properties
        $checked_ids = [];

        $session_array = [];
        if (!empty($filter))
            $session_array = $filter;
        //vdd($this->sessionGet('filter'));

        foreach ($session_array as $sub_arr)
            if (is_array($sub_arr))
                $checked_ids = ZArrayHelper::merge($checked_ids, $sub_arr);

        //vdd($checked_ids);
        foreach ($category_propertiesByBranch as $branch_id => $branch) {
            $content = '';
            $key = 0;

            foreach ($branch as $property) {
                $options = $this->core_options->where('shop_option_type_id', $property["id"]);


                $options_data = [];

                foreach ($options as $option) {
                    $options_data[$option['id']] = $option['name'];
                }

                //vdd($checked_ids);
                $content .= ZGAccordionWidget::widget([
                    'config' => [
                        'content' => ZMCheckboxGroupWidget::widget([
                            'data' => $options_data,
                            'name' => "ZDynamicModel[$branch_id.$key]",
                            'value' => $checked_ids,

                            'config' => [
                                'class' => 'optionCheckBoxes',
                                'textColor' => ZColor::color['black'],
                                'type' => ZMCheckboxGroupWidget::type['MaterialCheckboxWithLoader'],
                            ],
                            'event' => [
                                'change' => <<<JS
                                    function() {
                                        $('#activeFormCheck').submit()
                                    }
JS,
                            ]
                        ]),
                        'title' => $property['name'],
                        'class' => ''
                    ]
                ]);
                $key++;


                $column = new Form();
                $column->widget = ZMarketDropdownWidget::class;
                $column->options = [
                    'config' => [
                        'content' => $content,
                        'title' => $this->core_option_branches->where('id', $branch_id)->first()['name'] ?? 'not found',
                        'class' => '',
                        'onlyOneActive' => 0,
                        'initFirstActive' => 0,
                        'hideControl' => true,
                        'openNextOnClose' => true,
                    ]
                ];
                $app->columns['branches' . $branch_id] = $column;
            }
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

        //select  KOMMENT  O'CHIRILMASIN !!!!!
        /* $selected_options = [];
         foreach ($checked_ids as $option_id) {
             $option = CoreOption::findOne($option_id);
             $selected_options[$option_id] = $option->name ?? null;
         }

         if (\Dash\count($selected_options) > 0) {
             $column = new Form();
             $column->widget = ZSelect2Widget2::class;
             $column->options = [
                 'name' => 'selected_options',
                 'value' => $selected_options,
                 'data' => $selected_options,
                 'config' => [
                     'multiple' => true,
                     // 'readonly' => true
                 ]
             ];
             $app->columns['select2'] = $column;
         }*/
        //select

        $app->cards = [];

        return Az::$app->forms->former->model($app);
    }


    #endregion

    // todo:start
    #region filterProduct
    /**
     *
     * Function  filterProduct
     * @param $category_id
     * @param $min_price
     * @param $max_price
     * @param array $brand_checked_ids
     * @param array $shop_option_ids
     * @return  array|null
     *
     * @author JaloliddinovSalohiddin
     * @author OtabekNosirov
     * @author AkromovAziz
     *
     * Produktlani filter qiladi(categoriya boyicha,pricela boyicha,brendla boyicha,propertila boyicha)
     */


    public function filterProduct($category_id, $min_price, $max_price, $brand_checked_ids = [], $shop_option_ids = [])
    {


        $products = null;
        $arr = array_filter(explode(',', $shop_option_ids), function ($val) {
            if ($val
                !== "") return $val;
        });
        $arrBr = array_filter(explode(',', $brand_checked_ids), function ($val) {
            if ($val
                !== "") return $val;
        });

        if ($category_id !== null)
            $products = $this->shop_products
                ->where('shop_category_id', $category_id);

        if (!empty($products) && !empty($brand_checked_ids)) {
            $products = $products->whereIn('shop_brand_id', $arrBr);
        }

        if (!empty($products) && !empty($arr)) {

            $old_pr = [];
            foreach ($products as $product) {
                $cheked = 0;
                foreach ($arr as $id) {
                    if (in_array($id, json_decode($product['shop_option_ids'])))
                        $cheked++;

                }

                if ($cheked === \Dash\count($arr))
                    $old_pr[] = $product;

            }

            $products = $old_pr;
        }


        $shop_products = [];
        foreach ($products as $item)
            $shop_products[] = Az::$app->market->product->product($item['id'], null, false);

        $products = $shop_products;


        if (!empty($products) && !empty($max_price) && !empty($min_price)) {
            $shop_pr = [];
            foreach ($products as $val) {
                if ($val->min_price >= $min_price && $val->max_price <= $max_price)
                    $shop_pr[] = $val;

            }

            $products = $shop_products;
        }


        return $products;
    }
    #endregion
    //end

    #region filterFormItemsSession

    public function testfilterFormItemsSession()
    {
        $data = $this->getBranches();
        vd($data);
    }

    public function filterFormItemsSession($category_id, $company_id = null)
    {

        $filter = [];
        if (Az::$app->cores->session->get('filter'))
            $filter = Az::$app->cores->session->get('filter');

        $brand_filter = [];
        if (Az::$app->cores->session->get('brand_filter'))
            $brand_filter = Az::$app->cores->session->get('brand_filter');

        $price_filter = [];
        if (Az::$app->cores->session->get('price_filter'))
            $price_filter = Az::$app->cores->session->get('price_filter');

        return $this->filterFormItems($category_id, $filter, $price_filter, $brand_filter, $company_id);
    }

    #endregion

    #region singleProductForm

    public function testSingleProductForm()
    {
        $data = $this->singleProductForm();
        vd($data);
    }

    public function singleProductForm($product_id = null, $company_id = null)
    {

        $app = new ALLApp();

        $proporties = Az::$app->market->product->product($product_id, $company_id, true)->properties;

        $config = new Config();

        $app->configs = $config;
        $mergeProperties = [];
        foreach ($proporties as $a)
            $mergeProperties = array_merge($mergeProperties, $a);
        foreach ($mergeProperties as $key => $property) {
            $column = new Form();
            //$column->title = $property->name;
            $column->widget = ZGAccordionWidget::class;
            $column->options = [
                'config' => [
                    'content' => ZMCheckboxGroupWidget::widget([
                        'data' => $property->items,
                        'name' => "ZDynamicModel[$key]",
                        'value' => [12],
                        'event' => [
                            'change' => <<<JS
        function() {
            $('#activeFormCheck').submit()        
        }
JS,
                            /*'click' => <<<JS
                                           function () {
                                               $('#refreshMarketList').click();
                                               console.log('clicked');
                                               $('#market_list').css({"opacity": "0.5","z-index": "-1"});
                                               $('.market-company').parent().appendTo("#market_list");
                                               $('.market-company').removeClass("d-none");
                                               $('.market-company').parent().css({"z-index": "1"});
                                           }
                               JS,*/
                        ]
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

    #endregion

}
