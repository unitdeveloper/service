<?php

/**
 * Author: Sardor
 */

namespace zetsoft\service\market;

use Illuminate\Support\Collection;
use zetsoft\models\place\PlaceAdress;
use zetsoft\models\user\UserCompany;
use zetsoft\models\place\PlaceCountry;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\place\PlaceRegion;
use zetsoft\models\user\User;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

class AddressUmid extends ZFrame
{
    #region vars

    /**
     * @var Collection $core_companies
     */
    public $core_companies;

    /**
     * @var Collection $users
     */
    public $users;

    /**
     * @var Collection $core_orders
     */
    public $core_orders;

    /**
     * @var Collection $core_adress
     */
    public $core_adress;

    /**
     * @var Collection $place_regions
     */
    public $place_regions;

    /**
     * @var Collection $core_countries
     */
    public $core_countries;

    #endregion

    #region init

    public function init()
    {
        parent::init();
        $this->core_companies = collect(UserCompany::find()->all());
        $this->users = collect(User::find()->all());
        $this->core_orders = collect(ShopOrder::find()->all());
        $this->core_adress = collect(PlaceAdress::find()->all());
        $this->place_regions = collect(PlaceRegion::find()->all());
        $this->core_countries = collect(PlaceCountry::find()->all());
    }

    #endregion

    public function test()
    {
//        vdd($this->getUserAddresses(49));
        vdd($this->getAddress(49, 'user'));
    }

    #region for getAllAdresses
    /**
     *
     * Function  getAddress
     * @param $id
     * @param $type
     * @return  array|bool|void
     */
    public function getAddress($id, $type){
        $type = strtolower($type);
        switch ($type){
            case 'user':
                $obj = $this->users->firstWhere('id', $id);
                if($obj === null)
                    return [];

                $address = $this->core_adress->whereIn('id', $obj->core_adress_ids)->where('address_type', 'user');

                return $address;
                break;
            case 'order':
                $obj = $this->core_orders->firstWhere('id', $id);
                if($obj === null)
                    return [];

                $address = $this->core_adress->whereIn('id', $obj->core_adress_ids)->where('address_type', 'order');

                return $address;
                break;
            case 'company':
                //$obj = UserCompany::findOne($id);
                $obj = $this->core_companies->where('id', $id);
                if($obj === null)
                    return [];
                $address = $this->core_adress->
                    whereIn('id', $obj->core_address_ids)->where('address_type', 'company');
                return $address;
                break;
            default :
                return Az::error(__FUNCTION__. 'Address not found');


        }
    }

    #endregion
    #region getAddressForCheckOut
    public function getUserAddresses($id){
        $obj =  $this->users->firstWhere('id', $id);
        if($obj === null)
            return [];

        $address = $this->core_adress->whereIn('id', $obj->core_adress_ids);

        $data = [];

        foreach ($address as $addres){
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
    public function generateRegionName($model)
    {
        //$parent_region = CoreRegion::findOne($model->parent_id);
        $parent_region = $this->place_regions->firstWhere('id', $model->parent_id);
        
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
    public function getShipmentAddress($shipment = null){
        if($shipment === null) return [];

        if($shipment->order_id !== null)
         $orderBelongsToShipment = $this->core_orders->firstWhere('id', $shipment->order_id);
        else return [];
        
        if($orderBelongsToShipment->id !== null)
         $address = $this->getAddress($orderBelongsToShipment->id, 'order');
        else return [];

        return $address;
    }
    #endregion

    #region getRegion

    public function getRegionByCountry($selectedId = null)
    {
        if (empty($selectedId))
            return null;

        $out = [];
        if (isset($selectedId)) {
            $country = $this->core_countries->firstWhere('id', $selectedId);

            $regions = $this->place_regions->where('place_country_id', $country->id);

            foreach ($regions as $model) {
                $out[$model->id] = $model->name;
            }
        }

        return $out;

    }

    #endregion

}



