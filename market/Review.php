<?php

/**
 * Author: Xolmat
 * Date:    07.06.2020
 */

namespace zetsoft\service\market;

use yii\base\ErrorException;
use zetsoft\models\user\UserCompany;
use zetsoft\system\Az;
use zetsoft\dbitem\chat\ReviewItem;
use zetsoft\models\core\CoreMenuNew;
use zetsoft\models\shop\ShopReview;
use zetsoft\models\user\User;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZTest;
use zetsoft\system\kernels\ZFrame;
use function Dash\Curry\all;


class Review extends ZFrame
{


    #region init

    public function init()
    {
        parent::init();
    }

    #endregion


    #region reviewsByproductId

    public function GetReviewItemsTest()
    {
       // $model = ShopReview::find()-all();

        $coreCompanys = ShopReview::find()
            ->where([
                'user_company_id' => null,
                'type' => 'product',
                
            ])->all();

            vdd($coreCompanys);

        $data = $this->getReviewItems($coreCompanys);
        vd($data);
    }

    public function getReviewItems($coreRiviews)
    {
        $reviewItems = [];
        
        foreach ($coreRiviews as $core_review) {

            if ($coreRiviews != null)
                $user = User::find()->where([
                    'id' => $core_review->user_id
                ])->one();

            if ($user == null)
                vdd( new ErrorException($core_review->id . "li reviewni useri bazadan o'chib ketgan"));

            $reviewItem = new ReviewItem();
            $reviewItem->id = $core_review->id;
            $reviewItem->type = $core_review->type;
            $reviewItem->product_id = $core_review->attributes['shop_product_id'];
            $reviewItem->user_name = $user->name;
            $reviewItem->user_image = $user->photo;
            $reviewItem->text = $core_review->text;
            $reviewItem->photo = $core_review->photo;
            $reviewItem->rating = $core_review->rating;
            $reviewItem->rating_option = $core_review->rating_option;
            $reviewItem->experience = $core_review->experience;
            $reviewItem->recommend = $core_review->recommend;
            $reviewItem->anonymous = $core_review->anonymous;
            $reviewItem->like = $core_review->like;
            $reviewItem->created_at = $core_review->created_at;
            $reviewItem->virtues = $core_review->virtues;
            $reviewItem->drawbacks = $core_review->drawbacks;
            $reviewItem->dislike = $core_review->dislike;
            $reviewItem->type = $core_review->type;
            $reviewItem->isdislike = $this->isdislike($core_review->id);
            $reviewItem->islike = $this->islike($core_review->id);

            $core_child_reviews = ShopReview::find()
                ->where([
                    'parent_id' => $core_review->id
                ])->all();

            if ($core_child_reviews != [])
                $reviewItem->items = $this->getReviewItems($core_child_reviews);

            $reviewItems[] = $reviewItem;
        }
        return $reviewItems;
    }

    #endregion

    #region getReviewByProductId

    public function GetReviewByProductIdTest()
    {
        $data = $this->getReviewByProductId(1);
        vd($data);
    }

    public function getReviewByProductId($id)
    {
        if ($id != null) {

            $coreRiviews = ShopReview::find()
                ->where([
                    'shop_product_id' => $id,
                    'type' => 'product',
                    'parent_id' => [
                        null
                    ]
                ])->all();
          
            return $this->getReviewItems($coreRiviews);
        }
        return false;
    }

    #endregion

    #region getReviewByCompanyId

    public function GetReviewByCompanyIdTest()
    {
        $data = $this->getReviewByCompanyId(1);
        vd($data);
    }

    public function getReviewByCompanyId($id)
    {
        if ($id != null) {

            $coreCompanys = ShopReview::find()
                ->where([
                    'user_company_id' => $id,
                    'type' => 'company',
                    'parent_id' => [
                        '',
                        null
                    ]
                ])->all();

            $result = $this->getReviewItems($coreCompanys);

            return $result;
        }
        return false;
    }

    #endregion


    #region getReviewByBrandId

    public function GetReviewByBrandIdTest()
    {
        $data = $this->getReviewByBrandId(1);
        vd($data);
    }

    public function getReviewByBrandId($id)
    {

        if ($id != null) {

            $coreRiviews = ShopReview::find()
                ->where([
                    'shop_brand_id' => $id,
                    'type' => 'brand',
                    'parent_id' => [
                        '',
                        null
                    ]
                ])->all();

            $result = $this->getReviewItems($coreRiviews);

            return $result;
        }
        return false;
    }

    #endregion


    #region like

    public function LikeTest()
    {
        $data = $this->like(234);
        $item=vd($data);
        ZTest::assertEquals($item,$data);
    }

    public function like($id)
    {
        // array of
        $core_review = ShopReview::find()
            ->where([
                'id' => $id
            ])
            ->one();

        $like = [];
        $dislike = [];
        $getLike = $this->sessionGet('like');
        $getDisLike = $this->sessionGet('dislike');
        if ($getLike) {
            if (!in_array($id, $getLike)) {
                $like[] = $id;
                $this->sessionSet('like', $like);
                $core_review->like += 1;

                if (in_array($id, $getDisLike)) {
                    $core_review->dislike -= 1;
                }
                foreach ($getDisLike as $key => $value) {
                    if (in_array($value, $getDisLike))
                        unset($getDisLike[$key]);
                }
            } else {
                foreach ($getLike as $key => $value) {
                    if (in_array($value, $getLike))
                        unset($getLike[$key]);
                }
                $this->sessionSet('like', $like);
                $core_review->like -= 1;
            }
        } else {
            if (!empty($getDisLike) && in_array($id, $getDisLike)) {
                foreach ($getDisLike as $key => $value) {
                    if (in_array($value, $getDisLike))
                        unset($getDisLike[$key]);
                }
                $this->sessionSet('dislike', $dislike);
                $core_review->dislike -= 1;
            }

            $like[] = $id;
            $this->sessionSet('like', $like);
            $core_review->like += 1;
        }
        $core_review->save();
        return $core_review->like;
    }

    #endregion

    #region dislike

    public function testDislike()
    {
        $data = $this->dislike();
        vd($data);
    }

    public function dislike($id)
    {
        // array of
        $core_review = ShopReview::find()
            ->where([
                'id' => $id
            ])
            ->one();

        $like = [];
        $dislike = [];

        $getLike = $this->sessionGet('like');

        $getDisLike = $this->sessionGet('dislike');

        if ($getDisLike) {
            if (!in_array($id, $getDisLike)) {
                $dislike[] = $id;
                $this->sessionSet('dislike', $dislike);
                $core_review->dislike += 1;

                    if($getLike == null)
                        return '0';

                if (in_array($id, $getLike)) {
                    $core_review->like -= 1;
                }
                foreach ($getLike as $key => $value) {
                    if (in_array($value, $getLike))
                        unset($getLike[$key]);
                }

            } else {
                foreach ($getDisLike as $key => $value) {
                    if (in_array($value, $getDisLike))
                        unset($getDisLike[$key]);
                }
                $core_review->dislike -= 1;
                $this->sessionSet('dislike', $dislike);
            }
        } else {
            if ($getLike && in_array($id, $getLike)) {
                foreach ($getLike as $key => $value) {
                    if (in_array($value, $getLike))
                        unset($getLike[$key]);
                }
                $like = $getLike;
                $this->sessionSet('like', $like);
                $core_review->like -= 1;
            }

            $dislike[] = $id;
            $this->sessionSet('dislike', $dislike);
            $core_review->dislike += 1;
        }
        $core_review->save();
        return $core_review->dislike;
    }

    #endregion

    #region isLike

    public function testIsLike()
    {
        $data = $this->isLike();
        vd($data);
    }

    public function isLike($id)
    {
        $likes = $this->sessionGet('like');
        $isLike = false;
        if ($likes) {
            foreach ($likes as $like) {
                if ($like == $id) {
                    $isLike = true;
                }
            }
        }
        return $isLike;
    }

    #endregion

    #region

    public function testIsDislike()
    {
        $data = $this->isDislike();
        vd($data);
    }

    public function isDislike($id)
    {
        $dislikes = $this->sessionGet('dislike');
        $isDislike = false;
        if ($dislikes) {
            foreach ($dislikes as $dislike) {
                if ($dislike == $id)
                    $isDislike = true;
            }
        }
        return $isDislike;
    }

    #endregion


    #region setByReviewId

    public function testSetByReviewId()
    {
        $data = $this->setByReviewId();
        vd($data);
    }

    public function setByReviewId($reviewId, $text)
    {
        $user_id = $this->userIdentity()->id;
        $newReview = new  ShopReview();
        $newReview->parent_id = $reviewId;
        $newReview->text = $text;
        $newReview->type = 'product';
        $newReview->user_id = $user_id;
        $newReview->save();

    }
    #endregion


    #region Author: Axrorbek Nisonboyev

    public function setReviews($reviews = [])
    {
        $reviewType = ZArrayHelper::getValue($reviews, 'reviewType');
        $user_id = $this->userIdentity()->id;
        $review = new  ShopReview();
        $review->rating = ZArrayHelper::getValue($reviews , 'rating');
        $review->anonymous= ZArrayHelper::getValue($reviews , 'anonym');
        $review->recommend = ZArrayHelper::getValue($reviews , 'share');
        $review->text = ZArrayHelper::getValue($reviews , 'text');
        if ($reviewType === 'product') {
            $review->type = 'product';
        }
        if ($reviewType === 'company') {
            $review->type = 'company';
        }
        if ($reviewType === 'brand') {
            $review->type = 'brand';
        }
        $review->experience = ZArrayHelper::getValue($reviews , 'experience');
        $review->shop_product_id = ZArrayHelper::getValue($reviews , 'id');
        $review->user_id = 189;
        $date = date("Y-m-d H:i:s");
        $review->save();
        $lastReview = ShopReview::find()->where([
            'user_id' => 189,
            'created_at' => $date,
        ])->one();

        $result = $this->getLastReview($lastReview);
        return $result;
    }

    public function getLastReview($lastReview)
    {

        if ($lastReview != null)
            $user = User::find()->where([
                'id' => $lastReview->user_id
            ])->one();

        if ($user == null)
            vdd( new ErrorException($lastReview->id . "li reviewni useri bazadan o'chib ketgan"));

        $reviewItem = new ReviewItem();
        $reviewItem->id = $lastReview->id;
        $reviewItem->type = $lastReview->type;
        $reviewItem->product_id = $lastReview->attributes['shop_product_id'];
        $reviewItem->user_name = $user->name;
        $reviewItem->user_image = $user->photo;
        $reviewItem->text = $lastReview->text;
        $reviewItem->photo = $lastReview->photo;
        $reviewItem->rating = $lastReview->rating;
        $reviewItem->rating_option = $lastReview->rating_option;
        $reviewItem->experience = $lastReview->experience;
        $reviewItem->recommend = $lastReview->recommend;
        $reviewItem->anonymous = $lastReview->anonymous;
        $reviewItem->like = $lastReview->like;
        $reviewItem->created_at = $lastReview->created_at;
        $reviewItem->virtues = $lastReview->virtues;
        $reviewItem->drawbacks = $lastReview->drawbacks;
        $reviewItem->dislike = $lastReview->dislike;
        $reviewItem->type = $lastReview->type;
        $reviewItem->isdislike = $this->isdislike($lastReview->id);
        $reviewItem->islike = $this->islike($lastReview->id);

        $result = $reviewItem;
        return $result;

    }


    /*public function getReviewByType($id, $reviewType = null)
    {
        if ($id != null) {

            $reviews = ShopReview::find()
                ->where([
                    'shop_product_id' => $id,
                    'type' => $reviewType,
                    'parent_id' => [
                        '',
                        null
                    ]
                ])->all();

            $return = $this->getReviewItems($reviews);

            return $return;
        }
        return false;
    }*/
    #endregion

}
