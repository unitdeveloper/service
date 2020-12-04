<?php

/**
 *
 * Author:  Xolmat Ravshanov
 *
 */

namespace zetsoft\service\calls;

use zetsoft\dbitem\pbx\PbxKvstoreContactManagerItem;
use zetsoft\dbitem\pbx\PbxFaxUsersItem;
use zetsoft\dbitem\pbx\PbxKvstoreFreePbxModulesPagingItem;
use zetsoft\dbitem\pbx\PbxUsermanGroupsItem;
use zetsoft\dbitem\pbx\PbxUsermanUsersSetting;
use zetsoft\dbitem\pbx\PbxZuluInteractionsContactsItem;
use zetsoft\dbitem\pbx\PbxUsermanUsersItem;
use zetsoft\models\App\eyuf\db3\CxpanelUsers;
use zetsoft\models\App\eyuf\db3\Devices;
use zetsoft\models\App\eyuf\db3\FaxUsers;
use zetsoft\models\App\eyuf\db3\KvstoreFreePBXModulesContactmanager;
use zetsoft\models\App\eyuf\db3\Sip;
use zetsoft\models\App\eyuf\db3\UsermanGroups;
use zetsoft\models\App\eyuf\db3\UsermanUsers;
use zetsoft\models\App\eyuf\db3\UsermanUsersSettings;
use zetsoft\models\App\eyuf\db3\Users;
use zetsoft\models\App\eyuf\db3\ZuluInteractionsContacts;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;


class FreepbxUser extends ZFrame
{
    #region Vars
    public  $pbxuseritem;
    public  $pbxdeviceitem;
    public  $pbxsipitem;
    public  $pbxaccountitem;
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

      $this->usermanUserExample();


    }
    #endregion


    public function userExample()
    {

        /*
        INSERT INTO users (
            extension, password, name, voicemail, ringtimer, noanswer, recording,
            outboundcid, sipname, noanswer_cid, busy_cid, chanunavail_cid,
            noanswer_dest, busy_dest, chanunavail_dest, mohclass, auditor_exttype
        )

         VALUES (
             '12345',      extension
             '',      password
             'Test'     name
             , 'novm'     voicemail
             , '0',     ringtimer
             '',     noanswer
             '',     recording
             '',     outboundcid
             'test',     sipname
             '',      noanswer_cid
             '',     busy_cid
             '',     chanunavail_cid
             '',      noanswer_dest
             '',      busy_dest
             '',      chanunavail_dest
             'default',     mohclass
             99     auditor_exttype
         );

        */

    }

    public function deviceExample()
    {

        /*
            INSERT INTO devices (
                id, tech, dial, devicetype, user, description, emergency_cid
            )

    VALUES(
                        '12345', id
                        'sip',tech
                        'SIP/12345', dial
                        'fixed',devicetype
                        '12345', user
                        'Test',description
                        '', emergency_cid
                  )

                */

    }


    public function sipExample()
    {
        /*
           INSERT INTO sip (
               id, keyword, data, flags
           )

            VALUES

       ('12345', 'secret', 'supersecret', 2),
       ('12345', 'dtmfmode', 'rfc2833', 3),
       ('12345', 'canreinvite', 'no', 4),
       ('12345', 'context', 'from-internal', 5),
       ('12345', 'host', 'dynamic', 6),
       ('12345', 'trustrpid', 'yes', 7),
       ('12345', 'sendrpid', 'yes', 8),
       ('12345', 'type', 'friend', 9),


       ('12345', 'nat', 'yes', 10),
       ('12345', 'port', '5060', 11),
       ('12345', 'qualify', 'yes', 12),
       ('12345', 'qualifyfreq', '60', 13),
       ('12345', 'transport', 'udp', 14),
       ('12345', 'avpf', 'no', 15),
       ('12345', 'icesupport', 'no', 16),
       ('12345', 'encryption', 'yes', 17),
       ('12345', 'callgroup', '', 18),
       ('12345', 'pickupgroup', '', 19),
       ('12345', 'disallow', '', 20),
       ('12345', 'allow', '', 21),
       ('12345', 'dial', 'SIP/12345', 22),
       ('12345', 'mailbox', '12345@device', 23),
       ('12345', 'deny', '0.0.0.0/0.0.0.0', 24),
       ('12345', 'permit', '0.0.0.0/0.0.0.0', 25),
       ('12345', 'account', '12345', 26),
       ('12345', 'callerid', 'device <12345>', 27)
 */

    }

    public function user($pbxUserItem)
    {
        $user = new Users();
        $user->extension = $pbxUserItem->extension;
        $user->password = $pbxUserItem->password;
        $user->name = $pbxUserItem->name;
        $user->voicemail = $pbxUserItem->voicemail;
        $user->ringtimer = $pbxUserItem->ringtimer;
        $user->noanswer = $pbxUserItem->noanswer;
        $user->recording = $pbxUserItem->recording;
        $user->outboundcid = $pbxUserItem->outboundcid;
        $user->sipname = $pbxUserItem->sipname;
        $user->noanswer_cid = $pbxUserItem->noanswer_cid;
        $user->busy_cid = $pbxUserItem->busy_cid;
        $user->chanunavail_cid = $pbxUserItem->chanunavail_cid;
        $user->noanswer_dest = $pbxUserItem->noanswer_dest;
        $user->busy_dest = $pbxUserItem->busy_dest;
        $user->chanunavail_dest = $pbxUserItem->chanunavail_dest;
        $user->mohclass = $pbxUserItem->mohclass;
        $user->save();
    }
                                        
    public function usermanUserExample(){
        $item = new PbxUsermanUsersItem();
        $item->password = Az::$app->calls->hash->HashPassword('1234');
        $item->username = '516';
        $item->fname = '516';
        $item->lname = '516';
        $item->displayname = '516';

        $this->usermanUser($item);
    }

    public function usermanUser($pbxUsermanUsersItem)
    {
        $usermanUser = new UsermanUsers();
        $usermanUser->username = $pbxUsermanUsersItem->username;
        $usermanUser->password = $pbxUsermanUsersItem->password;
        $usermanUser->fname = $pbxUsermanUsersItem->fname;
        $usermanUser->lname = $pbxUsermanUsersItem->lname;
        $usermanUser->description = $pbxUsermanUsersItem->description;
        $usermanUser->save();
    }

    public function usermanGroupsExample(){
        $item = new PbxUsermanGroupsItem();
        $item->groupname = 'UCP';
        $item->description = 'With UCP';
        $this->usermanGroup($item);
    }

    public function usermanGroup($pbxusermanusersitem)
    {
        $usermanGroup = new UsermanGroups();
        $usermanGroup->groupname = $pbxusermanusersitem->groupname;
        $usermanGroup->description = $pbxusermanusersitem->description;
        $usermanGroup->save();
    }

    public function usermanUsersSettingExample(){
        $item = new PbxUsermanUsersSetting();
        $item->uid = '60';

        $item->AssignedModule = 'global';
        $item->AssignedKey = 'assigned';
        $item->AssignedVal = 0x5B5D;
        $item->AssignedType = 'json-arr';

        $item->DirtyModule = 'cxpanel';
        $item->DirtyKey = 'password_dirty';
        $item->DirtyVal = 0x31;
        $item->DirtyType = 'NULL';

        $item->HighModule = 'cxpanel';
        $item->HighKey = 'pbx_high';
        $item->HighVal = '';
        $item->HighType = 'NULL';

        $item->AdminModule = 'global';
        $item->AdminKey = 'pbx_admin';
        $item->AdminVal = 0x30;
        $item->AdminType = 'NULL';

        $item->LandingModule = 'global';
        $item->LandingKey = 'pbx_landing';
        $item->LandingVal = 0x696E646578;
        $item->LandingType = 'NULL';

        $item->LoginModule = 'global';
        $item->LoginKey = 'pbx_login';
        $item->LoginVal = 0x30;
        $item->LoginType = 'NULL';

        $item->LowModule = 'global';
        $item->LowKey = 'pbx_low';
        $item->LowVal = '';
        $item->LowType = 'NULL';

        $item->MenuorderModule = 'global';
        $item->MenuorderKey = 'menuorder';
        $item->MenuorderVal = 0x5B22636F6E666572656E636573222C226461796E69676874222C22706D73222C22717565756573222C227175657565736167656E74222C22766F6963656D61696C222C22766F6963656D61696C7472616E73666572222C2274696D65636F6E646974696F6E73222C2270726573656E63657374617465222C22646F6E6F7464697374757262222C2266696E646D65666F6C6C6F77222C22636F6E746163746D616E61676572222C2263616C6C666F7277617264222C227061726B696E67222C22656E64706F696E74225D;
        $item->MenuorderType = 'json-arr';

        $item->EnableModule = 'global';
        $item->EnableKey = 'enable';
        $item->EnableVal = 0x6E6F;
        $item->EnableType = 'NULL';

        $item->FmrModule = 'global';
        $item->FmrKey = 'fmr';
        $item->FmrVal = 0x64697361626C65;
        $item->FmrType = 'NULL';

        $item->FlushPageModule = 'global';
        $item->FlushPageKey = 'flushPage';
        $item->FlushPageVal = 0x31;
        $item->FlushPageType = 'NULL';

        $this->usermanSetting($item);
    }

    public function usermanSetting($pbxUsermanSetting)
    {
        $usermansetting = new UsermanUsersSettings();
        $usermansetting->uid = $pbxUsermanSetting->uid;
        $usermansetting->module = $pbxUsermanSetting->AssignedModule;
        $usermansetting->key = $pbxUsermanSetting->AssignedKey;
        $usermansetting->val = $pbxUsermanSetting->AssignedVal;
        $usermansetting->type = $pbxUsermanSetting->AssignedType;
        $usermansetting->save();

        $usermansetting = new UsermanUsersSettings();
        $usermansetting->uid = $pbxUsermanSetting->uid;
        $usermansetting->module = $pbxUsermanSetting->DirtyModule;
        $usermansetting->key = $pbxUsermanSetting->DirtyKey;
        $usermansetting->val = $pbxUsermanSetting->DirtyVal;
        $usermansetting->type = $pbxUsermanSetting->DirtyType;
        $usermansetting->save();

        $usermansetting = new UsermanUsersSettings();
        $usermansetting->uid = $pbxUsermanSetting->uid;
        $usermansetting->module = $pbxUsermanSetting->HighModule;
        $usermansetting->key = $pbxUsermanSetting->HighKey;
        $usermansetting->val = $pbxUsermanSetting->HighVal;
        $usermansetting->type = $pbxUsermanSetting->HighType;
        $usermansetting->save();

        $usermansetting = new UsermanUsersSettings();
        $usermansetting->uid = $pbxUsermanSetting->uid;
        $usermansetting->module = $pbxUsermanSetting->AdminModule;
        $usermansetting->key = $pbxUsermanSetting->AdminKey;
        $usermansetting->val = $pbxUsermanSetting->AdminVal;
        $usermansetting->type = $pbxUsermanSetting->AdminType;
        $usermansetting->save();

        $usermansetting = new UsermanUsersSettings();
        $usermansetting->uid = $pbxUsermanSetting->uid;
        $usermansetting->module = $pbxUsermanSetting->LandingModule;
        $usermansetting->key = $pbxUsermanSetting->LandingKey;
        $usermansetting->val = $pbxUsermanSetting->LandingVal;
        $usermansetting->type = $pbxUsermanSetting->LandingType;
        $usermansetting->save();

        $usermansetting = new UsermanUsersSettings();
        $usermansetting->uid = $pbxUsermanSetting->uid;
        $usermansetting->module = $pbxUsermanSetting->LoginModule;
        $usermansetting->key = $pbxUsermanSetting->LoginKey;
        $usermansetting->val = $pbxUsermanSetting->LoginVal;
        $usermansetting->type = $pbxUsermanSetting->LoginType;
        $usermansetting->save();

        $usermansetting = new UsermanUsersSettings();
        $usermansetting->uid = $pbxUsermanSetting->uid;
        $usermansetting->module = $pbxUsermanSetting->LowModule;
        $usermansetting->key = $pbxUsermanSetting->LowKey;
        $usermansetting->val = $pbxUsermanSetting->LowVal;
        $usermansetting->type = $pbxUsermanSetting->LowType;
        $usermansetting->save();

        $usermansetting = new UsermanUsersSettings();
        $usermansetting->uid = $pbxUsermanSetting->uid;
        $usermansetting->module = $pbxUsermanSetting->MenuorderModule;
        $usermansetting->key = $pbxUsermanSetting->MenuorderKey;
        $usermansetting->val = $pbxUsermanSetting->MenuorderVal;
        $usermansetting->type = $pbxUsermanSetting->MenuorderType;
        $usermansetting->save();

        $usermansetting = new UsermanUsersSettings();
        $usermansetting->uid = $pbxUsermanSetting->uid;
        $usermansetting->module = $pbxUsermanSetting->EnableModule;
        $usermansetting->key = $pbxUsermanSetting->EnableKey;
        $usermansetting->val = $pbxUsermanSetting->EnableVal;
        $usermansetting->type = $pbxUsermanSetting->EnableType;
        $usermansetting->save();

        $usermansetting = new UsermanUsersSettings();
        $usermansetting->uid = $pbxUsermanSetting->uid;
        $usermansetting->module = $pbxUsermanSetting->FmrModule;
        $usermansetting->key = $pbxUsermanSetting->FmrKey;
        $usermansetting->val = $pbxUsermanSetting->FmrVal;
        $usermansetting->type = $pbxUsermanSetting->FmrType;
        $usermansetting->save();

        $usermansetting = new UsermanUsersSettings();
        $usermansetting->uid = $pbxUsermanSetting->uid;
        $usermansetting->module = $pbxUsermanSetting->FlushPageModule;
        $usermansetting->key = $pbxUsermanSetting->FlushPageKey;
        $usermansetting->val = $pbxUsermanSetting->FlushPageVal;
        $usermansetting->type = $pbxUsermanSetting->FlushPageType;
        $usermansetting->save();
    }

    public function faxUserExample(){
        $item = new PbxFaxUsersItem();
        $item->user = '60';
        $item->faxenabled = true;
        $item->faxemail = 'aziz@gmail.com';
        $item->faxattachformat = 'pdf';
        $this->faxUsers($item);
    }

    public function faxUsers($pbxFaxUser)
    {
        $faxUser = new FaxUsers();
        $faxUser->user = $pbxFaxUser->user;
        $faxUser->faxenabled = $pbxFaxUser->faxenabled;
        $faxUser->faxemail = $pbxFaxUser->faxemail;
        $faxUser->faxattachformat = $pbxFaxUser->faxattachformat;
        $faxUser->save();
    }

    public function kvstoreContactmanagerExample(){
        $item = new PbxKvstoreContactManagerItem();
        $item->id = '60';
        $this->kvStoreContactmanager($item);
    }

    public function kvStoreContactmanager($kvstoreContactmanager)
    {
        $kvStore = new KvstoreFreePBXModulesContactmanager();

        $kvStore->id = $kvstoreContactmanager->id;
        $kvStore->save();
    }

    public function zuluIteractContactExample(){
        $item = new PbxZuluInteractionsContactsItem();
        $item->id = '6f47de45-f298-49f5-82ee-159dc0aee3a4';
        $item->calleridnumber = '60';
        $item->userman_id = '60';
        $item->calleridname = 'qwerty';
        $item->linkedid = '6f47de57-f220-49f5-82ee-159dc0aee3a4';
        $this->zuluIteractContact($item);
    }

    public function zuluIteractContact($pbxZuluIteractContact)
    {
        $zuluIteractionContacts = new ZuluInteractionsContacts();
        $zuluIteractionContacts->id = $pbxZuluIteractContact->id;
        $zuluIteractionContacts->zulu_id = $pbxZuluIteractContact->zulu_id;
        $zuluIteractionContacts->type = $pbxZuluIteractContact->type;
        $zuluIteractionContacts->calleridnumber = $pbxZuluIteractContact->calleridnumber;
        $zuluIteractionContacts->userman_id = $pbxZuluIteractContact->userman_id;
        $zuluIteractionContacts->calleridname = $pbxZuluIteractContact->calleridname;
        $zuluIteractionContacts->linkedid = $pbxZuluIteractContact->linkedid;
        $zuluIteractionContacts->contactman_id = $pbxZuluIteractContact->contactman_id;
        $zuluIteractionContacts->save();
    }

    public function devices()
    {
        $pbxdeviceitem = $this->pbxdeviceitem;
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

    public function sip()
    {
        $pbxsipitem = $this->pbxsipitem;
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

    public function createExtention($pbxuseritem, $pbxdeviceitem, $pbxsipitem)
    {
        $this->user($pbxuseritem);
        $this->devices($pbxdeviceitem);
        $this->sip($pbxsipitem);
        Az::$app->calls->fwconsole->run('reload');
    }

    public function createExtentionUser(){
        $this->createExtention($this->pbxuseritem, $this->pbxdeviceitem, $this->pbxsipitem);
        $this->createUser($this->pbxaccountitem);
        Az::$app->calls->fwconsole->run('reload');
    }



}
