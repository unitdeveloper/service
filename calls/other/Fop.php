<?php
namespace zetsoft\service\calls;

use PAMI\Client\Impl\ClientImpl;
use PAMI\Message\Action\AgentLogoffAction;
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
use PAMI\Message\Action\CoreShowChannelsAction;
use PAMI\Message\Action\RedirectAction;
use PAMI\Message\Action\HangupAction;
use phpDocumentor\Reflection\Types\This;
use zetsoft\system\kernels\ZFrame;


class Fop  extends ZFrame
{
    #region Vars
    public const ip = [
        '41' => '10.10.3.41',
        '31' => '10.10.3.31',
        '30' => '10.10.3.30'
    ];

    public $ip = self::ip['41'];

    public $scheme = 'tcp://';

    public $port = '5038';

    public $user = [
        'user' => 'amiuser',
        'pass' => 'amiuser'
    ];

    public $timeout = [
        'conn' => 10000,
        'read' => 10000
    ];
    public $statuses;

    public $liveChannels;

    public $connectedChannels;

    public $callerId = '1111';

    public $ext;

    public $filename;

    public $context = 'from-internal';

    public $priority = '1';

    public $async = true;
    public $conference;

    public $agent;
    public $soft;

    public $stateInterface;

    public $interface;

    public $penalty = '0';

    public $eventName = 'Dial';

    public $queue = '1111';
    public $varName;
    public $varValue;
    public $channel;

    private $queAdd;
    private $quepa;
    private $que;
    private $agentlogof;
    private $options;
    private $client;
    private $response;
    private $originate;
    public $orgresp;
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

        $this->extension = '300';
        $status = $this->originate();
        vd($status);

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
    public function close()
    {
        $this->client->close();
    }

    public function originate()
    {
        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->originate = new OriginateAction('SIP/' . $this->ext);

        $this->originate->setCallerId($this->ext);
        $this->originate->setContext($this->context);
        $this->originate->setPriority($this->priority);
        $this->originate->setExtension($this->callerId);

        $this->orgresp = $this->client->send($this->originate);
        $this->orgStatus = $this->orgresp->getKeys();

        $this->client->process();
    }

    public function listen()
    {
        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->originate = new OriginateAction('SIP/' . $this->callerId);

        $this->originate->setCallerId('222' . $this->ext);
        $this->originate->setContext($this->context);
        $this->originate->setPriority($this->priority);
        $this->originate->setExtension('222' . $this->ext);

        $this->orgresp = $this->client->send($this->originate);
        $this->orgStatus = $this->orgresp->getKeys();

        $this->client->process();
    }

    public function bargeIn()
    {
        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->originate = new OriginateAction('SIP/' . $this->callerId);

        $this->originate->setCallerId('224' . $this->ext);
        $this->originate->setContext($this->context);
        $this->originate->setPriority($this->priority);
        $this->originate->setExtension('224' . $this->ext);

        $this->orgresp = $this->client->send($this->originate);
        $this->orgStatus = $this->orgresp->getKeys();

        $this->client->process();
    }

    public function whisper()
    {
        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->originate = new OriginateAction('SIP/' . $this->callerId);

        $this->originate->setCallerId('223' . $this->ext);
        $this->originate->setContext($this->context);
        $this->originate->setPriority($this->priority);
        $this->originate->setExtension('223' . $this->ext);

        $this->orgresp = $this->client->send($this->originate);
        $this->orgStatus = $this->orgresp->getKeys();

        $this->client->process();
    }

    public function hangUp()
    {
        usleep(1000);
        $this->originate = new HangupAction($this->channel);

        $this->orgresp = $this->client->send($this->originate);
        $this->orgStatus = $this->orgresp->getKeys();

        $this->client->process();
    }

    public function redirect()
    {
        usleep(1000);
        $this->originate = new RedirectAction($this->channel, $this->callerId, $this->context, $this->priority);

        $this->orgresp = $this->client->send($this->originate);
        $this->orgStatus = $this->orgresp->getKeys();

        $this->client->process();
    }

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
    public function queueRemove()
    {
        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->queAdd = new queueRemoveAction($this->queue, $this->interface);

        $this->orgresp = $this->client->send($this->queAdd);
        $this->orgStatus = $this->orgresp->getKeys();

        $this->client->process();
    }

    public function queuePause()
    {
        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->quepa = new QueuePauseAction($this->interface, $this->queue, $this->reason = false);

        $this->orgresp = $this->client->send($this->quepa);
        $this->orgStatus = $this->orgresp->getKeys();

        $this->client->process();
    }

    public function queues()
    {
        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->que = new QueuesAction();

        $this->orgresp = $this->client->send($this->que);
        $this->orgStatus = $this->orgresp->getKeys();

        $this->client->process();
    }

    public function coreShowChannels()
    {
        usleep(1000);
        $this->response = new CoreShowChannelsAction();

        $this->orgresp = $this->client->send($this->response);
        $this->orgStatus = $this->orgresp->getEvents();

        $this->client->process();
        foreach ($this->orgStatus as $data){

            $name = $data->getKey('Channel');
            $stat =  $data->getKey('CallerIDNum');
            $this->liveChannels[$stat] =  $name;
        }
    }

    public function showConnectedChannels()
    {
        usleep(1000);
        $this->response = new CoreShowChannelsAction();

        $this->orgresp = $this->client->send($this->response);
        $this->orgStatus = $this->orgresp->getEvents();

        $this->client->process();
        foreach ($this->orgStatus as $data){

            $to = $data->getKey('ConnectedLineNum');
            $from =  $data->getKey('CallerIDNum');
            $this->connectedChannels[$from] =  $to;
        }
    }

    public function showDialPlan()
    {

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->showDialPlan = new ShowDialPlanAction($this->context, $this->ext);

        $this->orgresp = $this->client->send($this->showDialPlan);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();

    }


    public function agentlogoff(){

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->agentlogof = new AgentLogoffAction($this->agent, $this->soft);

        $this->orgresp = $this->client->send($this->agentlogof);
        $this->orgStatus = $this->orgresp->getKeys();

        $this->client->process();


    }

    public function status(){

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->corestatus  = new CoreStatusAction();

        $this->orgresp = $this->client->send($this->corestatus);
        $this->orgStatus = $this->orgresp->getKeys();

        $this->client->process();

    }

    public function sipPeers(){

        usleep(1000);
        $this->sippeer  = new SIPPeersAction();

        $this->orgresp = $this->client->send($this->sippeer);
        $this->orgStatus = $this->orgresp->getEvents();

        $this->client->process();
        foreach ($this->orgStatus as $data){

            $name = $data->getKey('ObjectName');
            $stat =  $data->getKey('status');
            $this->statuses[$name] =  $stat;
        }


    }

    public function sipShowRegistry(){

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->sipshow  = new SIPShowRegistryAction();

        $this->orgresp = $this->client->send($this->sipshow);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();

    }

    public function sipShowPeer(){

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->sipshowp  = new SIPShowPeerAction('SIPshowpeer');

        $this->orgresp = $this->client->send($this->sipshowp);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();
    }

    public function monitor(){

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->monitor  = new MonitorAction('SIP/'. $this->ext, $this->filename);

        $this->orgresp = $this->client->send($this->monitor);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();

    }

    public function stopmonitor(){

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->stopmon  = new stopMonitorAction('SIP/'. $this->ext);

        $this->orgresp = $this->client->send($this->stopmon);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();

    }

    public function meetMeList(){

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->meetmelist  = new MeetmeListAction($this->conference);

        $this->orgresp = $this->client->send($this->meetmelist);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();

    }

    public function setvar(){

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->setvar  = new SetVarAction($this->varName, $this->varValue, 'SIP/'. $this->ext);

        $this->orgresp = $this->client->send($this->setvar);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();

    }

    public function ping(){

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->ping  = new PingAction();

        $this->orgresp = $this->client->send($this->ping);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();

    }

    public function parkedCalls(){

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->parked = new ParkedCallsAction();

        $this->orgresp = $this->client->send($this->parked);
        $this->orgStatus = $this->orgresp->getKeys();
        $this->client->process();

    }
    #endregion
}
