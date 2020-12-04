<?php
/**
 * Author:  Xolmat Ravshanov
 */

namespace zetsoft\service\calls;


use zetsoft\dbitem\calls\AutoDialItem;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\user\User;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

class AutoDial extends ZFrame
{
    #region Vars
    
    #endregion




    public function clearNumber($number){
        $number = trim($number);
        $number = strtr($number,[
            '-' => ''
        ]);
        return $number;
    }


    // auto dial
    public function callAgent($agentId, $status = 'autodial')
    {
        $calls = [];
         if($agentId == null)
            return null;
       
        $agentNumber = User::findOne($agentId);
                                  
        $shopOrders = ShopOrder::find()
            ->where([
                'operator' => $agentId,
                'status_callcenter' => $status,
            ])
            ->all();

        foreach ($shopOrders as $key => $shopOrder) {
            $autodial = new AutoDialItem();
            $autodial->operator = $agentNumber->number;
            $autodial->client = $this->clearNumber($shopOrder->contact_phone);
            $autodial->order_id = $shopOrder->id;
            $autodial->order = $shopOrder;
            $calls[] = $autodial;
        }
        return $calls;
    }






    // auto dial
    public function callAgentOldStableVersion($agentId, $status = 'autodial')
    {
        $calls = [];
        if($agentId == null)
            return null;

        $agentNumber = User::findOne($agentId);

        $shopOrders = ShopOrder::find()
            ->where([
                'operator' => $agentId,
                'status_callcenter' => $status,
            ])
            ->all();

        foreach ($shopOrders as $key => $shopOrder) {
            $autodial = new AutoDialItem();
            $autodial->operator = $agentNumber->number;
            $autodial->client = $shopOrder->contact_phone;
            $autodial->order_id = $shopOrder->id;
            $autodial->order = $shopOrder;
            $calls[] = $autodial;
        }
        return $calls;
    }







    // auto dial
    public function callAgent2($agentId, $status = 'autodial')
    {
        $agentNumber = User::findOne($agentId);
        $shopOrder = ShopOrder::find()
            ->where([
                'operator' => $agentId,
                'status_callcenter' => $status
            ])
            ->one();

        $autodial = new AutoDialItem();
        $autodial->operator = $agentNumber->number;
        $autodial->client = $shopOrder->contact_phone;
        $autodial->order_id = $shopOrder->id;
        $autodial->order = $shopOrder;

        return $autodial;
    }


    public function callAgent1()
    {
        $calls = [];
        $autodial = new AutoDialItem();
        $autodial->operator = '305';
        $autodial->client = '4444';
        $autodial->order_id = '3244444444';
        $calls[] = $autodial;

        $autodial = new AutoDialItem();
        $autodial->operator = '305';
        $autodial->client = '233';
        $autodial->order_id = '1222222222222';
        $calls[] = $autodial;

        $autodial = new AutoDialItem();
        $autodial->operator = '305';
        $autodial->client = '304';
        $autodial->order_id = '2222222222222';
        $calls[] = $autodial;
        return $calls;
    }


}
