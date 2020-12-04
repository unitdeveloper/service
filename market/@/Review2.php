<?php

/**
 * Author: Xolmat
 * Date:    07.06.2020
 */

namespace zetsoft\service\market;

use Illuminate\Support\Collection;
use yii\base\ErrorException;
use zetsoft\dbitem\chat\ReviewItem;
use zetsoft\models\shop\ShopReview;
use zetsoft\models\user\User;
use zetsoft\system\kernels\ZFrame;


class Review2 extends ZFrame
{
    /**
     * @var Collection $core_reviews
     */
    private $core_reviews;

    /**
     * @var Collection $users
     */
    private $users;


    #region init

    public function init()
    {
        parent::init();
        $this->core_reviews = collect(ShopReview::find()->all());
        $this->users = collect(User::find()->all());
    }

    #endregion

    #region reviewsByproductId
    private function getReviewItems($coreReviews)
    {
        $reviewItems = [];

        foreach ($coreReviews as $core_review) {
              
            if ($core_review != null)
                $user = $this->users->firstWhere('id', $core_review->user_id);

            if ($user == null)
                return new ErrorException($core_review->id ."li reviewni useri bazadan o'chib ketgan");
             
            $reviewItem = new ReviewItem();
            $reviewItem->id = $core_review->id;
            $reviewItem->product_id = $core_review->attributes['shop_product_id'];
            $reviewItem->user_name = $user->name;
            $reviewItem->user_image = $user->photo;
            $reviewItem->text = $core_review->text;
            $reviewItem->rating = $core_review->rating;
            $reviewItem->like = $core_review->like;
            $reviewItem->created_at = $core_review->created_at;
            $reviewItem->dislike = $core_review->dislike;
            $reviewItem->isdislike = $this->isdislike($core_review->id);
            $reviewItem->islike = $this->islike($core_review->id);

            $core_child_reviews = $this->core_reviews->where('parent_id', $core_review->id);

            if ($core_child_reviews != [])
                $reviewItem->items = $this->getReviewItems($core_child_reviews);

            $reviewItems[] = $reviewItem;
        }
        return $reviewItems;
    }

    #region  Reviews By product id
    public function getReviewByProductId($id)
    {
        if ($id != null) {
            $filtered_core_reviews = $this->core_reviews
                ->where('shop_product_id', $id)
                ->where('type', 'product')
                ->whereIn('parent_id', ['', null]);

            $result = $this->getReviewItems($filtered_core_reviews);

            return $result;
        }
        return false;
    }
    #endregion

    #region reviewsByCompanyId
    public function getReviewByCompanyId($id)
    {
        if ($id != null) {
            $filtered_core_reviews = $this->core_reviews
                ->where('user_company_id', $id)
                ->where('type', 'company')
                ->whereIn('parent_id', ['', null]);

            $result = $this->getReviewItems($filtered_core_reviews);

            return $result;
        }
        return false;
    }

    #endregion

    #region reviewsByBrandId
    public function getReviewByBrandId($id)
    {
        if ($id != null) {
            $filtered_core_reviews = $this->core_reviews
                ->where('shop_brand_id', $id)
                ->where('type', 'brand')
                ->whereIn('parent_id', ['', null]);

            $result = $this->getReviewItems($filtered_core_reviews);

            return $result;
        }
        return false;
    }

    #endregion

    #region like
    public function like($id)
    {
        // array of
        $core_review = $this->core_reviews->firstWhere($id);
            
        $like = [];
        $dislike = [];
        $getLike = $this->sessionGet('like');
        $getDisLike = $this->sessionGet('dislike');
        if ($getLike) {
             if (!in_array($id,$getLike)) {
                $like[] = $id;
                $this->sessionSet('like',$like);
                $core_review->like += 1;

                if (in_array($id, $getDisLike)) {
                     $core_review->dislike -= 1;
                }
                foreach ($getDisLike as $key => $value){
                     if(in_array($value, $getDisLike))
                         unset($getDisLike[$key]);
                }
            }else{
                foreach ($getLike as $key => $value){
                    if(in_array($value, $getLike))
                       unset($getLike[$key]);
                }
                $this->sessionSet('like', $like);
                $core_review->like -= 1;
            }
        } else {
            if (!empty($getDisLike) && in_array($id, $getDisLike)) {
                foreach ($getDisLike as $key => $value){
                    if(in_array($value, $getDisLike))
                        unset($getDisLike[$key]);
                }
                $this->sessionSet('dislike', $dislike);
                $core_review->dislike -= 1;
            }

            $like[]  = $id;
            $this->sessionSet('like', $like);
            $core_review->like += 1;
        }
        $core_review->save();
        return $core_review->like;
    }

    #endregion

    #region dislike
    public function dislike($id)
    {
        // array of
        $core_review = $this->core_reviews->firstWhere($id);

        $dislike = [];

        $getLike = $this->sessionGet('like');

        $getDisLike = $this->sessionGet('dislike');
       
        if ($getDisLike) {
            if (!in_array($id,$getDisLike)) {
                $dislike[] = $id;
                $this->sessionSet('dislike',$dislike);
                $core_review->dislike += 1;

                if (in_array($id, $getLike)) {
                    $core_review->like -= 1;
                }
                foreach ($getLike as $key => $value){
                    if(in_array($value, $getLike))
                        unset($getLike[$key]);
                }
                
            } else{
                foreach ($getDisLike as $key => $value){
                    if(in_array($value, $getDisLike))
                        unset($getDisLike[$key]);
                }
                $core_review->dislike -= 1;
                $this->sessionSet('dislike', $dislike);
            }
        } else {
            if ($getLike && in_array($id, $getLike)) {
                foreach ($getLike as $key => $value){
                    if(in_array($value, $getLike))
                        unset($getLike[$key]);
                }
                $like = $getLike;
                $this->sessionSet('like', $like);
                $core_review->like -= 1;
            }
            
            $dislike[]  = $id;
            $this->sessionSet('dislike', $dislike);
            $core_review->dislike += 1;
        }
        $core_review->save();
        return $core_review->dislike;
    }
    #endregion

    #region check like isset
    public function islike($id){
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
    public function isdislike($id)
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
    public function setByReviewId($reviewId, $text)
    {   $user_id = $this->userIdentity()->id;
        $newReview = new  ShopReview();
        $newReview->parent_id = $reviewId;
        $newReview->text = $text;
        $newReview->type = 'product';
        $newReview->user_id = $user_id;
        $newReview->save();

    }
    #endregion

    #region Test
    public function test()
    {
        $this->getReviewByProductId(33);
    }

    #endregion


}
