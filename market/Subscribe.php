<?php

/**
 * Author: Sardor Shodmonov
 * Date:    17.05.2020
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 */

namespace zetsoft\service\market;

use Google\ApiCore\OperationResponse;
use yii\caching\TagDependency;
use zetsoft\dbitem\wdg\MenuItem;
use zetsoft\dbitem\shop\ProductItem;
use zetsoft\dbitem\shop\PropertyItem;
use zetsoft\dbitem\data\ALLApp;
use zetsoft\dbitem\data\Config;
use zetsoft\dbitem\data\ConfigDB;
use zetsoft\dbitem\data\Form;
use zetsoft\models\page\PageAction;
use zetsoft\models\shop\ShopBrand;
use zetsoft\models\shop\ShopCategory;
use zetsoft\models\shop\ShopElement;
use zetsoft\models\menu\Menu;
use zetsoft\models\menu\MenuImage;
use zetsoft\models\core\CoreMenuNew;
use zetsoft\models\shop\ShopOption;
use zetsoft\models\shop\ShopOptionType;
use zetsoft\models\shop\ShopProduct;
use zetsoft\models\chat\ChatSubscribe;
use zetsoft\models\user\User;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\helpers\ZUrl;
use zetsoft\system\kernels\ZFrame;
use zetsoft\widgets\former\ZDynaWidget;
use zetsoft\widgets\incores\ZMCheckboxGroupWidget;
use zetsoft\widgets\inputes\ZHCheckboxButtonGroupWidget;
use zetsoft\widgets\navigat\ZGAccordionWidget;
use function PHPUnit\Framework\isInstanceOf;


class Subscribe extends ZFrame
{

    #region init

    public function init()
    {

        parent::init();
    }

    #endregion

    #region getSubscriber

    public function GetAllSubscriberTest()
    {
        $data = $this->getAllSubscriber();
        vd($data);
        //Error!!!!!!
    }

    public function getAllSubscriber(){

       $all_subscriber = ChatSubscribe::findAll();
       return $all_subscriber;
    }

    #endregion

    #region getSubscriberFromId

    public function GetSubscriberFromIdTest()
    {
        $data = $this->getSubscriberFromId();
        vd($data);
    }

    public function getSubscriberFromId($id = null){
        if($id !== null){
            $subscriber = ChatSubscribe::findOne($id);
            return $subscriber;
        }else{
            return 'Not found!';
        }
    }

    #endregion

    #region createSubScriber

    public function CreateSubScriberTest()
    {
        $data = $this->createSubScriber();
        vd($data);
    }

    public function createSubScriber($email = null){

        if($email !== null){
            $new_subscriber = new ChatSubscribe();
            $new_subscriber->subsriber_email = $email;
            $new_subscriber->save();
        }
    }

    #endregion

    #region unsubscribe

    public function UnsubscribeTest()
    {
        $data = $this->unsubscribe();
        vd($data);
    }

    public function unsubscribe($email = null){
        if($email !== null){
            $subscriber = ChatSubscribe::find()->where([
                'subsriber_email'=>$email
            ])->one();
            return $subscriber->delete();
        }
    }

    #endregion
}
