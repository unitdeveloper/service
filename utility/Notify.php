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
use zetsoft\models\user\UserFriend;
use zetsoft\models\user\User;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\system\kernels\ZFrame;
use function False\true;

class Notify extends ZFrame
{


    #region Vars

    public $title;
    public $data;
    public $link;
    public $type = ChatNotify::type['success'];


    public $user;

    public $userRole;
    public $userId;
    public $userAll;

    #endregion

    #region Notify

    public function null()
    {
        $this->userId = null;
        $this->userRole = null;
        $this->data = null;
        $this->title = null;
        $this->type = null;
        $this->link = null;
    }

    public function all()
    {

        switch (true) {
            case is_string($this->user):
                $this->userRole = $this->user;
                break;

            case is_int($this->user):

                if ($this->user === 0)
                    $this->userAll = true;
                else
                    $this->userId = $this->user;
                break;
        }

        if (is_array($this->data) || is_object($this->data))
            $text = ZVarDumper::export($this->data);
        else
            $text = $this->data;

        $notify = ChatNotify::find()
            ->where([
                'user_id' => $this->userId,
                'user_all' => $this->userAll,
                'user_role' => $this->userRole,
                'title' => $this->title,
                'link' => $this->link,
                'text' => $text,
                'type' => $this->type
            ])
            ->limit(1)
            ->one();

        if ($notify !== null)
            return false;

        $notify = new ChatNotify();
        $notify->user_id = $this->userId;
        $notify->user_role = $this->userRole;
        $notify->user_all = $this->userAll;
        $notify->title = $this->title;
        $notify->read = false;
        $notify->remove = false;
        $notify->link = $this->link;
        $notify->text = $text;

        $notify->type = $this->type;
        $notify->time = Az::$app->cores->date->dateTime();

        if ($notify->save())
            return true;
        else
            return false;

    }


    #endregion

    #region Types

    public function success($title, $data, $user, $link)
    {

        $this->null();

        $this->user = $user;
        $this->type = ChatNotify::type['success'];
        $this->data = $data;
        $this->link = $link;
        $this->title = $title;

        $this->all();
    }

    public function info($title, $data, $user, $link)
    {

        $this->null();

        $this->user = $user;
        $this->type = ChatNotify::type['info'];
        $this->data = $data;
        $this->link = $link;
        $this->title = $title;

        $this->all();
    }

    public function warning($title, $data, $user, $link)
    {
        $this->null();
        $this->user = $user;
        $this->type = ChatNotify::type['warning'];
        $this->data = $data;
        $this->link = $link;
        $this->title = $title;

        $this->all();
    }

    public function danger($title, $data, $user, $link)
    {
        $this->null();
        $this->user = $user;
        $this->type = ChatNotify::type['danger'];
        $this->data = $data;
        $this->link = $link;
        $this->title = $title;

        $this->all();
    }

    #endregion

    #region Widget

    public function notifyQuery()
    {

        return ChatNotify::find()
            ->where(['or',
                [
                    'user_id' => $this->userIdentity()->id,
                ],
                [
                    'user_role' => $this->userRole(),
                ],
                [
                    'user_all' => true,
                ]
            ])
            ->orderBy(['time' => SORT_DESC]);
    }


    public function notifies()
    {
        $notifies = $this->notifyQuery()
            ->limit(20)
            ->all();
            
        $userIDs = ZArrayHelper::getColumn($notifies, 'created_by');

        $users = User::find()
            ->where([
                'id' => $userIDs
            ])
            ->indexBy('id')
            ->all();

        $data = [];

        /** @var ChatNotify[] $notifies */
        foreach ($notifies as $key => $notify) {

            /** @var User $user */

            $user = ZArrayHelper::getValue($users, $notify->created_by);

            $item = new NotifyItem();
            $item->time = $notify->time;
            $item->text = $notify->text;
            $item->title = $notify->title;
            $item->type = $notify->type;
            $item->link = $notify->link;
            $item->id = $notify->id;
            $item->read = $notify->read;

            if ($user !== null)
                $item->photo = $user->userPhoto();
            else
                $item->photo = Az::getAlias('/render/theme/ZCarolinaWidget/asset/img/user-photo.jpg');

            $data[] = $item;
        }


        return $data;
    }

    public function notifiesRead()
    {
        $notifies = $this->notifyQuery()
            ->all();


        /** @var ChatNotify[] $notifies */
        foreach ($notifies as $key => $notify) {
            $notify->read = true;
            $notify->save();

        }

    }


    public function notifiesBadge()
    {

        $notifies = $this->notifyQuery()
            ->andWhere([
                'read' => false,
            ])
            ->count();

        return $notifies;
    }

    #endregion


    #region Friend

    public function getFriendItem()
    {

        $requests = UserFriend::find()
            ->where([
                'friend' => $this->userIdentity()->id,
                'remove' => false,
                'status' => 0,
            ])
            ->limit(20)
            ->all();

        $users = User::find()
            ->indexBy('id')
            ->all();


        $data = [];

        foreach ($requests as $request) {

            /** @var User $user */


            $user = ZArrayHelper::getValue($users, $request->friend);
            $userNames = ZArrayHelper::getValue($users, $request->person);

            if ($user !== null) {
                $img = $user->userPhoto();
                $userName = $userNames->name;
                $item = new FriendItem();
                $item->person = $userName;
                $item->id = $request->id;
                $item->friend = $request->friend;
                $item->status = $request->status;
                $item->time = $request->time;
                $item->photo = $img;
                $data[] = $item;
            }

        }
        return $data;


    }

    public function getFriendBadge()
    {
        $badgeFriend = UserFriend::find()
            ->where([
                'friend' => $this->userIdentity()->id,
                'remove' => false,
                'status' => 0,
            ])
            ->count();

        return $badgeFriend;
    }

    #endregion

    #region Message

    public function getMessageItem()
    {

        $data = [];
        $messages = [];

        if ($this->userIdentity())
            $messages = ChatMessage::find()
                ->where([
                    'receiver' => $this->userIdentity()->id,
                    'read' => false,
                ])
                ->asArray()
                ->all();


        $users = User::find()
            ->indexBy('id')
            ->asArray()
            ->all();


        if (isset($_POST['id'])) {
            $customer = ChatMessage::findOne($_POST['id']);
            $customer->read = true;
            $customer->save();
        }
        foreach ($messages as $message) {

            /** @var User $user */

            $user = ($users !== null) ? ZArrayHelper::getValue($users, $message->sender) : '';

            if ($message->file !== null) {
                $img = User::findOne($user->id)->userPhoto();
            } else {
                $img = "https://upload.wikimedia.org/wikipedia/commons/d/d3/User_Circle.png";
            }

            if ($user === null)
                Az::error($message->receiver, 'User Not Exists');

            if ($user) {
                $item = new MessageItem();
                $item->name = $user->name;
                $item->user_id = $user->id;
                $item->photo = $img;
                $item->time = $message->created_at;
                $item->text = $message->text;
                $Item->read = $message->read;
                $item->id = $message->id;

                $data[] = $item;
            }

        }
        return $data;

    }

    #endregion

}
