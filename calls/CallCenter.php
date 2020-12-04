<?php

namespace zetsoft\service\calls;


use zetsoft\models\shop\ShopOrder;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\Az;


class CallCenter extends ZFrame
{

    public function setDateApprove(ShopOrder $model)
    {
        if ($model->isNewRecord)
            return null;

        if ($model->oldAttributes['status_callcenter'] == $model->status_callcenter)
            return null;


        if ($model->status_callcenter !== $model::status_callcenter['approved'])
            return null;


        $model->date_approve = Az::$app->cores->date->dateTime();

    }


}
