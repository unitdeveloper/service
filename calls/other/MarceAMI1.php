<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\calls\other;


use PAMI\Client\Impl\ClientImpl;
use PAMI\Message\Action\AgentLogoffAction;
use PAMI\Message\Action\ExtensionStateAction;
use PAMI\Message\Action\OriginateAction;
use PAMI\Message\Action\QueueAddAction;
use PAMI\Message\Action\QueueRemoveAction;
use PAMI\Message\Action\QueuePauseAction;
use PAMI\Message\Action\QueuesAction;
use PAMI\Message\Action\ShowDialPlanAction;
use PAMI\Message\Action\StatusAction;
use PAMI\Message\Action\CoreStatusAction;
use PAMI\Message\Action\StopMonitorAction;
use PAMI\Message\Action\SetVarAction;
use PAMI\Message\Action\SIPPeersAction;
use PAMI\Message\Action\SIPShowPeerAction;
use PAMI\Message\Action\SIPShowRegistryAction;
use PAMI\Message\Action\MeetmeListAction;
use PAMI\Message\Action\PingAction;
use PAMI\Message\Action\ParkedCallsAction;
use PAMI\Message\Action\MonitorAction;

use PAMI\Message\Event\AgentConnectEvent;
use PAMI\Message\Event\AgentloginEvent;
use PAMI\Message\Event\AgentsCompleteEvent;
use PAMI\Message\Event\CELEvent;
use PAMI\Message\Event\ChannelUpdateEvent;
use PAMI\Message\Event\DialEvent;
use PAMI\Message\Event\DTMFEvent;
use PAMI\Message\Event\ExtensionStatusEvent;
use PAMI\Message\Event\HangupEvent;
use PAMI\Message\Event\HoldEvent;
use PAMI\Message\Event\JoinEvent;
use PAMI\Message\Event\LeaveEvent;
use PAMI\Message\Event\ListDialPlanEvent;
use PAMI\Message\Event\MusicOnHoldEvent;
use PAMI\Message\Event\NewCalleridEvent;
use PAMI\Message\Event\NewchannelEvent;
use PAMI\Message\Event\NewextenEvent;
use PAMI\Message\Event\NewstateEvent;
use PAMI\Message\Event\OriginateResponseEvent;
use PAMI\Message\Event\ParkedCallEvent;
use PAMI\Message\Event\ParkedCallsCompleteEvent;
use PAMI\Message\Event\PeerEntryEvent;
use PAMI\Message\Event\PeerStatusEvent;
use PAMI\Message\Event\QueueMemberAddedEvent;
use PAMI\Message\Event\QueueMemberEvent;
use PAMI\Message\Event\QueueMemberRemovedEvent;
use PAMI\Message\Event\RegistryEvent;
use PAMI\Message\Event\RenameEvent;
use PAMI\Message\Event\ShowDialPlanCompleteEvent;
use PAMI\Message\Event\StatusCompleteEvent;
use PAMI\Message\Event\StatusEvent;
use PAMI\Message\Event\TransferEvent;
use PAMI\Message\Event\UnlinkEvent;
use PAMI\Message\Event\UnParkedCallEvent;
use PAMI\Message\Event\UserEventEvent;
use PAMI\Message\OutgoingMessage;
use PAMI\Message\Message;
use zetsoft\dbitem\App\eyuf\AutoDialItem;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\user\User;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

class MarceAMI1 extends ZFrame
{
    #region Vars

    public const ip = [
        '41' => '10.10.3.41',
        '31' => '10.10.3.31',
        '30' => '10.10.3.30'
    ];

    public $ip = Self::ip['31'];

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

    public $exts = ['202', '204', '555'];

    public $filename = 'D:/monitor/43jklw.wav';

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

    #endregion

    #region Cores

    public function test()
    {

      /*  $this->extension = '300';
        $status = $this->originate();
        vd($status);*/
        //$this->monitor();
       //$res = $this->extStatus('202', 'from-internal');

      $res = $this->call();
      vdd($res);
    }

    public function init()
    {
        parent::init();

        $this->options = array(
            'host' => $this->ip,
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

    #region Actions

    /**
     *
     * Function  originate
     */

    public function originateOld()
    {
        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->originate = new OriginateAction('SIP/' . $this->ext);
        $this->originate->setCallerId($this->callerId);
        $this->originate->setContext($this->context);
        $this->originate->setPriority($this->priority);
        $this->originate->setExtension($this->callerId);

        $this->orgresp = $this->client->send($this->originate);
        $this->orgStatus = $this->orgresp->getKeys();

        $this->client->process();
    }


    public function originate($number)
    {
     //   usleep(1000);
        $this->originate = new OriginateAction('SIP/' . $number->client);
        $this->originate->setCallerId($number->order_id);
        $this->originate->setContext($this->context);
        $this->originate->setPriority($this->priority);
        $this->originate->setExtension($number->operator);
        $this->orgresp = $this->client->send($this->originate);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();
       // $this->client->close();
    }



    // auto dial
    public function callAgent()
    {
        $calls = [];
         if(empty($this->agentId))
         {
             return false;
         }else
         {
            $agentNumber = User::findOne($this->userIdentity()->id);
            $shopOrders = ShopOrder::find()
                ->where([
                    'operator' => $this->agentId,
                    'status_callcenter' => ShopOrder::status_callcenter['ring']
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
    }




    public function call()
    {
        if(!$this->callAgent())
            return false;
            
        $numbers = $this->callAgent();
        while(true)
        {
            if(empty($numbers))
                die();
            
            foreach ($numbers as $key => $number)
            {
                $status = $this->extStatus($number->operator, 'from-internal');
                vd($status.' status');
                $status = (int)$status;


                switch (true){
                    case $status === 0:
                        $this->originate($number);
                           vd($number->client);
                                vd('called');
                        unset($numbers[$key]);
                    break;
                    case $status === -1 || $status === 4:
                    //-1 Unavailable - Related device(s) are not reachable.
                    //4 The extension's hint was removed from the dialplan.
                        die();
                    break;
                }
                /*if ($status === 0){
                       $this->originate($number);
                            vd($number->client);
                                    vd('called');
                                      unset($numbers[$key]);
                }*/
            }
        }
    }


    /**
     *
     * Function  autoDial
     * @return  mixed
     */
    public function autoDial()
    {
      $numbers = ['201', '204'];
        foreach ($numbers as $number){
           $this->response = $this->client->send(new StatusAction());
               $this->originate = new OriginateAction('SIP/' . $number);
               $this->originate->setCallerId($this->callerId);
               $this->originate->setContext($this->context);
               $this->originate->setPriority($this->priority);
               $this->originate->setExtension($this->callerId);
               $this->originate->setAsync(true);
               $this->orgresp = $this->client->send($this->originate);
               $this->orgStatus = $this->orgresp->getKeys();
               echo $number;
           }
           return $this->orgStatus;
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
    public function agentlogoff(){

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
    public function status(){

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->orgresp = $this->client->send($this->response);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();
        return $this->orgStatus;
    }


    public function extStatus($ext, $context){
        $this->response = $this->client->send(new ExtensionStateAction($ext, $context));
         $this->orgStatus = $this->response->getKeys();
         return $this->orgStatus['status'];
    }


    /**
     *
     * Function  sipPeers
     */
    public function sipPeers(){

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->sippeer  = new SIPPeersAction();

        $this->orgresp = $this->client->send($this->sippeer);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();

    }

    public function sipPeers1(){
        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $ext  = new SIPPeersAction();
        $this->orgresp = $this->client->send($ext);
        return  $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();
    }

    /**
     *
     * Function  sipShowRegistry
     */
    public function sipShowRegistry(){

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->sipshow  = new SIPShowRegistryAction();

        $this->orgresp = $this->client->send($this->sipshow);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();

    }

    /**
     *
     * Function  sipShowPeer
     */
    public function sipShowPeer(){

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->sipshowp  = new SIPShowPeerAction('SIPshowpeer');

        $this->orgresp = $this->client->send($this->sipshowp);
        $this->orgStatus = $this->orgresp->getKeys();
        vdd($this->orgStatus);
        $this->client->process();
    }

    /**
     *
     * Function  monitor
     */
    public function monitor(){

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->monitor  = new MonitorAction('SIP/'. $this->ext, $this->filename);

        $this->orgresp = $this->client->send($this->monitor);
        $this->stopmonitor();
        $this->orgStatus = $this->orgresp->getKeys();

        $this->client->process();

    }

    /**
     *
     * Function  stopmonitor
     */
    public function stopmonitor(){

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->stopmon  = new stopMonitorAction('SIP/'. $this->ext);

        $this->orgresp = $this->client->send($this->stopmon);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();

    }

    /**
     *
     * Function  meetMeList
     */
    public function meetMeList(){

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->meetmelist  = new MeetmeListAction($this->conference);

        $this->orgresp = $this->client->send($this->meetmelist);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();

    }

    /**
     *
     * Function  setvar
     */
    public function setvar(){

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->setvar  = new SetVarAction($this->varName, $this->varValue, 'SIP/'. $this->ext);
        $this->orgresp = $this->client->send($this->setvar);
        $this->orgStatus = $this->orgresp->getKeys();

        $this->client->process();
    }

    /**
     *
     * Function  ping
     */
    public function ping(){

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->ping  = new PingAction();

        $this->orgresp = $this->client->send($this->ping);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();

    }

    /**
     *
     * Function  parkedCalls
     */
    public function parkedCalls(){

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->parked = new ParkedCallsAction();

        $this->orgresp = $this->client->send($this->parked);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();

    }
    #endregion

    public function close()
    {
        $this->client->close();
    }



}
