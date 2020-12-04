<?php

/**
 * Author: Sardor
 * Date:    07.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 */

namespace zetsoft\service\market;

use FontLib\Table\Type\post;
use Google\ApiCore\OperationResponse;
use Illuminate\Support\Collection;
use yii\caching\TagDependency;
use zetsoft\webs\core\ZReturnAction;
use zetsoft\dbitem\shop\OrderElementItem;
use zetsoft\dbitem\shop\CompanyCardItem;
use zetsoft\dbitem\wdg\MenuItem;
use zetsoft\dbitem\shop\OrderItem;
use zetsoft\dbitem\shop\ProductItem;
use zetsoft\dbitem\shop\PropertyItem;
use zetsoft\dbitem\data\ALLApp;
use zetsoft\dbitem\data\Config;
use zetsoft\dbitem\data\ConfigDB;
use zetsoft\dbitem\data\Form;
use zetsoft\models\core\CoreSession;
use zetsoft\models\page\PageAction;
use zetsoft\models\place\PlaceAdress;
use zetsoft\models\shop\ShopBrand;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopCatalogWare;
use zetsoft\models\shop\ShopCategory;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\shop\ShopShipment;
use zetsoft\models\user\UserCompany;

use zetsoft\models\place\PlaceCountry;
use zetsoft\models\shop\ShopElement;
use zetsoft\models\menu\Menu;
use zetsoft\models\menu\MenuImage;
use zetsoft\models\core\CoreMenuNew;
use zetsoft\models\shop\ShopOption;
use zetsoft\models\shop\ShopOptionType;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopProduct;
use zetsoft\models\place\PlaceRegion;
use zetsoft\models\user\User;
use zetsoft\models\App\eyuf\Cupon;
use zetsoft\system\assets\ZColor;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\helpers\ZUrl;
use zetsoft\system\kernels\ZFrame;
use zetsoft\widgets\former\ZDynaWidget;
use zetsoft\widgets\former\ZFormWidget;
use zetsoft\widgets\incores\ZMCheckboxGroupWidget;
use zetsoft\widgets\incores\ZMRadioWidget;
use zetsoft\widgets\inputes\ZHCheckboxButtonGroupWidget;
use zetsoft\widgets\inputes\ZSelect2Widget;
use zetsoft\widgets\navigat\ZGAccordionWidget;
use zetsoft\widgets\values\ZFormViewWidget;
use function PHPUnit\Framework\isInstanceOf;
use function Spatie\array_keys_exist;


class OrderXolmat extends ZFrame
{

#region init

    public function init()
    {

        parent::init();
    }



#endregion

    #region test

    public function test()
    {

        $user_order_list = [];
        $user_id = 109;
        $orders = $this->getOrderList($user_id);
        foreach ($orders as $order) {
            $order_item = new OrderItem();
            $order_item->sum = $order->total_price;
            $order_item->status_client = $order->status_client;
            $order_item->created_at = $order->created_at;
            $id = (int)$order->id;
            $order_item->current_order_element_items[] = $this->getProductBelongsToOrder($id);
            $user_order_list[] = $order_item;
        }

        vd($user_order_list);
    }



    #endregion


#region getOrderList
    /**
     * If has not user_id and type return  all order list
     * Otherwise returns existing type and user orders
     * Function  getOrderList
     * @param null $user_id
     * @param null $type
     * @return  mixed
     */
    public function getOrderList($user_id = null, $status = null)
    {

        $type = strtolower($status);
        if ($user_id === null && $status === null) {
            return ShopOrder::find()->all();
        }
        if ($status === null && $user_id !== null) {
            $user_order_s = ShopOrder::find()->
            where([
                'user_id' => $user_id
            ])->all();

            return $user_order_s;
        }
        if ($status !== null && $user_id === null) {
            $user_order_s = ShopOrder::find()->where([
                'status' => $status
            ])->all();
            return $user_order_s;
        }
        $user_order_s = ShopOrder::find()->where([
            'user_id' => $user_id
        ])->andWhere([
            'status_client' => $status
        ])->all();
        return $user_order_s;
    }

#endregion
#region UserOrderList
    public function getUserOrderList()
    {

        $user_order_list = [];
       // $user_id = $this->userIdentity()->id;

            $i = 1;
        $orders = $this->getOrderList(139);
        foreach ($orders as $order) {
            $order_item = new OrderItem();
            $order_item->sum = $order->total_price;
            $order_item->status_client = $order->status_client;
            $order_item->created_at = $order->created_at;
            $order_item->id = $i++;
            $id = (int)$order->id;
            $order_item->current_order_element_items[] = $this->getProductBelongsToOrder($id);
            $user_order_list[] = $order_item;
        }
        return $user_order_list;
    }

#endregion


    #region UserOrderList
    public function getUserOrderList1($status = null)
    {
        if ($status === null)
            return null;

        $user_order_list = [];
        $user_id = $this->userIdentity()->id;
        $orders = $this->getOrderList(76, $status);
        foreach ($orders as $order) {
            $order_item = new OrderItem();
            $order_item->sum = $order->total_price;
            $order_item->created_at = $order->created_at;
            $id = (int)$order->id;
            $order_item->current_order_element_items[] = $this->getProductBelongsToOrder($id);
            $user_order_list[] = $order_item;
        }
        return $user_order_list;
    }

#endregion


    /** @var ShopOrder $shop_order */
    public function orderBuyed($shop_order)
    {

        $order_items = ShopOrderItem::find()
            ->where([
                'shop_order_id' => $shop_order->id
            ])
            ->all();

        $place_adress = PlaceAdress::findOne($shop_order->place_adress_id);
        $place_region = PlaceRegion::findOne($place_adress->place_region_id);

        /** @var ShopOrderItem $order_item */
        foreach ($order_items as $order_item) {

            $catalog_ware = ShopCatalogWare::findOne([
                'shop_catalog_id' => $order_item->shop_catalog_id,
                'ware_id' => $place_region->ware_id
            ]);


            if ($catalog_ware) {

                $catalog_ware->amount -= (int)$order_item->amount;

                if ($catalog_ware->save()) {
                    $catalog = ShopCatalog::findOne($order_item->shop_catalog_id);

                    $catalog->amount -= $order_item->amount;
                    $catalog->save();
                }

            }

        }

    }



#region getOrderAddres


    /**
     * Return order address
     * Give order object
     * Function  getOrderAddress
     * @param null $order obj
     * @return  array
     */
    public function getOrderAddress($order = null)
    {
        if ($order === null) {
            return [];
        }
        $current_order_address = Az::$app->market->address->getAddress($order[0]->id, 'order');
        return $current_order_address;
    }

#endregion


    public function getProductBelongsToOrder($order_id = null)
    {
        if ($order_id === null)
            return null;

        $orderItems = [];

        $current_order = ShopOrder::findOne($order_id);
        $order_items = ShopOrderItem::find()
            ->where([
                'shop_order_id' => $order_id
            ])
            ->all();
        //$current_order->getShopOrderItemsFromShopOrderItemIds();
        foreach ($order_items as $order_item) {
            $c_order = new OrderElementItem();
            //$core_catalog = $order_item->getCoreCatalog() ?? null;
            $c_order->amount = $order_item->amount;
            $c_order->sum = ($order_item->amount) * ($order_item->price);
            $c_order->created_at = $order_item->created_at;
            $c_order->status = $current_order->status_client;

            $core_catalog = ShopCatalog::findOne($order_item->shop_catalog_id) ?? null;
            if ($core_catalog === null) continue;
            $c_order->element_price = $core_catalog->price;

            //$user_company = $core_catalog->getUserCompany() ?? null;
            $user_company = UserCompany::findOne($core_catalog->user_company_id) ?? null;
            if ($user_company === null) continue;
            $c_order->catalog_name = $user_company->name;

                    //$shop_element = $core_catalog->getCoreElement() ?? null;
            $shop_element = ShopElement::findOne($core_catalog->shop_element_id) ?? null;
            if ($shop_element === null) continue;
            $c_order->element_name = $shop_element->name;
                    //$shop_product = $shop_element->getCoreProduct() ?? null;
                    $shop_product = ShopProduct::findOne($shop_element->shop_product_id) ?? null;
                    if ($shop_product === null) continue;
                    $c_order->image = $shop_product->image;
                    $orderItems[] = $c_order;



        }


        return $orderItems;
    }

    #region product.php
    #region order and shipment
    public function OrderForm()
    {
        $config = new Config();

        $app = new ALLApp();
        $app->configs = $config;

        //address
        $column = new Form();
        $column->dbType = dbTypeInteger;
        $column->widget = ZSelect2Widget::class;
        $column->options = [
            'data' => Az::$app->market->address->getUserAddresses(11),
            'name' => 'address_id',
            'label' => ShopShipment::shipment_type
        ];
        $app->columns['address_client/checkout/mainid'] = $column;
        //address

        //shipment_type_id
        $column = new Form();
        $column->dbType = dbTypeString;
        $column->widget = ZMRadioWidget::class;
        $column->options = [
            'config' => [
                'hasPlace' => true
            ],
            'data' => ShopShipment::shipment_type,
            'name' => 'shipment_type',
        ];
        $column->rules = [
            ['zetsoft\\system\\validate\\ZRequiredValidator']
        ];
        $app->columns['shipment_type'] = $column;
        //shipment_type_id

        //contact
        $column = new Form();
        $column->dbType = dbTypeJsonb;
        $column->widget = ZFormWidget::class;
        $column->valueWidget = ZFormViewWidget::class;
        $column->options = [
            'config' => [
                'formClass' => 'zetsoft\\former\\auth\\AuthContactForm',
                'title' => Az::l("Ваши контакты"),
                'titleClass' => ZColor::color['success-color']
            ],
        ];
        $column->valueOptions = [
            'config' => [
                'formClass' => 'zetsoft\\former\\auth\\AuthContactForm',

            ],
        ];
        $app->columns['contact_info'] = $column;
        //contact

        $app->cards = [];
        return Az::$app->forms->former->model($app);
    }

    public function getOwnShipments($operator_id)
    {
        $model = new ShopShipment();

        $model->configs->query = ShopShipment::find()
            ->select('core_shipment.*, core_order.*')
            ->leftJoin('core_order', 'core_order.id = core_shipment.order_id')
            ->where(['core_order.status' => 'checking'])
            ->andWhere(['core_order.operator_id' => $operator_id])
            ->with('core_shipment');

        return $model;
    }
#endregion
    #endregion


    public function testSaveOrder()
    {

        $post = [
            'contact_name' => 'CLI_Test',
            'place_adress_id' => 1,
            'comment_user' => 'CLI_Test_comment'
        ];
        vdd($this->saveOrders($post));

    }

    public function saveOrders($post = [])
    {

        $products = Az::$app->market->cart->cartOrders();

        /** @var Collection $items */

        $products = collect($products)->groupBy('company_id');

        $order_id = null;
        $products->each(function ($items, $company_id) use ($post, $order_id) {
            if ($items[0] === null) return null;
            $order = new ShopOrder();
            // vdd($items);
            $order->user_id = $this->userIdentity()->id;
            $order->contact_name = ZArrayHelper::getValue($post, 'contact_name');
            $order->place_adress_id = ZArrayHelper::getValue($post, 'place_adress_id');
            $order->contact_phone = ZArrayHelper::getValue($post, 'contact_phone');
            $order->comment_user = ZArrayHelper::getValue($post, 'comment_user');
            $order->payment_type = ZArrayHelper::getValue($post, 'payment_type');

            $order->user_company_id = $company_id;


            if ($order->save()) {
                $order_id = $order->id;
                foreach ($items[0]->items as $item) {

                    $catalog = ShopCatalog::findOne($item->catalogId);
                    $order_item = new ShopOrderItem();
                    $order_item->shop_order_id = $order->id;

                    $order_item->shop_catalog_id = $item->catalogId;
                    $order_item->amount = $item->amount;
                    $order_item->price = $catalog->price;

                    if ($order_item->save()) {
                        $catalog->amount = $catalog->amount - (int)$item->amount;

                        if (!$catalog->save()) {
                            return $catalog->errors();
                        }

                    } else return $order_item->errors();

                }
            } /* $items[0]->items->each(function ($item) use ($order) {

                     $catalog = ShopCatalog::findOne($item->catalogId);
                     $order_item = new ShopOrderItem();
                     $order_item->shop_order_id = $order->id;

                     $order_item->shop_catalog_id = $item->catalogId;
                     $order_item->amount = $item->amount;
                     $order_item->price = $catalog->price;

                     if($order_item->save()) {
                         $catalog->amount = $catalog->amount - (int)$item->amount;

                         if (!$catalog->save()){
                            return $catalog->errors();
                         }
                     }else return $order_item->errors();
                 });*/


            else
                return $order->errors();

        });
        $this->sessionDel('cart');
        return $order_id;
    }

    public function saveOrderFromApi($catalog, $amount, $user, $company)
    {
        $order = new ShopOrder();
        $order->configs->rules = [
            [validatorSafe]
        ];
        $order->user_id = $user->id;
        $order->contact_phone = $user->phone;
        $order->contact_name = $user->name;
        $order->user_company_id = $company->id;

        if ($order->save()) {
            $orderItem = new ShopOrderItem();
            $orderItem->configs->rules = [
                [validatorSafe]
            ];
            $orderItem->shop_catalog_id = $catalog->id;
            $orderItem->shop_order_id = $order->id;
            $orderItem->amount = $amount;
            $orderItem->price = $catalog->price;


            if ($orderItem->save())
                return $order;
            else
                return $orderItem->errors();
        } else
            $order->errors();
    }

}
