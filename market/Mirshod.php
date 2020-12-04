<?php

/**
 * Author: Mirshod Ibotov
 */

namespace zetsoft\service\market;

use zetsoft\models\place\PlaceAdress;
use zetsoft\models\test\TestMirshod;
use zetsoft\models\user\UserCompany;
use zetsoft\models\place\PlaceCountry;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\place\PlaceRegion;
use zetsoft\models\user\User;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;


class Mirshod extends ZFrame
{
      public function init()
      {
          parent::init();
      }
     //1ta id ni olib info qaytaradigan  and
     //bir nechta id ni olib bir nechta qaytaradigan  and
     //hammasini olib qaytaradigan and
     //bittasini olib o'zgartiradigan
     //bittasini o'chiradigan
     //bitta tabledan id va amaillarni olib bersin



     public function reninfo($id){
      $rent = TestMirshod::findOne($id);
       $rent->email = 'najim.jk@gmail.com';
       $rent->save();
      return $rent;
     }



    /**
     *
     * Function  GetMirshodById
     * @param $id
     * @return  TestMirshod|null
     */
        public function GetMirshodById($id){
          $mirshod = TestMirshod::findOne($id);
          return $mirshod;
        }
        public function GetMirshodByIds($ids){
        $mirshods = TestMirshod::findAll($ids);
        return $mirshods;
        }

    /**
     *
     * Function  GetEmailsAndIds
     * @return  array
     * @throws \Exception
     */
        public function GetEmailsAndIds(){

         $najim = [];
         $getIdsEmails = TestMirshod::find()->all();
         foreach ($getIdsEmails as $item){

           $najim[$item->id]= $item->email;
           }
           return $najim;

        }

    /**
     *
     * Function  GetAll
     * @return  array|\zetsoft\system\actives\ZActiveQuery[]
     * @throws \Exception
     */
         public function GetAll(){
         $allinfo = TestMirshod::find()->all();
         return $allinfo;

         }

    /**
     *
     * Function  queryWhere
     * @return  array|\zetsoft\system\actives\ZActiveQuery[]
     * @throws \Exception
     */
         public function queryWhere(){
         $finder = TestMirshod::find()->where([
           'id'=>35161,
           'email'=>'ms.asjkdns@gmail.com',
         ])->all();
         return $finder;
         }


    /**
     *
     * Function  update
     * @param $id
     */
                   public function update($id){
                   $updater = TestMirshod::findOne($id);
                    if ($updater !==null){
                    $updater->email = 'newamail@gmail.com';
                    }

                   }
}




