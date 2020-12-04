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
use phpDocumentor\Reflection\Types\Integer;
use zetsoft\dbitem\shop\CartOrderItem;
use zetsoft\dbitem\shop\CartOrder;
use zetsoft\dbitem\shop\CompanyCardItem;
use zetsoft\dbitem\shop\ProductItem;
use zetsoft\former\shop\ShopCompanyCardForm;
use zetsoft\models\place\PlaceAdress;
use zetsoft\models\place\PlaceRegion;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\user\UserCompany;
use zetsoft\models\shop\ShopElement;
use zetsoft\models\shop\ShopOption;
use zetsoft\models\shop\ShopProduct;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZUrl;
use zetsoft\system\kernels\ZFrame;
use zetsoft\widgets\incores\ZIRadioGroupWidget;
use function Symfony\Component\String\s;

class Cart extends ZFrame
{
    public ?Collection $shop_products;
    public ?Collection $shop_elements;
    public ?Collection $shop_catalogs;
    public ?Collection $core_companies;
    public ?Collection $place_adress;
    public $place_regions;
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
        $this->core_companies = collect(UserCompany::find()->asArray()->all());
        $this->place_adress = collect(PlaceAdress::find()->asArray()->all());
        $this->place_regions = collect(PlaceRegion::find()->asArray()->all());
        parent::init();
    }


    public function test()
    {
        // $this->byelementTest(); //Error: there is no method for this function
        // $this->GetCatalogsByElementTest();
        // $this->AddToCartTest();
        // $this->SetToCartTest();
        //$this->AddOrSetCartTest();
        // $this->CartOrdersTest();
        //$this->CartEmptyTest(); there is not variable in this function

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
    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    //there is no method for this function
    public function byelementTest()
    {
        $productId = 308;
        $array = [6, 1, 15];

        $data = byElement($productId, $array);
        vd($data);
    }

    #region for getCatalogsByElement

    /**
     *
     * Function  getCatalogsByElement
     * @param $productId
     * @param array $options
     * @return  array|bool|void
     */

    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    //this function shows Catalogs by element: options
    public function GetCatalogsByElementTest()
    {

        $productId = 41;
        $options = ["1", "2", "5", "8", "204", "206", "207"];
        $count = 5;
        $data = $this->getCatalogsByElement($productId, $options, $count);
        vd($data);
    }

    public function getCatalogsByElement($productId, $options = [], $count = null)
    {
        Az::start(__FUNCTION__);
        if ($productId === null)
            return [];
        $shop_product = $this->shop_products->where('id', $productId)->first();
        $elements = $this->shop_elements->where('shop_product_id', $productId);

        $shop_element = null;
        foreach ($elements as $element) {
            $bool = true;
            if ($element['shop_option_ids'] === null)
                $shop_option_ids = [];
            else
                $shop_option_ids = json_decode($element['shop_option_ids'], true);

            if (is_array($options))
                foreach ($options as $option_id) {
                    if (!in_array($option_id, $shop_option_ids)) {
                        $bool = false;
                        continue;
                    }
                }

            if ($bool) {
                $shop_element = $element;
                break;
            }
            if (empty($options)) $shop_element = $element;
        }

        if ($shop_element === null)
            return Az::error(__FUNCTION__ . '$element not found');

        $catalogs = $this->shop_catalogs->where('shop_element_id', $shop_element['id']);

        if ($count !== null)
            $catalogs = $catalogs->take($count);

        $cart = Az::$app->cores->session->get('cart');

        $items = [];
        foreach ($catalogs as $catalog) {
            $company = $this->core_companies->where('id', $catalog['user_company_id'])->first();

            if ($company === null)
                return null;

            $companyItem = new ShopCompanyCardForm();
            $companyItem->id = $company['id'] ?? 0;
            $companyItem->rating = $company['rating'] ?? 3;
            $companyItem->catalogId = $catalog['id'];
            $companyItem->name = $company['name'] ?? null;
            $companyItem->amount = $catalog['amount'];
            $companyItem->title = $company['title'] ?? null;

            $temp_product = new ShopProduct();
            $companyItem->measure = $temp_product->_measure[$shop_product['measure']] ?? $temp_product->_measure['pcs'];
            $companyItem->measureStep = ProductItem::measureStep[$shop_product['measure']] ?? ProductItem::measureStep['pcs'];
            $companyItem->url = ZUrl::to([
                'customer/markets/index',
                'id' => $company['id'] ?? null
            ]);
            if (\Dash\count($company['photo'])) {
                $path = '/uploaz/eyuf/UserCompany/photo/' . $company['id'] . '/' . ZArrayHelper::getValue(json_decode($company['photo'], true), 0);
                if (file_exists(Root . '/upload' . $path))
                    $companyItem->image = $path;
            }
            if (empty($companyItem->image))
                $companyItem->image = $this->defaultProductImage;


            $temp_item = new ProductItem();
            $companyItem->currency = $temp_item->currency;
            $companyItem->currencyType = $temp_item->currencyType;

            $currency = Az::$app->cores->session->get('currency');
            $companyItem->new_price = Az::$app->payer->currency2->convert($catalog['currency'], $currency, $catalog['price']);
            $companyItem->price_old = Az::$app->payer->currency2->convert($catalog['currency'], $currency, $catalog['price_old']);

            /*if($companyItem->price_old>$companyItem->new_price){
                $companyItem->discount=(int)round(($companyItem->price_old-$companyItem->new_price)/$companyItem->price_old*100);

            }*/

            $companyItem->cart_amount = collect($cart)->where('catalog_id', $catalog['id'])->first()['amount'] ?? 0;

            $items[] = $companyItem;

        }

        return $items;
    }

    #endregion

    #region for addToCart

    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    //this function add to cart
    public function AddToCartTest()
    {
        $catalog_id = 5;
        $amount = 5;
        $data = $this->addToCart($catalog_id, $amount);
        vdd($data);
    }

    public function addToCart($catalog_id, $amount = 1)
    {
        Az::start(__FUNCTION__);
        return $this->addOrSetCart($catalog_id, $amount, $this::cart_type['add']);
    }

    #endregion

    #region for setToCart

    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    //this function set to cart
    public function SetToCartTest()
    {
        $catalog_id = 5;
        $amount = 1;
        $data = $this->setToCart($catalog_id, $amount);
        vd($data);
    }


    public function setToCart($catalog_id, $amount = 1)
    {
        Az::start(__FUNCTION__);
        return $this->addOrSetCart($catalog_id, $amount, $this::cart_type['set']);
    }

    #endregion

    #region for addOrSetCart

    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    //this function add or set cart by $catalogId , $amount
    public function AddOrSetCartTest()
    {
        $catalogId = 42;
        $amount = 6;
        $type = true;
        $data = $this->addOrSetCart($catalogId, $amount, $type);
        vd($data);
    }

    public function addOrSetCart(int $catalogId, int $amount, string $type = 'set')
    {
        Az::start(__FUNCTION__);
        $cart = Az::$app->cores->session->get('cart');

        $data = [
            [
                'catalog_id' => $catalogId,
                'amount' => $amount
            ]
        ];

        $new_cart = [];
        $total_amount = 0;
        if ($cart) {
            $bor = false;
            foreach ($cart as $cart_item) {
                if ($cart_item['catalog_id'] === $catalogId) {
                    $bor = true;
                    switch ($type) {
                        case 'set':
                        case 'delete':
                        case 'clear':
                        case 'add':
                            $cart_item['amount'] = $amount;
                            break;
                    }

                }
                if ($cart_item['amount'] > 0) {
                    $new_cart[] = [
                        'catalog_id' => $cart_item['catalog_id'],
                        'amount' => $cart_item['amount']
                    ];
                }
                $total_amount += $cart_item['amount'];
            }

            if (!$bor) {
                $new_cart[] = [
                    'catalog_id' => $catalogId,
                    'amount' => $amount
                ];
                $total_amount += $amount;
            }

        } else {
            if ($amount > 0)
                $new_cart[] = [
                    'catalog_id' => $catalogId,
                    'amount' => $amount
                ];
            $total_amount += $amount;
        }

        Az::$app->cores->session->set('cart', $new_cart);

        $nechta_catalog_borligi = count($new_cart);

        return $nechta_catalog_borligi;
    }

    #endregion
    #region deleteProductFromCart
    public function deleteProductFromCart($catalogId, $amount, $type)
    {
        Az::start(__FUNCTION__);
        $cart = Az::$app->cores->session->get('cart');
        $total_amount = 0;
        if ($cart) {
            $bor = false;
            foreach ($cart as $cart_item) {
                if ($cart_item['catalog_id'] === $catalogId) {
                    $bor = true;
                    switch ($type) {
                        case 'add':
                            $cart_item['amount'] = $amount;
                            break;
                        case 'set':
                            $cart_item['amount'] = $amount;
                            break;
                        case 'delete':
                            $cart_item['amount'] = $amount;
                            break;
                        case 'clear':
                            $cart_item['amount'] = $amount;
                            break;
                    }
                }

                if ($cart_item['amount'] > 0) {
                    $new_cart[] = [
                        'catalog_id' => $cart_item['catalog_id'],
                        'amount' => $cart_item['amount']
                    ];
                }
                $total_amount += $cart_item['amount'];
            }

            if (!$bor) {
                $new_cart[] = [
                    'catalog_id' => $catalogId,
                    'amount' => $amount
                ];
                $total_amount += $amount;
            }

        } else {
            if ($amount > 0)
                $new_cart[] = [
                    'catalog_id' => $catalogId,
                    'amount' => $amount
                ];
            $total_amount += $amount;
        }

        Az::$app->cores->session->set('cart', $new_cart);

        $nechta_catalog_borligi = count($new_cart);

        return $nechta_catalog_borligi;
    }
    #endregion

#region for cartOrders

//Ravshanov Sardor
//telegram=@SardorRavshanov
//
    public function CartOrdersTest()
    {
        $clientAdressId = 24;
        $is_seperated_by_company = true;
        $var = $this->cartOrders($clientAdressId, $is_seperated_by_company);
        vd($var);

        $is_seperated_by_company = false;
        $var = $this->cartOrders($clientAdressId, $is_seperated_by_company);
        vd($var);

    }

//cart ni productitemlarga aylantirib beradi
    public function cartOrders($client_address_id = null, $is_seperated_by_company = true)
    {
        Az::start(__FUNCTION__);
        $cart = Az::$app->cores->session->get('cart');
        /*if ($this->isCLI())
        $cart = [
            [
                'catalog_id' => 12234,
                'amount' => 3,
            ],
            [
                'catalog_id' => 12245,
                'amount' => 3,
            ],
            [
                'catalog_id' => 12250,
                'amount' => 4,
            ],
            [
                'catalog_id' => 12275,
                'amount' => 9,
            ],
            [
                'catalog_id' => 12236,
                'amount' => 11,
            ],
        ];*/
        //art);            
        $productItems = [];
        if ($cart) {
            foreach ($cart as $item) {
                $cataloId = $item['catalog_id'];

                $productItem = Az::$app->market->product->productItemByCatalogId($cataloId);

                if ($productItem === null)
                    continue;

                $productItem->cart_amount = $item['amount'];
                $productItems[] = $productItem;
            }

        }


        if ($is_seperated_by_company === false)
            return $productItems;

        $productByCompany = collect($productItems)
            ->groupBy('company_id')
            ->map(function ($items, $key) use ($client_address_id) {

                $company = $this->core_companies->where('id', $key)->first();

                $item_collection = collect($items);

                if ($company !== null) {
                    $order = new CartOrder();
                    if ($client_address_id !== null) {
                        /*$order->delivery_price = Az::$app->maps->distance->cores($company['place_adress_id'], $client_address_id)->distance_in_km * $company['delivery_price_per_km'];*/
                        $client_place_adress = $this->place_adress->where('id', $client_address_id)->first();


                        $palace_region = $this->place_regions
                            ->where('id', $client_place_adress['place_region_id'])
                            ->first();
                        if ($palace_region) {
                            $order->delivery_price = $palace_region['delivery_price'];
                        }
                    }
                    $order->company_name = $company['name'];
                    $order->company_id = $company['id'];

                    if (\Dash\count($company['photo'])) {
                        $path = '/uploaz/eyuf/UserCompany/photo/' . $company['id'] . '/' . ZArrayHelper::getValue(json_decode($company['photo'], true), 0);

                        if (file_exists(Root . '/upload' . $path)) {
                            $order->company_image = $path;
                        }
                    }
                    if (empty($order->company_image))
                        $order->company_image = $this->defaultProductImage;


                    $order->total_price = $item_collection->sum('new_price');
                    $order->company_url = ZUrl::to([
                        'user/market-single/products',
                        'id' => $company['id']
                    ]);
                    $order->items = $items->toArray();

                    return $order;
                }
            });

        return $productByCompany->toArray();
    }

#endregion

#region for cartEmpty
//Ravshanov Sardor
//telegram=@SardorRavshanov
//
    public function CartEmptyTest()
    {
        $data = $this->cartEmpty();
        vd($data);
    }

    public function cartEmpty()
    {
        $this->sessionSet('cart', null);
    }

#endregion

}
