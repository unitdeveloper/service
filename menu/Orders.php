<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * Date:    11.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\menu;


use Dompdf\Dompdf;
use VsWord;
use zetsoft\dbitem\market\MyCard;
use zetsoft\dbitem\market\MyOrder;
use zetsoft\dbitem\market\MyProduct;
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


class Orders extends ZFrame
{
    public function orders($userID)
    {

        /** @var Order[] $orders */
        $orders = Order::findAll([
            'quantity' => 10
        ]);

        $user = User::find()
            ->where([
                'id' => $userID
            ]);


        $return = [];
        foreach ($orders as $order) {
            if ($order->quantity > 10) {

                $myOrder = new MyOrder();
                $myOrder->cost = 11313;
                $myOrder->quantity = $order->quantity;
                $myOrder->shipTax = 144;

                $return[] = $myOrder;
            }
        }

        return $return;
    }

    public function deliver()
    {
        echo 'deliver';
    }

    public function product()
    {
        $cards = Product::find()
            ->all();

        $returns = [];

        foreach ($cards as $card) {

            $myCard = new MyProduct();
            $myCard->id = $card->id;
            $myCard->name = $card->name;
            $myCard->title = $card->title;
            $myCard->photo = $card->photo;
            /*$myCard->price = $card->price;
            $myCard->price_old = $card->price_old;
            $myCard->star = $card->star;
            $myCard->sale = $card->sale;
            $myCard->user_id = $card->user_id;
            $myCard->order_ids = $card->order_ids;*/

            $returns[] = $myCard;

        }

        return $returns;

    }

    public function category()
    {
        $cards = Category::findAll('name');

        $returns = [];

        foreach ($cards as $card) {

            $myCard = new MyCard();
            $myCard->title = $card->title;
            $myCard->name = $card->name;
            $myCard->image = $card->image;
            $myCard->price = $card->price;
            $myCard->price_old = $card->price_old;
            $myCard->status = $card->status;

            $returns[] = $myCard;

        }

        return $returns;

    }
}


