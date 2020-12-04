<?php

/**
 * @author MurodovMirbosit
 * @license: DavlatorRavshan
 */

namespace zetsoft\service\menu;


use Dompdf\Dompdf;
use VsWord;
use zetsoft\dbitem\market\MyCard;
use zetsoft\dbitem\market\MyOrder;
use zetsoft\dbitem\market\MyProduct;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\user\UserCompany;
use zetsoft\models\user\User;
use zetsoft\models\App\eyuf\Card;
use zetsoft\models\App\eyuf\Category;
use zetsoft\models\App\eyuf\EyufDocument;
use zetsoft\models\App\eyuf\EyufDocumentType;
use zetsoft\models\App\eyuf\EyufInvoice;
use zetsoft\models\App\eyuf\Order;
use zetsoft\models\App\eyuf\EyufScholar;
use zetsoft\models\shop\Product;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;


class OrderNorms extends ZFrame
{


    public function getOrders()
    {
        return ShopOrder::find()->all();
    }

    public function setTotalPrice($model) {

        /** @var ShopOrder $model */
        return (int)$model->deliver_price + (int)$model->price;

    }


    public function setShopOrderTotalPrice()
    {

        /** @var ShopOrder $order */
        /** @var ShopOrderItem $shop_order_item */
        foreach ($this->getOrders() as $order) {

            $shop_order_items = ShopOrderItem::find()
                ->where([
                    'shop_order_id' => $order->id
                ])
                ->all();

            $total_price = 0;
            foreach ($shop_order_items as $shop_order_item) {
                $total_price += (int)$shop_order_item->price_all;
            }

            $order->price = $total_price;
            $order->total_price = $total_price + (int)$order->deliver_price;

            if ($order->status_accept !== ShopOrder::status_accept['delivery_transfer'])
                $order->delayed_deliver_date = null;
            $order->configs->rules = [[validatorSafe]];
            $order->save();

            if ($this->isInvalid($order))
                $order->delete();

        }

    }


    private function isInvalid($order)
    {

        /** @var ShopOrder $order */

        return empty($order->name)
            || empty($order->date_deliver)
            || empty($order->code);

    }

    public function setOrderItemsAmount()
    {

        $shop_order_items = ShopOrderItem::find()->all();

        /** @var ShopOrderItem $model */
        foreach ($shop_order_items as $model) {
            $model->price_all = (int)$model->amount * (int)$model->price;
            $model->amount_return = (int)$model->amount - (int)$model->amount_partial;
            $model->price_all_partial = (int)$model->amount_partial * (int)$model->price;
            $model->price_all_return = (int)$model->amount_return * (int)$model->price;
            $model->configs->rules = [
                [
                    validatorSafe
                ]
            ];
            $model->save();
        }

    }


    public function setInteger() {

        $shop_order_items = ShopOrder::find()->all();

        /** @var ShopOrder $model */
        $columns = [
            'price',
            'total_price',
            'deliver_price',
            'prepayment',
        ];
        
        foreach ($shop_order_items as $model) {

            foreach ($columns as $column) {
                if (empty($model->$column)) {
                    $model->$column = 0;
                    $model->configs->rules = [
                        [
                            validatorSafe
                        ]
                    ];
                    $model->save();
                }
            }
        }
    }
}


