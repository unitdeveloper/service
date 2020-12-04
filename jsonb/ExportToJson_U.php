<?php


/**
 * Author: Muminov Umid
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\jsonb;

use yii\helpers\ArrayHelper;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopCategory;
use zetsoft\models\shop\ShopElement;
use zetsoft\models\shop\ShopProduct;
use zetsoft\models\shop\ShopShipment;
use zetsoft\system\Az;
use zetsoft\models\shop\ShopCourier;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\user\User;
use zetsoft\models\ware\WareAccept;
use zetsoft\system\actives\ZActiveRecord;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZFormatter;
use zetsoft\system\helpers\ZJsonHelper;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\system\kernels\ZFrame;
use function Dash\Curry\all;
use function Dash\where;


class ExportToJson_U extends ZFrame
{

    public function priyomkiJson($wareacceptids, $usercompanyid)
    {
        $waremodels = WareAccept::find()->where(['id' => $wareacceptids])->all();

        foreach ($waremodels as $item) {
            $courier = ShopCourier::findOne($item['shop_courier_id']);
            $shoporders = ShopOrder::find()->
            where(['shop_shipment_id' => $item['shop_shipment_id']])->
            andWhere(['status_logistics' => 'completed'])->
            andWhere(['source' => $usercompanyid])->all();
            $kolichestvo_vip_po_proyektam = 0;
            foreach ($shoporders as $shoporder) {
                $kolichestvo_vip_po_proyektam += $shoporder['amount'];
            }
            $child = [
                "Дата" => $item['created_at'],
                "Номер" => $item['id'],
                "УникальныйИдентификатор" => $item['id'],
                "Курьер" => $courier->name,
                "Курьер_Код" => $courier->code,
                "Курьер_GUID" => $courier->id,
                "Выполнен" => $item['completed'],
                "КолЗаказовДС" => $item['dc_returns_group'],
                "КоличествоВыпПоПроектам" => $kolichestvo_vip_po_proyektam,
//                    "КоличествоВыпПоПроектам" => $item['dc_returns_group'],
//                  ware_accept->shop_shipment ->   shop_order_id(multi)               -> shop-order>user_company_id  -> shop_order->sum of amount
//                                  shop_order > completedlar
                "ЗарплатаКурьеру" => $item['salary_courier'],
                "Остаток" => $item['remain'],
            ];

            $parent["Приемки"][] = $child;
        }

        $json_data = json_encode($parent, JSON_UNESCAPED_UNICODE);

        $directory = Root . '/upload/uploaz/market/json_temp/';
        $now = date("d.m.Y_H-i-s");
        $filePath = $directory . 'Приемки_' . $now . '.json';
        file_put_contents($filePath, $json_data);
        return $filePath;
    }

    public function zakaziOperatorovJson($shoporderids)
    {
        $shopOrderModels = ShopOrder::find()->where(['id' => $shoporderids])->all();

        $parent = [];
        foreach ($shopOrderModels as $item) {
            $user = User::find()->where([
                'id' => $item['user_id']
            ])->andWhere([
                'role' => 'agent'
            ])->one();

            $operator_guid = "Нет информации";
            if ($item['user_id']) {
                $user = User::findOne($item['user_id']);
                $operator_guid = $user->name;
            }

            $priyomka_date = 'Нет информации';
            $priyomka_nomer = 'Нет информации';
            if ($item['shop_shipment_id']) {
                $shipment = ShopShipment::findOne($item['shop_shipment_id']);
                $wareAccept = $shipment->getWareAcceptOne;
                $priyomka_date = $wareAccept->date;
                $priyomka_nomer = $wareAccept->id;
            }

            $id = $item['id'];
            $shoporderitems = ShopOrderItem::find()->where(['shop_order_id' => $id])->all();
            $shoporderitemscount = ShopOrderItem::find()->where(['shop_order_id' => $id])->count();

            $vid_nomenklaturi = 'Нет информации';
            $vid_nomenklaturi_guid = 'Нет информации';
            if ($shoporderitemscount > 0) {
                foreach ($shoporderitems as $key => $shoporderitem) {
                    $catalog = ShopCatalog::findOne($shoporderitem['shop_catalog_id']);
                    if ($catalog) {
                        $vid_nomenklaturi_guid = $catalog['guid'];
                        $element = ShopElement::findOne($catalog['shop_element_id']);
                        if ($element) {
                            $product = ShopProduct::findOne($element['shop_product_id']);
                            if ($product) {
                                $category = ShopCategory::findOne($product['shop_category_id']);
                                if ($category) {
                                    if ($key + 1 < $shoporderitemscount)
                                        $vid_nomenklaturi .= $category['name'] . ', ';
                                    else
                                        $vid_nomenklaturi .= $category['name'];
                                }

                            }

                        }

                    }

                }
            }

            $child = [
                "Дата" => $item['date'],
                "Номер" => $item['number'],
                "УникальныйИдентификатор" => $item['id'],
                "Телефон" => $item['contact_phone'],
                "Оператор" => $user->title ?? 'Нет информации',
                "Оператор_Код" => $item['code'],
                "Оператор_GUID" => $operator_guid,
                "Количество" => $shoporderitemscount,
                "Сумма" => $item['price'],
                "ПриемкаДата" => $priyomka_date,
                "ПриемкаНомер" => $priyomka_nomer,
                "ВидНоменклатуры" => $vid_nomenklaturi,
                "ВидНоменклатуры_GUID" => $vid_nomenklaturi_guid,
            ];
            $parent["Заказы"][] = $child;

        }

        $json_data = json_encode($parent, JSON_UNESCAPED_UNICODE);

        $directory = Root . '/upload/uploaz/market/json_temp/';
        $now = date("d.m.Y_H-i-s");
        $filePath = $directory . 'Заказы_' . $now . '.json';
        file_put_contents($filePath, $json_data);
//        return $json_data;
        return $filePath;
    }

    public function modelToJson2($modelClassName, $checkKeys, $names)
    {

        $modelClass = $this->bootFull($modelClassName);

        /** @var ZActiveRecord $model */
        $model = new $modelClass();

        if ($checkKeys)
            $checkedItems = ArrayHelper::toArray($model::findAll($checkKeys));
        else
            $checkedItems = ArrayHelper::toArray($model::find()->all());

        $columnNames = $model->columns;
        $excludes = [
            'deleted_at',
            'deleted_by',
            'deleted_text',
            'created_at',
            'modified_at',
            'created_by',
            'modified_by'
        ];

        $titles = [];
        foreach ($columnNames as $key => $column) {
            $titles[$key] = $column->title;
        }

        $result = [];
        foreach ($checkedItems as $checkedItem) {

            $attributes = [];
            foreach ($checkedItem as $itemKey => $itemVal) {

                if (ZArrayHelper::isIn($itemKey, $excludes))
                    continue;

                $attributes[$titles[$itemKey]] = $itemVal;
            }

            $result[$model->configs->title][] = $attributes;
        }

        $json_data = json_encode($result, JSON_UNESCAPED_UNICODE);

        $directory = Root . '/upload/uploaz/market/json_temp/';
        $now = date('d.m.Y_H-i-s');
        $filePath = $directory . 'Заказы_' . $now . '.json';
        file_put_contents($filePath, $json_data);
        return $filePath;
    }


    public function modelsToJson($modelClass, $data, $checkKeys)
    {

        $model = new $modelClass();

        $result = [];

        $checkKeys = ZFormatter::filterValue($checkKeys);

        $titles = [];
        foreach ($model->columns as $key => $column) {
            $titles[$key] = $column->title;
        }

        $models = [];
        foreach ($data as $attributes) {

            $id = ZArrayHelper::getValue($attributes, 'id');
            if (!empty($checkKeys)) {
                if (!ZArrayHelper::isIn($id, $checkKeys))
                    continue;
            }

            $newAttributes = [];
            foreach ($attributes as $key => $value) {
                if (ZArrayHelper::keyExists($key, $titles))
                    $newAttributes[$titles[$key]] = $value;
            }

            $models[] = $newAttributes;
        }

        $result[$model->configs->title] = $models;

        $json_data = json_encode($result, JSON_UNESCAPED_UNICODE);

        $now = date('d.m.Y_H-i-s');
        $filePath = Root . '/upload/uploaz/market/json_temp/' . "$modelClassName-$now" . '.json';

        file_put_contents($filePath, $json_data);

        return $filePath;

    }


    public function modelToJson($modelClassName, $checkKeys, $names)
    {
        $modelClass = $this->bootFull($modelClassName);
        /** @var ZActiveRecord $model */
        $model = new $modelClass();

        if ($checkKeys)
            $checkedItems = ArrayHelper::toArray($model::findAll($checkKeys));
        else
            $checkedItems = ArrayHelper::toArray($model::find()->all());

        $columnNames = $model->columnsList();
        $del_val = ["deleted_at", "deleted_by", "deleted_text", "created_at", "modified_at", "created_by", "modified_by"];

        $size = count($del_val);
        for ($i = 0; $i < $size; $i++) {
            if (($key = array_search($del_val[$i], $columnNames)) !== false) {
                unset($columnNames[$key]);
            }
        }

        if (!empty($names))
            $columnNames = $names;

        $arrayParent = null;
        /*
                for ($i = 0; $i < count($checkedItems); $i++) {
                    $arrayChild = null;
                    for ($c = 0; $c < count($columnNames); $c++) {
                        $column = $columnNames[$c];
                        $value = $checkedItems[$i][$column];
                        $arrayChild[$column] = $value;
                    }
                    $child[] = $arrayChild[$column];
                }*/
        $all = [];
        foreach ($checkedItems as $checkedItem) {
            $arrayChild = [];
            foreach ($columnNames as $columnName) {
                $arrayChild[$columnName] = ZArrayHelper::getValue($checkedItem, $columnName);
            }
            $all[] = $arrayChild;
        }


        $ware_accepts = WareAccept::find()->all();

        $parent = [];
        foreach ($ware_accepts as $ware_accept) {


            $child = [
                'Дата' => $shoporder['date'],
                'Номер' => $shoporder['number'],
                'УникальныйИдентификатор' => $shoporder['id'],
                'Курьер' => $courier,
                //"Курьер_Код" => $shoporder['id'],
                //"Курьер_GUID" => $shoporder['id'],
                'Выполнен' => $shoporder['accepted'],
                //"КолЗаказовДС" => $shoporder['id'],
                //"КоличествоВыпПоПроектам" => $shoporder['id'],
                //"ЗарплатаКурьеру" => $shoporder['id'],
                //"Остаток" => $shoporder['id'],

            ];

        }

        foreach ($all as $key => $shoporder) {
            //vdd($shoporder['shop_shipment_id']);
            $shipment = ShopShipment::findOne($shoporder['shop_shipment_id']);
            $courier = null;
            if ($shipment)
                $courier = $shipment['shop_courier_id'];

            $child = [
                'Дата' => $shoporder['date'],
                'Номер' => $shoporder['number'],
                'УникальныйИдентификатор' => $shoporder['id'],
                'Курьер' => $courier,
                //"Курьер_Код" => $shoporder['id'],
                //"Курьер_GUID" => $shoporder['id'],
                'Выполнен' => $shoporder['accepted'],
                //"КолЗаказовДС" => $shoporder['id'],
                //"КоличествоВыпПоПроектам" => $shoporder['id'],
                //"ЗарплатаКурьеру" => $shoporder['id'],
                //"Остаток" => $shoporder['id'],

            ];
            $arrayParent['Приемки'][] = $child;
        }

        $json_data = json_encode($arrayParent, JSON_UNESCAPED_UNICODE);

        $directory = Root . '/upload/uploaz/market/json_temp/';
        $now = date('d.m.Y_H-i-s');
        $filePath = $directory . 'Заказы_' . $now . '.json';
        file_put_contents($filePath, $json_data);
        return $filePath;
    }

}
