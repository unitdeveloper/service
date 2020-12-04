<?php

/**
 * Author: Javohir
 * Date:    12.06.2020
 */

namespace zetsoft\service\market;

use yii\base\ErrorException;
use zetsoft\models\user\UserCompany;
use zetsoft\models\shop\ShopOverview;
use zetsoft\system\Az;
use zetsoft\dbitem\chat\ReviewItem;
use zetsoft\models\core\CoreMenuNew;
use zetsoft\models\shop\ShopReview;
use zetsoft\models\user\User;
use zetsoft\system\kernels\ZFrame;


class Overview extends ZFrame
{


    #region init

    public function init()
    {
        parent::init();
    }

    #endregion

    #region getOverviews

    public function GetOverviewsTest()
    {
        $coreOverviews= ShopOverview::find()
            ->where([
                'user_id' => null,


            ])->all();


        $data = $this->getOverviews($coreOverviews);
        vd($data);
    }

    public function  getOverviews($coreOverviews){

        $OverviewItems = [];
        foreach ($coreOverviews as $core_overview) {

            if ($coreOverviews != null)
                $user = User::find()->where([
                    'id' => $core_overview->user_id
                ])->one();

            if ($user == null)
                return   new ErrorException("The User by Id: ".$core_overview->id ."does not exsist!!!");

            $OverviewItem = new ReviewItem();

        }
    }

    #endregion


    #region getOverViewByBrandId

    public function GetOverViewByBrandIdTest()
    {
        $data = $this->getOverViewByBrandId();
        vd($data);
    }

    public function getOverViewByBrandId($id){
        $overview = ShopOverview::find()
            ->where([
                'shop_brand_id' => $id,
                'type' => 'brand',

            ])->asArray()->all();

     //   $result = $this->getOverviews($overview);
        return $overview;

    }

    #endregion

    #region getOverViewByCompanyId

    public function testGetOverViewByCompanyId()
    {
        $data = $this->getOverViewByCompanyId();
        vd($data);
    }

    public function getOverViewByCompanyId($id){
        $overview = ShopOverview::find()
            ->where([
                'user_company_id' => $id,
                'type' => 'company',

            ])->asArray()->all();

        //   $result = $this->getOverviews($overview);
        return $overview;

    }

    #endregion

    #region getOverViewByProductId

    public function testGetOverViewByProductId()
    {
        $data = $this->getOverViewByProductId();
        vd($data);
    }

    public function getOverViewByProductId($id){
        $overview = ShopOverview::find()
            ->where([
                'shop_product_id' => $id,
                'type' => 'product',

            ])->asArray()->all();

        //   $result = $this->getOverviews($overview);
        return $overview;

    }

    #endregion

    #region getOverviewList

    public function testGetOverviewList()
    {
        $data = $this->getOverviewList();
        vd($data);
    }

    public function getOverviewList(){
        $overview = ShopOverview::find()->asArray()->all();

        //   $result = $this->getOverviews($overview);
        return $overview;

    }

    #endregion

    #region getBrandList

    public function testGetBrandOverview()
    {
        $data = $this->getBrandOverview();
        vd($data);
    }

    public function getBrandOverview(){
        $overview = ShopOverview::find()->where(['type'=>'brand'])->asArray()->all();

        //   $result = $this->getOverviews($overview);
        return $overview;

    }

    #endregion

    #region getCompanyList

    public function testGetCompanyOverview()
    {
        $data = $this->getCompanyOverview();
        vd($data);
    }

    public function getCompanyOverview(){
        $overview = ShopOverview::find()->where(['type'=>'company'])->asArray()->all();

        //   $result = $this->getOverviews($overview);
        return $overview;

    }

    #endregion

    #region getProductList

    public function testGetProductOverview()
    {
        $data = $this->getProductOverview();
        vd($data);
    }

    public function getProductOverview(){
        $overview = ShopOverview::find()->where(['type'=>'product'])->asArray()->all();

        //   $result = $this->getOverviews($overview);
        return $overview;

    }

    #endregion
    

}
