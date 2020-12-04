<?php

namespace zetsoft\service\market;

use yii\helpers\ArrayHelper;
use zetsoft\models\place\PlaceAdress;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopCatalogWare;
use zetsoft\models\shop\ShopCourier;
use zetsoft\models\shop\ShopElement;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\shop\ShopProduct;
use zetsoft\models\shop\ShopShipment;
use zetsoft\models\user\UserCompany;
use zetsoft\models\ware\Ware;
use zetsoft\models\ware\WareAccept;
use zetsoft\models\ware\WareEnter;
use zetsoft\models\ware\WareEnterItem;
use zetsoft\models\ware\WareExit;
use zetsoft\models\ware\WareExitItem;
use zetsoft\models\ware\WareReturn;
use zetsoft\models\ware\WareTrans;
use zetsoft\models\ware\WareTransItem;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;
use function Dash\isEmpty;
use function zetsoft\apisys\edit\returnn;

class Wares extends ZFrame
{

    public function changeStatusChilds($model)
    {

        $statuses = [
            'complect_wait' => Az::l('В ожидании комплектации'),
            'on_complecting' => Az::l('На комплектации'),
            'shipment_ready' => Az::l('Готов к отгрузке'),
        ];

        $childs = null;
        if (!empty($model->children))
            $childs = ShopOrder::findAll([
                'id' => $model->children,
            ]);

        /** @var ShopOrder $child */
        if (!empty($childs)) {
            foreach ($childs as $child) {

                $child->parent = $model->id;
                if (ZArrayHelper::keyExists($model->status_logistics, $statuses))
                    $child->status_logistics = $model->status_logistics;

                /*
                $child->status_client = $model->status_client;
                $child->status_deliver = $model->status_deliver;
                */
                
                $child->configs->rules = validatorSafe;

                $child->saveAll();

            }
        }

    }

    public function changeParent($model)
    {

        $parent = ShopOrder::findOne([
            'parent' => $model->id
        ]);

        if (!$parent)
            return null;

        if (!$model->children || ZArrayHelper::isIn($parent->id, (array)$model->children)) {
            $parent->parent = null;
            $parent->configs->rules = validatorSafe;
            $parent->save();
        }

    }


    public function addInfo($order_id, $model = null, $save = true)
    {

        //start | DavlatovRavshan | 10.10.2020
        if (!$save)
            $order = $model;
        else
            $order = ShopOrder::findOne($order_id);

        if (!$order)
            return null;

        $order_items = ShopOrderItem::findAll([
            'shop_order_id' => $order_id
        ]);

        if (empty($order_items))
            return null;

        $price = 0;

        $user_company_ids = [];
        $ware_ids = [];
        $shop_element_ids = [];

        /** @var ShopOrderItem $order_item */
        foreach ($order_items as $order_item) {

            $price += (int)$order_item->price_all_partial;

            if (!empty((int)$order_item->user_company_id))
                $user_company_ids[(int)$order_item->user_company_id] = (int)$order_item->user_company_id;

            if (!empty((int)$order_item->ware_id))
                $ware_ids[(int)$order_item->ware_id] = (int)$order_item->ware_id;

            if (!empty($order_item->shop_catalog_id)) {
                $shop_catalog = ShopCatalog::findOne($order_item->shop_catalog_id);
                if ($shop_catalog)
                    $shop_element_ids[(int)$shop_catalog->shop_element_id] = (int)$shop_catalog->shop_element_id;
            }

        }

        $order->price = $price;
        $order->user_company_ids = $user_company_ids;
        $order->ware_ids = $ware_ids;
        $order->shop_element_ids = $shop_element_ids;

        if ($save) {
            $order->configs->rules = validatorSafe;
            $order->save();
        }

        //end | DavlatovRavshan | 10.10.2020

    }


    public function cloneOrderItems($modelId, $newModelId)
    {
        $shop_order_items = ShopOrderItem::findAll([
            'shop_order_id' => $modelId,
        ]);

        foreach ($shop_order_items as $shop_order_item) {

            /** @var ShopOrderItem $newOrderItem */
            $newOrderItem = $this->modelClone(ShopOrderItem::class, $shop_order_item->id);

            Az::debug($newOrderItem->id, 'Cloned');

            $newOrderItem->shop_order_id = $newModelId;
            $newOrderItem->configs->rules = validatorSafe;

            if ($newOrderItem->save()) {
                Az::debug($newOrderItem->id, 'Saved');
            }

        }
    }

    public function changeStatusLogisticsTo($id, $type)
    {

        $shop_order = ShopOrder::findOne($id);
        if ($shop_order) {
            $shop_order->status_logistics = $type;
            $shop_order->configs->rules = validatorSafe;
            $shop_order->save();
        }

    }


    #region  WareAccpet

    public function getRefund($ware_accept)
    {

        $returns = $ware_accept->dc_returns_group;
        if (empty($returns)) {
            return null;
        }

        $total = 0;
        foreach ($returns as $return) {
            $ware_return = WareReturn::findOne($return);
            if ($ware_return)
                $total += (int)$ware_return->total_price;
        }

        return $total;

    }

    public function getOrdersAccept($shop_shipment_id)
    {

        return ShopOrder::find()
            ->where([
                'shop_shipment_id' => $shop_shipment_id,
            ]);

    }

    public function getTotal($shop_shipment_id)
    {

        return $this->getOrdersAccept($shop_shipment_id)->count();

    }

    public function getCompleted($shop_shipment_id)
    {

        $orders = $this->getOrdersAccept($shop_shipment_id)
            ->andWhere([
                'status_accept' => ShopOrder::status_accept['completed'],
                'parent' => null,
            ]);

        return $orders->count();

    }

    public function getCompletedAll($shop_shipment_id)
    {

        $orders = $this->getOrdersAccept($shop_shipment_id)
            ->andWhere([
                'status_accept' => [
                    ShopOrder::status_accept['completed'],
                    ShopOrder::status_accept['part_paid'],
                ],
                'parent' => null,
            ]);

        return $orders->count();

    }

    public function getRefusal($shop_shipment_id)
    {

        $orders = $this->getOrdersAccept($shop_shipment_id)
            ->andWhere([
                'status_accept' => ShopOrder::status_accept['delivery_failure']
            ]);

        return $orders->count();

    }

    public function getDateTransfer($shop_shipment_id)
    {

        $orders = $this->getOrdersAccept($shop_shipment_id)
            ->andWhere([
                'status_accept' => ShopOrder::status_accept['delivery_transfer']
            ]);

        return $orders->count();

    }


    public function getTerminal($shop_shipment_id)
    {

        /** @var ShopOrder $order */
        $orders = $this->getOrdersAccept($shop_shipment_id)
            ->andWhere([
                'additional_payment_type' => 'uzcard'
            ])
            ->all();

        $total_terminal = 0;
        foreach ($orders as $order) {
            $total_terminal += (int)$order->additional_received_money;
        }

        return $total_terminal;

    }


    public function getCashless($shop_shipment_id)
    {

        /** @var ShopOrder $order */
        $orders = $this->getOrdersAccept($shop_shipment_id)
            ->andWhere([
                'additional_payment_type' => 'transfer'
            ])
            ->all();

        $total_terminal = 0;
        foreach ($orders as $order) {
            $total_terminal += (int)$order->additional_received_money;
        }

        return $total_terminal;

    }


    public function getSalesAmount($shop_shipment_id)
    {

        /** @var ShopOrder $order */
        $orders = $this->getOrdersAccept($shop_shipment_id)
            ->andWhere([
                'status_accept' => [
                    ShopOrder::status_accept['completed'],
                    ShopOrder::status_accept['part_paid'],
                ]
            ])
            ->all();

        /** @var ShopOrderItem $order_item */

        $price = 0;
        foreach ($orders as $order) {
            $price += (int)$order->price;
        }

        return $price;

    }

    //start: MurodovMirbosit
    public function aceptOrders($model)
    {
        /** @var WareAccept $model */
        if (empty($model->shop_shipment_id))
            return null;

        $orders = ShopOrder::find()
            ->where([
                'shop_shipment_id' => $model->shop_shipment_id,
            ])
            ->all();

        /** @var ShopOrder $order */
        foreach ($orders as $order) {

            if ($order->status_accept === ShopOrder::status_accept['part_paid']) {

                $order_items = ShopOrderItem::find()
                    ->where([
                        'shop_order_id' => $order->id
                    ])
                    ->andWhere(['!=', 'amount_return', '0'])
                    ->all();

                /** @var ShopOrderItem $order_item */
                foreach ($order_items as $order_item) {
                    Az::$app->market->wares->createWareEnterItem($order_item, WareEnter::source['return'], 'part');
                }

            }

            if (!empty($order->status_accept))
                $order->status_logistics = $order->status_accept;

            $order->configs->rules = validatorSafe;
            $order->save();

        }

    }

    //end: MurodovMirbosit
    //todo::end;

    //start|MurodovMirbosit 21.10.2020
    public function getReturnIdByOrderItem($model)
    {
        /** @var WareReturn $model */
        $shop_order_items = ShopOrderItem::find()->where(['shop_order_id' => $model->shop_order_ids])->all();

        /** @var ShopOrderItem $shop_order_item */
        foreach ($shop_order_items as $shop_order_item) {
            if (!empty($shop_order_item->ware_return_id)) {
                $shop_order_item->ware_return_id = $model->id;
            }
            $shop_order_item->ware_return_id = null;
        }
    }

    //end|MurodovMirbosit 21.10.2020
    public function getCourierReward($shop_shipment_id, $shop_courier_id)
    {

        /** @var ShopOrder $order */
        $completed = $this->getCompleted($shop_shipment_id);
        $shop_courier = ShopCourier::findOne($shop_courier_id);

        if ($shop_courier === null)
            return 0;

        return (int)$shop_courier->award_order * (int)$completed;

    }


    public function getExchangeReward($shop_shipment_id, $shop_courier_id)
    {

        /** @var ShopOrder $order */
        $exchange = $this->getExchange($shop_shipment_id);
        $shop_courier = ShopCourier::findOne($shop_courier_id);

        if ($shop_courier === null)
            return 0;

        return (int)$shop_courier->award_exchange * (int)$exchange;

    }


    public function getSalaryCourier($ware_accept)
    {

        return (int)$ware_accept->bonus + (int)$ware_accept->courier_reward + (int)$ware_accept->exchange_reward + (int)$ware_accept->refund_reward + (int)$ware_accept->add_delivery;

    }


    public function getRemain($ware_accept)
    {

        $sum = (int)$ware_accept->terminal + (int)$ware_accept->refund + (int)$ware_accept->converted + (int)$ware_accept->salary_courier + (int)$ware_accept->cashless;

        return $ware_accept->sales_amount - $sum;

    }


    public function getRefundReward($ware_accept, $shop_courier_id)
    {

        /** @var ShopOrder $order */
        $shop_courier = ShopCourier::findOne($shop_courier_id);

        $returns = $ware_accept->dc_returns_group;

        if (empty($returns))
            return 0;

        return (int)$shop_courier->award_return * (int)count($returns);

    }

    public function getCancel($shop_shipment_id)
    {

        $orders = $this->getOrdersAccept($shop_shipment_id)
            ->andWhere([
                'status_accept' => ShopOrder::status_accept['cancel']
            ]);

        return $orders->count();

    }

    public function getExchange($shop_shipment_id)
    {

        $orders = $this->getOrdersAccept($shop_shipment_id)
            ->andWhere([
                'status_accept' => ShopOrder::status_accept['exchange']
            ]);

        return $orders->count();

    }


    public function getAddDelivery($shop_shipment_id)
    {

        /** @var ShopOrder $order */
        $orders = $this->getOrdersAccept($shop_shipment_id)->all();

        $total_terminal = 0;
        foreach ($orders as $order) {
            $total_terminal += (int)$order->additional_deliver;
        }

        return $total_terminal;

    }


    #endregion


    #region Cores

    public function test()
    {
        // $this->coordinatesTest();
        $this->coordinatesTargetTest();
    }

    #endregion

    #region enter
    /**
     *
     * Function  enter
     * @param WareEnterItem $model
     * @param int $wareEnterId
     * @throws \Exception
     */

    public function EnterTest()
    {
        $data = $this->enter(WareEnterItem::findOne(1), 1);
        vd($data);
    }

    public function enter(WareEnterItem $model)
    {

        $ware_enter = WareEnter::findOne($model->ware_enter_id);

        if (!$ware_enter)
            return null;

        $user_company_id = $ware_enter->user_company_id;
        $shop_element_id = $model->shop_element_id;
        $ware_id = $ware_enter->ware_id;

        if (!empty($model->shop_catalog_id)) {
            $shop_catalog = ShopCatalog::findOne($model->shop_catalog_id);
        } else {
            $shop_catalog = ShopCatalog::findOne([
                'user_company_id' => $user_company_id,
                'shop_element_id' => $shop_element_id
            ]);
        }

        if ($shop_catalog !== null) {
            $shop_catalog->title = $model->title;
            $shop_catalog->amount += (int)$model->amount;
            $shop_catalog->price_old = $shop_catalog->price;
            $shop_catalog->user_company_id = $user_company_id;
            $shop_catalog->price = (int)$model->price;
            $shop_catalog->available = true;
            $shop_catalog->save();
        } else {
            $shop_catalog = new ShopCatalog();
            $shop_catalog->title = $model->title;
            $shop_catalog->user_company_id = $user_company_id;
            $shop_catalog->shop_element_id = $model->shop_element_id;
            $shop_catalog->amount = $model->amount;
            $shop_catalog->price = (int)$model->price;
            $shop_catalog->currency = $model->currency;
            $shop_catalog->available = true;
            $shop_catalog->configs->rules = [
                [
                    validatorSafe
                ]
            ];
            $shop_catalog->save();
        }

        $shop_catalog_ware = ShopCatalogWare::findOne([
            'shop_catalog_id' => $shop_catalog->id,
            'ware_id' => $ware_id,
            /*'best_before' => $model->best_before,*/
        ]);

        if ($shop_catalog_ware !== null) {
            $shop_catalog_ware->amount += (int)$model->amount;
        } else {
            $shop_catalog_ware = new ShopCatalogWare();
            $shop_catalog_ware->ware_id = $ware_enter->ware_id;
            $shop_catalog_ware->amount = $model->amount;
            $shop_catalog_ware->shop_catalog_id = $shop_catalog->id;
            //$shop_catalog_ware->best_before = $model->best_before;
        }
        $shop_catalog_ware->configs->rules = [
            [
                validatorSafe
            ]
        ];
        $shop_catalog_ware->save();
        $model->shop_catalog_ware_id = $shop_catalog_ware->id;
        $model->shop_catalog_id = $shop_catalog->id;

    }

    public function enter2(WareEnterItem $model)
    {
        $ware_enter = WareEnter::findOne($model->ware_enter_id);

        if ($ware_enter !== null && $model->ware_enter_id !== null) {
            $user_company_id = $ware_enter->user_company_id;
            $shopCatalog = ShopCatalog::find()
                ->where([
                    'user_company_id' => $user_company_id,
                    'shop_element_id' => $model->shop_element_id
                ])
                ->one();

            if ($shopCatalog !== null) {
                $shopCatalog->amount += (int)$model->amount;
                $shopCatalog->price_old = $shopCatalog->price;
                $shopCatalog->user_company_id = $user_company_id;
                $shopCatalog->price = $model->price;
                $shopCatalog->available = true;
                $shopCatalog->save();
            } else {
                $shopCatalog = new ShopCatalog();
                $shopCatalog->user_company_id = $user_company_id;
                $shopCatalog->shop_element_id = $model->shop_element_id;
                $shopCatalog->amount = $model->amount;
                $shopCatalog->price = $model->price;
                $shopCatalog->currency = $model->currency;
                $shopCatalog->available = true;
                $shopCatalog->save();
            }
            $shopCatalogWare = ShopCatalogWare::find()->where([
                'shop_catalog_id' => $shopCatalog->id,
                'shop_element_id' => $shopCatalog->shop_element_id,
                'best_before' => $model->best_before
            ])->one();

            if ($shopCatalogWare !== null) {
                $shopCatalogWare->amount += (int)$model->amount;
            } else {
                $shopCatalogWare = new ShopCatalogWare();
                $shopCatalogWare->ware_id = $ware_enter->ware_id;
                $shopCatalogWare->amount = $model->amount;
                $shopCatalogWare->shop_catalog_id = $shopCatalog->id;
                $shopCatalogWare->shop_element_id = $shopCatalog->shop_element_id;
                $shopCatalogWare->best_before = $model->best_before;
            }
            $model->shop_catalog_ware_id = $shopCatalogWare;
            return $shopCatalogWare->save();
        }
        return false;
    }

    public function exitTest()
    {
        $model = new WareExitItem();

        $model->ware_exit_id = '12';
        $model->shop_catalog_id = '8';
        $model->ware_series_id = '1';
        $model->amount = '10';

        $this->exit($model);
    }

    public function exit($model)
    {

        /** @var WareEnterItem $model */

        $wareEnter = WareEnter::findOne($model->ware_enter_id);

        if (!$wareEnter)
            return null;

        $shopCatalog = ShopCatalog::findOne([
            'shop_element_id' => $model->shop_element_id,
            'user_company_id' => $wareEnter->user_company_id
        ]);

        if ($shopCatalog !== null) {

            $shopCatalog->amount -= (int)$model->amount;
            $shopCatalog->save();

            $shopCatalogWare = ShopCatalogWare::findOne([
                'shop_catalog_id' => $shopCatalog->id,
                'ware_id' => $wareEnter->ware_id,
                'best_before' => $model->best_before
            ]);

            if ($shopCatalogWare !== null) {
                $shopCatalogWare->amount -= (int)$model->amount;
                $shopCatalogWare->configs->rules = [
                    [
                        validatorSafe
                    ]
                ];
                $shopCatalogWare->save();
            }

        }

    }

    /**
     *  TODO: check amount. shopCatalogWare minusga kirmasligi kere
     * Function  wareExit
     * @param WareExitItem $model
     * @throws \Exception
     */
    public function wareExit(WareExitItem $model)
    {
        $shopCatalog = ShopCatalog::findOne($model->shop_catalog_id);

        if ($shopCatalog !== null) {

            if (!empty($shopCatalog->parent))
                $shopCatalog = ShopCatalog::findOne($shopCatalog->parent);
            if($shopCatalog === null){
                return ;
            }
            $shopCatalog->amount -= (int)$model->amount;
            $shopCatalog->save();

            $wareExit = WareExit::findOne($model->ware_exit_id);

            if (!$wareExit)
                return null;

            $shopCatalogWare = ShopCatalogWare::findOne([
                'shop_catalog_id' => $model->shop_catalog_id,
                'ware_id' => $wareExit->ware_id,
                'best_before' => $model->best_before,
            ]);

            if ($shopCatalogWare !== null) {
                $shopCatalogWare->amount -= (int)$model->amount;
                $shopCatalogWare->configs->rules = [
                    [
                        validatorSafe
                    ]
                ];
                $shopCatalogWare->save();
                $model->shop_catalog_ware_id = $shopCatalogWare->id;
            }

        }
    }


    public function wareEnter($model)
    {

        /** @var WareExitItem $model */
        $shopCatalog = ShopCatalog::findOne($model->shop_catalog_id);

        if ($shopCatalog !== null) {

            if (!empty($shopCatalog->parent))
                $shopCatalog = ShopCatalog::findOne($shopCatalog->parent);

            $shopCatalog->amount += (int)$model->amount;
            $shopCatalog->save();

            $wareExit = WareExit::findOne($model->ware_exit_id);

            if (!$wareExit)
                return null;

            $shopCatalogWare = ShopCatalogWare::findOne([
                'shop_catalog_id' => $model->shop_catalog_id,
                'ware_id' => $wareExit->ware_id,
                'best_before' => $model->best_before
            ]);


            if ($shopCatalogWare !== null) {
                $shopCatalogWare->amount += (int)$model->amount;
                $shopCatalogWare->save();
                $model->shop_catalog_ware_id = $shopCatalogWare->id;
            }

        }

    }


    public function enterWareReturnItems($ware_return_id)
    {

        $shop_order_items = ShopOrderItem::find()
            ->where([
                'ware_return_id' => $ware_return_id
            ])
            ->all();
        /** @var ShopOrderItem $shop_order_item */
        foreach ($shop_order_items as $shop_order_item) {

            if (!$shop_order_item)
                return null;

            $shop_catalog = ShopCatalog::findOne($shop_order_item->shop_catalog_id);

            if (!$shop_catalog)
                return null;

            $shop_catalog->amount += (int)$shop_order_item->amount;
            $shop_catalog->save();

            /** @var ShopCatalogWare $shop_catalog_ware */
            $shop_catalog_ware = ShopCatalogWare::find()
                ->where([
                    'shop_catalog_id' => $shop_order_item->shop_catalog_id,
                    'ware_id' => $shop_order_item->ware_id,
                ])
                ->one();


            if (!$shop_catalog_ware)
                return null;

            $shop_catalog_ware->amount += (int)$shop_order_item->amount;
            $shop_catalog_ware->save();

        }

    }


    public function enterShopOrderItems($ware_return_id)
    {

        $ware_return = WareReturn::findOne($ware_return_id);

        $shop_order_items = ShopOrderItem::find()
            ->where([
                'ware_return_id' => $ware_return_id
            ])
            ->all();

        /** @var ShopOrderItem $shop_order_item */
        foreach ($shop_order_items as $shop_order_item) {

            if (!$shop_order_item)
                return null;

            $shop_catalog = ShopCatalog::findOne($shop_order_item->shop_catalog_id);

            if (!$shop_catalog)
                return null;

            $shop_catalog->amount += (int)$shop_order_item->amount;
            $shop_catalog->save();

            /** @var ShopCatalogWare $shop_catalog_ware */
            $shop_catalog_ware = ShopCatalogWare::find()
                ->where([
                    'shop_catalog_id' => $shop_order_item->shop_catalog_id,
                    'ware_id' => $ware_return->ware_id,
                ])
                ->one();

            if (!$shop_catalog_ware)
                return null;

            $shop_catalog_ware->amount += (int)$shop_order_item->amount;
            $shop_catalog_ware->save();

        }

    }


    public function enterShopOrderItem($shop_order_item_id)
    {

        $shop_order_item = ShopOrderItem::findOne($shop_order_item_id);

        if (!$shop_order_item)
            return null;

        $shop_catalog = ShopCatalog::findOne($shop_order_item->shop_catalog_id);

        if (!$shop_catalog)
            return null;

        $shop_catalog->amount += (int)$shop_order_item->amount;
        $shop_catalog->save();

        /** @var ShopCatalogWare $shop_catalog_ware */
        $shop_catalog_ware = ShopCatalogWare::find()
            ->where([
                'shop_catalog_id' => $shop_order_item->shop_catalog_id,
                'ware_id' => $shop_order_item->ware_id,
            ])
            ->one();

        if (!$shop_catalog_ware)
            return null;

        $shop_catalog_ware->amount += (int)$shop_order_item->amount;
        $shop_catalog_ware->save();

    }

    public function exitShopOrderItems($shop_order_id)
    {

        $shop_order_items = ShopOrderItem::find()
            ->where([
                'shop_order_id' => $shop_order_id
            ])
            ->all();

        /* @var ShopOrderItem $shop_order_item */
        foreach ($shop_order_items as $shop_order_item) {

            if (!$shop_order_item)
                return null;

            if ($shop_order_item->check_return === true) {
                $this->enterShopOrderItem($shop_order_id);
            }

            $shop_catalog = ShopCatalog::findOne($shop_order_item->shop_catalog_id);

            if (!$shop_catalog)
                return null;

            $shop_catalog->amount -= (int)$shop_order_item->amount;
            $shop_catalog->save();

            /** @var ShopCatalogWare $shop_catalog_ware */
            $shop_catalog_ware = ShopCatalogWare::find()
                ->where([
                    'shop_catalog_id' => $shop_order_item->shop_catalog_id,
                    'ware_id' => $shop_order_item->ware_id,
                ])
                ->one();

            if (!$shop_catalog_ware)
                return null;

            $shop_catalog_ware->amount -= (int)$shop_order_item->amount;
            $shop_catalog_ware->save();

        }

    }


    public function enterShopOrder(ShopOrder $model)
    {

        $shop_order_items = ShopOrderItem::find()
            ->where([
                'shop_order_id' => $model->id
            ])
            ->all();
        /*$wareEnter = new WareEnter();
        $wareEnter->user_company_id = $shop;
        $wareEnter->ware_id = ;
        $wareEnter->source = WareEnter::source['trans'];
        $wareEnter->save();*/

        /* @var ShopOrderItem $shop_order_item */
        foreach ($shop_order_items as $shop_order_item) {

            if (!$shop_order_item)
                return null;

            $shop_catalog = ShopCatalog::findOne($shop_order_item->shop_catalog_id);

            if (!$shop_catalog)
                return null;

            $shop_catalog->amount -= (int)$shop_order_item->amount;
            $shop_catalog->save();

            /** @var ShopCatalogWare $shop_catalog_ware */
            $shop_catalog_ware = ShopCatalogWare::find()
                ->where([
                    'shop_catalog_id' => $shop_order_item->shop_catalog_id,
                    'ware_id' => $shop_order_item->ware_id,
                ])
                ->one();

            if (!$shop_catalog_ware)
                return null;

            $shop_catalog_ware->amount += (int)$shop_order_item->amount;
            $shop_catalog_ware->save();

        }

    }

    #endregion

    #region Ware Trans
//    Transport catalogs between wares
    public function trans2(WareTransItem $model)
    {
        $wareTrans = WareTrans::findOne($model->ware_trans_id);

        if ($wareTrans !== null) {
            $shopCatalog = ShopCatalog::findOne($model->shop_catalog_id);
            /**
             * Create Ware Exit  Item
             */
            $exitItem = new WareExitItem();
            $exitItem->amount = $model->amount;
            $exitItem->source = WareExitItem::source['trans'];
            $exitItem->shop_catalog_id = $model->shop_catalog_id;
            $exitItem->best_before = $model->best_before;
            $exitItem->ware_exit_id = $wareTrans->ware_exit_id;
            $exitItem->save();

            /**
             * Create Ware Enter Item
             */
            $enterItem = new WareEnterItem();
            $enterItem->amount = $model->amount;
            $enterItem->ware_enter_id = $wareTrans->ware_enter_id;
            $enterItem->best_before = $model->best_before;
            $enterItem->price = $shopCatalog->price;
            $enterItem->shop_element_id = $shopCatalog->shop_element_id;

            $shop_product_id = ShopElement::findOne($shopCatalog->shop_element_id)->id;

            $enterItem->measure = ShopProduct::findOne($shop_product_id)->measure;
            $enterItem->currency = $shopCatalog->currency;
            $enterItem->save();
        }
    }


    public function trans(WareTransItem $model)
    {

        $ware_trans = WareTrans::findOne($model->ware_trans_id);

        $ware_exit_item = new WareExitItem();

        $ware_exit_item->ware_exit_id = $ware_trans->ware_exit_id;
        $ware_exit_item->shop_catalog_id = $model->shop_catalog_id;
        $ware_exit_item->best_before = $model->best_before;
        $ware_exit_item->ware_series_id = $model->ware_series_id;
        $ware_exit_item->amount = $model->amount;

        $ware_exit_item->save();

        $ware_enter_item = new WareEnterItem();
        $ware_enter_item->ware_enter_id = $ware_trans->ware_enter_id;
        $ware_enter_item->shop_catalog_id = $model->shop_catalog_id;
        $ware_enter_item->best_before = $model->best_before;
        $ware_enter_item->ware_series_id = $model->ware_series_id;
        $ware_enter_item->amount = $model->amount;
        $ware_enter_item->measure = $model->measure;
        $ware_enter_item->configs->rules = [
            [
                validatorSafe
            ]
        ];

        $ware_enter_item->save();

    }

    #endregion

    #region CoordinateTarget

    public function coordinatesTargetTest()
    {
        $shipmentId = 29;
        $data = $this->coordinatesTarget($shipmentId);
        vdd($data);
    }


    public function coordinatesTarget(int $id)
    {
        $val = $this->coordinates($id);
        $tempOrder = null;
        $tempWares = null;

        $orderAdress = ZArrayHelper::getValue($val, 'ordersAdress');

        if (!empty($orderAdress))
            foreach ($orderAdress as $key => $orders) {
                $tempOrder[$orders][] = $key;
            }

        $waresAdress = ZArrayHelper::getValue($val, 'waresAdress');

        if (!empty($waresAdress))
            foreach ($waresAdress as $key => $wares) {
                foreach ($wares as $place)
                    $tempWares[$place][] = $key;
            }

        $return['ordersAdress'] = $tempOrder;
        $return['waresAdress'] = $tempWares;
        return $return;
    }

    #endregion

    #region urlGeneratorGoogle
    public function urlGeneratorGoogle(int $id)
    {
        $val = $this->coordinates($id);
        $tempOrder = null;
        $tempWares = null;

        $orderAdress = ZArrayHelper::getValue($val, 'ordersAdress');

        if (!empty($orderAdress))
            foreach ($orderAdress as $key => $orders) {
                $tempOrder[$orders][] = $key;
            }

        $waresAdress = ZArrayHelper::getValue($val, 'waresAdress');

        if (!empty($waresAdress))
            foreach ($waresAdress as $key => $wares) {
                foreach ($wares as $place)
                    $tempWares[$place][] = $key;
            }

        $return['ordersAdress'] = $tempOrder;
        $return['waresAdress'] = $tempWares;


        $PlaceAddressCoordinates = array();
        foreach ($return['ordersAdress'] as $value => $key) {
            $PlaceAddressCoordinates[] = $value;
        }
        $placeAdresses = PlaceAdress::find()
            ->select(["id", "location"])
            ->where([
                'id' => $PlaceAddressCoordinates,
            ])
            ->all();
        $savedPlaceAdresses = ArrayHelper::getColumn($placeAdresses, 'location.0');

        $coord = array();
        foreach ($savedPlaceAdresses as $val) {
            $coord [] = $val['lat'] . ',' . $val['lng'];
            array_push($coord, '/');
        }
        $ready = array_pop($coord);
        $customerAddress = implode('', $coord);


        $urlGoogle = 'https://www.google.com/maps/dir/Chorsu+Tashkent/' . $customerAddress . '&destination&travelmode=Driving';

        return $urlGoogle;
    }

    ##endregion

    #region urlGeneratorYandex
    public function urlGeneratorYandex(int $id)
    {
        $val = $this->coordinates($id);
        $tempOrder = null;
        $tempWares = null;

        $orderAdress = ZArrayHelper::getValue($val, 'ordersAdress');

        if (!empty($orderAdress))
            foreach ($orderAdress as $key => $orders) {
                $tempOrder[$orders][] = $key;
            }

        $waresAdress = ZArrayHelper::getValue($val, 'waresAdress');

        if (!empty($waresAdress))
            foreach ($waresAdress as $key => $wares) {
                foreach ($wares as $place)
                    $tempWares[$place][] = $key;
            }

        $return['ordersAdress'] = $tempOrder;
        $return['waresAdress'] = $tempWares;


        /* @var Ware $PlaceAddressCoordinates */
        $PlaceAddressCoordinates = array();
        if (!empty($return['ordersAdress']))
            foreach ($return['ordersAdress'] as $value => $key) {
                $PlaceAddressCoordinates[] = $value;
            }
        $placeAdresses = PlaceAdress::find()
            ->select(["id", "location"])
            ->where([
                'id' => $PlaceAddressCoordinates,
            ])
            ->all();
        $savedPlaceAdresses = ArrayHelper::getColumn($placeAdresses, 'location.0');

        $coord = array();

        foreach ($savedPlaceAdresses as $val) {
            $coord [] = $val['lat'] . ',' . $val['lng'];
            array_push($coord, '~');
        }

        $ready = array_pop($coord);
        $customerAddress = implode('', $coord);


        $urlYandex = 'https://yandex.ru/maps/?rtext=chorsu+toshkent/' . $customerAddress . '&rtt=auto';

        return $urlYandex;
    }
    ##endregion

    #region coordinates

    public function coordinatesTest()
    {
        $shipmentId = 29;
        $data = $this->coordinates($shipmentId);
        vdd($data);
    }

    final public function coordinates(int $id): array
    {
        $return = [];

        $shopOrder = ShopOrder::find()->where(['shop_shipment_id' => $id])->select(['id', 'place_adress_id'])->asArray()->orderBy('place_adress_id')->all();

        $shop_order_ids = ZArrayHelper::getColumn($shopOrder, 'id');

        $return['ordersAdress'] = ZArrayHelper::map($shopOrder, 'id', 'place_adress_id');

        $orderItems = collect(ShopOrderItem::find()->where(['shop_order_id' => $shop_order_ids])->asArray()->all());

        foreach ($shopOrder as $item) {

            $order_items = $orderItems->where('shop_order_id', $item['id']);

            $were_ids = ZArrayHelper::getColumn($order_items, 'ware_id');
            if (empty($were_ids)) {
                break;
            }
            $wares = Ware::findAll($were_ids);
            $wares_place_ids = ZArrayHelper::getColumn($wares, 'place_adress_id');
            if (empty($wares_place_ids)) {
                break;
            }
            $return['waresAdress'][$item['id']] = $wares_place_ids;

        }


        return $return;
    }


    #endregion

    #region exit


    #endregion

    #region depWare

    public function testDepWare()
    {
        $data = $this->depWare();
        vd($data);
    }

    public function depWare($selectedId = null)
    {

        if (!$selectedId)
            return [];
        $shopCatalog = ShopCatalog::find()
            ->where(['user_company_id' => $selectedId])
            ->all();

        $shop_catalog_ids = ZArrayHelper::getColumn($shopCatalog, 'id');
        if (empty($shop_catalog_ids)) {
            return null;
        }
        $shopCatalogWare = ShopCatalogWare::find()
            ->where([
                'shop_catalog_id' => $shop_catalog_ids
            ])
            ->all();

        $ware_ids = ZArrayHelper::getColumn($shopCatalogWare, 'ware_id');
        if (empty($ware_ids)) {
            return null;
        }
        $ware = \zetsoft\models\ware\Ware::find()
            ->where([
                'id' => $ware_ids
            ])
            ->select([
                'id', 'name'
            ])
            ->asArray()
            ->all();

        if (empty($ware))
            return null;

        return ZArrayHelper::map($ware, 'id', 'name');
    }

    #endregion

    #region WareAccept  #bugfix javohir - 07.07.20 1:30
    public function wareAcceptCalculate($wareAccept)
    {
        /*if(isEmpty($wareAccept->shop_shipment_id))
           return null;*/


        $shopShipment = ShopShipment::findOne($wareAccept->shop_shipment_id);
        $shopOrders = collect(ShopOrder::findAll(['shop_shipment_id' => $shopShipment->id]));
//Result
        $wareAccept->total = count($shopOrders);               //Всего
        $wareAccept->completed = $shopOrders->where('status_accept', ShopOrder::status_accept['completed'])->count();  //Выполнен
        $wareAccept->exchange = $shopOrders->where('status_accept', ShopOrder::status_accept['exchange'])->count();   //Обмен
        $wareAccept->date_transfer = $shopOrders->where('status_accept', ShopOrder::status_accept['delivery_transfer'])->count(); //Перенос даты

        $wareAccept->refusal = $shopOrders->where('status_accept', ShopOrder::status_accept['delivery_failure'])->count();  //Отказ
        $wareAccept->cancel = $shopOrders->where('status_accept', ShopOrder::status_accept['cancel'])->count();   //Отменено

        $courier = ShopCourier::findOne($shopShipment->shop_courier_id);
//Total sums
        $total_price = $shopOrders->sum('total_price');
        $total_deliver_price = $shopOrders->sum('deliver_price');
        $wareAccept->sales_amount = (double)$total_deliver_price + (double)$total_price;  //Сумма реализации
        $wareAccept->courier_reward = $wareAccept->completed * $courier->award_order;       //Вознаграждение курьеру

        $wareAccept->exchange_reward = $wareAccept->exchange * $courier->award_exchange;  //Вознаграждение обмен
        $wareAccept->refund_reward = $wareAccept->exchange * $courier->award_return;    //Вознаграждение за ВДС
        $wareAccept->salary_courier = $wareAccept->courier_reward + $wareAccept->exchange_reward + $wareAccept->refund_reward + ($wareAccept->bonus ? 30000 : 0);  //ЗП курьеру

        $wareAccept->terminal = $shopOrders->whereIn('additional_payment_type', [ShopOrder::additional_payment_type['uzcard'], ShopOrder::additional_payment_type['humo']])->sum('additional_received_money');   //Терминал

        $wareAccept->add_delivery = $shopOrders->where('additional_payment_type', ShopOrder::additional_payment_type['add_deliver'])->sum('additional_received_money');  //Допольнительные доставки

//ВДС - dc_returns_group
        if (!$this->emptyVar($wareAccept->dc_returns_group) && $wareAccept->dc_returns_group != null) {
            $tempOrders = collect(ShopOrder::findAll($wareAccept->dc_returns_group));

            $wareAccept->refund = $tempOrders->sum('additional_received_money'); //Возврат денежных средств
        } else {
            $wareAccept->refund = 0;   //Возврат денежных средств
        }
        $wareAccept->cashless = $shopOrders->where('additional_payment_type', ShopOrder::additional_payment_type['transfer'])->sum('additional_received_money');     //Безналичные

        $wareAccept->remain = $wareAccept->sales_amount - ($wareAccept->salary_courier + $wareAccept->add_delivery + $wareAccept->terminal + $wareAccept->cashless);

//Currency
        $all_currency = Az::$app->payer->currency2->listCurrencies();
        $wareAccept->currency = $all_currency['USD'];
        $wareAccept->converted = round((int)$wareAccept->in_dollar * (int)$wareAccept->currency, 2);
    }


    public function getCurrency()
    {

        $all_currency = Az::$app->payer->currency2->listCurrencies();

        return $all_currency['USD'];
    }

    #endregion

    public function createWareEnterItem($shop_order_item, $source = WareEnter::source['enter'], $type = 'default')
    {

        /** @var ShopOrderItem $shop_order_item */
        if (!$shop_order_item)
            return null;

        $shop_catalog_id = $shop_order_item->shop_catalog_id;
        $ware_id = $shop_order_item->ware_id;
        $user_company_id = $shop_order_item->user_company_id;

        $shop_catalog_ware = ShopCatalogWare::find()
            ->where([
                'ware_id' => $ware_id,
                'shop_catalog_id' => $shop_catalog_id,
                'best_before' => $shop_order_item->best_before
            ])
            ->one();

        if (!$shop_catalog_ware) {

            $shop_catalog_ware = new ShopCatalogWare();
            $shop_catalog_ware->ware_id = $ware_id;
            $shop_catalog_ware->shop_catalog_id = $shop_catalog_id;
            $shop_catalog_ware->best_before = $shop_order_item->best_before;
            $shop_catalog_ware->amount = $shop_order_item->amount;

            $shop_catalog_ware->save();

        }

        $ware_enter = new WareEnter();
        $ware_enter->ware_id = $ware_id;
        $ware_enter->source = $source;
        $ware_enter->user_company_id = $user_company_id;
        $ware_enter->date = Az::$app->cores->date->date();

        $ware_enter->save();

        $amount = $shop_order_item->amount;
        if ($type === 'part')
            $amount = $shop_order_item->amount_return;

        $ware_enter_item = new WareEnterItem();
        $ware_enter_item->ware_enter_id = $ware_enter->id;
        $ware_enter_item->shop_catalog_id = $shop_catalog_id;
        $ware_enter_item->best_before = $shop_order_item->best_before;
        $ware_enter_item->shop_catalog_ware_id = $shop_catalog_ware->id;
        $ware_enter_item->amount = $amount;

        $ware_enter_item->save();

    }


    public function createWareExit($shop_order_id)
    {

        $shop_order_items = ShopOrderItem::find()
            ->where([
                'shop_order_id' => $shop_order_id
            ])
            ->all();

        /** @var ShopOrderItem $shop_order_item */

        foreach ($shop_order_items as $shop_order_item) {

            if ($shop_order_item->check_return === true) {
                $this->createWareEnterItem($shop_order_item, 'change');
                continue;
            }

            $shop_catalog_id = $shop_order_item->shop_catalog_id;
            $ware_id = $shop_order_item->ware_id;
            $user_company_id = $shop_order_item->user_company_id;

            $shop_catalog_ware = ShopCatalogWare::findOne([
                'ware_id' => $ware_id,
                'shop_catalog_id' => $shop_catalog_id,
                /*'best_before' => $shop_order_item->best_before,*/
            ]);

            if (!$shop_catalog_ware) {
                return false;
            }

            $ware_exit = new WareExit();
            $ware_exit->ware_id = $ware_id;
            $ware_exit->user_company_id = $user_company_id;
            $ware_exit->date = Az::$app->cores->date->date();
            $ware_exit->configs->rules = validatorSafe;

            $ware_exit->save();

            $ware_exit_item = new WareExitItem();
            $ware_exit_item->ware_exit_id = $ware_exit->id;
            $ware_exit_item->shop_catalog_id = $shop_catalog_id;
            $ware_exit_item->shop_catalog_ware_id = $shop_catalog_ware->id;
            $ware_exit_item->amount = $shop_order_item->amount;
            $ware_exit_item->best_before = $shop_order_item->best_before;
            $ware_exit_item->configs->rules = [[validatorSafe]];

            $ware_exit_item->save();

        }

        return null;
    }


    public function createWareEnter($shop_order_id, $source = null)
    {


        $shop_order_items = ShopOrderItem::find()
            ->where([
                'shop_order_id' => $shop_order_id
            ])
            ->all();

        /** @var ShopOrderItem $shop_order_item */

        foreach ($shop_order_items as $shop_order_item) {

            $shop_catalog_id = $shop_order_item->shop_catalog_id;
            $ware_id = $shop_order_item->ware_id;
            $user_company_id = $shop_order_item->user_company_id;

            $shop_catalog = ShopCatalog::findOne($shop_catalog_id);

            if (!$shop_catalog) {
                continue;
            }

            $shop_catalog_ware = ShopCatalogWare::find()
                ->where([
                    'ware_id' => $ware_id,
                    'shop_catalog_id' => $shop_catalog_id,
                    'best_before' => $shop_order_item->best_before,
                ])
                ->one();

            if ($shop_catalog_ware === null) {
                $shop_catalog_ware = new ShopCatalogWare();
                $shop_catalog_ware->ware_id = $ware_id;
                $shop_catalog_ware->shop_catalog_id = $shop_catalog_id;
                $shop_catalog_ware->best_before = $shop_order_item->best_before;
                $shop_catalog_ware->amount = $shop_order_item->amount;
                $shop_catalog_ware->save();
            }

            $ware_enter = new WareEnter();
            $ware_enter->ware_id = $ware_id;
            $ware_enter->source = $source;
            $ware_enter->user_company_id = $user_company_id;
            $ware_enter->date = Az::$app->cores->date->date();

            $ware_enter->save();

            $ware_enter_item = new WareEnterItem();
            $ware_enter_item->best_before = $shop_order_item->best_before;
            $ware_enter_item->price = $shop_order_item->price;
            $ware_enter_item->ware_enter_id = $ware_enter->id;
            $ware_enter_item->shop_catalog_id = $shop_catalog_id;
            $ware_enter_item->shop_element_id = $shop_catalog->shop_element_id;
            $ware_enter_item->shop_catalog_ware_id = $shop_catalog_ware->id;
            $ware_enter_item->amount = $shop_order_item->amount;
            $ware_enter_item->configs->rules = validatorSafe;

            $ware_enter_item->save();

        }
    }


    public function childs($shop_order_id)
    {

        $childs = ShopOrder::find()
            ->where([
                'parent' => $shop_order_id
            ])
            ->all();

        if (empty($childs)) {
            return false;
        }

        return $childs;

    }


    public function getShopElementsByUserCompany($ware_enter_id)
    {

        $ware_enter = WareEnter::findOne($ware_enter_id);
        $user_company_id = $ware_enter->user_company_id;

        $shop_catalogs = ShopCatalog::find()
            ->where([
                'user_company_id' => $user_company_id
            ])
            ->all();

        $shop_element_ids = ZArrayHelper::getColumn($shop_catalogs, 'shop_element_id');

        $shop_elements = ShopElement::find()
            ->where([
                'id' => $shop_element_ids
            ])
            ->all();

        return ZArrayHelper::map($shop_elements, 'id', 'name');

    }


    public function editWareEnter($model)
    {

        /** @var WareEnterItem $model */

        $oldAmount = ZArrayHelper::getValue($model->oldAttributes, 'amount');

        $shop_catalog = ShopCatalog::findOne($model->shop_catalog_id);

        if ($shop_catalog) {

            if (!empty($shop_catalog->parent))
                $shop_catalog = ShopCatalog::findOne($shop_catalog->parent);
            if ($shop_catalog === null)
                return;
            $shop_catalog->amount -= (int)$oldAmount;
            $shop_catalog->amount += (int)$model->amount;
            $shop_catalog->configs->rules = [
                [
                    validatorSafe
                ]
            ];
            $shop_catalog->save();
        }

        $ware_enter = WareEnter::findOne($model->ware_enter_id);
        if (!$ware_enter)
            return null;

        $ware_id = $ware_enter->ware_id;
        $shop_catalog_ware = ShopCatalogWare::findOne([
            'shop_catalog_id' => $shop_catalog->id,
            'ware_id' => $ware_id,
            'best_before' => $model->best_before,
        ]);

        if ($shop_catalog_ware) {

            $shop_catalog_ware->amount -= (int)$oldAmount;
            $shop_catalog_ware->amount += (int)$model->amount;
            $shop_catalog_ware->configs->rules = [
                [
                    validatorSafe
                ]
            ];
            $shop_catalog_ware->save();

        }

    }


    public function editWareExit($model)
    {

        /** @var WareExitItem $model */

        $oldAmount = ZArrayHelper::getValue($model->oldAttributes, 'amount');

        $shop_catalog = ShopCatalog::findOne($model->shop_catalog_id);

        if ($shop_catalog) {

            if (!empty($shop_catalog->parent))
                $shop_catalog = ShopCatalog::findOne($shop_catalog->parent);
            if ($shop_catalog === null)
                return;
            $shop_catalog->amount += (int)$oldAmount;
            $shop_catalog->amount -= (int)$model->amount;
            $shop_catalog->configs->rules = [
                [
                    validatorSafe
                ]
            ];
            $shop_catalog->save();

        }

        $ware_exit = WareExit::findOne($model->ware_exit_id);
        $ware_id = $ware_exit->ware_id;

        $shop_catalog_ware = ShopCatalogWare::findOne([
            'shop_catalog_id' => $shop_catalog->id,
            'ware_id' => $ware_id,
            'best_before' => $model->best_before,
        ]);

        if ($shop_catalog_ware) {

            $shop_catalog_ware->amount += (int)$oldAmount;
            $shop_catalog_ware->amount -= (int)$model->amount;
            $shop_catalog_ware->configs->rules = [
                [
                    validatorSafe
                ]
            ];
            $shop_catalog_ware->save();

        }

    }


}
