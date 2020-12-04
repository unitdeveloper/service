<?php

/**
 * Author: Jobir Yusupov
 * Date:    07.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 */

namespace zetsoft\service\market;


use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;
use zetsoft\widgets\incores\ZIRadioGroupWidget;

class Session extends ZFrame
{
    public const cart_type = [
        'add' => 'add',
        'set' => 'set',
    ];

    #endregion

    #region init

    public function init()
    {
        parent::init();
    }


    #endregion


    #region getCompareProductItems

    public function testGetCompareProductItems()
    {
        $data = $this->getCompareProductItems();
        vd($data);
    }

    public function getCompareProductItems()
    {
        //[1, 2, 3]
        $compares = Az::$app->cores->session->get('compare');
        //testlash uchun
        //$compares = [29, 20, 23];
        $productItems = [];
        if ($compares) {
            foreach ($compares as $product_id) {
                $productItem = Az::$app->market->product->product($product_id, null, true, false);
                $productItems[] = $productItem;
            }
        }
         
        return $productItems;
    }

    #region getCompareProductItemsWitchBranch

    public function testGetCompareProductItemsWitchBranch()
    {
        $data = $this->getCompareProductItemsWitchBranch();
        vd($data);
    }

    public function getCompareProductItemsWitchBranch()
    {

        $compares = Az::$app->cores->session->get('compare');
        //testlash uchun
        //$compares = [18, 19];
        $productItems = [];
        if ($compares) {
            foreach ($compares as $product_id) {
                $productItem = Az::$app->market->product->productWithBranch($product_id, null, true, true);
                $productItems[] = $productItem;
            }
        }
        return $productItems;
    }

    #endregion

    #region inCompare

    public function testInCompare()
    {
        $data = $this->inCompare();
        vd($data);
    }

    public function inCompare($product_id)
    {
        $compares = $this->sessionGet('compare');
        if ($compares)
            return in_array($product_id, $compares);
        else
            return false;
    }

    #endregion

    #region history

    public function testGetViewedProductItems()
    {
        $data = $this->getViewedProductItems();
        vd($data);
    }

    public function getViewedProductItems()
    {
        $compares = Az::$app->cores->session->get('viewed');
        $productItems = [];
        if ($compares) {
            foreach ($compares as $product_id) {
                $productItem = Az::$app->market->product->product($product_id);
                $productItems[] = $productItem;
            }
        }
        
        return $productItems;
    }

    #endregion

    #region getWishProductItems

    public function testGetWishProductItems()
    {
        $data = $this->getWishProductItems();
        vd($data);
    }

    public function getWishProductItems()
    {
        $wish_list = Az::$app->cores->session->get('wishList');

        $productItems = [];
        if ($wish_list) {
            foreach ($wish_list as $product_id) {
                $productItem = Az::$app->market->product->product($product_id);
                $productItems[] = $productItem;
            }
        }
        return $productItems;
    }

    #endregion

    #region inWish

    public function testInWish()
    {
        $data = $this->inWish();
        vd($data);
    }
    
    public function inWish($product_id)
    {
        $wishes = $this->sessionGet('wishList');
        if ($wishes)
            return in_array($product_id, $wishes);
        else
            return false;
    }

    #endregion

}
