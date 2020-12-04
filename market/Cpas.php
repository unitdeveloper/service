<?php

/**
 * Author: Ob
 */

namespace zetsoft\service\market;

use Illuminate\Support\Collection;
use zetsoft\former\cpas\CpasStatsForm;
use zetsoft\former\order\OrderForm;
use zetsoft\former\reports\ReportsCourierForm;
use zetsoft\former\reports\ReportsOrderStatusForm;
use zetsoft\former\reports\ReportsRejectCauseForm;
use zetsoft\former\reports\ReportsSoldProductsForm;
use zetsoft\models\track\CpasTeaser;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopCourier;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\shop\ShopRejectCause;
use zetsoft\models\shop\ShopShipment;
use zetsoft\models\user\UserCompany;
use zetsoft\models\ware\WareAccept;
use zetsoft\system\kernels\ZFrame;


class Cpas extends ZFrame
{
    /** @var Collection $orders */
    public $orders;
    public $order_items;
    public $catalogs;
    public $shipments;
    public $couriers;
    public $companies;
    public $wareAccept;
    public $reject_causes;
    public $cpa_tracks;

    #region init

    /**
     * @var mixed
     */

    public function init()
    {
        $this->cpa_tracks = collect(CpasTracker::find()->asArray()->all());
        $this->reject_causes = collect(ShopRejectCause::find()->asArray()->all());
        $this->orders = collect(ShopOrder::find()->asArray()->all());
        $this->order_items = collect(ShopOrderItem::find()->asArray()->all());
        $this->catalogs = collect(ShopCatalog::find()->asArray()->all());
        $this->shipments = collect(ShopShipment::find()->asArray()->all());
        $this->couriers = collect(ShopCourier::find()->asArray()->all());
        $this->companies = collect(UserCompany::find()->asArray()->all());
        $this->wareAccept = collect(WareAccept::find()->asArray()->all());
        parent::init();
    }

    #endregion

    #region test

    public function test()
    {
        vdd($this->getAllTracks());
    }

    #endregion test

    #region getAllTracks

    public function getAllTracks(){

        $cpa_tracks = $this->cpa_tracks;
        $catalogs = $this->catalogs->whereIn('id',$cpa_tracks->pluck('product_id'));
        
        $forms = [];

        foreach ($catalogs->all() as $catalog){

            $new_tracks = $cpa_tracks->where('product_id',$catalog['id']);

            $form = new CpasStatsForm();

            $form->product = $catalog['name'];
            $form->click_amount = $new_tracks->count();
            $form->unique = $new_tracks->unique('ip')->count();
            $form->application_req = $new_tracks->whereNotNull('application_req')->count();

            $froms[] =$form;
        }
        return $forms;
    }

    #endregion getAllTracks

}



