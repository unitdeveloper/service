<?php

namespace zetsoft\service\inputs;


use yii\data\Sort;
use yii\web\Response;
use zetsoft\models\core\CoreInput;
use zetsoft\models\cpas\CpasLand;
use zetsoft\models\cpas\CpasOffer;
use zetsoft\models\cpas\CpasOfferItem;
use zetsoft\models\cpas\CpasStream;
use zetsoft\models\drag\DragConfigDb;
use zetsoft\models\page\PageAction;
use zetsoft\models\page\PageBlocksType;
use zetsoft\models\pays\PaysPayment;
use zetsoft\models\place\PlaceAdress;
use zetsoft\models\place\PlaceCountry;
use zetsoft\models\place\PlaceRegion;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopCatalogWare;
use zetsoft\models\shop\ShopCourier;
use zetsoft\models\shop\ShopElement;
use zetsoft\models\shop\ShopOptionType;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\shop\ShopShipment;
use zetsoft\models\user\User;
use zetsoft\models\user\UserCompany;
use zetsoft\models\ware\Ware;
use zetsoft\models\ware\WareAccept;
use zetsoft\models\ware\WareEnter;
use zetsoft\models\ware\WareExit;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\module\Models;
use function Amp\Promise\rethrow;
use function Dash\Curry\get;
use function zetsoft\apisys\edit\returnn;

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */
class Depend extends ZFrame
{

    public function getCatalogsFromWare($ware_id) {

        $catalog_ware = ShopCatalogWare::findAll([
            'ware_id' => $ware_id
        ]);

        $catalog_ids = ZArrayHelper::getColumn($catalog_ware, 'shop_catalog_id');

        $shop_catalogs = ShopCatalog::find()
            ->where([
                'id' => $catalog_ids
            ])
            ->all();

        return ZArrayHelper::map($shop_catalogs, 'id', 'name');
    }


    public function getOrderItemsFromOrderId($shop_order_id = null)
    {

        $items = ShopOrderItem::find()
            ->where([
                'shop_order_id' => $shop_order_id
            ])
            ->orderBy([
                'id' => SORT_DESC
            ])
            ->all();

        $return = ZArrayHelper::map($items, 'id', 'id');
         
        return $return;

    }


    public function getCatalogsByCompany($user_company_id = null)
    {

    }

    public function shelfLifeByCatalogId(int $catalog_id = null)
    {
        $model = ShopCatalogWare::find()
            ->where([
                'shop_catalog_id' => $catalog_id
            ])
            ->select(['id', 'best_before'])
            ->asArray()
            ->all();

        return ZArrayHelper::map($model, 'best_before', 'best_before');
    }

    public function getAttrs($modelName = null)
    {

        if (!$modelName)
            return [];

        /** @var Models $model */

        $model = new $modelName();

        $return = [];
        foreach ($model->columns as $key => $column) {
            $title = ucfirst($key);
            if (!empty($column->title))
                $title = $column->title;

            $return[$key] = $title;
        }

        return $return;

    }

    public function getAttrsFromModel($modelClass = null)
    {

        if (!$modelClass)
            return [];

        $modelName = CoreInput::class;
        if (!empty($modelClass))
            $modelName = $modelClass;

        $attributes = Az::$app->smart->migra->getAttrsOfModel();

        $arr = [];
        if (ZArrayHelper::keyExists($this->bootFull($modelName), $attributes)) {
            $arr = $attributes[$this->bootFull($modelName)];
        }

        return $arr;

    }

    public function getPlaceAdressIdByUserId()
    {

        $user_id = $this->userIdentity()->id;
        if (!$user_id) {
            return [];
        }

        $user = User::findOne($user_id);

        $place_adress = [];
        if (!empty($user->place_adress_ids)) {
            $place_adress = PlaceAdress::find()
                ->where([
                    'id' => $user->place_adress_ids
                ])
                ->all();
        }

        return ZArrayHelper::map($place_adress, 'id', 'name');

    }

    public function getCatalogsByWareExitId($ware_id, $user_company_id)
    {

        $shop_catalog_wares = ShopCatalogWare::find()
            ->where([
                'ware_id' => $ware_id,
            ])
            ->all();

        $shop_catalog_ware_ids = ZArrayHelper::getColumn($shop_catalog_wares, 'shop_catalog_id');

        $shop_catalog = ShopCatalog::findAll([
            'id' => $shop_catalog_ware_ids,
            'user_company_id' => $user_company_id
        ]);

        return ZArrayHelper::map($shop_catalog, 'id', 'name');

    }

    public function getShipmentByCourierId($shop_courier_id = null, $shop_shipment_id = null)
    {

        if (!$shop_courier_id) {
            return [];
        }

        $ware_accept = WareAccept::find()
            ->where([
                'deleted_at' => null,
            ])
            ->asArray()
            ->all();

        $ware_accept_ids = ZArrayHelper::getColumn($ware_accept, 'shop_shipment_id');

        ZArrayHelper::removeValue($ware_accept_ids, (int)$shop_shipment_id);

        $shop_shipments = ShopShipment::find()
            ->orderBy([
                'id' => SORT_DESC
            ])
            ->where([
                'shop_courier_id' => $shop_courier_id,
            ])
            ->andWhere(['not in', 'id', $ware_accept_ids])
            ->all();

        return ZArrayHelper::map($shop_shipments, 'id', 'name');

    }

    public function getCouriersByOrderId($value = null)
    {
        
        if (!$value)
            return [];

        $shop_order = ShopOrder::findOne($value);



        if (!$shop_order)
            return [];

        $place_region_id = $shop_order->place_region_id;

        if (!$place_region_id)
            return [];

        $shop_courier = ShopCourier::find()
            ->where([
                'place_region_ids' => $place_region_id
            ])
            ->all();
        
        return ZArrayHelper::map($shop_courier, 'id', 'name');
    }

    public function getRegion($place_country_id = null)
    {

        if (!$place_country_id)
            return [];

        $regions = PlaceRegion::find()
            ->where([
                'place_country_id' => $place_country_id
            ])
            ->all();

        $name = (new PlaceRegion())->configs->name;

        return ZArrayHelper::map($regions, 'id', $name);

    }


    public function getUserCompaniesWithCatalogs()
    {

        $companies = UserCompany::find()->all();

        $return = [];
        /** @var UserCompany $company */
        foreach ($companies as $company) {

            $wares = $this->getWaresByUserCompany($company->id);
            if (empty($wares))
                continue;

            $return[$company->id] = $company->name;

        }

        return $return;

    }

    public function getWaresByUserCompany($user_company_id = null)
    {

        if (!$user_company_id)
            return [];

        $shop_catalogs = ShopCatalog::find()
            ->where([
                'user_company_id' => $user_company_id,
            ])
            ->asArray()
            ->all();

        $shop_catalog_ids = ZArrayHelper::getColumn($shop_catalogs, 'id');

        $shop_catalog_wares = ShopCatalogWare::find()
            ->where([
                'shop_catalog_id' => $shop_catalog_ids
            ])
            ->andWhere(['!=', 'amount', 0])
            ->asArray()
            ->all();

        $shop_catalog_ware_ids = ZArrayHelper::getColumn($shop_catalog_wares, 'ware_id');

        $wares = Ware::find()
            ->where([
                'id' => $shop_catalog_ware_ids
            ])
            ->all();

        return ZArrayHelper::map($wares, 'id', 'name');

    }

    public function sample($model = null, $column = null, $value = null)
    {

        if (!$model || !$column || $value)
            return [];

        if (!$value)
            return [];

        return Az::$app->smart->widget->getDependColumns($value, $model, $column);

    }

    public function getOptionTypesByBranchIds()
    {

        $value = $this->httpGet('depend');

        if (empty($value))
            return [];

        $option_types = ShopOptionType::find()
            ->where([
                'shop_option_branch_id' => $value,
            ])
            ->all();

        $return = [];
        foreach ($option_types as $option_type)
            $return[$option_type->id] = $option_type->name;

        return $return;

    }


    public function getOrdersByCourier()
    {
        $value = $this->httpGet('depend');
    }


    public function getCompaniesCatalog($user_company_id = null)
    {

        if (!$user_company_id)
            return [];

        $shop_catalog = ShopCatalog::find()
            ->where([
                'user_company_id' => $user_company_id,
                'deleted_at' => null,
            ])
            ->asArray()
            ->all();


        return ZArrayHelper::map($shop_catalog, 'id', 'name');

    }

    //start: MurodovMirbosit
    public function getElementsByUserCompany($user_company_id = null)
    {

        if (!$user_company_id) {
            return [];
        }

        $shop_element = ShopElement::findAll([
            'user_company_id' => $user_company_id
        ]);

        return ZArrayHelper::map($shop_element, 'id', 'name');

    }

    public function getWareByUserCompany($user_company_id = null)
    {

        if (!$user_company_id) {
            return [];
        }

        $shop_element = Ware::findAll([
            'user_company_id' => $user_company_id
        ]);

        return ZArrayHelper::map($shop_element, 'id', 'name');

    }

    //end


    public function getElementsByCpasOfferItemId($cpas_offer_item_id = null)
    {
        if (!$cpas_offer_item_id)
            return [];
        $dir = Root . '/render/cpanet/';
        $cpasOfferItem = CpasOfferItem::findOne($cpas_offer_item_id);
        $cpasOfferId = $cpasOfferItem->cpas_offer_id;
        $placeCountryId = $cpasOfferItem->place_country_id;
        $placeCountry = PlaceCountry::findOne($placeCountryId);
        $lang = $placeCountry->alpha2;
        $cpasOffer = CpasOffer::findOne($cpasOfferId);
        $offerName = $cpasOffer->title;
        $path = $dir . $offerName . '/' . $lang;
        $dirs = ZFileHelper::scanFolder($path);
        $result = [];
        foreach ($dirs as $key => $value) {
            $value = $offerName . '/' . $lang . '/' . bname($value);
            if (!CpasLand::find()->where(['path' => $value])->exists())
                $result[$value] = $value;
        }
        return $result;
    }

    #region getPaymentSystems

    public function getPaymentSystems($user_id = null)
    {
        if (!$user_id)
            return [];
        $pays = PaysPayment::find()->where(['user_id' => $user_id])->all();
        $return = [];
        foreach ($pays as $pay) {
            $value = $pay->value;
            $values = '';
            foreach ($value as $key => $val) {
                $values = $key . ' ' . $val;
            }


            $return[$pay->id] = $values;
        }
        //vdd($return);
        return $return;
    }

    #endregion

    #region getCpasLand

    public function getCpasLand($cpas_stream_id = null, $type = null)
    {
        //vdd($cpas_stream_id);
        //vdd($type);
        if ($cpas_stream_id === null || $type === null)
            return [];

        $stream = CpasStream::find()
            ->where([
                'id' => $cpas_stream_id
            ])
            ->one();
        //vdd($stream);
        $offerItems = CpasOfferItem::find()
            ->where([
                'cpas_offer_id' => $stream->cpas_offer_id
            ])
            ->all();
        //vdd($offerItems);
        $return = [];
        foreach ($offerItems as $offer) {
            $lands = CpasLand::find()
                ->where([
                    'cpas_offer_item_id' => $offer->id
                ])
                ->andWhere([
                    'type' => $type
                ])
                ->all();
            foreach ($lands as $land) {
                $return[$land->id] = $land->title;
            }

        }

        return $return;

    }

    #endregion
}
