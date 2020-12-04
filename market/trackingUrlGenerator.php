<?php

namespace zetsoft\service\market;

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
use function Dash\isEmpty;

class trackingUrlGenerator extends ZFrame
{



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

    public function enter(WareEnterItem $model, int $wareEnterId)
    {
        $ware_enter = WareEnter::findOne($wareEnterId);

        if ($ware_enter !== null) {
            $user_company_id = $ware_enter->user_company_id;
            $shopCatalog = ShopCatalog::find()->where(['user_company_id' => $user_company_id,
                'shop_element_id' => $model->shop_element_id
            ])->one();
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
                'ware_id' => $ware_enter->ware_id])
                ->one();

            if ($shopCatalogWare !== null) {
                $shopCatalogWare->amount += (int)$model->amount;
                $shopCatalogWare->save();
            } else {
                $shopCatalogWare = new ShopCatalogWare();
                $shopCatalogWare->shop_catalog_id = $shopCatalog->id;
                $shopCatalogWare->ware_id = $ware_enter->ware_id;
                $shopCatalogWare->amount = $model->amount;
                $shopCatalogWare->save();
            }
        }
    }

    public function exitTest()
    {
        $model = new WareExitItem();
        $model->ware_exit_id = "12";
        $model->shop_catalog_id = "8";
        $model->ware_series_id = "1";
        $model->amount = "10";
        $this->exit($model);
    }

    public function exit($model)
    {

        $shopCatalog = ShopCatalog::findOne($model->shop_catalog_id);

        if ($shopCatalog !== null) {

            $shopCatalog->amount -= (int)$model->amount;
            $shopCatalog->save();

            $wareExit = WareExit::findOne($model->ware_exit_id);
            $wareId = Ware::findOne($wareExit->ware_id)->id;
            $shopCatalogWare = ShopCatalogWare::find()
                ->where([
                    'shop_catalog_id' => $model->shop_catalog_id,
                    'ware_id' => $wareId
                ])
                ->one();


            $shopCatalogWare->amount -= (int)$model->amount;
            $shopCatalogWare->save();
            return true;
        }

    }

    public function wareExit($model)
    {

        /** @var WareExitItem $model */
        $shopCatalog = ShopCatalog::findOne($model->shop_catalog_id);

        if ($shopCatalog !== null) {

            $shopCatalog->amount -= (int)$model->amount;
            $shopCatalog->save();

            $wareExit = WareExit::findOne($model->ware_exit_id);

            $shopCatalogWare = ShopCatalogWare::find()
                ->where([
                    'shop_catalog_id' => $model->shop_catalog_id,
                    'ware_id' => $wareExit->ware_id
                ])
                ->one();

            /** @var ShopCatalogWare $shopCatalogWare */

            $shopCatalogWare->amount -= (int)$model->amount;

            $shopCatalogWare->save();

        }

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

        $ordersAdresses['ordersAdress'] = $tempOrder;
        $waresAdresses['waresAdress'] = $tempWares;


        /* @var Ware $wareAddress */
        $PlaceAddressCoordinates = array();
        foreach ($ordersAdresses['ordersAdress'] as $val => $key) {
            $PlaceAddressCoordinates[] = $val;
        }
        //    vd($arr);
        $placeAdresses = PlaceAdress::find()
            ->select(["id", "location"])
            ->where([
                'id' => $PlaceAddressCoordinates,
            ])
            ->all();
        $savedPlaceAdresses = ArrayHelper::getColumn($placeAdresses, 'location.0');
        return $return =$savedPlaceAdresses;

    }

    #endregion

    #region Generator

    #region exit
}
