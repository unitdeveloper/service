<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\maps;


use yii\helpers\ArrayHelper;
use zetsoft\models\place\PlaceAdress;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopCatalogWare;
use zetsoft\models\shop\ShopCourier;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\shop\ShopShipment;
use zetsoft\models\ware\Ware;
use zetsoft\models\ware\WareAccept;
use zetsoft\models\ware\WareEnter;
use zetsoft\models\ware\WareEnterItem;
use zetsoft\models\ware\WareExit;
use zetsoft\models\ware\WareExitItem;
use zetsoft\models\ware\WareReturn;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;


class navigation extends ZFrame
{

    #region Test

    public function test()
    {
       return 'hello';
    }
    #endregion

    #region Main

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
            array_push($coord, '/');
        }

        $ready = array_pop($coord);
        $customerAddress = implode('', $coord);


         $urlGoogle = 'https://www.google.com/maps/dir/chorsu+toshkent/' . $customerAddress . '&destination&travelmode=Driving';

        return $urlGoogle  ;
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

        return $urlYandex  ;
    }
    ##endregion

    #region urlGeneratorGoogleWithIP
    public function urlGeneratorGoogleWithIP(int $id, $latLong)
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
            $coord [] = $val['lat'] . ',' . $val['lng'] ;
            array_push($coord, '/');
        }
        $ready = array_pop($coord);
        $customerAddress = implode('', $coord);


        //$urlGoogle = 'https://www.google.com/maps/dir/chorsu+toshkent/' . $customerAddress . '&destination&travelmode=Driving';

         $urlGoogle = 'https://www.google.com/maps/dir/'.$latLong.'/' . $customerAddress . '&destination&travelmode=Driving';

        return $urlGoogle;
    }
    ##endregion

    #region urlGeneratorYandexWithIP
    public function urlGeneratorYandexWithIP(int $id, $latLong)
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


        $urlYandex = 'https://yandex.ru/maps/?rtext='.$latLong.'/' . $customerAddress . '&rtt=auto';

        return $urlYandex  ;
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
            if(empty($were_ids)){
                break;
            }
            $wares = Ware::findAll($were_ids);
            $wares_place_ids = ZArrayHelper::getColumn($wares, 'place_adress_id');
            if(empty($wares_place_ids)){
                break;
            }
            $return['waresAdress'][$item['id']] = $wares_place_ids;

        }


        return $return;
    }


    #endregion

    ##endregion


    #endregion
}
