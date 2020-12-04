<?php

/**
 *
 * Author:  Xolmat Ravshanov
 *
 */

namespace zetsoft\service\calls;


use PAMI\Client\Impl\ClientImpl;
use PAMI\Message\Action\AgentLogoffAction;
use PAMI\Message\Action\AgentsAction;
use PAMI\Message\Action\AttendedTransferAction;
use PAMI\Message\Action\BlindTransferAction;
use PAMI\Message\Action\BridgeAction;
use PAMI\Message\Action\BridgeInfoAction;
use PAMI\Message\Action\ChallengeAction;
use PAMI\Message\Action\ChangeMonitorAction;
use PAMI\Message\Action\ConfbridgeListAction;
use PAMI\Message\Action\ConfbridgeMuteAction;
use PAMI\Message\Action\ConfbridgeUnmuteAction;
use PAMI\Message\Action\CoreSettingsAction;
use PAMI\Message\Action\CoreShowChannelsAction;
use PAMI\Message\Action\CoreStatusAction;
use PAMI\Message\Action\CommandAction;
use PAMI\Message\Action\CreateConfigAction;
use PAMI\Message\Action\DBDelAction;
use PAMI\Message\Action\DBDelTreeAction;
use PAMI\Message\Action\DBGetAction;
use PAMI\Message\Action\DBPutAction;
use PAMI\Message\Action\DongleReloadAction;
use PAMI\Message\Action\DongleResetAction;
use PAMI\Message\Action\DongleRestartAction;
use PAMI\Message\Action\DongleSendPDUAction;
use PAMI\Message\Action\DongleSendSMSAction;
use PAMI\Message\Action\DongleSendUSSDAction;
use PAMI\Message\Action\DongleShowDevicesAction;
use PAMI\Message\Action\DongleStartAction;
use PAMI\Message\Action\DongleStopAction;
use PAMI\Message\Action\GetConfigAction;
use PAMI\Message\Action\GetConfigJSONAction;
use PAMI\Message\Action\HangupAction;
use PAMI\Message\Action\ListCategoriesAction;
use PAMI\Message\Action\ListCommandsAction;
use PAMI\Message\Action\LocalOptimizeAwayAction;
use PAMI\Message\Action\LoginAction;
use PAMI\Message\Action\LogoffAction;
use PAMI\Message\Action\MixMonitorAction;
use PAMI\Message\Action\MixMonitorMuteAction;
use PAMI\Message\Action\ModuleCheckAction;
use PAMI\Message\Action\ModuleLoadAction;
use PAMI\Message\Action\ModuleReloadAction;
use PAMI\Message\Action\ModuleUnloadAction;
use PAMI\Message\Action\OriginateAction;
use PAMI\Message\Action\ParkAction;
use PAMI\Message\Action\PauseMonitorAction;
use PAMI\Message\Action\PlayDTMFAction;
use PAMI\Message\Action\QueueAddAction;
use PAMI\Message\Action\QueueLogAction;
use PAMI\Message\Action\QueuePenaltyAction;
use PAMI\Message\Action\QueueReloadAction;
use PAMI\Message\Action\QueueRemoveAction;
use PAMI\Message\Action\QueuePauseAction;
use PAMI\Message\Action\QueueResetAction;
use PAMI\Message\Action\QueueRuleAction;
use PAMI\Message\Action\QueuesAction;
use PAMI\Message\Action\QueueStatusAction;
use PAMI\Message\Action\QueueSummaryAction;
use PAMI\Message\Action\QueueUnpauseAction;
use PAMI\Message\Action\StopMonitorAction;
use PAMI\Message\Action\PingAction;
use PAMI\Message\Action\ParkedCallsAction;
use PAMI\Message\Action\MonitorAction;

use PAMI\Message\Action\UnpauseMonitorAction;
use PAMI\Message\Action\UpdateConfigAction;
use yii\helpers\ArrayHelper;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;


class Fop extends ZFrame
{

    #region Vars
    public const ip = [
        '41' => '10.10.3.41',
        '31' => '10.10.3.31',
        '30' => '10.10.3.30',
        '153' => '10.10.3.153',
        '60' => '10.10.3.60'
    ];

    public $ip = Self::ip['60'];

    private $scheme = 'tcp://';

    public $port = '5038';

    private $user = [
        'user' => 'amiuser',
        'pass' => 'amiuser'
    ];

    private $timeout = [
        'conn' => 999999999999999999999,
        'read' => 999999999999999999999
    ];

    public $callerId = '1111';

    public $ext = '204';


    public $context = 'from-internal';
    public $priority = '1';

    public $async = true;
    public $conference;

    public $agentId;
    public $agent;
    public $soft;

    public $stateInterface;

    public $interface;

    public $penalty = '0';

    public $eventName = 'Dial';

    public $queue = '1111';
    public $varName;
    public $varValue;
    private $options;
    private $client;
    public $hangup;
    private $response;
    private $originate;
    private $orgresp;
    private $orgStatus;
    public $memberName;
    public $paused = false;
    public $blindtransfer;

    public $corestatus;
    public $sippeer;
    public $sipshow;
    public $db;
    public $sipshowp;
    public $stopmon;
    public $dongle;
    public $command;
    public $login;
    public $monitor;
    public $setvar;
    public $listCommands;
    public $meetmelist;
    public $showDialPlan;
    public $queuepaus;
    public $ping;
    public $parked;
    public $configFile;

    public $factoryCre;
    public $reason;
    public $time = 30;
    public $sleepTime = 30;
    public $beforeSleepTime = 30;
    public $afterSleepTime = 30;
    public $liveChannels = [];
    public $remoteIp;
    public $currentIp;
    public $cmdLine = '"d:/Develop/Projects/ALL/server/php7/7_44/php.exe" D:/Develop/Projects/ALL/asrorz/zetsoft/excmd/asrorz.php caller/marce-ami/run --agentId=';
    #endregion


    #region Cores
    public function init()
    {
        parent::init();

        if (!empty($this->remoteIp))
            $this->currentIp = $this->remoteIp;
        else
            $this->currentIp = $this->ip;

        $this->options = array(
            'host' => $this->currentIp,
            'scheme' => $this->scheme,
            'port' => $this->port,
            'username' => $this->user['user'],
            'secret' => $this->user['pass'],
            'connect_timeout' => $this->timeout['conn'],
            'read_timeout' => $this->timeout['read']
        );
        $this->client = new ClientImpl($this->options);
        $this->client->open();
    }
    #endregion

    #region Test
    public function test()
    {


        $res = $this->getConfig('extensions.conf');
        file_put_contents('C:/Users/xolmat.ravshanov/Desktop/conf.txt', $res);

        vdd($res);
    }
    #endregion

    /**
     *
     * Function  originate
     * @param $ext local number
     * @param $caller  tashqaridagi number
     * @param $callerId caller number
     * @param string $context context ex: from-internal, from-trunck
     * @param string $priority 1
     * @return  mixed
     */

    public function originate($ext, $caller, $callerId, $context = 'from-internal', $priority = '1')
    {
        $this->originate = new OriginateAction('SIP/' . $ext);
        $this->originate->setCallerId($callerId);
        $this->originate->setContext($context);
        $this->originate->setPriority($priority);
        $this->originate->setExtension($caller);
        $this->orgresp = $this->client->send($this->originate);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();
        return $this->orgresp;
    }


    /**
     *
     * Function  getLiveExtention
     * @param string $ext
     * @return  mixed
     * live
     */

    public function getLiveExtention($ext)
    {
        usleep(1000);
        $this->response = new CoreShowChannelsAction();

        $this->orgresp = $this->client->send($this->response);
        $this->orgStatus = $this->orgresp->getEvents();
        foreach ($this->orgStatus as $data) {
            $name = $data->getKey('Channel');
            $stat = $data->getKey('CallerIDNum');
            $this->liveChannels[$stat] = $name;
        }
        $this->client->process();
        if (ZArrayHelper::keyExists($ext, $this->liveChannels))
            return $this->liveChannels[$ext];
        return false;
    }

    /**
     * Function  hangup
     * @param $ext
     * desc  hangup qilish ext uchun extention live bo'lishi kerak
     * getLiveExtention($ext) full channelni qaytaradi
     */

    public function hangup($ext)
    {
        $this->hangup = new HangupAction($this->getLiveExtention($ext), 16);
        $res = $this->client->send($this->hangup);
        $this->client->process();
        return $res;
    }

    /**
     *
     * Function  CurrentCalls
     * @return  int current Calls live
     * @desc  hozir bo'layotgan caller soni qaytaradi // int
     *
     */

    public function currentCalls()
    {
        usleep(1000);
        $this->response = new CoreStatusAction();
        $this->orgresp = $this->client->send($this->response);
        $this->orgStatus = $this->orgresp->getEvents();
        $this->client->process();
        return $this->orgresp->getKeys()['corecurrentcalls'];
    }

    #region conference
    public function conference()
    {
        $this->conference = new ConfbridgeListAction(1111);
        $response = $this->client->send($this->conference);
        $this->client->process();
        return $response;
    }

    public function conferenceMute($channel, $conference)
    {
        $this->conference = new ConfbridgeMuteAction($channel, $conference);
        $response = $this->client->send($this->conference);
        $this->client->process();
        return $response;
    }

    public function conferenceUnMute($channel, $conference)
    {
        $this->conference = new ConfbridgeUnmuteAction($channel, $conference);
        $response = $this->client->send($this->conference);
        $this->client->process();
        return $response;
    }

    #endregion

    /**
     * Function  agents
     * @return  mixed  list of agents
     */
    #region Agent
    public function agents()
    {
        $this->agent = new AgentsAction();
        $response = $this->client->send($this->agent);
        $this->client->process();
        return $response;
    }

    public function agentLogOff($ext, $soft = true)
    {
        $this->agent = new AgentLogoffAction($ext, $soft);
        $response = $this->client->send($this->agent);
        $this->client->process();
        return $response;
    }

    public function loginExample()
    {
        $user = '111';
        $password = '111';
        return $this->login($user, $password);
    }

    public function login($user, $password, $eventMask = null)
    {
        $this->login = new LoginAction($user, $password, $eventMask);
        $response = $this->client->send($this->login);
        $this->client->process();
        return $response;
    }

    public function logOffExample()
    {
        $this->logOff();
    }

    public function logOff()
    {
        $this->login = new LogoffAction();
        $response = $this->client->send($this->login);
        $this->client->process();
        return $response;
    }
    #endregion


    #region Transfer
    public function attendedTransfer($channel, $extension, $context, $priority)
    {
        $this->transfer = new AttendedTransferAction($channel, $extension, $context, $priority);
        $response = $this->client->send($this->transfer);
        $this->client->process();
        return $response;
    }

    public function blindTransfer($channel, $extension, $context)
    {
        $this->transfer = new BlindTransferAction($channel, $extension, $context);
        $response = $this->client->send($this->transfer);
        $this->client->process();
        return $response;
    }
    #endregion

    #region Bridge
    /**
     *   $tone   Play courtesy tone to Channel2
     */
    public function bridge($channel1, $channel2, $tone = false)
    {
        $this->bridge = new BridgeAction($channel1, $channel2, $tone);
        $response = $this->client->send($this->bridge);
        $this->client->process();
        return $response;
    }

    public function bridgeInfo($bridgeUniqueid)
    {
        $this->bridge = new BridgeInfoAction($bridgeUniqueid);
        $response = $this->client->send($this->bridge);
        $this->client->process();
        return $response;
    }


    #endregion

    #region Monitor
    public function changeMonitor($channel, $filename)
    {
        $this->monitor = new ChangeMonitorAction($channel, $filename);
        $response = $this->client->send($this->monitor);
        $this->client->process();
        return $response;
    }

    public function mixMonitor($file)
    {
        $this->monitor = new MixMonitorAction($file);
        $response = $this->client->send($this->monitor);
        $this->client->process();
        return $response;
    }

    public function mixMonitorMute($state)
    {
        $this->monitor = new MixMonitorMuteAction($state);
        $response = $this->client->send($this->monitor);
        $this->client->process();
        return $response;
    }

    /**
     * @param string $channel Channel to monitor.
     * @param string $filename Absolute path to target filename.
     */

    public function monitor($channel, $filename)
    {
        $this->monitor = new MonitorAction($channel, $filename);
        $response = $this->client->send($this->monitor);
        $this->client->process();
        return $response;
    }

    public function pauseMonitor($channel)
    {
        $this->monitor = new PauseMonitorAction($channel);
        $response = $this->client->send($this->monitor);
        $this->client->process();
        return $response;
    }

    public function stopMonitor($channel)
    {
        $this->monitor = new StopMonitorAction($channel);
        $response = $this->client->send($this->monitor);
        $this->client->process();
        return $response;
    }

    public function unpauseMonitor($channel)
    {
        $this->monitor = new UnpauseMonitorAction($channel);
        $response = $this->client->send($this->monitor);
        $this->client->process();
        return $response;
    }

    #endregion

    #region Parked
    public function park($channel1, $channel2, $timeout = false, $lot = false)
    {
        $this->park = new ParkAction($channel1, $channel2, $timeout, $lot);
        $response = $this->client->send($this->park);
        $this->client->process();
        return $response;
    }

    public function parkedCalls($channel1, $channel2, $timeout = false, $lot = false)
    {
        $this->park = new ParkedCallsAction($channel1, $channel2, $timeout, $lot);
        $response = $this->client->send($this->park);
        $this->client->process();
        return $response;
    }



    #endregion

    #region Module
    public function moduleCheck($module)
    {
        $this->module = new ModuleCheckAction($module);
        $response = $this->client->send($this->module);
        $this->client->process();
        return $response;
    }


    public function moduleLoad($module)
    {
        $this->module = new ModuleLoadAction($module);
        $response = $this->client->send($this->module);
        $this->client->process();
        return $response;
    }

    public function moduleReload($module)
    {
        $this->module = new ModuleReloadAction($module);
        $response = $this->client->send($this->module);
        $this->client->process();
        return $response;
    }

    public function moduleUnload($module)
    {
        $this->module = new ModuleUnloadAction($module);
        $response = $this->client->send($this->module);
        $this->client->process();
        return $response;
    }
    #endregion

    #region DB
    public function dbDel($family, $key)
    {
        $this->db = new DBDelAction($family, $key);
        $response = $this->client->send($this->db);
        $this->client->process();
        return $response;
    }

    public function dbDelTree($family, $key = false)
    {
        $this->db = new DBDelTreeAction($family, $key = false);
        $response = $this->client->send($this->db);
        $this->client->process();
        return $response;
    }

    public function dbGetExample()
    {
        $family = 'cidname';
        $key = '';
        return $this->dbGet($family, $key);
    }

    public function dbGet($family, $key)
    {
        $this->db = new DBGetAction($family, $key);
        $response = $this->client->send($this->db);
        $this->client->process();
        return $response;
    }

    public function dbPut($family, $key)
    {
        $this->db = new DBPutAction($family, $key);
        $response = $this->client->send($this->db);
        $this->client->process();
        return $response;
    }

    #endregion

    #region dongle  .

    public function dongleShowDevices()
    {
        $this->dongle = new DongleShowDevicesAction();
        $response = $this->client->send($this->dongle);
        $this->client->process();
        return $response;
    }

    public function dongleStart()
    {
        $this->dongle = new DongleStartAction();
        $response = $this->client->send($this->dongle);
        $this->client->process();
        return $response;
    }

    public function dongleStop($when, $device)
    {
        $this->dongle = new DongleStopAction($when, $device);
        $response = $this->client->send($this->dongle);
        $this->client->process();
        return $response;
    }

    public function dongleReload($when)
    {
        $this->dongle = new DongleReloadAction($when);
        $response = $this->client->send($this->dongle);
        $this->client->process();
        return $response;
    }

    public function dongleReset($device)
    {
        $this->dongle = new DongleResetAction($device);
        $response = $this->client->send($this->dongle);
        $this->client->process();
        return $response;
    }

    public function dongleRestart($when, $device)
    {
        $this->dongle = new DongleRestartAction($when, $device);
        $response = $this->client->send($this->dongle);
        $this->client->process();
        return $response;
    }

    public function dongleSendPDUA($device, $pdu)
    {
        $this->dongle = new DongleSendPDUAction($device, $pdu);
        $response = $this->client->send($this->dongle);
        $this->client->process();
        return $response;
    }

    public function dongleSendSms($device, $number, $message)
    {
        $this->dongle = new DongleSendSMSAction($device, $number, $message);
        $response = $this->client->send($this->dongle);
        $this->client->process();
        return $response;
    }

    public function dongleSendUssd($device, $ussd)
    {
        $this->dongle = new DongleSendUSSDAction($device, $ussd);
        $response = $this->client->send($this->dongle);
        $this->client->process();
        return $response;
    }
    #endregion

    #region Queue
    public function queueAdd($stateInterface)
    {
        $this->queue = new QueueAddAction($stateInterface);
        $response = $this->client->send($this->queue);
        $this->client->process();
        return $response;
    }

    public function queueLog($uniqueId)
    {
        $this->queue = new QueueLogAction($uniqueId);
        $response = $this->client->send($this->queue);
        $this->client->process();
        return $response;
    }


    public function queuePause($interface, $queue = false, $reason = false)
    {
        $this->queue = new QueuePauseAction($interface, $queue, $reason);
        $response = $this->client->send($this->queue);
        $this->client->process();
        return $response;
    }


    public function queuePenalty($interface, $penalty, $queue)
    {
        $this->queue = new QueuePenaltyAction($interface, $penalty, $queue);
        $response = $this->client->send($this->queue);
        $this->client->process();
        return $response;
    }


    public function queueReload(
        $queue = false,
        $members = false,
        $rules = false,
        $parameters = false
    )
    {
        $this->queue = new QueueReloadAction(
            $queue,
            $members,
            $rules,
            $parameters
        );

        $response = $this->client->send($this->queue);
        $this->client->process();
        return $response;
    }


    public function queueRemove($queue, $interface)
    {
        $this->queue = new QueueRemoveAction($queue, $interface);
        $response = $this->client->send($this->queue);
        $this->client->process();
        return $response;
    }


    public function queueReset($queue = false)
    {
        $this->queue = new QueueResetAction($queue);
        $response = $this->client->send($this->queue);
        $this->client->process();
        return $response;
    }


    public function queueRule($rule = false)
    {
        $this->queue = new QueueRuleAction($rule);
        $response = $this->client->send($this->queue);
        $this->client->process();
        return $response;
    }

    public function queues()
    {
        $this->queue = new QueuesAction();
        $response = $this->client->send($this->queue);
        $this->client->process();
        return $response;
    }


    public function queueStatus($queue = false, $member = false)
    {
        $this->queue = new QueueStatusAction($queue, $member);
        $response = $this->client->send($this->queue);
        $this->client->process();
        return $response;
    }


    public function queueSummary($queue = false)
    {
        $this->queue = new QueueSummaryAction($queue);
        $response = $this->client->send($this->queue);
        $this->client->process();
        return $response;
    }


    public function queueUnpause($interface, $queue = false, $reason = false)
    {
        $this->queue = new QueueUnpauseAction($interface, $queue, $reason);
        $response = $this->client->send($this->queue);
        $this->client->process();
        return $response;
    }
    #endregion

    #region Sip


    #endregion

    #region DTMF
    public function playdtmf($channel, $digit)
    {
        $this->play = new PlayDTMFAction($channel, $digit);


        $response = $this->client->send($this->play);
        $this->client->process();
        return $response;
    }
    #endregion

    #region Tools
    public function createConfig()
    {
        $this->configFile = new CreateConfigAction();
        $response = $this->client->send($this->configFile);
        $this->client->process();
        return $response;
    }

    public function getConfig($filename, $category = false)
    {
        $this->configFile = new GetConfigAction($filename, $category);
        $response = $this->client->send($this->configFile);
        $this->client->process();
        return $response;
    }

    public function getConfigJson($filename)
    {
        $this->configFile = new GetConfigJSONAction($filename);
        $response = $this->client->send($this->configFile);
        $this->client->process();
        return $response;
    }

    public function updateConfig()
    {
        $this->configFile = new UpdateConfigAction();


        $response = $this->client->send($this->configFile);
        $this->client->process();
        return $response;

    }

    public function listCategories($file)
    {
        $this->categories = new ListCategoriesAction($file);
        $response = $this->client->send($this->categories);
        $this->client->process();
        return $response;
    }

    public function listCommands()
    {
        $this->listCommands = new ListCommandsAction();
        $response = $this->client->send($this->listCommands);
        $this->client->process();
        return $response;
    }

    public function localOptimizeAway($channel)
    {
        $this->localOptimize = new  LocalOptimizeAwayAction($channel);
        $response = $this->client->send($this->localOptimize);
        $this->client->process();
        return $response;
    }

    public function challenge()
    {
        $this->challenge = new ChallengeAction();
        $response = $this->client->send($this->challenge);
        $this->client->process();
        return $response;
    }

    public function coreSettings()
    {
        $this->settings = new CoreSettingsAction();
        $response = $this->client->send($this->settings);
        $this->client->process();
        return $response;
    }

    public function coreStatus()
    {
        $this->coreStatus = new CoreStatusAction();
        $response = $this->client->send($this->coreStatus);
        $this->client->process();
        return $response;
    }

    public function events()
    {
        $this->events = new EventsAction();
        $response = $this->client->send($this->events);
        $this->client->process();
        return $response;
    }

    public function ping()
    {
        $this->ping = new PingAction();
        $response = $this->client->send($this->ping);
        $this->client->process();
        return $response;
    }

    public function commandExample()
    {
        $command = 'database show';
        $this->command($command);
    }

    public function command($command)
    {
        $this->command = new CommandAction($command);
        $response = $this->client->send($this->command);
        $this->client->process();
        return $response;
    }
    #endregion

    #region Agi
    public function agi($channel, $command, $commandId)
    {
        $this->agi = new AGIAction($channel, $command, $commandId);
        $response = $this->client->send($this->agi);
        $this->client->process();
        return $response;
    }
    #endregion

}
