<?php

/**
 *
 * Author:  Xolmat Ravshanov
 *
 */

namespace zetsoft\service\calls;

use zetsoft\models\App\eyuf\db3\CxpanelUsers;
use zetsoft\models\App\eyuf\db3\Devices;
use zetsoft\models\App\eyuf\db3\Sip;
use zetsoft\models\App\eyuf\db3\Users;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;


class Freepbx extends ZFrame
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
               $this->sip();
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
            INSERT INTO devices (id, tech, dial, devicetype, user, description, emergency_cid)

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
       /* $hash = $this->crypt_private($password, $this->gensalt_private($random));*/

    }


    public function createExt(){

        $cookieJar = CookieJar::fromArray([
            'cookie_name' => 'cookie_value'
        ], 'example.com');

        $client->request('GET', '/get', ['cookies' => $cookieJar]);

    }


    public function sipExample()
    {
        /*
         *
         *
           INSERT INTO sip (id, keyword, data, flags)
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

    public function user()
    {
        $user = new Users();
        $user->extension = $this->pbxuseritem->extention;
        $user->password = $this->pbxuseritem->password;
        $user->name = $this->pbxuseritem->name;
        $user->voicemail = $this->pbxuseritem->voicemail;
        $user->ringtimer = $this->pbxuseritem->ringtimer;
        $user->noanswer = $this->pbxuseritem->noanswer;
        $user->recording = $this->pbxuseritem->recording;
        $user->outboundcid = $this->pbxuseritem->outboundcid;
        $user->sipname = $this->pbxuseritem->sipname;
        $user->noanswer_cid = $this->pbxuseritem->noanswer_cid;
        $user->busy_cid = $this->pbxuseritem->busy_cid;
        $user->chanunavail_cid = $this->pbxuseritem->chanunavail_cid;
        $user->noanswer_dest = $this->pbxuseritem->noanswer_dest;
        $user->busy_dest = $this->pbxuseritem->busy_dest;
        $user->chanunavail_dest = $this->pbxuseritem->chanunavail_dest;
        $user->mohclass = $this->pbxuseritem->mohclass;
        $user->save();

    }

    public function devices()
    {
        $device = new Devices();
        $device->id = $this->pbxdeviceitem->id;
        $device->tech = $this->pbxdeviceitem->tech;
        $device->dial = $this->pbxdeviceitem->dial;
        $device->devicetype = $this->pbxdeviceitem->devicetype;
        $device->user = $this->pbxdeviceitem->user;
        $device->description = $this->pbxdeviceitem->description;
        $device->emergency_cid = $this->pbxdeviceitem->emergency_cid;
        $device->save();
    }


    public function sip()
    {
        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->secret;
        $sip->data = $this->pbxsipitem->secretData;
        $sip->flags = $this->pbxsipitem->secretFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->dtmfmode;
        $sip->data = $this->pbxsipitem->dtmfmodeData;
        $sip->flags = $this->pbxsipitem->dtmfmodeFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->canreinvite;
        $sip->data = $this->pbxsipitem->canreinviteData;
        $sip->flags = $this->pbxsipitem->canreinviteFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->context;
        $sip->data = $this->pbxsipitem->contextData;
        $sip->flags = $this->pbxsipitem->contextFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->host;
        $sip->data = $this->pbxsipitem->hostData;
        $sip->flags = $this->pbxsipitem->hostFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->trustrpid;
        $sip->data = $this->pbxsipitem->trustrpidData;
        $sip->flags = $this->pbxsipitem->trustrpidFlags;
        $sip->save();


        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->sendrpid;
        $sip->data = $this->pbxsipitem->sendrpidData;
        $sip->flags = $this->pbxsipitem->sendrpidFlags;
        $sip->save();


        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->type;
        $sip->data = $this->pbxsipitem->typeData;
        $sip->flags = $this->pbxsipitem->typeFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->nat;
        $sip->data = $this->pbxsipitem->natData;
        $sip->flags = $this->pbxsipitem->natFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->port;
        $sip->data = $this->pbxsipitem->portData;
        $sip->flags = $this->pbxsipitem->portFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->qualify;
        $sip->data = $this->pbxsipitem->qualifyData;
        $sip->flags = $this->pbxsipitem->qualifyFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->qualifyfreq;
        $sip->data = $this->pbxsipitem->qualifyfreqData;
        $sip->flags = $this->pbxsipitem->qualifyfreqFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->udp;
        $sip->data = $this->pbxsipitem->udpData;
        $sip->flags = $this->pbxsipitem->udpFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->avpf;
        $sip->data = $this->pbxsipitem->avpfData;
        $sip->flags = $this->pbxsipitem->avpfFlags;
        $sip->save();


        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->icesupport;
        $sip->data = $this->pbxsipitem->icesupportData;
        $sip->flags = $this->pbxsipitem->icesupportFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->encryption;
        $sip->data = $this->pbxsipitem->encryptionData;
        $sip->flags = $this->pbxsipitem->encryptionFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->callgroup;
        $sip->data = $this->pbxsipitem->callgroupData;
        $sip->flags = $this->pbxsipitem->callgroupFlags;
        $sip->save();


        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->pickupgroup;
        $sip->data = $this->pbxsipitem->pickupgroupData;
        $sip->flags = $this->pbxsipitem->pickupgroupFlags;
        $sip->save();


        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->disallow;
        $sip->data = $this->pbxsipitem->disallowData;
        $sip->flags = $this->pbxsipitem->disallowFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->allow;
        $sip->data = $this->pbxsipitem->allowData;
        $sip->flags = $this->pbxsipitem->allowFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->dial;
        $sip->data = $this->pbxsipitem->dialData;
        $sip->flags = $this->pbxsipitem->dialFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->mailbox;
        $sip->data = $this->pbxsipitem->mailboxData;
        $sip->flags = $this->pbxsipitem->mailboxFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->deny;
        $sip->data = $this->pbxsipitem->denyData;
        $sip->flags = $this->pbxsipitem->denyFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->permit;
        $sip->data = $this->pbxsipitem->permitData;
        $sip->flags = $this->pbxsipitem->permitFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->account;
        $sip->data = $this->pbxsipitem->accountData;
        $sip->flags = $this->pbxsipitem->accountFlags;
        $sip->save();

        $sip = new Sip();
        $this->pbxsipitem->calleridData = "device <$this->pbxsipitem->id>";
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->callerid;
        $sip->data = $this->pbxsipitem->calleridData;
        $sip->flags = $this->pbxsipitem->calleridFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->max_audio_streams;
        $sip->data = $this->pbxsipitem->max_audio_streamsData;
        $sip->flags = $this->pbxsipitem->max_audio_streamsFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->media_encryption;
        $sip->data = $this->pbxsipitem->media_encryptionData;
        $sip->flags = $this->pbxsipitem->media_encryptionFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->timers;
        $sip->data = $this->pbxsipitem->timersData;
        $sip->flags = $this->pbxsipitem->timersFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->timers_min_se;
        $sip->data = $this->pbxsipitem->timers_min_seData;
        $sip->flags = $this->pbxsipitem->timers_min_seFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->direct_media;
        $sip->data = $this->pbxsipitem->direct_mediaData;
        $sip->flags = $this->pbxsipitem->direct_mediaFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->media_encryption_optimistic;
        $sip->data = $this->pbxsipitem->media_encryption_optimisticData;
        $sip->flags = $this->pbxsipitem->media_encryption_optimisticFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->refer_blind_progress;
        $sip->data = $this->pbxsipitem->refer_blind_progressData;
        $sip->flags = $this->pbxsipitem->refer_blind_progressFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->device_state_busy_at;
        $sip->data = $this->pbxsipitem->device_state_busy_atData;
        $sip->flags = $this->pbxsipitem->device_state_busy_atFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->match;
        $sip->data = $this->pbxsipitem->matchData;
        $sip->flags = $this->pbxsipitem->matchFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->maximum_expiration;
        $sip->data = $this->pbxsipitem->maximum_expirationData;
        $sip->flags = $this->pbxsipitem->maximum_expirationFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->minimum_expiration;
        $sip->data = $this->pbxsipitem->minimum_expirationData;
        $sip->flags = $this->pbxsipitem->minimum_expirationFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->rtp_timeout;
        $sip->data = $this->pbxsipitem->rtp_timeoutData;
        $sip->flags = $this->pbxsipitem->rtp_timeoutFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->rtp_timeout_hold;
        $sip->data = $this->pbxsipitem->rtp_timeout_holdData;
        $sip->flags = $this->pbxsipitem->rtp_timeout_holdFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->outbound_proxy;
        $sip->data = $this->pbxsipitem->outbound_proxyData;
        $sip->flags = $this->pbxsipitem->outbound_proxyFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->message_context;
        $sip->data = $this->pbxsipitem->message_contextData;
        $sip->flags = $this->pbxsipitem->message_contextFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->secret_origional;
        $sip->data = $this->pbxsipitem->secret_origionalData;
        $sip->flags = $this->pbxsipitem->secret_origionalFlags;
        $sip->save();

        $sip = new Sip();
        $sip->id = $this->pbxsipitem->id;
        $sip->keyword = $this->pbxsipitem->sipdriver;
        $sip->data = $this->pbxsipitem->sipdriverData;
        $sip->flags = $this->pbxsipitem->sipdriverFlags;
        $sip->save();
    }


    public function createCxUser()
    {
        $user = new CxpanelUsers();
        $user->user_id = $this->pbxaccountitem->user_id;
        $user->display_name = $this->pbxaccountitem->display_name;
        $user->peer = $this->pbxaccountitem->peer . $this->pbxaccountitem->user_id;
        $user->add_extension = $this->pbxaccountitem->add_extension;
        $user->full = $this->pbxaccountitem->full;
        $user->add_user = $this->pbxaccountitem->add_user;
        $user->hashed_password = $this->pbxaccountitem->hashed_password;
        $user->initial_password = $this->pbxaccountitem->initial_password;
        $user->auto_answer = $this->pbxaccountitem->auto_answer;
        $user->parent_user_id = $this->pbxaccountitem->parent_user_id;
        $user->password_dirty = $this->pbxaccountitem->password_dirty;
        $user->save();

    }


    public function createExtention()
    {
        $this->user($this->pbxuseritem);
        $this->devices($this->pbxdeviceitem);
        $this->sip($this->pbxsipitem);
        Az::$app->calls->fwconsole->run('reload');
    }

    public function createExtentionUser(){
        $this->createExtention($this->pbxuseritem, $this->pbxdeviceitem, $this->pbxsipitem);
        $this->createUser($this->pbxaccountitem);
        Az::$app->calls->fwconsole->run('reload');
    }



}
