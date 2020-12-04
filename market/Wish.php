<?php

/**
 * Author:  Maxamadjonov Jaxongir, Xolmat, Jobir
 * Date:    07.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 */


namespace zetsoft\service\market;


use zetsoft\system\kernels\ZFrame;
use zetsoft\system\Az;


class Wish extends ZFrame
{


    public function WriteWish1Test(){
        $data=$this->writeWish1(1);
        vd($data);
    }
    public function writeWish1($product_id)
    {
        $wish_list = [];

        $getWish = Az::$app->cores->session->get('wishList');
        if (is_array($getWish)) {
            if (!in_array($product_id, $getWish)) {
                $wish_list = $getWish;
                array_push($wish_list, $product_id);

                Az::$app->cores->session->set('wishList', $wish_list);
            } else {
                $wish_list = $getWish;
                $key = array_search($product_id, $wish_list, true);
                unset($wish_list[$key]);
                Az::$app->cores->session->set('wishList', $wish_list);
            }
        } else {
            array_push($wish_list, $product_id);
            Az::$app->cores->session->set('wishList', $wish_list);
        }
        if (Az::$app->cores->session->get('wishList') == null) {
            return 0;
        } else {
            return count(Az::$app->cores->session->get('wishList'));
        }

    }

    public function WriteWishTest(){
        $data=$this->writeWish(1);
        vd($data);
    }
    public function writeWish($product_id)
    {
        $wish_list = [];
        $getWish = $this->sessionGet('wishList');
        if (is_array($getWish)) {
            if (!in_array($product_id, $getWish)) {
                $wish_list = $getWish;
                $wish_list[] = $product_id;
                $this->sessionSet('wishList', $wish_list);
            } else {
                $wish_list = $getWish;
                $key = array_search($product_id, $wish_list);
                unset($wish_list[$key]);
                $this->sessionSet('wishList', $wish_list);
            }
        } else {
            $wish_list[] = $product_id;
            $this->sessionSet('wishList', $wish_list);
        }
       if ($this->sessionGet('wishList'))
            return count($this->sessionGet('wishList'));
        else
            return 0;
    }
       //[1, 334]

    public function WriteCompare1Test(){
        $data=$this->writeCompare1(1);
    }
    public function writeCompare1($product_id)
    {
        $compare_list = [];
        $getCompare = Az::$app->cores->session->get('compare');
        if (is_array($getCompare)) {
            if (!in_array($product_id, $getCompare)) {
                $compare_list = $getCompare;
                $compare_list[] = $product_id;
                Az::$app->cores->session->set('compare', $compare_list);
            } else {
                $compare_list = $getCompare;
                $key = array_search($product_id, $compare_list, true);
                unset($compare_list[$key]);
                Az::$app->cores->session->set('compare', $compare_list);
            }
        } else {
            $compare_list[] = $product_id;
            Az::$app->cores->session->set('compare', $compare_list);
        }
        if (Az::$app->cores->session->get('compare') == null) {
            return 0;
        } else {
            return count(Az::$app->cores->session->get('compare'));
        }
    }
    public function WriteCompareTest(){
        $data=$this->writeCompare(1);
    }
    public function writeCompare($product_id)
    {
        $compare_list = [];
        $getCompare = $this->sessionGet('compare');
        if (is_array($getCompare)) {
            if (!in_array($product_id, $getCompare)) {
                $compare_list = $getCompare;
                $compare_list[] = $product_id;
                $this->sessionSet('compare', $compare_list);
            } else {
                $compare_list = $getCompare;
                $key = array_search($product_id, $compare_list);
                unset($compare_list[$key]);
                $this->sessionSet('compare', $compare_list);
            }
        } else {
            $compare_list[] = $product_id;
            $this->sessionSet('compare', $compare_list);
        }
        if ($this->sessionGet('compare'))
            return count($this->sessionGet('compare'));
        else
            return 0;
    }

    public function writeViewedTest(){
        $data=$this->writeViewed(1);
    }
    public function writeViewed($product_id)
    {
        $viewed_list = [];

        $getViewed = Az::$app->cores->session->get('viewed');
        if (is_array($getViewed)) {
            if (!in_array($product_id, $getViewed)) {
                $viewed_list = $getViewed;
                array_push($viewed_list, $product_id);

                Az::$app->cores->session->set('viewed', $viewed_list);
            } else {
                /*$viewed_list = $getViewed;
                $key = array_search($product_id, $viewed_list, true);
                unset($viewed_list[$key]);
                Az::$app->cores->session->set('viewed', $viewed_list);*/
            }
        } else {
            $viewed_list[] = $product_id;
            Az::$app->cores->session->set('viewed', $viewed_list);
        }
        if (Az::$app->cores->session->get('viewed') == null) {
            return 0;
        } else {
            return count(Az::$app->cores->session->get('viewed'));
        }

    }
    public function CheckWishTest(){
        $data=$this->CheckWish(1);
    }

    public function CheckWish($product_id)
    {
        if (is_array($this->sessionGet('wishList'))) {
            if (in_array($product_id, $this->sessionGet('wishList')))return true;
            else return false;
        }
    }
    public function CheckCompareTest(){
        $data=$this->CheckCompare(1);
    }
    public function CheckCompare($product_id)
    {
        if (is_array($this->sessionGet('compare'))) {
            if (in_array($product_id, $this->sessionGet('compare')))return true;
            else return false;
        }
    }
    public function test()
    {
        $this->writeCompare(33);
//        $this->writeViewed(33);
//        $this->writeWish(33);
    }

}




