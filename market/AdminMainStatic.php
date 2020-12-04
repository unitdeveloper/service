<?php

/**
 * Author: Maladoy
 */

namespace zetsoft\service\market;


use zetsoft\dbitem\shop\AdminItem;
use zetsoft\dbitem\shop\AdminNewItem;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\user\User;
use zetsoft\models\user\UserCompany;
use zetsoft\system\kernels\ZFrame;


class AdminMainStatic extends ZFrame
{
    public $seller;
    public $market;
    public $user;
    public $order;
    public $agent;
    

    #region init

    public function init()
    {
        parent::init();
        $this->user = User::find()->select('role')->asArray()->all();
        $this->market = UserCompany::find()->asArray()->all();
        $this->order = ShopOrder::find()->asArray()->all();

    }

    #endregion

    public function adminInfo()
    {
        $result = new AdminNewItem();
        $result->user = 0;
        $result->order = collect($this->order)->count();
        $result->seller = collect($this->user)->where('role', 'seller')->count();
        $result->user = collect($this->user)->where('role', 'client')->count();
        $result->agent = collect($this->user)->where('role', 'agent')->count();
        
        return (array)$result;
    }
}
