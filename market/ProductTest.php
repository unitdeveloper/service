<?php

/**
 * Author: Jobir Yusupov
 * Date:    07.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 */

namespace zetsoft\service\market;


use zetsoft\widgets\incores\ZIRadioGroupWidget;

class ProductTest extends Product
{

    public function test()
    {
        //$this->productByStatusTest();
        $this->sortProductsTest();
        //$this->allCompaniesTest();
        //$this->allProductsTest();
    }

    public function allProductsTest()
    {
        $category_id = 457;
        $company_id = 14;
        $page = 5;
        $limit = 1;
        $sort = [];

        $data = parent::allProducts($category_id, $company_id, $page, $limit, $sort);
        vd($data);
    }



    public function allCompaniesTest()
    {

        $page = 0;
        $limit = 5; //nulldan boshqa qiymat berilsa [] bo'sh array return qivotti


        $data = parent::allCompanies($page, $limit);
         vd($data);
    }


    public function sortProductsTest()
    {

        $shop_products = null;
        $sort = null;
        $category_id = 313;

        $data = parent::sortProducts($shop_products, $sort, $category_id);
        vd($data);  //getAddressAddress not found
        
    }

    public function productByStatusTest()
    {
         $category_id = 69;
         $status = 4;
         $count = 3;
        $company_id = null;

        $data =  productByStatus($category_id, $status, $count, $company_id);

    }



}
