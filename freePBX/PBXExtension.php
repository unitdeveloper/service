<?php

namespace zetsoft\service\freePBX;

use GuzzleHttp\Client;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;
use GuzzleHttp\Cookie\CookieJar;


class PBXExtension extends ZFrame
{
    
    public function login()
    {

        $client = new Client(['cookies' => true]);

        //header('Access-Control-Allow-Credentials: true');
        $res = $client->request('GET', 'http://10.10.3.60/admin/config.php#', [
            'username' => 'admin',
            'password' => 'Production'
        ]);
        echo $res->getStatusCode();
// "200"
        echo $res->getHeader('content-type')[0];
// 'application/json; charset=utf8'
        echo $res->getBody();
// {"type":"User"...'

// Send an asynchronous request.
        $request = new \GuzzleHttp\Psr7\Request('GET', 'http://10.10.3.60/admin/config.php');
        $promise = $client->sendAsync($request)->then(function ($response) {
            echo 'I completed! ' . $response->getBody();
        });
        $promise->wait();
        $r  =  $client -> request ( 'GET' ,  'http://10.10.3.60/admin/config.php' );

       // $this->create();

    }


    public function create()
    {
        //$client = new Client(['base_uri' => 'https://checkout.test.paycom.uz']);
        $client = new Client();

        $response = $client->request('POST', 'http://10.10.3.60/admin/config.php?display=extensions&extdisplay=546', [

            'form_params' => [

              /*  "action" => "add",
                "extdisplay" => "",
                "action" => "add",
                "extdisplay" => "",
                "tech" => "sip",
                "hardware" => "generic",
                "extension" => "888",
                "name" => "888",
                "outboundcid" => "",
                "emergency_cid" => "",
                "devinfo_secret" => "888",
                "langcode" => "",
                "userman_directory" => "1",
                "userman_assign" => "add",
                "userman_password" => "",
                "userman_group[]" => "1",
                "vm" => "disabled",
                "vmx_option_0_number" => "",
                "vmx_option_0_system_default" => "checked",
                "fmfm_ddial" => "disabled",
                "fmfm_calendar_enable" => "0",
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
                "Announcementsfmfm" => "popover",
                "Call_Flow_Controlfmfm" => "popover",
                "Call_Recordingfmfm" => "popover",
                "Callbackfmfm" => "popover",
                "Conference_Profmfm" => "popover",
                "Conferencesfmfm" => "popover",
                "Custom_Applicationsfmfm" => "popover",
                "DISAfmfm" => "popover",
                "Directoryfmfm" => "popover",
                "Extensionsfmfm" => "from-did-direct,111,1",
                "Fax_Recipientfmfm" => "ext-fax,1,1",
                "Feature_Code_Adminfmfm" => "ext-featurecodes,*30,1",
                "Follow_Mefmfm" => "ext-local,,dest",
                "IVRfmfm" => "popover",
                "Inbound_Routesfmfm" => "from-trunk,,1",
                "Languagesfmfm" => "popover",
                "Misc_Destinationsfmfm" => "popover",
                "Paging_and_Intercomfmfm" => "popover",
                "Phonebook_Directoryfmfm" => "app-pbdirectory,pbdirectory,1",
                "Queue_Prioritiesfmfm" => "popover",
                "Queuesfmfm" => "ext-queues,757,1",
                "Queues_Profmfm" => "popover",
                "Ring_Groupsfmfm" => "popover",
                "Sipstationfmfm" => "sipstation-welcome,${EXTEN},1",
                "Terminate_Callfmfm" => "app-blackhole,hangup,1",
                "Text_To_Speechfmfm" => "popover",
                "Time_Conditionsfmfm" => "popover",
                "Trunksfmfm" => "ext-trunk,1,1",
                "Voicemailfmfm" => "ext-local,vmb315,1",
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
                "devinfo_transport" => "wss,udp,tcp,tls",
                "devinfo_avpf" => "yes",
                "devinfo_force_avp" => "yes",
                "devinfo_icesupport" => "yes",
                "devinfo_rtcp_mux" => "yes",
                "devinfo_encryption" => "yes",
                "devinfo_videosupport" => "yes",
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
                "cwtone" => "enabled",
                "call_screen" => "0",
                "answermode" => "disabled",
                "intercom" => "enabled",
                "qnostate" => "usestate",
                "recording_in_external" => "recording_in_external=>force",
                "recording_out_external" => "recording_out_external=>force",
                "recording_in_internal" => "recording_in_internal=>force",
                "recording_out_internal" => "recording_out_internal=>force",
                "recording_ondemand" => "recording_ondemand=>enabled",
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
                "Announcements0" => "popover",
                "Call_Flow_Control0" => "popover",
                "Call_Recording0" => "popover",
                "Callback0" => "popover",
                "Conference_Pro0" => "popover",
                "Conferences0" => "popover",
                "Custom_Applications0" => "popover",
                "DISA0" => "popover",
                "Directory0" => "popover",
                "Extensions0" => "from-did-direct,111,1",
                "Fax_Recipient0" => "ext-fax,1,1",
                "Feature_Code_Admin0" => "ext-featurecodes,*30,1",
                "IVR0" => "popover",
                "Inbound_Routes0" => "from-trunk,,1",
                "Languages0" => "popover",
                "Misc_Destinations0" => "popover",
                "Paging_and_Intercom0" => "popover",
                "Phonebook_Directory0" => "app-pbdirectory,pbdirectory,1",
                "Queue_Priorities0" => "popover",
                "Queues0" => "ext-queues,757,1",
                "Queues_Pro0" => "popover",
                "Ring_Groups0" => "popover",
                "Sipstation0" => "sipstation-welcome,${EXTEN},1",
                "Terminate_Call0" => "app-blackhole,hangup,1",
                "Text_To_Speech0" => "popover",
                "Time_Conditions0" => "popover",
                "Trunks0" => "ext-trunk,1,1",
                "Voicemail0" => "ext-local,vmb315,1",
                "Voicemail_Blasting0" => "popover",
                "noanswer_dest" => "goto0",
                "noanswer_cid" => "",
                "goto1" => "",
                "Announcements1" => "popover",
                "Call_Flow_Control1" => "popover",
                "Call_Recording1" => "popover",
                "Callback1" => "popover",
                "Conference_Pro1" => "popover",
                "Conferences1" => "popover",
                "Custom_Applications1" => "popover",
                "DISA1" => "popover",
                "Directory1" => "popover",
                "Extensions1" => "from-did-direct,111,1",
                "Fax_Recipient1" => "ext-fax,1,1",
                "Feature_Code_Admin1" => "ext-featurecodes,*30,1",
                "IVR1" => "popover",
                "Inbound_Routes1" => "from-trunk,,1",
                "Languages1" => "popover",
                "Misc_Destinations1" => "popover",
                "Paging_and_Intercom1" => "popover",
                "Phonebook_Directory1" => "app-pbdirectory,pbdirectory,1",
                "Queue_Priorities1" => "popover",
                "Queues1" => "ext-queues,757,1",
                "Queues_Pro1" => "popover",
                "Ring_Groups1" => "popover",
                "Sipstation1" => "sipstation-welcome,${EXTEN},1",
                "Terminate_Call1" => "app-blackhole,hangup,1",
                "Text_To_Speech1" => "popover",
                "Time_Conditions1" => "popover",
                "Trunks1" => "ext-trunk,1,1",
                "Voicemail1" => "ext-local,vmb315,1",
                "Voicemail_Blasting1" => "popover",
                "busy_dest" => "goto1",
                "busy_cid" => "",
                "goto2" => "",
                "Announcements2" => "popover",
                "Call_Flow_Control2" => "popover",
                "Call_Recording2" => "popover",
                "Callback2" => "popover",
                "Conference_Pro2" => "popover",
                "Conferences2" => "popover",
                "Custom_Applications2" => "popover",
                "DISA2" => "popover",
                "Directory2" => "popover",
                "Extensions2" => "from-did-direct,111,1",
                "Fax_Recipient2" => "ext-fax,1,1",
                "Feature_Code_Admin2" => "ext-featurecodes,*30,1",
                "IVR2" => "popover",
                "Inbound_Routes2" => "from-trunk,,1",
                "Languages2" => "popover",
                "Misc_Destinations2" => "popover",
                "Paging_and_Intercom2" => "popover",
                "Phonebook_Directory2" => "app-pbdirectory,pbdirectory,1",
                "Queue_Priorities2" => "popover",
                "Queues2" => "ext-queues,757,1",
                "Queues_Pro2" => "popover",
                "Ring_Groups2" => "popover",
                "Sipstation2" => "sipstation-welcome,${EXTEN},1",
                "Terminate_Call2" => "app-blackhole,hangup,1",
                "Text_To_Speech2" => "popover",
                "Time_Conditions2" => "popover",
                "Trunks2" => "ext-trunk,1,1",
                "Voicemail2" => "ext-local,vmb315,1",
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
                "cxpanel_auto_answer" => "1",
                "intercom_override" => "intercom_override=>reject",
                "devinfo_secret_origional" => "",
                "devinfo_sipdriver" => "chan_sip",
                "endpointExt[1]" => "",*/


                "action" => "edit",
                "extdisplay" => "546",
                "tech" => "sip",
                "hardware" => "",
                "name" => "Fayzullo",
                "outboundcid" => "99999",
                "emergency_cid" => "",
                "devinfo_secret" => "546",
                "langcode" => "",
                "userman_directory" => "1",
                "userman_assign" => "67",
                "userman_group[]" => "1",
                "vm" => "disabled",
                "vmx_option_0_number" => "",
                "vmx_option_0_system_default" => "checked",
                "fmfm_ddial" => "disabled",
                "fmfm_calendar_enable" => "0",
                "fmfm_pre_ring" => "7",
                "fmfm_strategy" => "ringallv2-prim",
                "fmfm_grptime" => "20",
                "fmfm_grplist" => "546",
                "fmfm_annmsg_id" => "",
                "fmfm_ringing" => "Ring",
                "fmfm_grppre" => "",
                 "fmfm_dring" => "",
                 "fmfm_rvolume" => "",
                 "fmfm_needsconf" => "disabled",
                 "fmfm_changecid" => "default",
                 "gotofmfm" => "Follow_Me",
                 "Announcementsfmfm" => "popover",
                 "Call_Flow_Controlfmfm" => "popover",
                 "Call_Recordingfmfm" => "popover",
                 "Callbackfmfm" => "popover",
                 "Conference_Profmfm" => "popover",
                 "Conferencesfmfm" => "popover",
                 "Custom_Applicationsfmfm" => "popover",
                 "DISAfmfm" => "popover",
                 "Directoryfmfm" => "popover",
                 "Extensionsfmfm" => "from-did-direct,111,1",
                 "Fax_Recipientfmfm" => "ext-fax,1,1",
                 "Feature_Code_Adminfmfm" => "ext-featurecodes,*30,1",
                 "Follow_Mefmfm" => "ext-local,546,dest",
                 "IVRfmfm" => "popover",
                 "Inbound_Routesfmfm" => "from-trunk,,1",
                 "Languagesfmfm" => "popover",
                 "Misc_Destinationsfmfm" => "popover",
                 "Paging_and_Intercomfmfm" => "popover",
                 "Phonebook_Directoryfmfm" => "app-pbdirectory,pbdirectory,1" ,
                 "Queue_Prioritiesfmfm" => "popover",
                 "Queuesfmfm" => "ext-queues,757,1",
                 "Queues_Profmfm" => "popover",
                 "Ring_Groupsfmfm" => "popover",
                 "Sipstationfmfm" => "sipstation-welcome, Extension 1",
                 "Terminate_Callfmfm" => "app-blackhole,hangup,1",
                 "Text_To_Speechfmfm" => "popover",
                 "Time_Conditionsfmfm" => "popover",
                 "Trunksfmfm" => "ext-trunk,1,1",
                 "Voicemailfmfm" => "ext-local,vmb315,1",
                 "Voicemail_Blastingfmfm" => "popover",
                 "fmfm_goto" => "gotofmfm",
                 "newdid_name" => "",
                 "newdid" => "",
                 "newdidcid" => "",
                 "devinfo_dtmfmode" => "rfc4733",
                 "devinfo_context" => "from-internal",
                 "devinfo_defaultuser" => "",
                 "devinfo_trustrpid" => "yes",
                 "devinfo_send_connected_line" => "yes",
                 "devinfo_user_eq_phone" => "no",
                 "devinfo_sendrpid" => "pai",
                 "devinfo_qualifyfreq" => "60",
                 "devinfo_transport" => "",
                 "devinfo_avpf" => "no",
                 "devinfo_icesupport" => "no",
                 "devinfo_rtcp_mux" => "no",
                 "devinfo_namedcallgroup" => "",
                 "devinfo_namedpickupgroup" => "",
                 "devinfo_disallow" => "",
                 "devinfo_allow" => "",
                 "devinfo_dial" => "PJSIP/546",
                 "devinfo_mailbox" => "546@device",
                 "devinfo_vmexten" => "",
                 "devinfo_accountcode" => "",
                 "devinfo_max_contacts" => "1",
                 "devinfo_media_use_received_transport" => "no",
                 "devinfo_rtp_symmetric" => "yes",
                 "devinfo_rewrite_contact" => "yes",
                 "devinfo_force_rport" => "yes",
                 "devinfo_mwi_subscription" => "auto",
                 "devinfo_aggregate_mwi" => "yes",
                 "devinfo_bundle" => "no",
                 "devinfo_max_audio_streams" => "1",
                 "devinfo_max_video_streams" => "1",
                 "devinfo_media_encryption" => "dtls",
                 "devinfo_timers" => "yes",
                 "devinfo_timers_min_se" => "90",
                 "devinfo_direct_media" => "yes",
                 "devinfo_media_encryption_optimistic" => "no",
                 "devinfo_refer_blind_progress" => "yes",
                 "devinfo_device_state_busy_at" => "0",
                 "devinfo_match" => "",
                 "devinfo_maximum_expiration" => "7200",
                 "devinfo_minimum_expiration" => "60",
                 "devinfo_rtp_timeout" => "0",
                 "devinfo_rtp_timeout_hold" => "0",
                 "devinfo_outbound_proxy" => "",
                 "devinfo_message_context" => "",
                 "cid_masquerade" => "",
                 "sipname" => "",
                 "ringtimer" => "0",
                 "rvolume" => "",
                 "cfringtimer" => "0",
                 "concurrency_limit" => "3",
                 "callwaiting" => "enabled",
                 "cwtone" => "disabled",
                 "call_screen" => "0",
                 "answermode" => "disabled",
                 "intercom" => "enabled",
                 "qnostate" => "usestate",
                 "recording_in_external" => "recording_in_external=>dontcare",
                 "recording_out_external" => "recording_out_external=>dontcare",
                 "recording_in_internal" => "recording_in_internal=>dontcare",
                 "recording_out_internal" => "recording_out_internal=>dontcare",
                 "recording_ondemand" => "recording_ondemand=>disabled",
                 "recording_priority" => "10",
                 "dictenabled" => "disabled",
                 "dictformat" => "ogg",
                 "dictemail" => "",
                 "dictfrom" => "dictate@freepbx.org",
                 "in_default_directory" => "0",
                 "dtls_enable" => "yes",
                 "dtls_auto_generate_cert" => "0",
                 "dtls_certificate" => "2",
                 "dtls_verify" => "fingerprint",
                 "dtls_setup" => "actpass",
                 "dtls_rekey" => "0",
                 "goto0" => "",
                 "Announcements0" => "popover",
                 "Call_Flow_Control0" => "popover",
                 "Call_Recording0" => "popover",
                 "Callback0" => "popover",
                 "Conference_Pro0" => "popover",
                 "Conferences0" => "popover",
                 "Custom_Applications0" => "popover",
                 "DISA0" => "popover",
                 "Directory0" => "popover",
                 "Extensions0" => "from-did-direct,111,1",
                 "Fax_Recipient0" => "ext-fax,1,1",
                 "Feature_Code_Admin0" => "ext-featurecodes,*30,1",
                 "Follow_Me0" => "ext-findmefollow,FM546,1",
                 "IVR0" => "popover",
                 "Inbound_Routes0" => "from-trunk,,1",
                 "Languages0" => "popover",
                 "Misc_Destinations0" => "popover",
                 "Paging_and_Intercom0" => "popover",
                 "Phonebook_Directory0" => "app-pbdirectory,pbdirectory,1",
                 "Queue_Priorities0" => "popover",
                 "Queues0" => "ext-queues,757,1",
                 "Queues_Pro0" => "popover",
                 "Ring_Groups0" => "popover",
                 "Sipstation0" => "sipstation-welcome, Extension,1",
                 "Terminate_Call0" => "app-blackhole,hangup,1",
                 "Text_To_Speech0" => "popover",
                 "Time_Conditions0" => "popover",
                 "Trunks0" => "ext-trunk,1,1",
                 "Voicemail0" => "ext-local,vmb315,1",
                 "Voicemail_Blasting0" => "popover",
                 "noanswer_dest" => "goto0",
                 "noanswer_cid" => "",
                 "goto1" => "",
                 "Announcements1" => "popover",
                 "Call_Flow_Control1" => "popover",
                 "Call_Recording1" => "popover",
                 "Callback1" => "popover",
                 "Conference_Pro1" => "popover",
                 "Conferences1" => "popover",
                 "Custom_Applications1" => "popover",
                 "DISA1" => "popover",
                 "Directory1" => "popover",
                 "Extensions1" => "from-did-direct,111,1",
                 "Fax_Recipient1" => "ext-fax,1,1",
                 "Feature_Code_Admin1" => "ext-featurecodes,*30,1",
                 "Follow_Me1" => "ext-findmefollow,FM546,1",
                 "IVR1" => "popover",
                 "Inbound_Routes1" => "from-trunk,,1",
                 "Languages1" => "popover",
                 "Misc_Destinations1" => "popover",
                 "Paging_and_Intercom1" => "popover",
                 "Phonebook_Directory1" => "app-pbdirectory,pbdirectory,1",
                 "Queue_Priorities1" => "popover",
                 "Queues1" => "ext-queues,757,1",
                 "Queues_Pro1" => "popover",
                 "Ring_Groups1" => "popover",
                 "Sipstation1" => "sipstation-welcome,Extension,1",
                 "Terminate_Call1" => "app-blackhole,hangup,1",
                 "Text_To_Speech1" => "popover",
                 "Time_Conditions1" => "popover",
                 "Trunks1" => "ext-trunk,1,1",
                 "Voicemail1" => "ext-local,vmb315,1",
                 "Voicemail_Blasting1" => "popover",
                 "busy_dest" => "goto1",
                 "busy_cid" => "",
                 "goto2" => "",
                 "Announcements2" => "popover",
                 "Call_Flow_Control2" => "popover",
                 "Call_Recording2" => "popover",
                 "Callback2" => "popover",
                 "Conference_Pro2" => "popover",
                 "Conferences2" => "popover",
                 "Custom_Applications2" => "popover",
                 "DISA2" => "popover",
                 "Directory2" => "popover",
                 "Extensions2" => "from-did-direct,111,1",
                 "Fax_Recipient2" => "ext-fax,1,1",
                 "Feature_Code_Admin2" => "ext-featurecodes,*30,1",
                 "Follow_Me2" => "ext-findmefollow,FM546,1",
                 "IVR2" => "popover",
                 "Inbound_Routes2" => "from-trunk,,1",
                 "Languages2" => "popover",
                 "Misc_Destinations2" => "popover",
                 "Paging_and_Intercom2" => "popover",
                 "Phonebook_Directory2" => "app-pbdirectory,pbdirectory,1",
                 "Queue_Priorities2" => "popover",
                 "Queues2" => "ext-queues,757,1",
                 "Queues_Pro2" => "popover",
                 "Ring_Groups2" => "popover",
                 "Sipstation2" => "sipstation-welcome, Extension ,1",
                 "Terminate_Call2" => "app-blackhole,hangup,1",
                 "Text_To_Speech2" => "popover",
                 "Time_Conditions2" => "popover",
                 "Trunks2" => "ext-trunk,1,1",
                 "Voicemail2" => "ext-local,vmb315,1",
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
                 "intercom_override" => "intercom_override=>reject",
                 "extension" => "546",
                 "devinfo_secret_origional" => "18b25354ffda2b1fbf5ab9c770d28805",
                 "devinfo_sipdriver" => "chan_sip",
                 "endpointExt[1]" => "546",
                 "changesipdriver" => "yes",

            ]
            
        ]);

        
        



    }

    #endregion

    #region getVerifyCode
    /**
     *
     * Function  getVerifyCode
     * @param $token
     * @return  string
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author Muminov Umid
     *
     * https://help.paycom.uz/ru/metody-subscribe-api/cards.get_verify_code
     */

    public function getVerifyCode($token)
    {
        $method = 'cards.get_verify_code';
        $client = new Client(['base_uri' => 'https://checkout.test.paycom.uz']);

        $response = $client->request('POST', '/api', [
            'headers' => [
                'X-Auth' => '5f2fe4c73ddb59936a2e76ec',

            ],
            'json' => [
                "method" => $method,
                "params" => [
                    "token" => $token
                ],
            ]
        ]);

        $qwer = $response->getBody();
        $result = json_decode($qwer);

        if (isset($result->result->sent) == true) {
            return "Успешно";
        } else {
            return $result->error->message;
        }

        //vdd($result);

    }

    #endregion

    #region Verify
    /**
     *
     * Function  verify
     * @param $token
     * @param $code
     * @return  string
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author Muminov Umid
     *
     * https://help.paycom.uz/ru/metody-subscribe-api/cards.verify
     */

    public function verify($token, $code)
    {
        $method = 'cards.verify';
        $client = new Client(['base_uri' => 'https://checkout.test.paycom.uz']);

        $response = $client->request('POST', '/api', [
            'headers' => [
                'X-Auth' => '5f2fe4c73ddb59936a2e76ec',

            ],
            'json' => [
                "method" => $method,
                "params" => [
                    "token" => $token,
                    "code" => $code
                ],
            ]
        ]);

        $qwer = $response->getBody();
        $result = json_decode($qwer);

        if (isset($result->result->card->verify) == true) {
            return "Успешно проверена";
        } else {
            return $result->error->data->message;
        }


        //vdd($result);
    }
    #endregion


    #endregion

    #region Server_side

    
    #region checkCard
    /**
     *
     * Function  checkCard
     * Methods for the server side of the trading application:
     * @param $token
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author Muminov Umid
     * https://help.paycom.uz/ru/metody-subscribe-api/cards.check
     */

    public function checkCard()
    {
        $method = 'cards.check';
        $client = new Client(['base_uri' => 'https://checkout.test.paycom.uz']);

        $response = $client->request('POST', '/api', [
            'headers' => [
                'X-Auth' => '5f2fe4c73ddb59936a2e76ec',
            ],
            'json' => [
                "method" => $method,
                "params" => [
                    "token" => $this->token
                ],
            ]
        ]);

        $qwer = $response->getBody();
        $result = json_decode($qwer);

        if (isset($result->result->success) === true) {
            return Az::l('Успешно удален');
        }

        return $result->error->message;

    }
    #endregion

    #region

    /**
     *
     * Function  removeCard
     * @param $token
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author Muminov Umid
     *
     * https://help.paycom.uz/ru/metody-subscribe-api/cards.remove
     */
    public function removeCard($token)
    {
        $method = "cards.remove";
        $client = new Client(['base_uri' => 'https://checkout.test.paycom.uz']);

        $response = $client->request('POST', '/api', [
            'headers' => [
                'X-Auth' => '5f2fe4c73ddb59936a2e76ec',
            ],
            'json' => [
                "method" => $method,
                "params" => [
                    "token" => $token
                ],
            ]
        ]);

        $qwer = $response->getBody();
        $result = json_decode($qwer);

        /*if (isset($result->result->success) == true) {
            return "Успешно удален";
        } else {
            return $result->error->message;
        }*/

        vdd($result);
    }

    #endregion


    #region Checks

    #region createReceipts
    /**
     *
     * Function  createReceipts
     * @param $amount 50 000 kam bosa request bormidi
     * @param $order_id
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author Muminov Umid
     *
     * https://help.paycom.uz/ru/metody-subscribe-api/receipts.create
     */
    public function createReceipts($amount, $order_id)
    {
        $method = 'receipts.create';

        $client = new Client(['base_uri' => $this->baseUri]);

        $response = $client->request('POST', '/api', [
            'headers' => [
                'X-Auth' => '5f2fe4c73ddb59936a2e76ec',
            ],
            'json' => [
                "method" => $method,
                "params" => [
                    "amount" => $amount,
                    "account" => [
                        "order_id" => $order_id
                    ],
                ],
            ]
        ]);

        $qwer = $response->getBody();
        $result = json_decode($qwer);

        return $result->result->receipt;

        //vdd($result);

    }

    #endregion


    #region payReceipts

    /**
     *
     * Function  payReceiptsTest
     * @author Muminov Umid
     *
     * https://help.paycom.uz/ru/metody-subscribe-api/receipts.pay
     */
    public function payReceiptsTest()
    {

    }

    public function payReceipts($token, $phone, $id)
    {
        $method = "receipts.pay";
        $client = new Client(['base_uri' => 'https://checkout.test.paycom.uz']);

        /**
         *
         *
         */

        $response = $client->request('POST', '/api', [
            'headers' => [
                'X-Auth' => '5f2d650cdb2875332a0f133d',
            ],
            'json' => [
                "method" => $method,
                "params" => [
                    "id" => $id,
                    "token" => $token,
                    "payer" => [
                        "phone" => $phone,
                    ]
                ],
            ]
        ]);

        $qwer = $response->getBody();
        $result = json_decode($qwer);

        vdd($result);

    }
    #endregion

    #region sendReceipts

    /**
     *
     * Function  sendReceipts
     * @param $id
     * @param $phone
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author Muminov Umid
     *
     * https://help.paycom.uz/ru/metody-subscribe-api/receipts.send
     */

    public function sendReceipts($id, $phone)
    {
        $method = "receipts.send";
        $client = new Client(['base_uri' => 'https://checkout.test.paycom.uz']);

        $response = $client->request('POST', '/api', [
            'headers' => [
                'X-Auth' => '5f2fe4c73ddb59936a2e76ec',
            ],
            'json' => [
                "method" => $method,
                "params" => [
                    "id" => $id,
                    "phone" => $phone,
                ],
            ]
        ]);

        $qwer = $response->getBody();
        $result = json_decode($qwer);

        //vdd($result);

    }
    #endregion


    #endregion
}
