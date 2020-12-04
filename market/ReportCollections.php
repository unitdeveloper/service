<?php


namespace zetsoft\service\market;

use Illuminate\Support\Collection;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\shop\ShopShipment;
use zetsoft\system\kernels\ZFrame;

class ReportCollections extends ZFrame
{

    //start|NurbekMakhmudov|2020-10-22

    
    //Для проверки документа ПЕРЕДАЧА
    public ?Collection $shop_shipment = null;
    public ?Collection $shop_order_item = null;

    public $chess_id = null;

    public $filter = [];

    /**
     * @param array $filter
     * @throws \Exception
     * @license NurbekMakhmudov
     * @todo if Collection null, add new Collection
     */
    public function data($filter = [])
    {
        $this->chess_id = $this->paramGet('chess_id');
        $this->filter = $filter;

        if ($this->shop_shipment === null)
            $this->shop_shipment = collect(ShopShipment::find()->asArray()->all());

        if ($this->shop_order_item === null)
            $this->shop_order_item = collect(ShopOrderItem::find()->asArray()->all());

    }


    //end|NurbekMakhmudov|2020-10-22


}