<?php

/**
 * Author: Xolmat Ravshanov
 * Date:    07.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 */

namespace zetsoft\service\market;


use zetsoft\dbitem\data\ALLApp;
use zetsoft\dbitem\data\Config;
use zetsoft\dbitem\data\FormDb;
use zetsoft\models\dyna\DynaMulti;
use zetsoft\models\shop\ShopBrand;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopCategory;
use zetsoft\models\shop\ShopElement;
use zetsoft\models\shop\ShopOptionBranch;
use zetsoft\models\shop\ShopProduct;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\widgets\images\ZImageWidget;
use zetsoft\widgets\incores\ZMCheckboxGroupWidget;
use zetsoft\widgets\inputes\ZBootstrapImgCheckboxGroupWidgetM;
use zetsoft\widgets\navigat\ZGAccordionWidget;
use zetsoft\widgets\navigat\ZMarketDropdownWidget;


class FilterForm extends ZFrame
{


    public function getFilterbrands($category_id)
    {

        // $branches = $this->getBranches();
        $models = [];
        /* $allBrands = [
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
         */
        $allBrands = [];
        $shopElementIds = [];
        $shopProductIds = [];
        $shopCategoryIds = [];
        $shopBrandIds = [];

        /*  $shop_catalogs = ShopCatalog::find()
          ->where([
              'user_company_id' => $company_id
          ])->all();
           foreach ($shop_catalogs as $shop_catalog)
                  $shopElementIds[] = $shop_catalog->shop_element_id;

            $shop_elements = ShopElement::find()
            ->where([
               'id' => $shopElementIds
            ])->all();

            foreach ($shop_elements as $shop_element)
                  $shopProductIds[] = $shop_element->shop_product_id;

              $shop_products = ShopProduct::find()
              ->where([
                 'id' => $shopProductIds
              ])->all();

              foreach ($shop_products as $shop_product)
                      $shopCategoryIds[] = $shop_product->shop_category_id;


                 $shop_categories = ShopCategory::find()
                 ->where([
                     'id' => $shopCategoryIds
                 ])->all();*/


        $shop_category = ShopCategory::findOne($category_id);
        /*
           foreach ($shop_categories as $shop_category){
                    foreach ($shop_category->shop_brand_ids as $brand_id)
                    $shopBrandIds[] = $brand_id;
           }*/

        //   $shopBrandIds = array_unique($shopBrandIds);
        /*
         $shop_brands = ShopBrand::find()
         ->where([
            'id' => $shopBrandIds,
         ]);*/

        $shop_brands = ShopBrand::findAll($shop_category->shop_brand_ids);


        $brand_data = [];
        foreach ($shop_brands->image as $brand) {
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
                        'content' => ZBootstrapImgCheckboxGroupWidgetM::widget([
                            'data' => $brand_data,
                            'config' => [
                                'class' => 'col-md-4',
                                'WHClass' => 'w-75 h-75',
                                'imgSize' => ZBootstrapImgCheckboxGroupWidgetM::size['3'],
                                'type' => ZBootstrapImgCheckboxGroupWidgetM::type['checkbox'],
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
                            'data' => $data1
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

    public function getBranches()
    {
        $branches = [];

        $models = ShopOptionBranch::find()->all();

        foreach ($models as $model) {
            $branches[$model->id] = $model->name;
        }

        return $branches;
    }

    public function getDynaFilterNames()
    {
        $names = [];
        $filterNames = DynaMulti::find()->all();

        foreach ($filterNames as $filterName) {
            if (!empty($filterName))
                $names[] = $filterName->name;
        }

        return $names;
    }

    public function getFilters($models)
    {
    
        $return = [];

        /** @var DynaMulti[] $models */
        foreach ($models as $model) {
                       
            if (empty($model->attr) && empty($model->val))
                continue;

            if (!$model->active)
                continue;

            $group = 'and';
            switch ($model->group) {

                case 'or':
                    $group = 'or';
                    break;

                case 'not':
                    $group = 'not';
                    break;

            }
            
            switch ($model->operator) {

                case 'between':

                    $val1 = null;
                    if(!empty(ZArrayHelper::getValue($model->val, 'value1')))
                        $val1 = ZArrayHelper::getValue($model->val, 'value1');

                    $val2 = null;
                    if(!empty(ZArrayHelper::getValue($model->val, 'value2')))
                        $val2 = ZArrayHelper::getValue($model->val, 'value2');

                    if (!$val1 || !$val2) {
                        $array = [];
                        break;
                    }

                    $array = [
                        $model->operator,
                        $model->attr,
                        $val1,
                        $val2,
                        'query' => $group
                    ];

                    break;

                default:

                    $val = null;
                    if (!empty(ZArrayHelper::getValue($model->val, 'value')))
                        $val = ZArrayHelper::getValue($model->val, 'value');

                    $array = [
                        $model->operator,
                        $model->attr,
                        $val,
                        'query' => $group
                    ];

                        /*vd($model->val);
                            if ($val === null) {
                                $array = [
                                    $model->operator,
                                    $model->attr,
                                    $val,
                                    'query' => $group
                            ];
                        }*/

                    break;
                    
            }
            $return[] = $array;
        }

        return $return;

    }

    public function getFiltersChess($models)
    {

        $return = [];

        /** @var DynaMulti[] $models */
        foreach ($models as $model) {

                             
            if (empty($model->attr) && empty($model->val))
                continue;

            if (!$model->active)
                continue;
                     
            $group = 'and';
            switch ($model->group) {

                case 'or':
                    $group = 'or';

                    break;

                case 'not':
                    $group = 'not';
                    break;

            }
            switch ($model->operator) {
                case '=':
                    $val = null;
                    if (!empty(ZArrayHelper::getValue($model->val, 'value')))
                        $val = ZArrayHelper::getValue($model->val, 'value');

                    $array = [
                        $model->attr => $val,
                        'query' => $group
                    ];

                    break;
                case 'between':

                    $val1 = null;
                    if(!empty(ZArrayHelper::getValue($model->val, 'value1')))
                        $val1 = ZArrayHelper::getValue($model->val, 'value1');

                    $val2 = null;
                    if(!empty(ZArrayHelper::getValue($model->val, 'value2')))
                        $val2 = ZArrayHelper::getValue($model->val, 'value2');

                    if (!$val1 || !$val2) {
                        $array = [];
                        break;
                    }

                    

                    $array = [
                        $model->operator,
                        $model->attr,
                        $val1,
                        $val2,
                        'query' => $group
                    ];
                     
                    break;

                default:
                    
                    $val = null;
                    
                    if (!empty(ZArrayHelper::getValue($model->val, 'value')))
                        $val = ZArrayHelper::getValue($model->val, 'value');

                    $array = [
                        $model->operator,
                        $model->attr,
                        $val,
                        'query' => $group
                    ];
                    /*vd($model->val);
                    if ($val === null) {
                        $array = [
                            $model->operator,
                            $model->attr,
                            $val,
                            'query' => $group
                        ];
                    }*/

                    break;

            }
            $return[] = $array;
        }
        return $return;

    }

}
