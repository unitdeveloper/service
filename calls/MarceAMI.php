<?php

/**
 *
 * Author:
 * Xolmat Ravshanov
 * &&
 * Bobur Komiljonov
 */

namespace zetsoft\service\calls;

require Root . '/vendors/netter/phone/vendor/autoload.php';
//require Root . '/vendors/netapp/vendor/autoload.php';

require Root . '/system/consts/PAMI/EventFactoryImpl.php';

use PAMI\Client\Impl\ClientImpl;
use PAMI\Message\Action\AgentLogoffAction;
use PAMI\Message\Action\AgentsAction;
use PAMI\Message\Action\CoreShowChannelsAction;
use PAMI\Message\Action\CoreStatusAction;
use PAMI\Message\Action\CommandAction;
use PAMI\Message\Action\ExtensionStateAction;
use PAMI\Message\Action\OriginateAction;
use PAMI\Message\Action\QueueAddAction;
use PAMI\Message\Action\QueueRemoveAction;
use PAMI\Message\Action\QueuePauseAction;
use PAMI\Message\Action\QueuesAction;
use PAMI\Message\Action\ShowDialPlanAction;
use PAMI\Message\Action\StatusAction;
use PAMI\Message\Action\StopMonitorAction;
use PAMI\Message\Action\SetVarAction;
use PAMI\Message\Action\SIPPeersAction;
use PAMI\Message\Action\SIPShowPeerAction;
use PAMI\Message\Action\SIPShowRegistryAction;
use PAMI\Message\Action\MeetmeListAction;
use PAMI\Message\Action\PingAction;
use PAMI\Message\Action\ParkedCallsAction;
use PAMI\Message\Action\MonitorAction;


use React\EventLoop\Factory;
use zetsoft\dbitem\App\eyuf\AutoDialItem;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\user\User;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;


class MarceAMI extends ZFrame
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

    private const user = [

        60 => [
            'user' => 'amiuser',
            'pass' => 'amiuser'
        ],

        41 => [
            'user' => 'amiuser',
            'pass' => 'amiuser'
        ],

    ];

    private const timeout = [
        'conn' => 999999999999999999999,
        'read' => 999999999999999999999
    ];

    private $user = self::user[60];

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
    private $queAdd;
    private $quepa;
    private $que;
    private $agentlogof;
    private $options;
    private $client;
    private $response;
    private $originate;
    private $orgresp;
    private $orgStatus;
    public $memberName;
    public $paused = false;

    public $corestatus;
    public $sippeer;
    public $sipshow;
    public $sipshowp;
    public $stopmon;
    public $monitor;
    public $setvar;
    public $meetmelist;
    public $showDialPlan;
    public $queuepaus;
    public $ping;
    public $parked;

    public $factoryCre;
    public $reason;
    public $time = 30;
    public $sleepTime = 30;
    public $beforeSleepTime = 30;

    public $afterSleepTime = 7;

    public $liveChannels = [];
    public $remoteIp;
    public $currentIp;
    public $cmdLine = '"d:/Develop/Projects/ALL/server/php7/7_44/php.exe" D:/Develop/Projects/ALL/asrorz/zetsoft/excmd/asrorz.php caller/marce-ami/run --agentId=';
    #endregion

    #region Cores

    public function test()
    {

        /*  $this->extension = '300';
          $status = $this->originate();
          vd($status);*/
        //$this->monitor();
        // $res = $this->extStatus('202', 'from-internal');
        // $res = $this->call();

        // $res = $this->command();
        // $res = $this->currentCalls();

        //$res =  $this->coreShowChannels();
        //$res =  $this->coreShowChannels();
        /** @var current calls $res */
        // $res =  $this->agents();
        // $res =  $this->sipShowPeer();
        //$res =  $this->sipPeers();
        //vdd($res);
        //foreach($res as $r)
        // vdd($r->getKeys('objectname'));
        // vdd($r->getKeys('Channel'));
        // $res = $this->testoriginate();
        //$res =  $this->testCall();
        // vdd($res);

        //$res = $this->originateOld();
        //$res = $this->originateOld111();

    }


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
            'connect_timeout' => self::timeout['conn'],
            'read_timeout' => self::timeout['read']
        );
        $this->client = new ClientImpl($this->options);
        $this->client->open();
    }
    #endregion

    #region Actions


    /**
     * Function  originate
     */
    public function originateOld()
    {

        $this->response = $this->client->send(new StatusAction());
        $this->originate = new OriginateAction('SIP/' . '309');
        $this->originate->setCallerId('934631525');
        $this->originate->setContext('from-internal');
        $this->originate->setPriority('1');
        $this->originate->setExtension('998443212');
        $this->orgresp = $this->client->send($this->originate);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();
        return $this->orgresp;

    }


    public function originateOld111()
    {

        $this->response = $this->client->send(new StatusAction());
        $this->originate = new OriginateAction('SIP/' . '309');
        $this->originate->setApplication('MusicOnHold');
        $this->originate->setData('hello');
        $this->originate->setExtension('934631525');
        $this->orgresp = $this->client->send($this->originate);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();
        return $this->orgresp;

    }


    public function clearNumber($number)
    {

        $number = trim($number);
        $number = strtr($number, [
            '-' => ''
        ]);
        return $number;

    }

    public function testoriginate()
    {
        $autodial = new AutoDialItem();

        $autodial->operator = '405';
        $autodial->client = '305';
        $autodial->order_id = '44';

        $res = Az::$app->calls->marceAMI->originate($autodial);
        vdd($res);

    }

    public function testCall()
    {
        $this->call(124);
    }

    public function originateTest()
    {
        $autodial = new AutoDialItem();
        $autodial->client = '934631525';
        $autodial->order_id = '123';
        $autodial->operator = '301';
        return $this->originate($autodial);
    }


    public function originate($number)
    {
        $this->response = $this->client->send(new StatusAction());
        $this->originate = new OriginateAction('SIP/' . $number->operator);
        $this->originate->setCallerId($number->order_id);
        $this->originate->setContext($this->context);
        $this->originate->setPriority($this->priority);
        $this->originate->setExtension($number->client);
        $this->originate->setAccount($number->order_id);
        $this->orgresp = $this->client->send($this->originate);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();
        return $this->orgStatus['response'];
    }

    public function originateOld2($number)
    {
        $this->response = $this->client->send(new StatusAction());
        $this->originate = new OriginateAction('SIP/' . $number->client);
        $this->originate->setCallerId($number->order_id);
        $this->originate->setContext($this->context);
        $this->originate->setPriority($this->priority);
        $this->originate->setExtension($number->operator);
        $this->originate->setAccount($number->order_id);
        $this->orgresp = $this->client->send($this->originate);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();
        return $this->orgStatus['response'];
    }


    public function originateOld3()
    {
        $this->response = $this->client->send(new StatusAction());
        $this->originate = new OriginateAction('SIP/' . '301');
        $this->originate->setCallerId('301');
        $this->originate->setContext($this->context);
        $this->originate->setPriority($this->priority);
        $this->originate->setExtension('909427076');
        $this->originate->setAccount('301');
        $this->orgresp = $this->client->send($this->originate);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();
        return $this->orgStatus['response'];
    }


    // auto dial
    public function callAgent($agentId)
    {
        $calls = [];

        if (empty($this->agentId))
            return null;

        $agentNumber = User::findOne($this->agentId);

        $shopOrders = ShopOrder::find()
            ->where([
                'operator' => $this->agentId,
                'status_callcenter' => ShopOrder::status_callcenter['autodial']
            ])->all();

        foreach ($shopOrders as $key => $shopOrder) {
            $autodial = new AutoDialItem();
            $autodial->operator = $agentNumber->number;
            $autodial->client = $shopOrder->contact_phone;
            $autodial->order_id = $shopOrder->id;
            $calls[] = $autodial;
        }

        return $calls;
    }


    public function call($agentId)
    {
    
        $numbers = Az::$app->calls->autoDial->callAgent($agentId);

        if (empty($numbers))
            return Az::error($agentId, 'Empty');

        foreach ($numbers as $key => $number) {

            while (true) {

                $agentStatus = User::findOne($agentId);

                $status = $this->extStatus($number->operator, $this->context);

                vd($status . ' Status Extention');
                vd($agentStatus->autodial . 'Autodial');
                vd($agentStatus->status . 'Status');
                $status = (int)$status;


                if ($agentStatus->status !== 'online' || !$agentStatus->autodial || $status !== 0)
                    sleep(2);
                else
                    break;

            }
            
            $statusCall = $this->originate($number);
            $statusCall = strtolower($statusCall);

            vd($statusCall . 'Status call' . $number->client);
            if ($statusCall === 'error') {
                vd($statusCall . 'Inside Error');

                $number->order->status_callcenter = ShopOrder::status_callcenter['ring'];
                $number->order->status_autodial = ShopOrder::status_autodial[$statusCall];
                $number->order->configs->rules = [
                    [validatorSafe]
                ];
                $number->order->columns();
                $number->order->save();

            } else {

                vd($statusCall . 'Inside Success');

                //$number->order->status_callcenter = ShopOrder::status_callcenter['ring'];

                $number->order->configs->rules = [
                    [validatorSafe]
                ];
                $number->order->columns();
                $number->order->status_autodial = ShopOrder::status_autodial[$statusCall];
                $number->order->save();

            }

            vd($number->client . ' called');
            vd($number->client . 'Call ended' . $statusCall);

            while (true) {
                $status = $this->extStatus($number->operator, $this->context);
                $status = (int)$status;
                $agentStatus = User::findOne($agentId);
                vd($status . "STATUS EXENTENTION AFTER ORIGINATE");

                if ($status !== 0 || $agentStatus->status !== 'online' || !$agentStatus->autodial)
                    sleep(1);
                else
                    break;
            }

            vd('________Sleep________');
            sleep($this->afterSleepTime);
            vd('________Sleep________');
        }
    }


    public function restartAutodial()
    {
        $root = Root;
        shell_exec($root . '/scripts/runner/exitx.exe');
        $this->run();
    }

    public function startAutodial()
    {
        $root = Root;
        shell_exec($root . '/scripts/runner/exitx.exe');
        $this->run();
    }

    public function exitAutodial()
    {
        $root = Root;
        shell_exec($root . '/scripts/runner/exitx.exe');
    }


    /*
     * AutoDial
     */
    public function callOldWorkingVersion($agentId)
    {

        $numbers = Az::$app->calls->autoDial->callAgent($agentId);

        if (empty($numbers))
            return Az::error($agentId, 'Empty');


        foreach ($numbers as $key => $number) {

            while (true) {

                $agentStatus = User::findOne($agentId);

                if ($agentStatus->status !== 'online' || !$agentStatus->autodial) {
                    sleep(5);
                } else {
                    $status = $this->extStatus($number->operator, $this->context);
                    vd($status . ' Status extention');

                    $status = (int)$status;
                    if ($status !== 0)
                        sleep(5);
                    else
                        break;
                }
            }


            $statusCall = $this->originate($number);
            $statusCall = strtolower($statusCall);
            vd($statusCall . 'Status call' . $number->client);
            if ($statusCall == 'error') {

                $number->order->status_callcenter = ShopOrder::status_callcenter['ring'];
                $number->order->status_autodial = ShopOrder::status_autodial[$statusCall];
                $number->order->save();

            } else {
                $number->order->status_autodial = ShopOrder::status_autodial[$statusCall];
                $number->order->save();
            }

            vd($number->client . ' called');
            vd($number->client . 'Call ended' . $statusCall);


            vd('________Sleep________');
            sleep($this->sleepTime);
            vd('________Sleep________');

        }
    }


    public function run()
    {
        $ids = User::find()
            ->where([
                'role' => 'agent'
            ])
            ->asArray()
            ->all();

        foreach ($ids as $id) {
        
            $root = Root;
            $php = "d:/Develop/Projects/ALL/server/php7/7_44/php.exe";
            $cmd = "caller/marce-ami/call";
            $arg = "--agentId={id}";

            $line = ' "{php}" {root}/excmd/asrorz.php {cmd} {arg}';

            $arg = strtr($arg, [
                '{id}' => $id['id'],
            ]);

            $line = strtr($line, [
                '{php}' => $php,
                '{root}' => $root,
                '{cmd}' => $cmd,
                '{arg}' => $arg,
            ]);

            Az::$app->utility->execs->execContinous($line, 15);

            vd($id['id']);

        }
    }


    public function runProductionOld()
    {
        $ids = User::find()
            ->where([
                'role' => 'agent'
            ])
            ->asArray()
            ->all();

        foreach ($ids as $id) {

            $file = Root . '/scripts/runner/runx.exe';
            $root = Root;
            $php = "d:/Develop/Projects/ALL/server/php7/7_44/php.exe";
            $cmd = "caller/marce-ami/call";
            $arg = "--agentId={id}";

            $line = ' 15 "{php}" {root}/excmd/asrorz.php {cmd} {arg}';

            $arg = strtr($arg, [
                '{id}' => $id['id'],
            ]);

            $line = strtr($line, [
                '{php}' => $php,
                '{root}' => $root,
                '{cmd}' => $cmd,
                '{arg}' => $arg,
            ]);

            Az::$app->utility->execs->exec($file . $line, App, 'background');

            vd($arg);

        }

    }


    public function runOld1()
    {
        $ids = User::find()
            ->where([
                'role' => 'agent'
            ])
            ->asArray()
            ->all();

        foreach ($ids as $id) {


            $file = Root . '\scripts\runner\runx.exe';
            vd($file);

            /*Az::$app->utility->execs->exec($file.' 30 "d:/Develop/Projects/ALL/server/php7/7_44/php.exe" "D:/Develop/Projects/ALL/asrorz/zetsoft/excmd/asrorz.php" caller/marce-ami/call --agentId='.$id['id'], true);*/


            Az::$app->utility->execs->exec($file . ' 30 "D:/Develop/Projects/ALL/server/php7/7_44/php.exe" "D:/Develop/Projects/ALL/asrorz/zetsoft/excmd/asrorz.php" caller/marce-ami/call --agentId=' . '124', false);

            vd($id['id']);

        }

    }


    public function start()
    {
        $file = Root . '/scripts/runner/runx.exe';
        Az::$app->utility->execs->exec($file . ' " 3 d:/Develop/Projects/ALL/server/php7/7_44/php.exe" D:/Develop/Projects/ALL/asrorz/zetsoft/excmd/asrorz.php caller/marce-ami/run', true);

    }


    public function runold()
    {
        $loop = Factory::create();
        $loop->addPeriodicTimer($this->time, function (\React\EventLoop\TimerInterface $timer) use ($loop) {

            $ids = User::find()
                ->where([
                    'role' => 'agent',
                    'autodial' => true
                ])
                ->asArray()
                ->all();

            foreach ($ids as $id) {
                $file = Root . '/scripts/runner/Call.exe';

                Az::$app->utility->execs->exec($file . ' "d:/Develop/Projects/ALL/server/php7/7_44/php.exe" D:/Develop/Projects/ALL/asrorz/zetsoft/excmd/asrorz.php caller/marce-ami/run --agentId=' . $id['id'], true);

                vd($id['id']);
            }
        });

        $loop->run();
    }


    public function call222($agentId)
    {
        $numbers = Az::$app->calls->autoDial->callAgent1();
        //$numbers = Az::$app->calls->autoDial->callAgent($agentId);

        if (empty($numbers))
            return Az::error($agentId, 'called all numbers that you have selected');

        foreach ($numbers as $key => $number) {

            while (true) {

                $status = $this->extStatus($number->operator, 'from-internal');
                vd($status . ' status');
                $status = (int)$status;

                if ($status !== 0)
                    sleep(5);
                else
                    break;

            }

            $statusCall = $this->originate($number);
            $statusCall = strtolower($statusCall);
            //$number->order->status_autodial = ShopOrder::status_autodial[$statusCall];
            //$number->order->save();

            vd($statusCall);
            vd($number->client);
            vd('called');


        }

    }


    public function call11($agentId)
    {
        $numbers = Az::$app->calls->autoDial->callAgent1();
        //$numbers = Az::$app->calls->autoDial->callAgent($agentId);

        if (empty($numbers))
            return Az::error($agentId, 'called all numbers that you have selected');

        foreach ($numbers as $key => $number) {
            while (true) {
                $status = $this->extStatus($number->operator, 'from-internal');
                $status = (int)$status;

                if ($status === 0) {
                    $statusCall = $this->originate($number);
                    vd($statusCall);
                    vd($number->client);
                    vd('called');
                    vd($status . ' status');
                    unset($numbers[$key]);
                    break;
                } else {
                    sleep(1);
                    vd('status 0 teng emas');
                }

            }

            // //$number->order->status_autodial = ShopOrder::status_autodial['dial-success'];
            // //$number->order->save();

        }

    }


    /*
     * old version works stable
     */

    public function callOld1()
    {

        $numbers = Az::$app->calls->autoDial->callAgent1();

        //$numbers = Az::$app->calls->autoDial->callAgent($agentId);

        while (true) {
            if (empty($numbers))
                die('called all numbers that you have selected');


            foreach ($numbers as $key => $number) {
                $status = $this->extStatus($number->operator, 'from-internal');
                vd($status . ' status');
                $status = (int)$status;

                switch (true) {
                    case $status === 0:
                        $this->originate($number);
                        vd($number->client);
                        vd('called');
                        unset($numbers[$key]);

                        /*$number->order->status_autodial = ShopOrder::status_autodial['dial-success'];
                        $number->order->save();
                        */

                        break;
                    case $status === -1 || $status === 4:
                        die();
                        break;
                }

            }

            sleep(5 * 60);
        }

    }


    public function callOld()
    {
        $numbers = Az::$app->calls->autoDial->callAgent1();
        //$numbers = Az::$app->calls->autoDial->callAgent($agentId);
        while (true) {
            if (empty($numbers))
                die('called all numbers that you have selected');

            foreach ($numbers as $key => $number) {
                $status = $this->extStatus($number->operator, 'from-internal');
                vd($status . ' status');
                $status = (int)$status;

                switch (true) {
                    case $status === 0:
                        vd($number->client);
                        vd('called');
                        $this->originate($number);
                        /*   $this->response = $this->client->send(new StatusAction());
                           $this->orgresp = $this->client->send($this->response);
                           $this->orgStatus = $this->orgresp->getKeys();
                           vd($this->orgStatus);*/
                        unset($numbers[$key]);
                        /* $order = ShopOrder::findOne($number->order_id);
                         $order->status_autodial = ShopOrder::status_autodial['dial-success'];
                         $order->order->save();*/
                        break;
                    case $status === -1 || $status === 4:
                        die();
                        break;
                }

            }
        }

    }


    public function coreShowChannels()
    {
        usleep(1000);
        $this->response = new CoreShowChannelsAction();

        $this->orgresp = $this->client->send($this->response);
        $this->orgStatus = $this->orgresp->getEvents();
        foreach ($this->orgStatus as $data) {

            $name = $data->getKey('Channel');
            $stat = $data->getKey('CallerIDNum');
            $this->liveChannels[$stat] = $name;
            //$this->liveChannels[] = $stat;
        }
        $this->client->process();
        return $this->liveChannels;
    }

    /**
     *
     * Function  CurrentCalls
     * @return  int current Calls live
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

    /**
     *
     * Function  queueAdd
     */
    public function queueAdd()
    {
        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->queAdd = new QueueAddAction($this->queue, $this->interface);

        $this->queAdd->setStateInterface($this->stateInterface);
        $this->queAdd->setPenalty($this->penalty);
        $this->queAdd->setMemberName($this->memberName);
        $this->queAdd->setPaused($this->paused);

        $this->orgresp = $this->client->send($this->queAdd);
        $this->orgStatus = $this->orgresp->getKeys();

        $this->client->process();
    }


    /**
     *
     * Function  queueRemove
     */
    public function queueRemove()
    {
        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->queAdd = new queueRemoveAction($this->queue, $this->interface);

        $this->orgresp = $this->client->send($this->queAdd);
        $this->orgStatus = $this->orgresp->getKeys();

        $this->client->process();
    }

    /**
     *
     * Function  queuePause
     */
    public function queuePause()
    {
        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->quepa = new QueuePauseAction($this->interface, $this->queue, $this->reason = false);

        $this->orgresp = $this->client->send($this->quepa);
        $this->orgStatus = $this->orgresp->getKeys();

        $this->client->process();
    }

    /**
     *
     * Function  queues
     */
    public function queues()
    {
        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->que = new QueuesAction();

        $this->orgresp = $this->client->send($this->que);
        $this->orgStatus = $this->orgresp->getKeys();

        $this->client->process();
    }

    /**
     *
     * Function  showDialPlan
     */
    public function showDialPlan()
    {

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->showDialPlan = new ShowDialPlanAction($this->context, $this->ext);

        $this->orgresp = $this->client->send($this->showDialPlan);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();

    }

    /**
     *
     * Function  agentlogoff
     */
    public function agentlogoff()
    {

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->agentlogof = new AgentLogoffAction($this->agent, $this->soft);

        $this->orgresp = $this->client->send($this->agentlogof);
        $this->orgStatus = $this->orgresp->getKeys();

        $this->client->process();


    }

    /**
     *
     * Function  status
     * @return  mixed
     */
    public function status()
    {

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->orgresp = $this->client->send($this->response);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();
        return $this->orgStatus;
    }


    public function extStatus($ext, $context)
    {
        if (empty($ext))
            return null;

        $this->response = $this->client->send(new ExtensionStateAction($ext, $context));
        $this->orgStatus = $this->response->getKeys();
        return $this->orgStatus['status'];
    }


    /**
     *
     * Function  sipPeers
     */
    public function sipPeers()
    {

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->sippeer = new SIPPeersAction();

        $this->orgresp = $this->client->send($this->sippeer);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();
        return $this->orgresp;

    }

    /**
     *
     * Function  sipShowRegistry
     */
    public function sipShowRegistry()
    {

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->sipshow = new SIPShowRegistryAction();

        $this->orgresp = $this->client->send($this->sipshow);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();

    }

    /**
     *
     * Function  sipShowPeer
     */
    public function sipShowPeer()
    {

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->sipshowp = new SIPShowPeerAction('SIPshowpeer');

        $this->orgresp = $this->client->send($this->sipshowp);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();
        return $this->orgStatus;
    }

    /**
     *
     * Function  monitor
     */
    public function monitor()
    {

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->monitor = new MonitorAction('SIP/' . $this->ext, $this->filename);

        $this->orgresp = $this->client->send($this->monitor);
        $this->stopmonitor();
        $this->orgStatus = $this->orgresp->getKeys();

        $this->client->process();

    }

    /**
     *
     * Function  stopmonitor
     */
    public function stopmonitor()
    {

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->stopmon = new stopMonitorAction('SIP/' . $this->ext);

        $this->orgresp = $this->client->send($this->stopmon);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();

    }

    /**
     *
     * Function  meetMeList
     */
    public function meetMeList()
    {

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->meetmelist = new MeetmeListAction($this->conference);

        $this->orgresp = $this->client->send($this->meetmelist);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();

    }

    /**
     *
     * Function  setvar
     */
    public function setvar()
    {

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->setvar = new SetVarAction($this->varName, $this->varValue, 'SIP/' . $this->ext);
        $this->orgresp = $this->client->send($this->setvar);
        $this->orgStatus = $this->orgresp->getKeys();

        $this->client->process();
    }

    /**
     *
     * Function  ping
     */
    public function ping()
    {

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->ping = new PingAction();

        $this->orgresp = $this->client->send($this->ping);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();

    }

    public function agents()
    {

        usleep(1000);

        $agents = new AgentsAction();

        $this->orgresp = $this->client->send($agents);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();
        return $this->orgStatus;
    }


    public function command()
    {
        $agents = new CommandAction();
        $this->orgresp = $this->client->send($agents);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();
        return $this->orgresp;
    }

    /**
     *
     * Function  parkedCalls
     */
    public function parkedCalls()
    {

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->parked = new ParkedCallsAction();

        $this->orgresp = $this->client->send($this->parked);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();

    }

}
