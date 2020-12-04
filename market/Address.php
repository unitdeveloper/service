<?php

/**
 * Author: Sardor
 */

namespace zetsoft\service\market;

use zetsoft\models\place\PlaceAdress;
use zetsoft\models\shop\ShopCategory;
use zetsoft\models\user\UserCompany;
use zetsoft\models\place\PlaceCountry;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\place\PlaceRegion;
use zetsoft\models\user\User;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;


class Address extends ZFrame
{


    #region init

    public function init()
    {
        parent::init();
    }

    #endregion

    #region for getAllAdresses
    /**
     *
     * Function  getAddress
     * @param $id
     * @param $type
     * @return  array|bool|void
     */

    public function GetAddressTest()
    {
        $data = $this->getAddress(5, 'user');
        vd($data);
    }

    public function getAddress($id, $type)
    {
        $type = strtolower($type);
        switch ($type) {
            case 'user':
                $obj = User::findOne($id);
                if ($obj === null)
                    return [];
                $address = PlaceAdress::find()->
                where(['id' => $obj->core_adress_ids])->andWhere(['address_type' => 'user'])->all();
                return $address;
                break;
            case 'order':
                $obj = ShopOrder::findOne($id);
                if ($obj === null)
                    return [];
                $address = PlaceAdress::find()->
                where(['id' => $obj->core_adress_id])->andWhere(['address_type' => 'order'])->all();
                return $address;
                break;
            case 'company':
                $obj = UserCompany::findOne($id);
                if ($obj === null)
                    return [];
                $address = PlaceAdress::find()->
                where(['id' => $obj->core_address_ids])->andWhere(['address_type' => 'company'])->all();
                return $address;
                break;
            default :
                return Az::error(__FUNCTION__ . 'Address not found');
        }
    }

    #endregion

    #region getAddressForCheckOut

    public function GetUserAddressesTest()
    {
        $data = $this->getUserAddresses(10);
        vd($data);
    }

    public function getUserAddresses($id)
    {
        $obj = User::findOne($id);
        if ($obj === null)
            return [];
        $address = PlaceAdress::find()->
        where(['id' => $obj->core_adress_ids])->all();

        $data = [];
        /** @var PlaceAdress $model */
        foreach ($address as $addres) {

            $data[$addres->id] = $addres->name;
        }

        return $data;
    }

    #endregion
    /***
     *  This function need for generate region full name for CoreRegion Model's beforeSave function
     * Function  generateRegionName
     * @param $model
     */

    #region generateRegionName

    public function GenerateRegionNameTest()
    {
        $data = $this->generateRegionName(PlaceRegion::findOne(59));
        vd($data);
        //Модель не найден!!!!!
    }

    public function generateRegionName($model)
    {
        $parent_region = PlaceRegion::findOne($model->parent_id);

        $model->name = $model->title . ', ' . $parent_region->name;
    }

    #endregion

    #region getShippingAddres
    /**
     *
     * Function  getShipmentAddress
     * @param null $shipment
     * @return  array|bool|void
     */

    public function GetShipmentAddressTest()
    {
        $data = $this->getShipmentAddress();
        vd($data);
    }

    public function getShipmentAddress($shipment = null)
    {
        if ($shipment === null) return [];

        if ($shipment->order_id !== null)
            $orderBelongsToShipment = ShopOrder::findOne($shipment->order_id);
        else return [];

        if ($orderBelongsToShipment->id !== null)
            $address = $this->getAddress($orderBelongsToShipment->id, 'order');
        else return [];

        return $address;
    }

    #endregion

    #region getRegion

    public function GetRegionByCountryTest()
    {
        $data = $this->getRegionByCountry();
        vd($data);
    }

    public function getRegionByCountry($selectedId = null)
    {


        if (empty($selectedId))
            return null;


        $out = [];
        if (isset($selectedId)) {

            $country = PlaceCountry::findOne($selectedId);
            $regions = PlaceRegion::find()
                ->where([
                    'place_country_id' => $country->id
                ])->all();


            $data = [];
            foreach ($regions as $model) {
                $name_attr = $model->configs->name;
                $out[$model->id] = $model->$name_attr;
            }
        }

        return $out;

    }

    #endregion
}



