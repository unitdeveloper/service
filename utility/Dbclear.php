<?php

/**
 *
 *
 * Author:  Asror Zakirov
 *
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\utility;

use zetsoft\dbitem\chat\FriendItem;
use zetsoft\dbitem\chat\MessageItem;
use zetsoft\dbitem\core\NotifyItem;
use zetsoft\models\chat\ChatMessage;
use zetsoft\models\chat\ChatNotify;
use zetsoft\models\shop\ShopOption;
use zetsoft\models\shop\ShopOptionBranch;
use zetsoft\models\user\UserFriend;
use zetsoft\models\user\User;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\system\kernels\ZFrame;

class Dbclear extends ZFrame
{


    public function dbitem()
    {

        /*  $table=ShopOption::find()->all();
          foreach ($table as $row)
          {*/
        $option_type = ShopOption::find()/*->where(['id'=>$row->id])*/ ->all();
        /*if(empty($option_type)){
           vd($row);
           // $row->delete();
        }*/
        foreach ($option_type as $row1) {
            $option_branch = ShopOptionBranch::findOne(['id' => $row1->id]);
            if (empty($option_branch)) {
                vd($row1);
                //$row1->delete();
            }
        }

        // }

    }
}
