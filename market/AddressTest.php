<?php

/**
 * Author: Sardor
 */

namespace zetsoft\service\market;

use zetsoft\models\place\PlaceAdress;
use zetsoft\models\user\UserCompany;
use zetsoft\models\place\PlaceCountry;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\place\PlaceRegion;
use zetsoft\models\user\User;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;


class AddressTest extends Address
{
    public function test()
    {
       $this->getAddressTest();
    }


    public function getAddressTest()
    {
         $id = 1;
         $type = 'latest';
         $data = parent::getAddress($id, $type);
        vd($data);
    }
}



