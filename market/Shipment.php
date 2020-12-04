<?php

/**
 * Author: Sardor
 */

namespace zetsoft\service\market;

use Google\ApiCore\OperationResponse;
use PhpParser\Node\Stmt\Else_;
use yii\caching\TagDependency;
use zetsoft\dbitem\shop\CompanyCardItem;
use zetsoft\dbitem\wdg\MenuItem;
use zetsoft\dbitem\shop\ProductItem;
use zetsoft\dbitem\shop\PropertyItem;
use zetsoft\dbitem\data\ALLApp;
use zetsoft\dbitem\data\Config;
use zetsoft\dbitem\data\ConfigDB;
use zetsoft\dbitem\data\Form;
use zetsoft\former\courier\CourierForm;
use zetsoft\former\order\OrderOperatorForm;
use zetsoft\former\order\OrderForm;
use zetsoft\models\page\PageAction;
use zetsoft\models\place\PlaceAdress;
use zetsoft\models\shop\ShopBrand;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopCategory;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\user\UserCompany;
use zetsoft\models\place\PlaceCountry;
use zetsoft\models\shop\ShopCourier;
use zetsoft\models\shop\ShopElement;
use zetsoft\models\menu\Menu;
use zetsoft\models\menu\MenuImage;
use zetsoft\models\core\CoreMenuNew;
use zetsoft\models\shop\ShopOption;
use zetsoft\models\shop\ShopOptionType;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopProduct;
use zetsoft\models\place\PlaceRegion;
use zetsoft\models\shop\ShopShipment;
use zetsoft\models\user\User;
use zetsoft\models\App\eyuf\Region;
use zetsoft\models\ware\WareAccept;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\helpers\ZUrl;
use zetsoft\system\kernels\ZFrame;
use zetsoft\widgets\former\ZDynaWidget;
use zetsoft\widgets\incores\ZMCheckboxGroupWidget;
use zetsoft\widgets\inputes\ZHCheckboxButtonGroupWidget;
use zetsoft\widgets\navigat\ZGAccordionWidget;
use function Dash\Curry\negate;
use function Dash\where;
use function PHPUnit\Framework\isInstanceOf;
use function Spatie\array_keys_exist;


class Shipment extends ZFrame
{
    public $order;
    public $orderItem;
    public $courier;
    public $region;
    public $adress;
    public $shipment;
    public $catalog;
    public $operators;
    public $users;
    public $ware_accept;

#region init

    public function init()
    {
        $this->order = collect(ShopOrder::find()->all());
        $this->orderItem = collect(ShopOrderItem::find()->all());
        $this->courier = collect(ShopCourier::find()->all());
        $this->region = collect(PlaceRegion::find()->all());
        $this->adress = collect(PlaceAdress::find()->all());
        $this->shipment = collect(ShopShipment::find()->all());
        $this->users = collect(User::find()->all());
        $this->catalog = collect(ShopCatalog::find()->all());
        $this->ware_accept = collect(WareAccept::find()->all());
        // $this->operators = collect(User::find()->where(['operator'=>'operator'])->all());
        parent::init();
    }

    public function test()
    {
       // $this->GetShipmentListTest();
       // $this->GetUserShipmentListTest();
       // $this->GetShipmentBelongsToOrderTest();
       // $this->GetOperatorsTest();   there is no variable in this function
       // $this->ShipmentByCourierTest();  Error
    }

#endregion
#region getShipmentList
    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    //this function shows shipmentList by $id , $status
    public function GetShipmentListTest()
    {
        $id = null;
        $status = null;
        $data = $this->getShipmentList($id, $status);
        vd($data);
    }

    public function getShipmentList($id = null, $status = null)
    {
        Az::start(__FUNCTION__);
        if ($id === null && $status === null) {
            $shipment = ShopShipment::find()->all();
        }
        if ($status === null && $id !== null) {
            $shipment = ShopShipment::findOne($id);
        }
        if ($id === null && $status !== null) {
            $shipment = ShopShipment::find()->where([
                'status' => $status
            ])->all();
        }
        return $shipment;
    }

    public function infoShipment()
    {
        $order = ShopOrder::find()->all();


    }


    /**
     * Return authenticated users all shipment list
     *
     * Function  getUserShipmentList
     * @param null $status
     * @return  array
     */
    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    //this function shows user_shipments by status
    public function GetUserShipmentListTest()
    {
        $status = null;
        $data = $this->getShipmentList($status);
        vd($data);
    }

    public function getUserShipmentList($status = null): array
    {
        Az::start(__FUNCTION__);
        $user_id = $this->userIdentity()->id;
        $order_list = Az::$app->cores->order->getOrderList($user_id);

        $user_shipments = [];
        if ($status !== null) {
            foreach ($order_list as $order) {
                if ($this->getShipmentBelongsToOrder($order->id, $status) !== null) {
                    $user_shipments[] = $this->getShipmentBelongsToOrder($order->id, $status);
                }
            }
        }
        return $user_shipments;

    }
    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    //this function shows ShipmentBelongs by $order_id , $status
    public function GetShipmentBelongsToOrderTest()
    {
        $order_id = null;
        $status = null;
        $data = $this->getShipmentBelongsToOrder($order_id, $status);
        vd($data);
    }

    public function getShipmentBelongsToOrder($order_id = null, $status = null)
    {
        Az::start(__FUNCTION__);
        if ($order_id === null || $status === null) {
            return [];
        }
        $all = ShopShipment::find()->where([
            'order_id' => $order_id,

        ])->andWhere([
            'status' => $status
        ])->one();
        return $all;
    }


#endregion
    public function getOrderInfoTest()
    {
        vdd($this->getOrderInfo());
    }

    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    //there is no variable in this function
    public function GetOperatorsTest()
    {
        $this->getOperators();
    }

    public function getOperators()
    {

        $operators = $this->orderItem;
        $catalog = $this->catalog;
        $users = $this->users;
        $orders = $this->order;
        $forms = [];
        foreach ($operators as $item) {

            $catalogName = $catalog->where('id', $item->shop_catalog_id)->first();
            $val = $orders->where('id', $item->shop_order_id)->first();
            $userName = $users->where('id', $val !== null ? $val->user_id : '')->first();

            $form = new OrderOperatorForm();

            $form->id = $item->id;
            $form->product = $catalogName->name ?? '---';
            $form->created_at = $item->created_at;
            $form->user = $userName->name ?? '---';
            $forms[] = $form;

        }
        return $forms;
    }

    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    //
    public function ShipmentByCourierTest()
    {
        $courier_id=1;
        $data=shipmentByCourier($courier_id);
        vd($data);
    }

    public function shipmentByCourier($courier_id)
    {
        Az::start(__FUNCTION__);
        $courier = $this->courier->where('id', $courier_id)->first();
        $forms = [];
        if ($courier !== null) {
            $shipments = $this->shipment->where('shop_courier_id', $courier->id);
            foreach ($shipments as $shipment) {

                $form = new CourierForm();
                $form->id = $shipment->id;
                $form->name = $shipment->name;
                $form->status = $shipment->status;
                $form->date_deliver = $shipment->date_deliver;
                $form->created_at = $shipment->created_at;
                $form->shipment_type = $shipment->shipment_type;
                $forms[] = $form;

            }
        }

        return $forms;
    }

}



