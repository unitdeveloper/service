<?php

/**
 * @author NurbekMakhmudov
 */


namespace zetsoft\service\tests;

use Illuminate\Support\Collection;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\user\User;
use zetsoft\system\kernels\ZFrame;




class NurbekTest extends ZFrame
{

    public ?Collection $shop_order = null;
    public ?Collection $user = null;

    public $filter = [];

    public function data($filter = [])
    {

        $this->filter = $filter;

        if ($this->shop_order === null)
            $this->shop_order = collect(ShopOrder::find()->asArray()->all());

        if ($this->user === null)
            $this->user = collect(User::find()->asArray()->all());
    }

    /**
     * @author NurbekMakhmudov
     * @todo get method running time
     */
    public function runningTime()
    {
        $boot = new \Boot();
        $boot->start();
        $this->all();
        echo $boot->finish();
    }




}
