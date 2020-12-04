<?php

/**
 * Author: Xolmat
 * Date:    07.06.2020
 * ModifyedBy: Javohir
 */

namespace zetsoft\service\market;

use phpDocumentor\Reflection\Types\Integer;
use yii\base\ErrorException;
use zetsoft\dbitem\chat\QuestionItem;
use zetsoft\models\shop\ShopQuestion;
use zetsoft\system\Az;
use zetsoft\models\core\CoreMenuNew;
use zetsoft\models\shop\ShopReview;
use zetsoft\models\user\User;
use zetsoft\system\kernels\ZFrame;


class Question extends ZFrame
{

    #region init

    public function init()
    {
        parent::init();


    }

    // Ravshanov Sardor
    public function test()
    {
       // $this->GetAnswersByQuestionIdTest();
       // $this->GetCommentsByAnswerIdTest();
       // $this->GetQuestionsByProductIdTest();
      //$this->CountQuestionsByIdTest();
      //  $this->CountReviewsByIdTest();
       // $this->GetQuestionsByCompanyIdTest();
      //  $this->GetQuestionItemsTest(); Error: 'Invalid argument supplied for foreach()'
        //$this->LikeTest();
        //$this->DislikeTest();
       // $this->IslikeTest();
       // $this->IsDislikeTest();
       // $this->ActionVoteTest();
       // $this->ActionDisVoteTest();
    }

    #endregion

    #region getAnswersByQuestionId

    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    //this function show answer by question id
    public function GetAnswersByQuestionIdTest()
    {
        $id = 172;
        $data = $this->getAnswersByQuestionId($id);
        vd($data);
    }

    public function getAnswersByQuestionId($id)
    {
        Az::start(__FUNCTION__);
        if ($id !== null) {
            $answers = ShopQuestion::find()->where([
                'parent_id' => $id,
                'type' => 'answers'
            ])->all();
            if (!empty($answers))
                return $answers;
        }
        return false;
    }

    #endregion

    #region getCommentsByAnswerId

    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    //this function shows comment by answer id
    public function GetCommentsByAnswerIdTest()
    {
        $id = 172;
        $data = $this->getCommentsByAnswerId($id);
        vd($data);
    }

    public function getCommentsByAnswerId($id)
    {
        Az::start(__FUNCTION__);
        if ($id !== null) {
            $allComments = ShopQuestion::find()->where([
                'parent_id' => $id,
                'type' => 'comment'
            ])->all();
            if (!empty($allComments))
                return $allComments;
        }
        return false;
    }

    #endregion

    #region getQuestionsByProductId

    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    //this function shows questions by productId
    public function GetQuestionsByProductIdTest()
    {
        $id = 172;
        $data = $this->getQuestionsByProductId($id);
        vd($data);
    }

    public function getQuestionsByProductId($id)
    {
        Az::start(__FUNCTION__);
        if ($id !== null) {
            $questions = ShopQuestion::find()->where([
                'shop_product_id' => $id,
                'type' => 'question',
                'parent_id' => [
                    null
                ]
            ])->all();
            $result = $this->getQuestionItems($questions);
            return $result;
        }
        return false;
    }

    #endregion

    #region countQuestionsById
    /** to count questions by Id depending on type / Javohir */
    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    //
    public function CountQuestionsByIdTest()
    {
        $id = 184;
        $type = true;
        $data = $this->countQuestionsById($id, $type);
        vd($data);
    }

    public function countQuestionsById($id, $type)
    {
        Az::start(__FUNCTION__);
        if ($type === 'product') {
            return ShopQuestion::find()->where([
                'shop_product_id' => $id,
                'type' => 'question',
            ])->count();
        }

        if ($type === 'brand') {
            return ShopQuestion::find()->where([
                'shop_brand_id' => $id,
                'type' => 'question',
            ])->count();
        }

        if ($type === 'company') {
            return ShopQuestion::find()->where([
                'user_company_id' => $id,
                'type' => 'question',
            ])->count();
        }

        return vdd('the question type -' . $type . ' - error (go to Question service)');
    }

    #endregion

    #region countReviewsById
    /** to count reviews by Id depending on type / Javohir */

    public function CountReviewsByIdTest()
    {
        $id = 184;
        $type = "";

        $data = $this->countReviewsById($id, $type);
        vd($data);
    }

    public function countReviewsById($id, $type)
    {
        Az::start(__FUNCTION__);
        if ($type === 'product') {
            return ShopReview::find()->where([
                'shop_product_id' => $id,
                'type' => $type,
            ])->count();
        }

        if ($type === 'brand') {
            return ShopReview::find()->where([
                'shop_brand_id' => $id,
                'type' => $type,
            ])->count();
        }

        if ($type === 'company') {
            return ShopReview::find()->where([
                'user_company_id' => $id,
                'type' => $type,
            ])->count();
        }

        return vdd('the question type -' . $type . ' - error (go to Question service)');
    }

    #endregion

    #region getQuestionsByCompanyId
    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    //this function shows questions by companyId  , if 'type' => 'question'
    public function GetQuestionsByCompanyIdTest()
    {
        $id = 201;
        $data = $this->getQuestionsByCompanyId($id);
        vd($data);
    }

    public function getQuestionsByCompanyId($id)
    {
        Az::start(__FUNCTION__);
        if ($id !== null) {
            $questions = ShopQuestion::find()->where([
                'user_company_id' => $id,
                'type' => 'question',
                'parent_id' => [
                    null
                ]
            ])->all();
            $result = $this->getQuestionItems($questions);
            return $result;
        }
        return false;
    }

    #region

    #region
    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    //'Invalid argument supplied for foreach()'
    public function GetQuestionItemsTest()
    {
        $Items = 201;
        $data = $this->getQuestionItems($Items);
        vd($data);
    }

    public function getQuestionItems($Items)
    {
        Az::start(__FUNCTION__);
        $questionItems = [];
        foreach ($Items as $item) {

            $questionItem = new QuestionItem();
            $questionItem->text = $item->text;
            $questionItem->id = $item->id;
            $questionItem->use = $item->id;
            $questionItem->type = $item->type;
            $questionItem->votes = $item->votes;
            $questionItem->created_at = $item->created_at;

            if (!empty($item->id) && $this->getAnswersByQuestionId($item->id)) {
                $questionItem->items = $this->getQuestionItems($this->getAnswersByQuestionId($item->id));
            }
            if (!empty($item->id) && $this->getCommentsByAnswerId($item->id)) {
                $questionItem->items = $this->getQuestionItems($this->getCommentsByAnswerId($item->id));
            }
            $questionItems[] = $questionItem;
        }

        return $questionItems;
    }


    #region like

    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    // 
    public function LikeTest()
    {
        $id = 1;
        $data = $this->like($id);
        vd($data);
    }

    public function like($id)
    {
        Az::start(__FUNCTION__);
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
    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    //
    public function DislikeTest()
    {
        $id = 201;
        $data = $this->dislike($id);
        vd($data);
    }

    public function dislike($id)
    {
        Az::start(__FUNCTION__);
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

    #region islike
    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    //
    public function IslikeTest()
    {
        $id = 42;
        $data = $this->islike($id);
        vd($data);
    }

    public function islike($id)
    {
        Az::start(__FUNCTION__);
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

    #region isdislike
    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    //
    public function IsDislikeTest()
    {
        $id = 42;
        $data = $this->isDislike($id);
        vd($data);
    }

    public function isDislike($id)
    {
        Az::start(__FUNCTION__);
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

    #region actionVote
    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    //
    public function ActionVoteTest()
    {
        $id = 42;
        $data = $this->actionVote($id);
        vd($data);
    }

    public function actionVote($id)
    {
        Az::start(__FUNCTION__);
        if ($this->sessionGet('vote') == $id) {
            $this->sessionSet('disVote', 'none');
            return 'done';
        }

        $this->sessionSet('vote', $id);

        $question = ShopQuestion::findOne($id);
        $votes = 0;
        if ($question->votes === null) {
            return 0;
        }

        $question->votes = $question->votes + 1;
        $question->save();
        return 'Vote_' . $id;
    }

    #endregion

    #region actionDisVote
    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    //
    public function ActionDisVoteTest()
    {
        $id=42;
        $data = $this->actionDisVote($id);
        vd($data);
    }

    public function actionDisVote($id)
    {
        Az::start(__FUNCTION__);
        if ($this->sessionGet('disVote') == $id) {
            $this->sessionSet('vote', 'none');
            return 0;
        }

        $this->sessionSet('disVote', $id);

        $question = ShopQuestion::findOne($id);
        $votes = 0;
        if ($question->votes === null || $question->votes < 1) {
            return 0;
        }
        $question->votes = $question->votes - 1;
        $question->save();
        return 'DisVote_' . $id;


    }

    #endregion

}
