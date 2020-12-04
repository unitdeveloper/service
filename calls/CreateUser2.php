<?php

/**
 *
 * Author:  Xolmat Ravshanov
 *
 */

namespace zetsoft\service\calls;

use zetsoft\system\kernels\ZFrame;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Client;

class CreateUser2 extends ZFrame
{
    #region Vars

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

    }
    #endregion


    #region Test
    public function Extention()
    {

        $client = new Client(['cookies' => true]);

        /*  $cookieJar = CookieJar::fromArray([
              'cookie' => 'f2k50uevn2fuj8f5iu477f2762'
          ], 'http://10.10.3.41/admin/config.php?display=extensions');*/

        $client->request('POST', 'http://10.10.3.41/admin/config.php', [
            'headers' =>
                [
                    'Host' => '10.10.3.41',
                    "User-Agent" => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.83 Safari/537.36',
                    "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9",
                    "Accept-Language" => "ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7",
                    "Accept-Encoding" => "gzip, deflate",
                    "Accept-Charset" => "ISO-8859-1,utf-8;q=0.7,*;q=0.7",
                    "Keep-Alive" => "300",
                    'Connection' => 'keep-alive',
                    'Content-Length' => '32',
                    'Cache-Control' => 'max-age=0',
                    'Upgrade-Insecure-Requests' => '1',
                    'Origin' => 'http://10.10.3.41',
                    "Content-Type" => 'application/x-www-form-urlencoded',
                    "Referer" => "http://10.10.3.41/admin/config.php",
                    "Cookie" => 'searchHide=1; bannerMessages=%5B%22bea62b8a3283c67cfa012758aae342367dcdd1e0%22%2C%22a23fbd5c9017426952af2e6f089477f9ed09f513%22%5D; lang=en_US; extensions-all.bs.table.pageNumber=1; extensions-all.bs.table.searchText=7'
                ],
            'form_params' => [
                'username' => 'admin',
                'password' => 'Formula1'
            ]

        ]);


        $response = $client->request('POST', 'http://10.10.3.41/admin/config.php', [
            /*       'cookies' => $cookieJar,*/

            'headers' => [
                'Host' => '10.10.3.41',
                'Connection' => 'keep-alive',
                'Content-Length' => '32',
                'Cache-Control' => 'max-age=0',
                'Upgrade-Insecure-Requests' => '1',
                'Origin' => 'http://10.10.3.41',
                "Content-Type" => 'application/x-www-form-urlencoded',
                "User-Agent" => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.83 Safari/537.36',
                "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9",
                "Referer" => "http://10.10.3.41/admin/config.php",
                "Accept-Encoding" => "gzip, deflate",
                "Accept-Language" => "ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7",


            ],

            'form_params' => [
                "extdisplay" => "",
                "action" => "add",
                "tech" => "sip",
                "hardware" => "generic",
                "extension" => "755",
                "name" => "755",
                "outboundcid" => "755",
                "emergency_cid" => "755",
                "devinfo_secret" => "755",
                "langcode" => "",
                "userman_directory" => "1",
                "userman_assign" => "add",
                "userman_password" => "1652a8d94deae2f730759b75223307bd",
                "userman_group[]" => "2",
                "vm" => "disabled",
                "vmx_option_0_number" => "",
                "vmx_option_0_system_default" => "checked",
                "fmfm_ddial" => "disabled",
                "fmfm_pre_ring" => "7",
                "fmfm_strategy" => "ringallv2-prim",
                "fmfm_grptime" => "20",
                "fmfm_grplist" => "",
                "fmfm_annmsg_id" => "",
                "fmfm_ringing" => "Ring",
                "fmfm_grppre" => "",
                "fmfm_dring" => "",
                "fmfm_rvolume" => "",
                "fmfm_needsconf" => "disabled",
                "fmfm_changecid" => "default",
                "gotofmfm" => "Follow_Me",
                "Announcementsfmfm" => "app-announcement-1,s,1",
                "Call_Flow_Controlfmfm" => "popover",
                "Call_Recordingfmfm" => "popover",
                "Callbackfmfm" => "popover",
                "Conference_Profmfm" => "popover",
                "Conferencesfmfm" => "popover",
                "Custom_Applicationsfmfm" => "popover",
                "DISAfmfm" => "popover",
                "Directoryfmfm" => "directory,2,1",
                "Extensionsfmfm" => "from-did-direct,111,1",
                "Feature_Code_Adminfmfm" => "ext-featurecodes,*30,1",
                "Follow_Mefmfm" => "ext-local,,dest",
                "IVRfmfm" => "popover",
                "Inbound_Routesfmfm" => "from-trunk,,1",
                "Languagesfmfm" => "popover",
                "Misc_Destinationsfmfm" => "popover",
                "Paging_and_Intercomfmfm" => "popover",
                "Phonebook_Directoryfmfm" => "app-pbdirectory,pbdirectory,1",
                "Queue_Prioritiesfmfm" => "popover",
                "Queuesfmfm" => "ext-queues,1000,1",
                "Queues_Profmfm" => "popover",
                "Ring_Groupsfmfm" => "popover",
                "Sipstationfmfm" => "sipstation-welcome,755,1",
                "Terminate_Callfmfm" => "app-blackhole,hangup,1",
                "Text_To_Speechfmfm" => "ext-tts,1,1",
                "Time_Conditionsfmfm" => "popover",
                "Trunksfmfm" => "popover",
                "Voicemail_Blastingfmfm" => "popover",
                "fmfm_goto" => "gotofmfm",
                "newdid_name" => "",
                "newdid" => "",
                "newdidcid" => "",
                "devinfo_dtmfmode" => "rfc2833",
                "devinfo_canreinvite" => "no",
                "devinfo_context" => "from-internal",
                "devinfo_host" => "dynamic",
                "devinfo_defaultuser" => "",
                "devinfo_trustrpid" => "yes",
                "devinfo_user_eq_phone" => "no",
                "devinfo_sendrpid" => "pai",
                "devinfo_type" => "friend",
                "devinfo_sessiontimers" => "accept",
                "devinfo_nat" => "yes",
                "devinfo_port" => "5060",
                "devinfo_qualify" => "yes",
                "devinfo_qualifyfreq" => "60",
                "devinfo_transport" => "udp,tcp,tls",
                "devinfo_avpf" => "no",
                "devinfo_force_avp" => "no",
                "devinfo_icesupport" => "no",
                "devinfo_rtcp_mux" => "no",
                "devinfo_encryption" => "no",
                "devinfo_videosupport" => "inherit",
                "devinfo_namedcallgroup" => "",
                "devinfo_namedpickupgroup" => "",
                "devinfo_disallow" => "",
                "devinfo_allow" => "",
                "devinfo_dial" => "",
                "devinfo_accountcode" => "",
                "devinfo_mailbox" => "",
                "devinfo_vmexten" => "",
                "devinfo_deny" => "0.0.0.0/0.0.0.0",
                "devinfo_permit" => "0.0.0.0/0.0.0.0",
                "cid_masquerade" => "",
                "sipname" => "",
                "ringtimer" => "0",
                "rvolume" => "",
                "cfringtimer" => "0",
                "concurrency_limit" => "3",
                "callwaiting" => "enabled",
                "cwtone" => "disabled",
                "call_screen" => "0",
                "intercom" => "enabled",
                "qnostate" => "usestate",
                "recording_in_external" => "recording_in_external=force",
                "recording_out_external" => "recording_out_external=force",
                "recording_in_internal" => "recording_in_internal=force",
                "recording_out_internal" => "recording_out_internal=force",
                "recording_ondemand" => "recording_ondemand=disabled",
                "recording_priority" => "10",
                "dictenabled" => "disabled",
                "dictformat" => "ogg",
                "dictemail" => "",
                "dictfrom" => "dictate@freepbx.org",
                "in_default_directory" => "0",
                "dtls_enable" => "no",
                "dtls_certificate" => "2",
                "dtls_verify" => "fingerprint",
                "dtls_setup" => "actpass",
                "dtls_rekey" => "0",
                "goto0" => "",
                "Announcements0" => "app-announcement-1,s,1",
                "Call_Flow_Control0" => "popover",
                "Call_Recording0" => "popover",
                "Callback0" => "popover",
                "Conference_Pro0" => "popover",
                "Conferences0" => "popover",
                "Custom_Applications0" => "popover",
                "DISA0" => "popover",
                "Directory0" => "directory,2,1",
                "Extensions0" => "from-did-direct,111,1",
                "Feature_Code_Admin0" => "ext-featurecodes,*30,1",
                "IVR0" => "popover",
                "Inbound_Routes0" => "from-trunk,,1",
                "Languages0" => "popover",
                "Misc_Destinations0" => "popover",
                "Paging_and_Intercom0" => "popover",
                "Phonebook_Directory0" => "app-pbdirectory,pbdirectory,1",
                "Queue_Priorities0" => "popover",
                "Queues0" => "ext-queues,1000,1",
                "Queues_Pro0" => "popover",
                "Ring_Groups0" => "popover",
                "Sipstation0" => "sipstation-welcome,755,1",
                "Terminate_Call0" => "app-blackhole,hangup,1",
                "Text_To_Speech0" => "ext-tts,1,1",
                "Time_Conditions0" => "popover",
                "Trunks0" => "popover",
                "Voicemail_Blasting0" => "popover",
                "noanswer_dest" => "goto0",
                "noanswer_cid" => "",
                "goto1" => "",
                "Announcements1" => "app-announcement-1,s,1",
                "Call_Flow_Control1" => "popover",
                "Call_Recording1" => "popover",
                "Callback1" => "popover",
                "Conference_Pro1" => "popover",
                "Conferences1" => "popover",
                "Custom_Applications1" => "popover",
                "DISA1" => "popover",
                "Directory1" => "directory,2,1",
                "Extensions1" => "from-did-direct,111,1",
                "Feature_Code_Admin1" => "ext-featurecodes,*30,1",
                "IVR1" => "popover",
                "Inbound_Routes1" => "from-trunk,,1",
                "Languages1" => "popover",
                "Misc_Destinations1" => "popover",
                "Paging_and_Intercom1" => "popover",
                "Phonebook_Directory1" => "app-pbdirectory,pbdirectory,1",
                "Queue_Priorities1" => "popover",
                "Queues1" => "ext-queues,1000,1",
                "Queues_Pro1" => "popover",
                "Ring_Groups1" => "popover",
                "Sipstation1" => "sipstation-welcome,755,1",
                "Terminate_Call1" => "app-blackhole,hangup,1",
                "Text_To_Speech1" => "ext-tts,1,1",
                "Time_Conditions1" => "popover",
                "Trunks1" => "popover",
                "Voicemail_Blasting1" => "popover",
                "busy_dest" => "goto1",
                "busy_cid" => "",
                "goto2" => "",
                "Announcements2" => "app-announcement-1,s,1",
                "Call_Flow_Control2" => "popover",
                "Call_Recording2" => "popover",
                "Callback2" => "popover",
                "Conference_Pro2" => "popover",
                "Conferences2" => "popover",
                "Custom_Applications2" => "popover",
                "DISA2" => "popover",
                "Directory2" => "directory,2,1",
                "Extensions2" => "from-did-direct,111,1",
                "Feature_Code_Admin2" => "ext-featurecodes,*30,1",
                "IVR2" => "popover",
                "Inbound_Routes2" => "from-trunk,,1",
                "Languages2" => "popover",
                "Misc_Destinations2" => "popover",
                "Paging_and_Intercom2" => "popover",
                "Phonebook_Directory2" => "app-pbdirectory,pbdirectory,1",
                "Queue_Priorities2" => "popover",
                "Queues2" => "ext-queues,1000,1",
                "Queues_Pro2" => "popover",
                "Ring_Groups2" => "popover",
                "Sipstation2" => "sipstation-welcome,755,1",
                "Terminate_Call2" => "app-blackhole,hangup,1",
                "Text_To_Speech2" => "ext-tts,1,1",
                "Time_Conditions2" => "popover",
                "Trunks2" => "popover",
                "Voicemail_Blasting2" => "popover",
                "chanunavail_dest" => "goto2",
                "chanunavail_cid" => "",
                "pinless" => "disabled",
                "endpointBrand[1]" => "Select",
                "endpointMac[1]" => "",
                "endpointTemplate[1]" => " ",
                "endpointModel[1]" => " ",
                "endpointAccount[1]" => "account1",
                "cxpanel_add_extension" => "1",
                "cxpanel_auto_answer" => "0",
                "dp_prefix" => "",
                "dp_first_name" => "",
                "dp_second_name" => "",
                "dp_last_name" => "",
                "dp_suffix" => "",
                "dp_organization" => "",
                "dp_job_title" => "",
                "dp_location" => "",
                "dp_email" => "",
                "dp_notes" => "",
                "dp_line_label" => "",
                "dp_digit_map" => "",
                "dp_voicemail_uri" => "",
                "dp_transport" => "",
                "dp_media_encryption" => "",
                "dp_reregistration_timeout" => "",
                "dp_registration_retry_interval" => "",
                "dp_registration_max_retries" => "",
                "intercom_override" => "intercom_override=reject",
                "devinfo_secret_origional" => "",
                "devinfo_sipdriver" => "chan_sip",
                "endpointExt[1]" => "",
            ]
        ]);


        vdd($response);
    }

    #endregion

    public function login()
    {
        $client = new Client();

        $response = $client->request('POST', 'http://10.10.3.41/admin/config.php', [
            'headers' =>
                [
                    'Host' => '10.10.3.41',
                    'Connection' => 'keep-alive',
                    'Content-Length' => '32',
                    'Cache-Control' => 'max-age=0',
                    'Upgrade-Insecure-Requests' => '1',
                    'Origin' => 'http://10.10.3.41',
                    "Content-Type" => 'application/x-www-form-urlencoded',
                    "User-Agent" => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.83 Safari/537.36',
                    "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9",
                    "Referer" => "http://10.10.3.41/admin/config.php",
                    "Accept-Encoding" => "gzip, deflate",
                    "Accept-Language" => "ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7"
                ],

            'form_params' => [
                'username' => 'admin',
                'password' => 'Formula11'
            ]

        ]);

        vdd($response);
    }


}
