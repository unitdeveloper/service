<?php

/**
 * Author: Sardor
 * Date:    07.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 */

namespace zetsoft\service\market;

use Illuminate\Support\Collection;
use zetsoft\models\shop\ShopCoupon;
use zetsoft\models\core\CoreMenuNew;
use zetsoft\system\kernels\ZFrame;

class Coupon2 extends ZFrame
{
    /**
     * @var Collection $coupons;
     */
    private $coupons;

    #region init

    public function init()
    {
        parent::init();
        $this->coupons = collect(ShopCoupon::find()->all());
    }

    public function test()
    {
        //vdd($this->getCouponByCode(1)); //Ishliydi
       //$this->getCouponList();   //Ishliydi
       // $this->getCouponBySatus(5);
       
    }



    #endregion

    #region getCouponList

    /**
     * Accept coupon status
     * Return coupon list
     * Function  getCouponList
     * @param null $status
     */
    public function getCouponBySatus($status = null){
        if($status === null)return [];

        $coupon = $this->coupons->where('status', $status);

        return $coupon;
    }

    /**
     *
     * Function  getCuponList
     * @param null $cupon_id
     * @param null $type
     */
    public function getCouponList($coupon_id = null, $type = null){
        if($type !== null) $type = strtolower($type);
        if($coupon_id === null){
            $coupon = $this->coupons->all();
        }
        return $coupon;
    }

    #endregion

    #region GetCouponByCode

        public function getCouponByCode($coupon_code = null){
            $coupon = $this->coupons->firstWhere('code', $coupon_code);
            return $coupon;
        }

    #endregion


}
