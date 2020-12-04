<?php

/**
 * Author: Jaxongir
 */

namespace zetsoft\service\market;


use zetsoft\dbitem\shop\AdminItem;
use zetsoft\dbitem\shop\SellerInfoItem;
use zetsoft\former\chart\ChartFormAdmin;
use zetsoft\former\chart\ChartFormSeller;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\system\Az;
use zetsoft\models\user\UserCompany;
use zetsoft\models\core\CoreMenuNew;
use zetsoft\models\shop\ShopProduct;
use zetsoft\models\user\User;
use zetsoft\models\App\eyuf\Cupon;
use zetsoft\system\kernels\ZFrame;
use function Dash\reduce;


class GuestStatistic extends ZFrame
{
    public $order;

    #region init

    public function init()
    {
        parent::init();
        $this->order = ShopOrder::find();
    }

    #endregion

    public function userInfo($user_id)
    {
        $result = null;
        $orders = $this->order->where([
            'user_id' => $user_id
        ])->asArray()->all();
        $result['order'] = count($orders);
        return (array)$result;
    }

    public function userInfoChart($user_id)
    {
        $result = (object)$this->userInfo($user_id);
        $form = new ChartFormSeller();
        $form->name = '';
        $form->order = $result->order;
        return [$form];
    }

    public function getStatsUrls()
    {
        return [
            'market' => '/shop/admin/user-company/index.aspx?UserCompany[type]=market',
            'user' => '/shop/admin/user/index.aspx?User[role]=client',
            'seller' => '/shop/admin/user/index.aspx?User[role]=seller',
            'courier' => '/shop/admin/user/index.aspx?User[role]=deliver',
            'logistics' => '/shop/admin/user/index.aspx?User[role]=logistics',
            'agent' => '/shop/admin/user/index.aspx?User[role]=agent',
            'element' => '/shop/admin/shop-element/index.aspx',
            'product' => '/shop/admin/shop-product/index.aspx',
            'catalog' => '/shop/admin/shop-catalog/index.aspx',
            'order' => '/shop/admin/shop-order/index.aspx',

        ];
    }

    public function getStatsIcons()
    {
        return [
            'market' => 'fa-store',
            'user' => 'fa-users',
            'seller' => 'fa-tags',
            'courier' => 'fa-truck',
            'product' => 'fa-shopping-bag',
            'catalog' => 'fa-warehouse',
            'order' => 'fa-check-square',
            'agent' => 'fa-headphones',
            'logistics' => 'fa-road',
            'element' => 'fa-check',
        ];
    }

    public function getStatsValue()
    {
        return [
            'market' => Azl . ('Магазины'),
            'user' => Azl . ('Клиенты'),
            'seller' => Azl . ('Продавцы'),
            'courier' => Azl . ('Курьеры'),
            'product' => Azl . ('Продукты'),
            'element' => Azl . ('Товары'),
            'catalog' => Azl . ('В складе'),
            'order' => Azl . ('Заказы'),
            'agent' => Az::l('Операторы'),
            'logistics' => Az::l('Логисты')
        ];
    }

    public function getStatsInfoByRole($role)
    {

        $companyId = $this->userIdentity()->user_company_id;

        switch ($role) {
            case 'Администратор':
                return Az::$app->market->adminStatistic->adminInfo();
            case 'Продавец':
                return Az::$app->market->sellerStatistic->sellerInfo($companyId);
                break;
            default:
                return Az::$app->market->userStatistic->userInfo($this->userIdentity()->id);
        }

    }


}
