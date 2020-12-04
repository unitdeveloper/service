<?php

/**
 *
 * Author:  Xolmat Ravshanov
 *
 */

namespace zetsoft\service\calls;




use zetsoft\dbitem\pbx\PbxContactManagerEntryNumbersItem;
use zetsoft\dbitem\pbx\PbxCxpanelUsersItem;
use zetsoft\dbitem\pbx\PbxFindmefollow;
use zetsoft\dbitem\pbx\PbxKvstoreItem;
use zetsoft\dbitem\pbx\PbxKvstoreFreePbxModulesPagingItem;
use zetsoft\dbitem\pbx\PbxSipItem;
use zetsoft\dbitem\pbx\PbxUserItem;
use zetsoft\dbitem\pbx\PbxUsermanUsersItem;
use zetsoft\models\App\eyuf\db3\ContactmanagerEntryNumbers;
use zetsoft\models\App\eyuf\db3\CxpanelUsers;
use zetsoft\models\App\eyuf\db3\Devices;
use zetsoft\models\App\eyuf\db3\FaxUsers;
use zetsoft\models\App\eyuf\db3\Findmefollow;
use zetsoft\models\App\eyuf\db3\KvstoreFreePBXModulesPaging;
use zetsoft\models\App\eyuf\db3\Sip;
use zetsoft\models\App\eyuf\db3\Users;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;


class FreepbxExtention extends ZFrame
{
    #region Vars
    public $pbxuseritem;
    public $pbxdeviceitem;
    public $pbxsipitem;
    public $pbxaccountitem;
    public $user_id;
    public $user= '504';
    #endregion

    #region Cores
    public function init()
    {
        parent::init();

    }
    #endregion

    #region Test
    public function test()
    {
       /// $this->userExample();
      // $this->findmeFollowExample();
      $this->sipExample();
    }

    #endregion

    /**
     *
     * Function  kvstore
     * @param $kvstoreitem
     * @param $id number of extention Null ($user_id)
     */

    public function kvstoreExample(){
        $item = new PbxKvstoreFreePbxModulesPagingItem();
        $item->id = '503';
        $this->kvstore($item);
    }

    public function kvstore($kvstoreitem)
    {
        $kvstore = new KvstoreFreePBXModulesPaging();
        $kvstore->key = $kvstoreitem->key;
        $kvstore->val = $kvstoreitem->val;
        $kvstore->type = $kvstoreitem->type;
        $kvstore->id = $kvstoreitem->id;
        $kvstore->save();

    }

    public function contactExample(){
        $item = new PbxContactManagerEntryNumbersItem();
        $item->number = '22';
        $item->extension = '222';
        $item->countrycode = '';
        $item->nationalnumber = '';
        $item->countrycode = '';
        $item->regioncode = '';
        $item->locale = '';
        $item->stripped = '222';
        $item->type = 'internal';
        $item->flags = '';
        $item->E164 = '';
        $item->possibleshort = '';
        $this->contactManager($item);
    }

    public function contactManager($contactManagerEntryNumbersItem)
    {
        $contactManager = new ContactmanagerEntryNumbers();
        $contactManager->entryid = $contactManagerEntryNumbersItem->entryid;
        $contactManager->number = $contactManagerEntryNumbersItem->number;
        $contactManager->extension = $contactManagerEntryNumbersItem->extension;
        $contactManager->countrycode = $contactManagerEntryNumbersItem->countrycode;
        $contactManager->nationalnumber = $contactManagerEntryNumbersItem->nationalnumber;
        $contactManager->regioncode = $contactManagerEntryNumbersItem->regioncode;
        $contactManager->locale = $contactManagerEntryNumbersItem->locale;
        $contactManager->stripped = $contactManagerEntryNumbersItem->stripped;
        $contactManager->type = $contactManagerEntryNumbersItem->type;
        $contactManager->flags = $contactManagerEntryNumbersItem->flags;
        $contactManager->E164 = $contactManagerEntryNumbersItem->E164;
        $contactManager->possibleshort = $contactManagerEntryNumbersItem->possibleshort;
        $contactManager->save();
    }

    /**
     *
     * Function  cxpanelUsers
     * @param $cxpanelUsersItem
     * @param $user_id number of extention ($user_id)
     */

    public function cxpanelUsersExample(){
            $user =new PbxCxpanelUsersItem();
            $user->user_id = '503';
            $user->display_name= '503';
            $user->peer = 'PJSIP/503';
            $user->parent_user_id = '503';
            $this->cxpanelUsers($user);
    }


    public function cxpanelUsers($cxpanelUsersItem)
    {
        $cxpanelUsers = new CxpanelUsers();
       /* $cxpanelUsers->cxpanel_user_id = $cxpanelUsersItem->cxpanel_user_id;*/
        $cxpanelUsers->user_id = $cxpanelUsersItem->user_id;
        $cxpanelUsers->display_name = $cxpanelUsersItem->display_name;
        $cxpanelUsers->peer = $cxpanelUsersItem->peer;
        $cxpanelUsers->add_extension = $cxpanelUsersItem->add_extension;
        $cxpanelUsers->full = $cxpanelUsersItem->full;
        $cxpanelUsers->add_user = $cxpanelUsersItem->add_user;
        $cxpanelUsers->hashed_password = $cxpanelUsersItem->hashed_password;
        $cxpanelUsers->initial_password = $cxpanelUsersItem->initial_password;
        $cxpanelUsers->auto_answer = $cxpanelUsersItem->auto_answer;
        $cxpanelUsers->parent_user_id = $cxpanelUsersItem->parent_user_id;
        $cxpanelUsers->password_dirty = $cxpanelUsersItem->password_dirty;
        $cxpanelUsers->save();
    }

    public function findMeExample(){
        $find = new Findmefollow();



    }

    public function findmeFollowExample(){
        $find = new PbxFindmefollow();
        $find->grpnum = $this->user;
        $find->grplist = $this->user;
        $find->postdest = 'ext-local,'.$this->user.',dest';
        $this->findmeFollow($find);
    }

    public function findmeFollow($findmeFollowItem)
    {
        $findmeFollow = new Findmefollow();
        $findmeFollow->grpnum = $findmeFollowItem->grpnum;
        $findmeFollow->strategy = $findmeFollowItem->strategy;
        $findmeFollow->grptime = $findmeFollowItem->grptime;
        $findmeFollow->strategy = $findmeFollowItem->strategy;
        $findmeFollow->grppre = $findmeFollowItem->grppre;
        $findmeFollow->grplist = $findmeFollowItem->grplist;
        $findmeFollow->annmsg_id = $findmeFollowItem->annmsg_id;
        $findmeFollow->postdest = $findmeFollowItem->postdest;
        $findmeFollow->dring = $findmeFollowItem->dring;
        $findmeFollow->rvolume = $findmeFollowItem->rvolume;
        $findmeFollow->remotealert_id = $findmeFollowItem->reomtealert_id;
        $findmeFollow->needsconf = $findmeFollowItem->needsconf;
        $findmeFollow->toolate_id = $findmeFollowItem->toolate_id;
        $findmeFollow->pre_ring = $findmeFollowItem->pre_ring;
        $findmeFollow->ringing = $findmeFollowItem->ringing;
        $findmeFollow->calendar_enable = $findmeFollowItem->calendar_enable;
        $findmeFollow->calendar_id = $findmeFollowItem->calendar_id;
        $findmeFollow->calendar_group_id = $findmeFollowItem->calendar_group_id;
        $findmeFollow->calendar_match = $findmeFollowItem->calendar_match;
        $findmeFollow->save();
    }

    public function faxUsers($faxUsersItem)
    {
        $faxUsers = new FaxUsers();
        $faxUsers->user = $faxUsersItem->user;
        $faxUsers->faxenabled = $faxUsersItem->faxenabled;
        $faxUsers->faxemail = $faxUsersItem->faxemail;
        $faxUsers->faxattachformat = $faxUsersItem->faxattachformat;
        $faxUsers->save();
    }

    public function userExample(){
        $user = new PbxUserItem();
        $user->extention = $this->user;
        $user->password  =  'password';
        $user->name   = $this->user;
        $this->user($user);
    }



    public function user($pbxuseritem)
    {
        $user = new Users();
        $user->extension = $pbxuseritem->extention;
        $user->password = $pbxuseritem->password;
        $user->name = $pbxuseritem->name;
        $user->voicemail = $pbxuseritem->voicemail;
        $user->ringtimer = $pbxuseritem->ringtimer;
        $user->noanswer = $pbxuseritem->noanswer;
        $user->recording = $pbxuseritem->recording;
        $user->outboundcid = $pbxuseritem->outboundcid;
        $user->sipname = $pbxuseritem->sipname;
        $user->noanswer_cid = $pbxuseritem->noanswer_cid;
        $user->busy_cid = $pbxuseritem->busy_cid;
        $user->chanunavail_cid = $pbxuseritem->chanunavail_cid;
        $user->noanswer_dest = $pbxuseritem->noanswer_dest;
        $user->busy_dest = $pbxuseritem->busy_dest;
        $user->chanunavail_dest = $pbxuseritem->chanunavail_dest;
        $user->mohclass = $pbxuseritem->mohclass;
        $user->save();


    }

    public function devices($pbxdeviceitem)
    {
        $device = new Devices();
        $device->id = $pbxdeviceitem->id;
        $device->tech = $pbxdeviceitem->tech;
        $device->dial = $pbxdeviceitem->dial;
        $device->devicetype = $pbxdeviceitem->devicetype;
        $device->user = $pbxdeviceitem->user;
        $device->description = $pbxdeviceitem->description;
        $device->emergency_cid = $pbxdeviceitem->emergency_cid;
        $device->save();
    }

    public function sipExample()
    {
        $sipExample = new PbxSipItem();
        $sipExample->account = $this->user;
        $sipExample->id = $this->user;

        $this->sip($sipExample);
    }


    public function sip($pbxsipitem)
    {
        $sip = new Sip();
        $sip->id = $pbxsipitem->id;
        $sip->keyword = $pbxsipitem->secret;
        $sip->data = $pbxsipitem->secretData;
        $sip->flags = $pbxsipitem->secretFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $pbxsipitem->id;
        $sip->keyword = $pbxsipitem->dtmfmode;
        $sip->data = $pbxsipitem->dtmfmodeData;
        $sip->flags = $pbxsipitem->dtmfmodeFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $pbxsipitem->id;
        $sip->keyword = $pbxsipitem->canreinvite;
        $sip->data = $pbxsipitem->canreinviteData;
        $sip->flags = $pbxsipitem->canreinviteFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $pbxsipitem->id;
        $sip->keyword = $pbxsipitem->context;
        $sip->data = $pbxsipitem->contextData;
        $sip->flags = $pbxsipitem->contextFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $pbxsipitem->id;
        $sip->keyword = $pbxsipitem->host;
        $sip->data = $pbxsipitem->hostData;
        $sip->flags = $pbxsipitem->hostFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $pbxsipitem->id;
        $sip->keyword = $pbxsipitem->trustrpid;
        $sip->data = $pbxsipitem->trustrpidData;
        $sip->flags = $pbxsipitem->trustrpidFlags;
        $sip->save();


        $sip = new Sip();
        $sip->id = $pbxsipitem->id;
        $sip->keyword = $pbxsipitem->sendrpid;
        $sip->data = $pbxsipitem->sendrpidData;
        $sip->flags = $pbxsipitem->sendrpidFlags;
        $sip->save();


        $sip = new Sip();
        $sip->id = $pbxsipitem->id;
        $sip->keyword = $pbxsipitem->type;
        $sip->data = $pbxsipitem->typeData;
        $sip->flags = $pbxsipitem->typeFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $pbxsipitem->id;
        $sip->keyword = $pbxsipitem->nat;
        $sip->data = $pbxsipitem->natData;
        $sip->flags = $pbxsipitem->natFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $pbxsipitem->id;
        $sip->keyword = $pbxsipitem->port;
        $sip->data = $pbxsipitem->portData;
        $sip->flags = $pbxsipitem->portFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $pbxsipitem->id;
        $sip->keyword = $pbxsipitem->qualify;
        $sip->data = $pbxsipitem->qualifyData;
        $sip->flags = $pbxsipitem->qualifyFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $pbxsipitem->id;
        $sip->keyword = $pbxsipitem->qualifyfreq;
        $sip->data = $pbxsipitem->qualifyfreqData;
        $sip->flags = $pbxsipitem->qualifyfreqFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $pbxsipitem->id;
        $sip->keyword = $pbxsipitem->udp;
        $sip->data = $pbxsipitem->udpData;
        $sip->flags = $pbxsipitem->udpFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $pbxsipitem->id;
        $sip->keyword = $pbxsipitem->avpf;
        $sip->data = $pbxsipitem->avpfData;
        $sip->flags = $pbxsipitem->avpfFlags;
        $sip->save();


        $sip = new Sip();
        $sip->id = $pbxsipitem->id;
        $sip->keyword = $pbxsipitem->icesupport;
        $sip->data = $pbxsipitem->icesupportData;
        $sip->flags = $pbxsipitem->icesupportFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $pbxsipitem->id;
        $sip->keyword = $pbxsipitem->encryption;
        $sip->data = $pbxsipitem->encryptionData;
        $sip->flags = $pbxsipitem->encryptionFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $pbxsipitem->id;
        $sip->keyword = $pbxsipitem->callgroup;
        $sip->data = $pbxsipitem->callgroupData;
        $sip->flags = $pbxsipitem->callgroupFlags;
        $sip->save();


        $sip = new Sip();
        $sip->id = $pbxsipitem->id;
        $sip->keyword = $pbxsipitem->pickupgroup;
        $sip->data = $pbxsipitem->pickupgroupData;
        $sip->flags = $pbxsipitem->pickupgroupFlags;
        $sip->save();


        $sip = new Sip();
        $sip->id = $pbxsipitem->id;
        $sip->keyword = $pbxsipitem->disallow;
        $sip->data = $pbxsipitem->disallowData;
        $sip->flags = $pbxsipitem->disallowFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $pbxsipitem->id;
        $sip->keyword = $pbxsipitem->allow;
        $sip->data = $pbxsipitem->allowData;
        $sip->flags = $pbxsipitem->allowFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $pbxsipitem->id;
        $sip->keyword = $pbxsipitem->dial;
        $sip->data = $pbxsipitem->dialData;
        $sip->flags = $pbxsipitem->dialFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $pbxsipitem->id;
        $sip->keyword = $pbxsipitem->mailbox;
        $sip->data = $pbxsipitem->mailboxData;
        $sip->flags = $pbxsipitem->mailboxFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $pbxsipitem->id;
        $sip->keyword = $pbxsipitem->deny;
        $sip->data = $pbxsipitem->denyData;
        $sip->flags = $pbxsipitem->denyFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $pbxsipitem->id;
        $sip->keyword = $pbxsipitem->permit;
        $sip->data = $pbxsipitem->permitData;
        $sip->flags = $pbxsipitem->permitFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $pbxsipitem->id;
        $sip->keyword = $pbxsipitem->account;
        $sip->data = $pbxsipitem->accountData;
        $sip->flags = $pbxsipitem->accountFlags;
        $sip->save();

        $sip = new Sip();
        $pbxsipitem->calleridData = "device <$pbxsipitem->id>";
        $sip->id = $pbxsipitem->id;
        $sip->keyword = $pbxsipitem->callerid;
        $sip->data = $pbxsipitem->calleridData;
        $sip->flags = $pbxsipitem->calleridFlags;
        $sip->save();
    }




    public function createCxUser()
    {
        $pbxaccountitem = $this->pbxaccountitem;
        $user = new CxpanelUsers();
        $user->user_id = $pbxaccountitem->user_id;
        $user->display_name = $pbxaccountitem->display_name;
        $user->peer = $pbxaccountitem->peer . $pbxaccountitem->user_id;
        $user->add_extension = $pbxaccountitem->add_extension;
        $user->full = $pbxaccountitem->full;
        $user->add_user = $pbxaccountitem->add_user;
        $user->hashed_password = $pbxaccountitem->hashed_password;
        $user->initial_password = $pbxaccountitem->initial_password;
        $user->auto_answer = $pbxaccountitem->auto_answer;
        $user->parent_user_id = $pbxaccountitem->parent_user_id;
        $user->password_dirty = $pbxaccountitem->password_dirty;
        $user->save();

    }


    public function userExtention()
    {

        
        Az::$app->calls->freepbxUser->usermanUser();
        Az::$app->calls->freepbxUser->usermanSetting();


        $this->user();
        $this->devices();
        $this->sip();
        $this->contactManager();
        $this->faxUsers();
        $this->kvstore();
        $this->findmeFollow();
        $this->cxpanelUsers();
    }


}
