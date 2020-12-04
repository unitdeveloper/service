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


class BrandTest extends Brand
{
    public function test()
    {
        //$this->getBrand();
        //$this->getPriceString();

         $this->getCompanyListTest();
    }

    public function getCompanyListTest()
    {

        $productId = 653;
         $options = [];

       $example= parent::companyList($productId, $options);

       vd($example);
    }


}



