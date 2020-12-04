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

namespace zetsoft\service\chat;

use yii\caching\TagDependency;
use yii\helpers\FileHelper;
use zetsoft\dbdata\App\eyuf\RoleData;
use zetsoft\models\App\eyuf\EyufScholar;
use zetsoft\models\chat\ChatMessage;
use zetsoft\models\page\PageAction;
use zetsoft\models\page\PageBlocks;
use zetsoft\models\page\PageBlocksType;
use zetsoft\models\page\PageControl;
use zetsoft\models\page\PageModule;
use zetsoft\models\place\PlaceAdress;
use zetsoft\models\place\PlaceCountry;
use zetsoft\models\user\UserCompany;
use zetsoft\models\user\UserContact;
use zetsoft\models\user\User;
use zetsoft\system\actives\ZActiveRecord;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\kernels\ZView;
use zetsoft\system\module\Models;

class Main extends ZFrame
{
    public function getFriendsList($user_id)
    {
        $friends = $this->friends($user_id);

        $chats = ChatMessage::find()->where(['sender' => $user_id])->orWhere(['receiver' => $user_id])->orderBy(['time' => SORT_DESC])->asArray()->all();
        $return = [];
        $users = [];

        foreach ($friends as $key => $friend) {
            if (!empty($chats)) {
                foreach ($chats as $chat) {
                    if ($chat['sender'] === $user_id)
                        $chatterId = $chat['receiver'];
                    else
                        $chatterId = $chat['sender'];
                    if (ZArrayHelper::isIn($chatterId, $users)) {
                        continue;
                    }
                    if ($chatterId === $friend) {
                        $users[] = $chatterId;
                        $friend = User::findOne($friend);

                        if ($friend !== null) {
                            $return[$key]['id'] = $friend->id;
                            $return[$key]['name'] = $friend->title;
                            $return[$key]['message'] = [
                                'from' => $chat['sender'],
                                'text' => $chat['text']
                            ];
                            $return[$key]['time'] = $chat['time'];
                            $return[$key]['read'] = $chat['read'];
                            $return[$key]['status'] = $friend->status;
                            $return[$key]['last'] = $friend->lastseen;
                            $return[$key]['avatar'] = $friend->photo;
                        }
                    }
                }
            }
        }
        $newReturn = [];
        foreach ($friends as $key => $list) {
            if (!ZArrayHelper::isIn($list, $users)) {
                $friend = User::findOne($list);
                $newReturn[$key]['id'] = $friend->id;
                $newReturn[$key]['name'] = $friend->title;
                $newReturn[$key]['message'] = null;
                $newReturn[$key]['time'] = null;
                $newReturn[$key]['read'] = null;
                $newReturn[$key]['status'] = $friend->status;
                $newReturn[$key]['last'] = $friend->lastseen;
                $newReturn[$key]['avatar'] = $friend->photo;
            }
        }
        $return = ZArrayHelper::merge($return, $newReturn);
        return $return;
    }

    public function getAllList($user_id)
    {
        $friends = User::find()->select('id')->where(['!=','id', $this->userIdentity()->id])->asArray()->all();

        $chats = ChatMessage::find()->where(['sender' => $user_id])->orWhere(['receiver' => $user_id])->orderBy(['time' => SORT_DESC])->asArray()->all();
        $return = [];
        $users = [];

        foreach ($friends as $key => $friend) {
            if (!empty($chats)) {
                foreach ($chats as $chat) {
                    if ($chat['sender'] === $user_id)
                        $chatterId = $chat['receiver'];
                    else
                        $chatterId = $chat['sender'];
                    if (ZArrayHelper::isIn($chatterId, $users)) {
                        continue;
                    }
                    if ($chatterId === $friend) {
                        $users[] = $chatterId;
                        $friend = User::findOne($friend);

                        if ($friend !== null) {
                            $return[$key]['id'] = $friend->id;
                            $return[$key]['name'] = $friend->title;
                            $return[$key]['message'] = [
                                'from' => $chat['sender'],
                                'text' => $chat['text']
                            ];
                            $return[$key]['time'] = $chat['time'];
                            $return[$key]['read'] = $chat['read'];
                            $return[$key]['status'] = $friend->status;
                            $return[$key]['last'] = $friend->lastseen;
                            $return[$key]['avatar'] = $friend->photo;
                        }
                    }
                }
            }
        }
        $newReturn = [];
        foreach ($friends as $key => $list) {
            if (!ZArrayHelper::isIn($list, $users)) {
                $friend = User::findOne($list);
                $newReturn[$key]['id'] = $friend->id;
                $newReturn[$key]['name'] = $friend->title;
                $newReturn[$key]['message'] = null;
                $newReturn[$key]['time'] = null;
                $newReturn[$key]['read'] = null;
                $newReturn[$key]['status'] = $friend->status;
                $newReturn[$key]['last'] = $friend->lastseen;
                $newReturn[$key]['avatar'] = $friend->photo;
            }
        }
        $return = ZArrayHelper::merge($return, $newReturn);
        return $return;
    }
    /**
     *
     * Function  getChatClick
     * get chat list and friend information on click user
     * @param int $user_id
     * @param int $friend_id
     * @return  array
     * @throws \Exception
     */
    public function getChatClick(int $user_id, int $friend_id): array
    {
        $chats = ChatMessage::find()->where(['receiver' => $user_id])->orWhere(['sender' => $user_id])->asArray()->all();
        
        $return = [];
        $return['messages'] = [];
        $return['friend'] = [];
        if (!empty($chats)) {
            foreach ($chats as $key => $chat) {
                if ($chat['receiver'] === $friend_id || $chat['sender'] === $friend_id) {
                    $return['messages'][$key]['chat_id'] = $chat['id'];
                    $return['messages'][$key]['text'] = $chat['text'];
                    $return['messages'][$key]['read'] = $chat['read'];
                    $return['messages'][$key]['time'] = $chat['time'];
                    if ($chat['receiver'] === $friend_id) {
                        $user = \zetsoft\models\user\User::findOne($user_id);
                        $return['messages'][$key]['avatar'] = $user->photo;
                        $return['messages'][$key]['from'] = $user_id;
                    } else {
                        $user = \zetsoft\models\user\User::findOne($friend_id);
                        $return['messages'][$key]['avatar'] = $user->photo;
                        $return['messages'][$key]['from'] = $friend_id;
                    }
                }
            }
        }

        $friend = \zetsoft\models\user\User::findOne($friend_id);
        if ($friend)
            $return['friend'] = [
                'id' => $friend->id,
                'name' => $friend->title,
                'status' => $friend->status,
                'blocked' => $this->getBlocked($user_id, $friend_id),
            ];
        return $return;
    }

    /**
     *
     * Function  getRequestList
     * returns requestList of User
     * @param int $user_id
     * @return  array
     * @throws \Exception
     */
    public function getRequestList(int $user_id): array
    {
        $requests = UserContact::find()->where(['friend' => $user_id])->andWhere(['status' => UserContact::status['await']])->orderBy(['created_at' => SORT_DESC])->asArray()->all();
        $return = [];
        if (!empty($requests)) {
            foreach ($requests as $key => $list) {
                if ($list['person'] !== $user_id)
                    $person = \zetsoft\models\user\User::findOne($list['person']);
                else
                    $person = \zetsoft\models\user\User::findOne($list['friend']);

                if ($person !== null) {
                    $return[$key]['id'] = $person->id;
                    $return[$key]['name'] = $person->title;
                    $return[$key]['status'] = $person->status;
                    $return[$key]['avatar'] = $person->photo;
                }
            }
        }
        return $return;
    }

    /**
     *
     * Function  getUsersList
     * returns all Users
     * @param int $user_id
     * @return  array
     * @throws \Exception
     */
    public function getUsersList(int $user_id): array
    {
        $status[] = UserContact::status['accepted'];
        $status[] = UserContact::status['await'];
        $contacts = zcollect(UserContact::find()->where(['person' => $user_id])->orWhere(['friend' => $user_id])->andWhere(['status' => $status])->asArray()->all());
        $users = User::find()->asArray()->all();
        $return = [];
        foreach ($users as $key => $user) {
            if ($user['id'] === $user_id) {
                continue;
            }
            $filtered = $contacts->filter(function ($value, $key) use ($user) {
                if ($value['person'] === $user['id'] || $value['friend'] === $user['id'])
                    return true;
            });
            $value = $filtered->all();
            $return[$key]['name'] = $user['title'];
            $return[$key]['id'] = $user['id'];
            $return[$key]['role'] = $user['role'];
            $return[$key]['avatar'] = $user['photo'];
            if (!empty($value)) {
                foreach ($value as $item) {
                    $return[$key]['status'] = $item['status'];
                }
            } else {
                $return[$key]['status'] = 'none';
            }
        }
        return $return;
    }

    /**
     *
     * Function  getUserInform
     * generate information about friend
     * @param int $friend_id
     * @param int $user_id
     * @return  array
     */
    public function getUserInform(int $friend_id, int $user_id): array
    {
        $user = User::findOne($friend_id);
        $return = [];

        $return['id'] = $friend_id;
        if ($user !== null) {
            $return['name'] = $user->title;
            $userCompany = UserCompany::findOne($user->user_company_id);
            if ($userCompany !== null) {
                $return['company'] = $userCompany->name;
            }
            $placeAddres = PlaceAdress::findOne($user->place_region_id);
            if ($placeAddres !== null) {
                $placeCountry = PlaceCountry::findOne($placeAddres->place_country_id);
                if ($placeCountry !== null)
                    $return['country'] = $placeCountry->name;
            }
            $return['role'] = $user->role;
            $return['avatar'] = $user->photo;
            $return['blocked'] = $this->getBlocked($friend_id, $user_id);
        }
        return $return;
    }

    public function getFriend($user_id, $friend_id, $status = null)
    {
        $contacts = UserContact::find()->where(['person' => $user_id])->orWhere(['friend' => $user_id])->all();
        $return = null;
        if ($contacts)
            foreach ($contacts as $contact) {
                if (($contact->person === $user_id && $contact->friend === $friend_id) || ($contact->person === $friend_id && $contact->friend === $user_id)) {
                    $return = $contact;
                    break;
                }
            }
        return $return;
    }

    /**
     *
     * Function  blockUser
     * block or unblock user
     * @param int $user_id
     * @param int $friend_id
     * @throws \Exception
     */
    public function blockUser(int $user_id, int $friend_id): void
    {
        $contacts = UserContact::find()->where(['person' => $user_id])->orWhere(['friend' => $user_id])->andWhere(['status' => UserContact::status['accepted']])->all();
        foreach ($contacts as $contact) {
            if (($contact->person === $user_id && $contact->friend === $friend_id) || ($contact->person === $friend_id && $contact->friend === $user_id)) {
                if ($contact->blocked !== 0) {
                    $contact->blocked = 0;
                } else {
                    $contact->blocked = $user_id;
                }
                $contact->save();
            }
        }
    }

    /**
     *
     * Function  saveMessage
     * @param int $user_id
     * @param int $receiver_id
     * @param string $msg
     * @return  bool
     */
    public function saveMessage(int $user_id, int $receiver_id, string $msg): bool
    {
        $model = new ChatMessage();

        $model->sender = $user_id;
        $model->receiver = $receiver_id;
        $model->text = $msg;
        $model->read = false;
        $model->time = date('Y-m-d H:i:s');
        return $model->save();
    }

    /**
     *
     * Function  getBlocked
     * get information about is user blocked or no
     * @param int $user_id
     * @param int $friend_id
     * @return  mixed|null
     * @throws \Exception
     */
    private function getBlocked(int $user_id, int $friend_id)
    {
        $contacts = UserContact::find()->where(['person' => $friend_id])->orWhere(['friend' => $friend_id])->andWhere(['status' => UserContact::status['accepted']])->all();
        $blocked = null;
        if (!$this->emptyOrNullable($contacts)) {
            foreach ($contacts as $contact) {
                if (($contact->person === $user_id && $contact->friend === $friend_id) || ($contact->person === $friend_id && $contact->friend === $user_id)) {
                    $blocked = $contact->blocked;
                }
            }
            if ($blocked === 0)
                $blocked = null;
        }
        return $blocked;
    }


    /**
     *
     * Function  friends
     * return friends of user
     * @param int $user_id
     * @param string $status
     * @return  array
     * @throws \Exception
     */
    private function friends(int $user_id, $status = UserContact::status['accepted']): array
    {
        $contacts = UserContact::find()->where(['person' => $user_id])->orWhere(['friend' => $user_id])->andWhere(['status' => $status])->asArray()->all();
        $friends = [];
        foreach ($contacts as $contact) {
            if ($contact['person'] === $user_id)
                $friend_id = $contact['friend'];
            else
                $friend_id = $contact['person'];
            if (ZArrayHelper::isIn($friend_id, $friends)) {
                continue;
            }
            $friends[] = $friend_id;
        }
        return $friends;
    }
}
