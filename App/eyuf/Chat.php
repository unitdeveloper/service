<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * Date:    11.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\App\eyuf;


use kartik\form\ActiveForm;
use PhpOffice\PhpSpreadsheet\Shared\OLE\PPS\Root;
use Yii;
use zetsoft\models\user\UserBlocked;
use zetsoft\models\place\PlaceCountry;
use zetsoft\models\chat\ChatMessage;
use zetsoft\models\user\User;
use zetsoft\models\App\eyuf\Program;
use zetsoft\models\App\eyuf\EyufScholar;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\widgets\inputes\ZFileInputWidget;
use kartik\widgets\Growl;
use yii\web\Response;

class Chat extends ZFrame
{

    public function test()
    {
      return 2;
    }
    public function getUserPhoto($id)
    {
        $photo = User::findOne($id);
        $url = $photo->userPhoto();
        if (!file_exists($url))
            $url = 'https://upload.wikimedia.org/wikipedia/commons/d/d3/User_Circle.png';
        return $url;
    }


    public function getUserFile($userId, $id)
    {


        $file = ChatMessage::find()
            ->select('file')
            ->where([
                'sender' => $userId,
                'read' => $id
            ])
            ->orWhere([
                'sender' => $id,
                'receiver' => $userId
            ])->all();

    }

    public function Insert($text, $usId)
    {
        $recId = $this->userIdentity()->id;
        $time = Date('h:i');
        $model = new ChatMessage();
        $model->text = $text;
        $model->sender = $recId;
        $model->receiver = $usId;
        $model->time = $time;
        $model->read = false;
        $model->save();

        Yii::$app->getResponse()->redirect('/cores/chat.aspx?userId=' . $usId);
    }

    public function File($file, $usId)
    {

        if (isset($_POST['file'])) {
            $post_name = $_FILES['file']['name'];
            $post_temp = $_FILES['file']['tmp_name'];

            if ($post_name) {
                move_uploaded_file($post_temp, Root . ".exweb/eyuf/upload/user/File/$usId/$post_temp");
            } else {
                die("Failed to upload ");
            }
        }

        $recId = $this->userIdentity()->id;
        $time = Date('h:i');
        $model = new ChatMessage();
        $model->file = $post_name;
        $model->sender = $recId;
        $model->receiver = $usId;
        $model->time = $time;
        $model->read = false;
        $model->save();

        Yii::$app->getResponse()->redirect('/cores/chat.aspx?userId=' . $usId);
    }

    public function updateRead($id, $selfId)
    {
        $update = ChatMessage::find()
            ->where([
                'sender' => $id
            ])
            ->andWhere([
                'receiver' => $selfId
            ])
            ->all();

        foreach ($update as $read) {
            $read->read = true;
            $read->save();
        }


    }

    public function getUserName()
    {
        $userId = $this->httpGet('userId');

        if (isset($userId) && $userId != null) {
            $query = User::find()
                ->select('name')
                ->where([
                    'id' => $userId
                ])
                ->one();

            $currName = $query->name;
            return $currName;
        }
    }

    public function getName()
    {
        $userId = $this->httpGet('userId');
        $queryUser = User::find()
            ->where(['id' => $userId]);
        $user = $queryUser->all();
        foreach ($user as $val) {
            $userName = $val['name'];
        }

        return $userName;
    }

    public function getMessage()
    {
        $userId = $this->httpGet('userId');
        $recId = $this->userIdentity()->id;

        $queryUser = User::find()
            ->where(['id' => $userId]);
        $user = $queryUser->all();
        foreach ($user as $val) {
            $userName = $val['name'];
        }

        $query = ChatMessage::find()
            ->where(['and',
                ['sender' => $userId],
                ['receiver' => $recId]
            ])
            ->orWhere(['and',
                ['sender' => $recId],
                ['receiver' => $userId]
            ])
            ->orderBy(['id' => SORT_ASC]);

        $messages = $query->all();
        return $messages;
    }

    public function meniBloklaganlar($userId)
    {


        $model = UserBlocked::find()
            ->where(['and',
                ['person' => $userId],
                ['blocked' => $this->userIdentity()->id]
            ]); 

        $return = $model->all();
        return $return;


    }

    public function menBloklaganlar($userId)
    {

        $model = UserBlocked::find()
            ->where(['and',
                ['person' => $this->userIdentity()->id],
                ['blocked' => $userId]
            ]);

        $return = $model->all();
        return $return;

    }

    public function getUnblock($userId)
    {


        $model = UserBlocked::find()
            ->where(['and',
                ['person' => $this->userIdentity()->id],
                ['blocked' => $userId]
            ]);

        $return = $model->all();
        return $return;


    }

    public function getSmallMess()
    {

        $userId = $this->httpGet('userId');
        $recId = $this->userIdentity()->id;

        $queryUser = User::find()
            ->where(['id' => $userId]);
        $user = $queryUser->all();
        foreach ($user as $val) {
            $userName = $val['name'];
        }

        $query = ChatMessage::find()
            ->where(['and',
                ['sender' => $userId],
                ['receiver' => $recId]
            ])
            ->orWhere(['and',
                ['sender' => $recId],
                ['receiver' => $userId]
            ])
            ->orderBy(['id' => SORT_ASC]);

        $messages = $query->all();
        return $messages;

    }


}
