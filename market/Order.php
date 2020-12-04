<?php

/**
 * Author: Sardor
 * Date:    07.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 * @license OtabekNosirov
 * @license AkromovAzizjon
 * @license JaloliddinovSalohiddin
 *
 */

namespace zetsoft\service\market;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use zetsoft\dbitem\data\ALLApp;
use zetsoft\dbitem\data\Config;
use zetsoft\dbitem\data\Form;
use zetsoft\dbitem\shop\OrderElementItem;
use zetsoft\dbitem\shop\OrderItem;
use zetsoft\dbitem\shop\SellerItem;
use zetsoft\former\chart\ChartForm;
use zetsoft\former\order\OrderPayBackCCForm;
use zetsoft\former\order\OrderPortionFormForm;
use zetsoft\former\shop\ShopOrderItemDForm;
use zetsoft\models\App\eyuf\Cupon;
use zetsoft\models\core\CoreMenuNew;
use zetsoft\models\place\PlaceAdress;
use zetsoft\models\place\PlaceRegion;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopCatalogWare;
use zetsoft\models\shop\ShopElement;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\shop\ShopProduct;
use zetsoft\models\shop\ShopShipment;
use zetsoft\models\user\UserCompany;
use zetsoft\models\ware\WareEnter;
use zetsoft\system\assets\ZColor;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZTest;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\module\Models;
use zetsoft\widgets\former\ZFormWidget;
use zetsoft\widgets\incores\ZMRadioWidget;
use zetsoft\widgets\inputes\ZSelect2Widget;
use zetsoft\widgets\values\ZFormViewWidget;


class Order extends ZFrame
{

    public $orders;
    public $shopOrderItems;
    public $placeAdresses;
    public $regions;
    public $status_cpa;
    public $client;
    public $methods = [
        'post' => "POST",
        'get' => "GET",
        'delete' => "DELETE",
        'put' => "PUT",
        'patch' => "PATCH"
    ];


    public function returnPrice($ware_return_id)
    {

        $shop_order_items = ShopOrderItem::find()
            ->where([
                'ware_return_id' => $ware_return_id,
            ]);

        $total_price = 0;
        /** @var ShopOrderItem $shop_order_item */
        foreach ($shop_order_items as $shop_order_item) {

            $total_price += (int)$shop_order_item->price_all_return;

        }

        return $total_price;

    }

    public function returnPriceMinus($ware_return_id)
    {

        $shop_order_items = ShopOrderItem::find()
            ->where([
                'ware_return_id' => $ware_return_id,
            ]);

        $total_price = 0;
        /** @var ShopOrderItem $shop_order_item */
        foreach ($shop_order_items as $shop_order_item) {

            $total_price += (int)$shop_order_item->price_all_return;

        }

        return $total_price;

    }

    #region init

    public function init()
    {
        $this->orders = collect(ShopOrder::find()->all());
        $this->shopOrderItems = collect(ShopOrderItem::find()->all());
        $this->placeAdresses = collect(PlaceAdress::find()->all());
        $this->regions = collect(PlaceRegion::find()->all());

        parent::init();
    }


    public function test()
    {
        $this->testSaveOrderFromCpaApi();
    }


    #endregion
    #region Rename
    //start: MurodovMirbosit
    public function renameOrder()
    {

        $orders = ShopOrderItem::find()->all();

        foreach ($orders as $order) {
            $order->name = $this->getOrderName($order);
            $order->configs->rules = [
                [
                    validatorSafe
                ]
            ];
            $order->save();
        }
    }

    private function getOrderName($model)
    {

        /** @var ShopOrderItem $model */

        Az::$app->forms->wiData->clean();
        Az::$app->forms->wiData->model = $model;
        Az::$app->forms->wiData->attribute = 'shop_order_id';
        $name = Az::$app->forms->wiData->value();
        $element = ShopElement::findOne($model->shop_order_id);
        if (!$element)
            return false;

        return 'Товар ' . $element->name;
    }
    //end
    #endregion
    #region Create

    public function createTest()
    {
        $order = new ShopOrder();
        $order->user_id = 110;
        $order->contact_name = 'AAAAAAAAA';
        $order->contact_phone = 'BBBBBBB';
        $order->date_deliver = date('Y-m-d H:i:s');
        $order->place_adress_id = 65;
        $order->save();
    }

    #endregion

    #region getOrderList

    public function getOrderData()
    {
        $data = [];
        foreach ($this->orders as $order)
            $data[$order->id] = $order->name;

        return $data;
    }

    /**
     * If has not user_id and type return  all order list
     * Otherwise returns existing type and user orders
     * Function  getOrderList
     * @param null $user_id
     * @param null $type
     * @return  mixed
     */

    public function GetOrderListTest()
    {
        $data = $this->getOrderList(144);
        $value = \Dash\count($data);
        $expectedValue = 92;
        ZTest::assertEquals($expectedValue, $value);
    }

    public function getOrderList($user_id = null, $status = null)
    {

        $type = strtolower($status);
        if ($user_id === null && $status === null) {
            /*vdd(ShopOrder::find()->all());*/
            return ShopOrder::find()->all();
        }
        if ($status === null && $user_id !== null) {
            $user_order_s = ShopOrder::find()->
            where([
                'user_id' => $user_id
            ])->all();

            return $user_order_s;
        }
        if ($status !== null && $user_id === null) {
            $user_order_s = ShopOrder::find()->where([
                'status_logistics' => $status
            ])->all();
            return $user_order_s;
        }
        $user_order_s = ShopOrder::find()->where([
            'user_id' => $user_id
        ])->andWhere([
            'status' => $status
        ])->all();
        return $user_order_s;
    }

    #endregion

    #region UserOrderList

    public function GetUserOrderListTest()
    {
        $data = $this->getUserOrderList();
        vd($data);
    }

    public function getUserOrderList()
    {

        $user_order_list = [];
        $user_id = $this->userIdentity()->id;
        $orders = $this->getOrderList($user_id);
        foreach ($orders as $order) {
            $order_item = new OrderItem();
            $order_item->sum = $order->total_price;
            $order_item->status_client = $order->status_client;
            $order_item->created_at = $order->created_at;
            $id = (int)$order->id;
            $order_item->current_order_element_items[] = $this->getProductBelongsToOrder($id);
            $user_order_list[] = $order_item;
        }
        return $user_order_list;
    }

    #endregion

    #region orderBuyed
    /** @var ShopOrder $shop_order */

    public function OrderBuyedTest()
    {
        $data = $this->orderBuyed();
        vd($data);
    }

    public function orderBuyed($shop_order)
    {

        $order_items = ShopOrderItem::find()
            ->where([
                'shop_order_id' => $shop_order->id
            ])
            ->all();

        $place_adress = PlaceAdress::findOne($shop_order->place_adress_id);
        $place_region = PlaceRegion::findOne($place_adress->place_region_id);

        /** @var ShopOrderItem $order_item */
        foreach ($order_items as $order_item) {
            $catalog_ware = ShopCatalogWare::findOne([
                'shop_catalog_id' => $order_item->shop_catalog_id,
                'ware_id' => $place_region->ware_id
            ]);


            if ($catalog_ware) {

                $catalog_ware->amount -= (int)$order_item->amount;

                if ($catalog_ware->save()) {
                    $catalog = ShopCatalog::findOne($order_item->shop_catalog_id);

                    $catalog->amount -= $order_item->amount;
                    $catalog->save();
                }

            }

        }

    }

    #endregion

    #region getOrderAddress

    /**
     * Return order address
     * Give order object
     * Function  getOrderAddress
     * @param null $order obj
     * @return  array
     */

    public function GetOrderAddressTest()
    {
        $data = $this->getOrderAddress();
        vd($data);
    }

    public function getOrderAddress($order = null)
    {
        if ($order === null) {
            return [];
        }
        $current_order_address = Az::$app->market->address->getAddress($order[0]->id, 'order');
        return $current_order_address;
    }

    #endregion

    #region getProductBelongsToOrder

    public function getProductBelongsToOrderTest()
    {
        $data = $this->getProductBelongsToOrder();
        vdd($data);

        //ZTest::assertEquals($item,$data);


    }

    public function getProductBelongsToOrder($order_id = null)
    {
        if ($order_id === null)
            return null;

        $orderItems = [];

        $current_order = ShopOrder::findOne($order_id);
        $order_items = ShopOrderItem::find()
            ->where([
                'shop_order_id' => $order_id
            ])
            ->all();
        //$current_order->getShopOrderItemsFromShopOrderItemIds();
        foreach ($order_items as $order_item) {
            $c_order = new OrderElementItem();
            //$core_catalog = $order_item->getCoreCatalog() ?? null;
            $c_order->amount = $order_item->amount;
            $c_order->sum = ($order_item->amount) * ($order_item->price);
            $c_order->created_at = $order_item->created_at;
            $c_order->status = $current_order->status_client;

            $core_catalog = ShopCatalog::findOne($order_item->shop_catalog_id) ?? null;
            if ($core_catalog === null) continue;
            $c_order->element_price = $core_catalog->price;

            //$core_company = $core_catalog->getCoreCompany() ?? null;
            $core_company = UserCompany::findOne($core_catalog->user_company_id) ?? null;
            if ($core_company === null) continue;
            $c_order->catalog_name = $core_company->name;

            //$shop_element = $core_catalog->getCoreElement() ?? null;
            $shop_element = ShopElement::findOne($core_catalog->shop_element_id) ?? null;
            if ($shop_element === null) continue;
            $c_order->element_name = $shop_element->name;
            //$shop_product = $shop_element->getCoreProduct() ?? null;
            $shop_product = ShopProduct::findOne($shop_element->shop_product_id) ?? null;
            if ($shop_product === null) continue;
            $c_order->image = $shop_product->image;
            $orderItems[] = $c_order;


        }


        return $orderItems;
    }

    #endregion

    #region OrderForm

    public function orderFormTest()
    {
        $data = $this->orderForm();
        //vd(count($data));
    }

    public function orderForm()
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
        $app->columns['address_client/checkout/mainid'] = $column;
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

    #endregion

    #region getOwnShipments

    public function GetOwnShipmentsTest()
    {
        $data = $this->getOwnShipments();
        vd($data);
        //error
    }

    public function getOwnShipments($operator_id)
    {
        $model = new ShopShipment();

        $model->configs->query = ShopShipment::find()
            ->select('core_shipment.*, core_order.*')
            ->leftJoin('core_order', 'core_order.id = core_shipment.order_id')
            ->where(['core_order.status' => 'checking'])
            ->andWhere(['core_order.operator_id' => $operator_id])
            ->with('core_shipment');

        return $model;
    }

    #endregion

    #region saveOrders

    public function SaveOrderTest()
    {

        $post = [
            'contact_name' => 'CLI_Test',
            'place_adress_id' => 1,
            'comment_user' => 'CLI_Test_comment'
        ];
        vdd($this->saveOrders($post));

    }

    public function saveOrders($order)
    {

        $products = Az::$app->market->cart->cartOrders();

        /** @var Collection $items */

        $order->user_id = $this->userIdentity()->id;
        /*        $order->contact_name = ZArrayHelper::getValue($post, 'contact_name');
                $order->place_adress_id = ZArrayHelper::getValue($post, 'place_adress_id');
                $order->contact_phone = ZArrayHelper::getValue($post, 'contact_phone');
                $order->comment_user = ZArrayHelper::getValue($post, 'comment_user');
                $order->payment_type = ZArrayHelper::getValue($post, 'payment_type');*/
        $order->status_client = ShopOrder::status_client['accepted'];
        $order->status_logistics = ShopOrder::status_logistics['new'];
        $order->status_callcenter = ShopOrder::status_callcenter['new'];

        $id_list = [];
        foreach ($products as $product) {
            $id_list[] = $product->company_id;
        }

        $order->user_company_ids = $id_list;

        $ids = [];

        if ($order->save()) {
            $ids[] = $order->id;
            foreach ($products as $items)
                foreach ($items->items as $item) {
                    $catalog = ShopCatalog::findOne($item->catalogId);
                    $order_item = new ShopOrderItem();
                    $order_item->shop_order_id = $order->id;

                    $order_item->shop_catalog_id = $item->catalogId;
                    $order_item->amount = $item->cart_amount;
                    $order_item->name = $item->name;


                    if ($order_item->save()) {
                        $catalog->amount -= (int)$item->amount;

                        if (!$catalog->save()) {
                            return $catalog->errors;
                        }

                    } else return $order_item->errors;

                }

        } else
            /** @var Models $order */
            return $order->errors;


        /*       $products->each(function ($items, $company_id) use ($post, $ids) {
                   if ($items[0] === null)
                   return null;
                   $order = new ShopOrder();
                   // vdd($items);
                   $order->user_id = $this->userIdentity()->id;
                   $order->contact_name = ZArrayHelper::getValue($post, 'contact_name');
                   $order->place_adress_id = ZArrayHelper::getValue($post, 'place_adress_id');
                   $order->contact_phone = ZArrayHelper::getValue($post, 'contact_phone');
                   $order->comment_user = ZArrayHelper::getValue($post, 'comment_user');
                   $order->payment_type = ZArrayHelper::getValue($post, 'payment_type');
                   $order->status_client = ShopOrder::status_client['forming'];
                   $order->user_company_id = $company_id;


                   if ($order->save()) {
                       $ids[] = $order->id;
                       foreach ($items[0]->items as $item) {

                           $catalog = ShopCatalog::findOne($item->catalogId);
                           $order_item = new ShopOrderItem();
                           $order_item->shop_order_id = $order->id;

                           $order_item->shop_catalog_id = $item->catalogId;
                           $order_item->amount = $item->amount;
                           $order_item->price = $catalog->price;

                           if ($order_item->save()) {
                               $catalog->amount = $catalog->amount - (int)$item->amount;

                               if (!$catalog->save()) {
                                   return $catalog->errors();
                               }

                           } else return $order_item->errors();

                       }
                   } else
                       return $order->errors();

               });*/
        //$this->sessionDel('cart');
        $this->sessionSet('savedOrders', $ids);
        return $ids;
    }

    #endregion

    #region getInfoOrder

    /**
     *
     * Function  getInfoOrder
     *
     * Orderni companyasi bo'yicha info chiqarib beradi
     *
     * @param $company_id
     * @return  SellerItem
     * @throws \Exception
     */
    // todo:start

    public function getInfoOrder($company_id)
    {
        $result = new SellerItem();

        $orders = ShopOrder::find()->select('id,user_company_ids,status_client')->where([
            'in', 'user_company_ids', $company_id
        ])->asArray()->all();
        $key = '';

        $order_items = ShopOrderItem::find()->select('shop_order_id')->asArray()->all();

        foreach ($orders as $order) {
            $key = $order['status_client'];
            $count = 0;
            if (!empty($key)) {
                foreach ($order_items as $order_item)
                    if ($order_item['shop_order_id'] === $order['id']) $count++;

                try {
                    $result->{$key} += $count;
                } catch (Exception $e) {
                    vd($key);
                }
                $result->count_order += $count;
            }
        }
        return $result;
    }

    // todo:end
    public function getAllOrder()
    {
        $result = new SellerItem();

        $orders = ShopOrder::find()->select('id,user_company_ids,status_client')->asArray()->all();
        $key = '';

        $order_items = ShopOrderItem::find()->select('shop_order_id')->asArray()->all();
        foreach ($orders as $order) {
            if ($order['status_client'] !== null)
                $key = $order['status_client'];
            $count = 0;
            if (!empty($key)) {

                foreach ($order_items as $order_item)
                    if ($order_item['shop_order_id'] === $order['id'])
                        $count++;

                /*                try {
                                    $result->{$key} += $count;
                                } catch (\Exception $e) {
                                    vd($key);
                                }*/
                $result->count_order += $count;

            }
        }
        return $result;
    }

    #endregion


    public function getElementFormUserCompany($ware_id, $user_company_id)
    {

        $shop_catalog_ware = ShopCatalogWare::find()
            ->where([
                'ware_id' => $ware_id
            ])
            ->andWhere(['!=', 'amount', 0])
            ->asArray()
            ->all();

        $shop_catalog_ids = ZArrayHelper::getColumn($shop_catalog_ware, 'shop_catalog_id');

        $shop_catalog = ShopCatalog::find()
            ->where([
                'id' => $shop_catalog_ids,
                'user_company_id' => $user_company_id,
            ])
            ->andWhere(['!=', 'amount', 0])
            ->asArray()
            ->all();

        return ZArrayHelper::map($shop_catalog, 'id', 'name');

    }


    /**
     *
     * Function  getInfoOrderAdmin
     * @param $user_id
     * @return  SellerItem
     * @throws \Exception
     *
     *
     */

    /**
     *
     * Function  getInfoOrderAdmin
     *
     * Adminga panelga tegishli statistikala
     *
     * @param $user_id
     * @return  SellerItem
     * @throws \Exception
     */
    // todo:start
    public function getInfoOrderAdmin($user_id)
    {
        $company_id = '';
        $result = new SellerItem();
        $orders = ShopOrder::find()->where([
            'user_company_ids' => $company_id
        ])->asArray()->all();
        foreach ($orders as $order) {
            $order_item = ShopOrderItem::find();


            if (!empty($order['status_client'])) {

                $result->{$order['status_client']} += $order_item->where([
                    'shop_order_id' => $order['id']
                ])->count();


                $result->count_order += $order_item->where([
                    'shop_order_id' => $order['id']
                ])->count();
            }


        }


        return $result;
    }
    // todo:end


    #region RandomColor
    /**
     *
     * Function  random_color_part
     *
     * Har xil rangni HEX formatida generatisya qiladi
     *
     * @return  string
     * @throws \Exception
     */
    //start:

    public function random_color_part()
    {
        return str_pad(dechex(random_int(0, 255)), 2, '0', STR_PAD_LEFT);
    }

    public function random_color()
    {
        return $this->random_color_part() . $this->random_color_part() . $this->random_color_part();
    }

    //end

    #endregion

    #region payBackCallCenter
    /**
     *
     * Function  payBackCallCenter
     *
     * Viykup dlya Call Center otchyotlari
     *
     */
    #reference , created_at , status_client , date_deliver , date_transfer ,amount ,place, total_price
    // todo:start

    public function payBackCallCenter()
    {

        $orders = collect($this->orders);
        $shopOrderItems = collect($this->shopOrderItems);
        $regions = collect($this->regions);
        $placeAdress = collect($this->placeAdresses);

        $forms = [];
        foreach ($orders as $order) {

            $region_id = $placeAdress->where('id', $order->place_adress_id)->first->place_region_id;

            $region = $regions->where('id', $region_id)->first;
            $payBack = new OrderPayBackCCForm();
            $payBack->id = $order->id;
            $payBack->created_at = $order->created_at;
            $payBack->status_client = $order->status_client;
            $payBack->date_deliver = $order->date_deliver ?? '----';
            $payBack->date_transfer = $order->date_transfer ?? '----';
            $payBack->amount = $shopOrderItems->where('shop_order_id', $order->id)->first->amount->amount ?? '----';
            $payBack->place = $region->name->name ?? '----';
            $payBack->total_price = $shopOrderItems->where('shop_order_id', $order->id)->first->price_all->price_all ?? '----';
            $forms[] = $payBack;
        }
        return $forms;

    }

    // todo:end


    #endregion paybackCallCenter

    #region getPortionOrder
    #id , #order_id + #order_created_at , #order_amount
    /**
     *
     * Function  getPortionOrder
     *
     * Zakazlarni Poriyasini otchyotlari
     *
     * @return  array
     */

    // todo:start

    public function getPortionOrder()
    {
        $orders = collect($this->orders);
        $shopOrderItems = collect($this->shopOrderItems);
        $forms = [];
        foreach ($orders as $order) {
            $portion = new OrderPortionFormForm();
            $portion->order_id = Azl . ('Заказ #') . $order->id . Azl . (' дата ') . $order->created_at;
            $portion->order_amount = $shopOrderItems->where('shop_order_id', $order->id)->first->amount->amount ?? '----';
            $forms[] = $portion;
            //$portion->order_created_at = $order->created_at;
        }
        //vdd($forms);
        return $forms;

    }

    // todo:end
    #endregion

    #region getOrderStatsByOrders
    /**
     *
     * Function  getOrderStatsByStatus
     *
     * Zakazlani statusi bo'yicha statistikani chiqarib beradi
     *
     * @param int $day
     * @param null $companyId
     * @return  array
     * @throws \Exception
     */

    // todo:start

    public function getOrderStatsByStatus($day = 7, $companyId = null)
    {

        $date = date("Y-m-d h:i:s", strtotime("-" . $day . " days"));

        $orders = $companyId ? ShopOrder::find()
            ->where([
                '>=', 'created_at', $date,
            ])->andWhere([
                'user_company_ids' => $companyId
            ]) : ShopOrder::find()
            ->where([
                '>=', 'created_at', $date,
            ]);


        $ordersByStatus = $companyId ? ShopOrder::find()->where([
            '>=', 'created_at', $date,
        ])->andWhere([
            'user_company_ids' => $companyId
        ])->andWhere([
            'status_client' => ShopOrder::status_client['accepted']
        ])->orWhere([
            'status_client' => ShopOrder::status_client['delivered']
        ])
            :
            ShopOrder::find()->where([
                '>=', 'created_at', $date,
            ])->andWhere([
                'status_client' => ShopOrder::status_client['accepted']
            ])->orWhere([
                'status_client' => ShopOrder::status_client['delivered']
            ]);;

        //sorting and getting orders date in array  *ob*

        $dates = [];

        foreach ($orders->all() as $order) {
            $dates[] = $order['created_at'];
        }

        //sorter

        usort($dates, function ($time1, $time2) {
            if (strtotime($time1) > strtotime($time2))
                return 1;
            else if (strtotime($time1) < strtotime($time2))
                return -1;
            else
                return 0;
        });

        foreach ($dates as $date) {
            $order = $orders->where(['created_at' => $date])->one();

            $form = new ChartForm();
            $form->name = $order['created_at'];

            if ($order->status_client === ShopOrder::status_client['accepted'] || $order->status_client === ShopOrder::status_client['delivered']) {
                $form->in = 0;
                $form->out = $order['total_price'];
            } else {
                $form->out = 0;
                $form->in = $order['total_price'];
            }
            $forms[] = $form;

        }

        $result = [
            'forms' => $forms,
            'orders' => $orders,
            'ordersByStatus' => $ordersByStatus,
        ];
        return $result;
    }

    // todo:end

    #endregion

    #region saveOrderFromApi

    public function testSaveOrderFromApi()
    {
        $data = $this->saveOrderFromApi();
        vd($data);
    }

    public function saveOrderFromApi($catalog, $amount, $user, $company)
    {
        $order = new ShopOrder();
        $order->configs->rules = [
            [validatorSafe]
        ];
        $order->user_id = $user->id;
        $order->contact_phone = $user->phone;
        $order->contact_name = $user->name;
        $order->user_company_ids = $company->id;
        $order->status_callcenter = ShopOrder::status_callcenter['new'];
        $order->status_client = ShopOrder::status_client['accepted'];

        
        if ($order->save()) {
            $this->modelSave($order);
            $orderItem = new ShopOrderItem();
            $orderItem->configs->rules = [
                [validatorSafe]
            ];
            $orderItem->shop_catalog_id = $catalog->id;
            $orderItem->shop_order_id = $order->id;
            $orderItem->amount = $amount;
            $orderItem->price = $catalog->price;

            if ($orderItem->save()) {
                $this->modelSave($orderItem);
                return $order;
            } else
                return $orderItem->errors();
        } else
            $order->errors();
    }

    #endregion saveOrderFromCpaApi

    #region saveOrderFromCpaApi
    public function saveOrderFromCpaApi($user_name, $phone_number, $catalog_id, $amount, $cpas_track_id = null, $source_id = null)
    {
        $catalog = ShopCatalog::findOne($catalog_id);

        $order = new ShopOrder();
        $order->contact_phone = $phone_number;
        $order->contact_name = $user_name;
        $order->cpas_track = $cpas_track_id;
        $order->source = $source_id;
        $order->status_callcenter = ShopOrder::status_callcenter['new'];
        $order->status_client = ShopOrder::status_client['accepted'];
        $order->configs->rules = [
            [validatorSafe]
        ];

        $order->columns();

        if ($order->save()) {


            $order_item = new ShopOrderItem();
            $order_item->shop_catalog_id = $catalog_id;
            $order_item->shop_order_id = $order->id;
            $order_item->user_company_id = $catalog->user_company_id ?? 0;
            $order_item->price = $catalog->price ?? 0;
            $order_item->amount = $amount;
            $order_item->amount_partial = $amount;
            $catalog = ShopCatalog::findOne($catalog_id);
            if ($catalog) {
                //vdd($catalog);
                $user_company_id = $catalog->user_company_id;
                $ware = ShopCatalogWare::find()->where(['shop_catalog_id' => $catalog_id])->one();
                if ($ware) {
                    $ware_id = $ware->ware_id;
                    $best_before = $ware->best_before;
                    $order_item->user_company_id = $user_company_id;
                    $order_item->ware_id = $ware_id;
                    $order_item->best_before = $best_before;
                }


            }
            $order_item->configs->rules = [
                [validatorSafe]
            ];

            if ($order_item->save())
                return $order->id;
        }
        return false;
    }

    public function testSaveOrderFromCpaApi()
    {
        $catalog_id = 20;
        $user_name = 'new user';
        $user_phone = '998908766565';
        $amount = '3';
        vdd($this->saveOrderFromCpaApi($user_name, $user_phone, $catalog_id, $amount));
    }

    #endregion


    #region

    #endregion


    #region OrderList

    public function OrderItemsList($ids = [])
    {

        $orderItems = ShopOrderItem::find()
            ->where([
                'shop_order_id' => $ids
            ])
            ->all();

        //vdd($orderItems);
        $items = [];
        $i = 0;
        /** @var ShopOrderItem $orderItem */
        foreach ($orderItems as $orderItem) {
            $catalog = ShopCatalog::findOne($orderItem->shop_catalog_id);

            $element = ShopElement::findOne($catalog->shop_element_id);
            $product = ShopProduct::findOne($element->shop_product_id);

            $item = new ShopOrderItemDForm();
            $item->id = ++$i;
            $item->image = '/uploaz/' . App . '/ShopProduct/image/' . $product->id . '/' . ZArrayHelper::getValue($product->image, 0);
            $item->name = $product->name;
            $item->amount = $orderItem->amount;
            $item->price = $orderItem->price;

            $items[] = $item;

        }

        return $items;
    }

    #endregion


    /**
     *
     * Function  getOrderStatus
     *
     * Zakazni idsi boyicha statusini chiqarish
     *
     * @param $id
     * @return  string
     */
    // todo:start
    public function getOrderStatus($id)
    {
        if (is_array($id))
            return ShopOrder::status_client['accepted'];

        $order = ShopOrder::findOne($id);

        if ($order === null)
            return ShopOrder::status_client['accepted'];
        return $order->status_client;
    }

    // todo:end
    public function getOrderInfo()
    {
        $order = ShopOrder::find()->all();
        vd($order);

    }

    public function beforeSaveOrder(ShopOrder $model)
    {
        $items = ShopOrderItem::find()
            ->where([
                'shop_order_id' => $model->id
            ])
            ->all();

        $ids = [];
        /** @var  ShopOrderItem $item */
        foreach ($items as $item) {

            if ($item->shop_catalog_id === null)
                continue;

            $catalog = ShopCatalog::findOne($item->shop_catalog_id);

            if ($catalog === null)
                continue;

            $elem = ShopElement::findOne($catalog->shop_element_id);

            if ($elem === null)
                continue;

            $ids[] = $elem->id;

        }

        $model->shop_element_ids = $ids;

    }

    public function orderApproved(ShopOrder  $order)
    {
        $b1 = $order->status_callcenter === ShopOrder::status_callcenter['approved'];
        $b2 = $order->status_callcenter !== ZArrayHelper::getValue($order->oldAttributes, 'status_callcenter');

        if ($b1 && $b2) {
            $order->status_logistics = ShopOrder::status_logistics['complect_wait'];
            $order->date_approve = Az::$app->cores->date->dateTime();
        }

        return $order;


    }

    public function nullShipment(ShopOrder $order)
    {
        //start | DavlatovRavshan | 10.10.2020
        $excludes = [
            'new' => Az::l('Новый'),
            'complect_wait' => Az::l('В ожидании комплектации'),
            'on_complecting' => Az::l('На комплектации'),
            'notset' => Az::l('Не комплект'),
            'shipment_ready' => Az::l('Готов к отгрузке'),
            /*'cancelled' => Az::l('Отменен колл центром'),
            'part_refunded' => Az::l('Возврат частично'),
            'delivery_failure' => Az::l('Отказ во время доставки'),
            'delivery_transfer' => Az::l('Перенос даты доставки'),
            'exchange' => Az::l('Обмен'),
            'cancel' => Az::l('Отменено'),
            'annulled' => Az::l('Аннулирован'),*/
        ];

        if (ZArrayHelper::keyExists($order->status_logistics, $excludes)) {
            $order->shop_shipment_id = null;
        }
        //end | DavlatovRavshan | 10.10.2020

    }

    public function enterOrder(ShopOrder $order)
    {

        $cancels = [
            'cancelled',
            'cancel',
            'delivery_failure',
        ];

        $oldAttributes = ZArrayHelper::getValue($order->oldAttributes, 'status_logistics');

        $b1 = ZArrayHelper::isIn($order->status_logistics, $cancels);
        $b2 = !ZArrayHelper::isIn($oldAttributes, $cancels);
        //$b2 = $order->status_logistics !== ZArrayHelper::getValue($order->oldAttributes, 'status_logistics');

        if ($b1 && $b2) {
            Az::$app->market->wares->createWareEnter($order->id, WareEnter::source['cancel']);
        }

    }

    public function exitOrder(ShopOrder $order)
    {
        $b1 = $order->status_logistics === ShopOrder::status_logistics['shipment_ready'];
        $b2 = $order->status_logistics !== ZArrayHelper::getValue($order->oldAttributes, 'status_logistics');

        if ($b1 && $b2) {
            Az::$app->market->wares->createWareExit($order->id);
        }
    }

    #region requestCpa

    public function sendStatusToCpa(ShopOrder $model)
    {

        if ($model->isNewRecord)
            return null;

        if (empty($model->source))
            return null;
        $user_company = UserCompany::findOne($model->source);
        if ($user_company) {

            $token = $user_company->auth_key;

            if ($model->cpas_track) {
                //if ($status === 'status_callcenter')
                switch ($model->status_callcenter) {
                    case 'cancel':
                        $this->status_cpa = 'cancel';
                        break;
                    case 'approved':
                        $this->status_cpa = 'accept';
                        break;
                    case 'double':
                        $this->status_cpa = 'trash';
                        break;
                    case 'incorrect':
                        $this->status_cpa = 'trash';
                        break;
                    case 'not_ordered':
                        $this->status_cpa = 'trash';
                        break;

                    default:
                        return null;
                        break;


                }
                /*else if ( $status === 'status_logistics')
                    switch ($model->status_logistics){
                        case 'cancel':
                            $this->status_cpa = 'cancel';
                            break;
                        case 'completed':
                            $this->status_cpa = 'accept';
                            break;
                        case 'delivery_failure':
                            $this->status_cpa = 'cancel';
                            break;

                        default:
                            return null;
                            break;
                    }*/
            }


            //vdd($this->status_cpa);
            if ($this->status_cpa) {
                $this->requestStatus($token, $model->cpas_track);
                return true;
            }

        }

        return null;

    }

    public function sendStatusLogistics(ShopOrder $model)
    {
        if ($model->isNewRecord)
            return null;
        $token = 'qvhFgB9YC4UUHfZNG6Pd8WZ6k7H6waw5Vg2UeAXJgKe44fFxSwzStCMQ3jmv2gtz';
        $status_logistics = $model->status_logistics;
        if ($model->cpas_track) {
            switch ($status_logistics) {
                case 'cancel':
                    $this->status_cpa = 'cancel';
                    break;
                case 'completed':
                    $this->status_cpa = 'accept';
                    break;
                case 'delivery_failure':
                    $this->status_cpa = 'cancel';
                    break;

                default:
                    return null;
                    break;
            }
        }

        if ($this->status_cpa) {
            $this->requestStatus($token, $model->cpas_track);
            return true;
        }
        return null;
    }


    #region requestStatus


    public function requestStatus($token, $track_id)
    {
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ];
        $this->client = new Client();
        $response = $this->client->request($this->methods['get'], 'http://arbit.zetsoft.uz/api/cpas/lead/status-lead.aspx', [
            'headers' => $headers,
            'query' => [
                'track_id' => $track_id,
                'status' => $this->status_cpa
            ]
        ]);
        return json_decode($response->getbody());
    }


    #endregion


    #endregion

}
