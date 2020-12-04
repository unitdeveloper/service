<?php


namespace zetsoft\service\calls;


use zetsoft\system\kernels\ZFrame;
use Clue\React\Ami\Factory;
use Clue\React\Ami\Client;
use Clue\React\Ami\ActionSender;
use Clue\React\Ami\Protocol\Response;
use Clue\React\Ami\Protocol\Event;
class ReactAmiF_Jamoliddin extends ZFrame
{
    #region Vars

    public const ip = [
        '41' => '10.10.3.41',
        '30' => '10.10.3.30'
    ];

    public $ip = Self::ip['41'];

    public $user = [
        'user' => 'amiuser',
        'pass' => 'amiuser'
    ];
    public $callerId = '777';
    public $ext = '204';
    public $port = '5038';
    public $exts = ['202', '203', '202','205'];
    public $context = 'from-internal';
    public $channel;
    public $username;
    public $secret;
    public $variable;
    public $value;
    public $file;
    public $format;
    public $mix;
    public $peer ='204';
    public $mixMonitorId;
    public $priority = '1';
    public $caller;
    public $async = true;
    public $client;
    public $agent = '202';
    public $soft = true;
    public $command;
    public $conf = [];
    public $eventName;
    public $actionG;
    public $memberName;
    public $action;
    public $sender;
    public $factoryCre;
    public $loop;
    public $factory;
    public $promise;
    public $authtype;
    public $operation;
    public $filter;
    public $filename;
    public $category;
    public $queue = '1111';
    public $members;
    public $member;
    public $rules;
    public $parametres;
    public $interface;
    public $rule;
    public $reason;
    public $event;
    public $message;
    public $penalty;
    public $paused;
    public $membername;
    public $stateInterface;
    public $ringinuse;
    public $allvariables = true;
    public $variables = [];
    #endregion

    #region Cores
    public function limit(array $ids,int $limit){
        $length= count($ids);
          $end=0;
        while($length>=0){
        $up=[];
          for($i=$end; $i<$limit; $i++){
             $up[]=$ids[$i];
             $end=$i;
          }
          $end++;
          vd($end);
           $this->exts=$up;
          $this->originate();
          unset($up);
           $length-=$limit;
        }

    }
    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        $this->loop = \React\EventLoop\Factory::create();
        $this->factory = new Factory($this->loop);
        $this->factoryCre = $this->factory->createClient($this->user['user'] . ':' . $this->user['pass'] . '@' . $this->ip . ':' . $this->port);

    }
    #endregion

    #region Action

    public function originate()
    {

        $this->factoryCre->then(function (Client $client) {
            $this->sender = new ActionSender($client);
            foreach ($this->exts as $ext){
                $this->action = $client->createAction('Originate', array(
                    'Channel' => 'SIP/' . $ext,
                    'Callerid' => $this->callerId,
                    'Context' => $this->context,
                    'Exten' => $this->callerId,
                    'Priority' => $this->priority,
                    'Async' => $this->async),
                );
            $this->promise = $client->request($this->action);

              
        }
$client->on('close', function (Event $event) {vd($event);});
        });

        $this->loop->run();
    }

    public function getStatusClient()
    {
    $this->factoryCre->then(function (Client $client) {

        $this->action = $client->createAction('Status');
        $this->promise = $client->request($this->action)->then(function (\Clue\React\Ami\Protocol\Response $response) {
            echo 'Result:' . PHP_EOL;
            vdd($response);
        });

    });
    $this->loop->run();
}

    public function originateMultiple()
    {
        $this->factoryCre->then(function (Client $client) {
            echo 'Client connected...' . PHP_EOL;
            foreach ($this->exts as $num) {
                $this->sender = new ActionSender($client);
                $this->action = $client->createAction('Originate', array(
                    'Channel' => 'SIP/' . $num,
                    'Callerid' => $this->callerId,
                    'Context' => $this->context,
                    'Exten' => $this->callerId,
                    'Priority' => $this->priority,
                    'MyTask' => $this->async
                ));
                $this->promise = $client->request($this->action);
                echo $num;
            }
        });
    }

    public function showCommands()
    {

        $this->factoryCre->then(function (Client $client) {
            $this->sender = new \Clue\React\Ami\ActionSender($client);
            $this->sender->listCommands()->then(function (\Clue\React\Ami\Protocol\Response $response) {
                echo 'Available commands:' . PHP_EOL;
                var_dump($response);
            });
        });

    }

    public function getStatus()
    {
        $this->factoryCre->then(function (Client $client) {
            $this->sender = new ActionSender($client);
            $this->action = $client->createAction('ExtensionState', array(
                'ActionId' => uniqid(),
                'Exten' => $this->ext,
                'Context' => $this->context
            ));

            $this->promise = $client->request($this->action);
            $this->promise->then(function (\Clue\React\Ami\Protocol\Response $response) {
                echo 'Result:' . PHP_EOL;
                return $response;
            });
        });


    }

    public function queueAdd()
    {
        $this->factoryCre->then(function (Client $client) {
            $this->sender = new ActionSender($client);
            $this->action = $client->createAction('QueueAdd', array(
                'ActionID' => uniqid(),
                'Queue' => $this->queue,
                'Interface' => $this->interface,
                'MemberName' => $this->memberName,
                'Penalty' => $this->penalty,
                'Paused' => $this->paused,
                'StateInterface' => $this->stateInterface
            ));

            $this->promise = $client->request($this->action);
            $this->promise->then(function (\Clue\React\Ami\Protocol\Response $response) {
                echo 'Result:' . PHP_EOL;
                var_dump($response);
            });
        });
    }

    public function queueChangePriorityCaller()
    {
        $this->factoryCre->then(function (Client $client) {

            $this->sender = new ActionSender($client);
            $this->action = $client->createAction('QueueChangePriorityCaller', array(
                'ActionID' => uniqid(),
                'Queue' => $this->queue,
                'Caller' => $this->caller,
                'Priority' => $this->priority
            ));

            $this->promise = $client->request($this->action);
            $this->promise->then(function (\Clue\React\Ami\Protocol\Response $response) {
                echo 'Result:' . PHP_EOL;
                var_dump($response);
            });
        });
    }

    public function queueLog()
    {
        $this->factoryCre->then(function (Client $client) {
            $this->sender = new ActionSender($client);
            $this->action = $client->createAction('QueueLog', array(
                'ActionID' => uniqid(),
                'Queue' => $this->queue,
                'Event' => $this->event,
                'Uniqueid' => uniqid(),
                'Interface' => $this->interface,
                'Message' => $this->message
            ));

            $this->promise = $client->request($this->action);
            $this->promise->then(function (\Clue\React\Ami\Protocol\Response $response) {
                echo 'Result:' . PHP_EOL;
                var_dump($response);
            });
        });
    }

    public function queueMemberRingInUse()
    {
        $this->factoryCre->then(function (Client $client) {

            $this->sender = new ActionSender($client);
            $this->action = $client->createAction('QueueMemberRingInUse', array(
                'ActionID' => uniqid(),
                'Queue' => $this->queue,
                'RingInUse' => $this->ringinuse,
                'Interface' => $this->interface
            ));

            $this->promise = $client->request($this->action);
            $this->promise->then(function (\Clue\React\Ami\Protocol\Response $response) {
                echo 'Result:' . PHP_EOL;
                var_dump($response);
            });
        });
    }

    public function queuePause()
    {
        $this->factoryCre->then(function (Client $client) {

            $this->sender = new ActionSender($client);
            $this->action = $client->createAction('QueuePause', array(
                'ActionID' => uniqid(),
                'Queue' => $this->queue,
                'Reason' => $this->reason,
                'Interface' => $this->interface,
                'Paused' => $this->paused
            ));

            $this->promise = $client->request($this->action);
            $this->promise->then(function (\Clue\React\Ami\Protocol\Response $response) {
                echo 'Result:' . PHP_EOL;
                var_dump($response);
            });
        });
    }

    public function queuePenalty()
    {
        $this->factoryCre->then(function (Client $client) {

            $this->sender = new ActionSender($client);
            $this->action = $client->createAction('QueuePenalty', array(
                'ActionID' => uniqid(),
                'Queue' => $this->queue,
                'Interface' => $this->interface,
                'Penalty' => $this->penalty
            ));

            $this->promise = $client->request($this->action);
            $this->promise->then(function (\Clue\React\Ami\Protocol\Response $response) {
                echo 'Result:' . PHP_EOL;
                var_dump($response);
            });
        });
    }

    public function queueReload()
    {
        $this->factoryCre->then(function (Client $client) {

            $this->sender = new ActionSender($client);
            $this->action = $client->createAction('QueueReload', array(
                'ActionID' => uniqid(),
                'Queue' => $this->queue,
                'Members' => $this->members,
                'Rules' => $this->rules,
                'Parametres' => $this->parametres
            ));

            $this->promise = $client->request($this->action);
            $this->promise->then(function (\Clue\React\Ami\Protocol\Response $response) {
                echo 'Result:' . PHP_EOL;
                var_dump($response);
            });
        });
    }

    public function queueRemove()
    {
        $this->factoryCre->then(function (Client $client) {

            $this->sender = new ActionSender($client);
            $this->action = $client->createAction('QueueRemove', array(
                'ActionID' => uniqid(),
                'Queue' => $this->queue,
                'Interface' => $this->interface
            ));

            $this->promise = $client->request($this->action);
            $this->promise->then(function (\Clue\React\Ami\Protocol\Response $response) {
                echo 'Result:' . PHP_EOL;
                var_dump($response);
            });
        });
    }

    public function queueReset()
    {
        $this->factoryCre->then(function (Client $client) {

            $this->sender = new ActionSender($client);
            $this->action = $client->createAction('QueueReset', array(
                'ActionID' => uniqid(),
                'Queue' => $this->queue
            ));

            $this->promise = $client->request($this->action);
            $this->promise->then(function (\Clue\React\Ami\Protocol\Response $response) {
                echo 'Result:' . PHP_EOL;
                var_dump($response);
            });
        });
    }

    public function queueRule()
    {
        $this->factoryCre->then(function (Client $client) {

            $this->sender = new ActionSender($client);
            $this->action = $client->createAction('QueueRule', array(
                'ActionID' => uniqid(),
                'Rule' => $this->rule
            ));

            $this->promise = $client->request($this->action);
            $this->promise->then(function (\Clue\React\Ami\Protocol\Response $response) {
                echo 'Result:' . PHP_EOL;
                var_dump($response);
            });
        });
    }

    public function queueStatus()
    {
        $this->factoryCre->then(function (Client $client) {

            $this->sender = new ActionSender($client);
            $this->action = $client->createAction('QueueStatus', array(
                'ActionID' => uniqid(),
                'Queue' => $this->queue,
                'Member' => $this->member
            ));

            $this->promise = $client->request($this->action);
            $this->promise->then(function (\Clue\React\Ami\Protocol\Response $response) {
                echo 'Result:' . PHP_EOL;
                var_dump($response);
            });
        });
    }

    public function queueSummary()
    {
        $this->factoryCre->then(function (Client $client) {

            $this->sender = new ActionSender($client);
            $this->action = $client->createAction('QueueSummary', array(
                'ActionID' => uniqid(),
                'Queue' => $this->queue
            ));

            $this->promise = $client->request($this->action);
            $this->promise->then(function (\Clue\React\Ami\Protocol\Response $response) {
                echo 'Result:' . PHP_EOL;
                return $response;
            });
        });
    }

    public function showDialPlan()
    {
        $this->factoryCre->then(function (Client $client) {

            $this->sender = new ActionSender($client);
            $this->action = $client->createAction('ShowDialPlan', array(
                'ActionID' => uniqid(),
                'Extension' => $this->ext,
                'Context' => $this->context
            ));

            $this->promise = $client->request($this->action);
            $this->promise->then(function (\Clue\React\Ami\Protocol\Response $response) {
                echo 'Result:' . PHP_EOL;
                var_dump($response);
            });
        });
    }

    public function StopMixMonitor()
    {
        $this->factoryCre->then(function (Client $client) {

            $this->sender = new ActionSender($client);
            $this->action = $client->createAction('StopMixMonitor', array(
                'ActionID' => uniqid(),
                'Channel' => $this->channel,
                '[MixMonitorID]' => $this->mixMonitorId
            ));

            $this->promise = $client->request($this->action);
            $this->promise->then(function (\Clue\React\Ami\Protocol\Response $response) {
                echo 'Result:' . PHP_EOL;
                var_dump($response);
            });
        });
    }

    public function sipshowpeer()
    {
        $this->factoryCre->then(function (Client $client) {

            $this->sender = new ActionSender($client);
            $this->action = $client->createAction('SIPshowpeer', array(
                'ActionID' => uniqid(),
                'Peer' => $this->peer
            ));

            $this->promise = $client->request($this->action);
            $this->promise->then(function (\Clue\React\Ami\Protocol\Response $response) {
                echo 'Result:' . PHP_EOL;
                var_dump($response);
            });
        });
    }

    public function sipshowregistry()
    {
        $this->factoryCre->then(function (Client $client) {

            $this->sender = new ActionSender($client);
            $this->action = $client->createAction('SIPshowregistry', array(
                'ActionID' => uniqid()
            ));

            $this->promise = $client->request($this->action);
            $this->promise->then(function (\Clue\React\Ami\Protocol\Response $response) {
                echo 'Result:' . PHP_EOL;
                var_dump($response);
            });
        });
    }

    public function monitor()
    {
        $this->factoryCre->then(function (Client $client) {

            $this->sender = new ActionSender($client);
            $this->action = $client->createAction('Monitor', array(
                'ActionID' => uniqid(),
                'Channel' => $this->channel,
                'File' => $this->file,
                'Format' => $this->format,
                'Mix' => $this->mix
            ));

            $this->promise = $client->request($this->action);
            $this->promise->then(function (\Clue\React\Ami\Protocol\Response $response) {
                echo 'Result:' . PHP_EOL;
                var_dump($response);
            });
        });
    }

    public function setvar()
    {
        $this->factoryCre->then(function (Client $client) {

            $this->sender = new ActionSender($client);
            $this->action = $client->createAction('Setvar', array(
                'ActionID' => uniqid(),
                'Channel' => $this->channel,
                'Variable' => $this->variable,
                'Value' => $this->value
            ));

            $this->promise = $client->request($this->action);
            $this->promise->then(function (\Clue\React\Ami\Protocol\Response $response) {
                echo 'Result:' . PHP_EOL;
                var_dump($response);
            });
        });
    }

    public function login()
    {
        $this->factoryCre->then(function (Client $client) {

            $this->sender = new ActionSender($client);
            $this->action = $client->createAction('Login', array(
                'ActionID' => uniqid(),
                'Username' => $this->username,
                'Secret' => $this->secret
            ));

            $this->promise = $client->request($this->action);
            $this->promise->then(function (\Clue\React\Ami\Protocol\Response $response) {
                echo 'Result:' . PHP_EOL;
                var_dump($response);
            });
        });
    }

    public function logoff()
    {
        $this->factoryCre->then(function (Client $client) {

            $this->sender = new ActionSender($client);
            $this->action = $client->createAction('Logoff', array(
                'ActionID' => uniqid()
            ));

            $this->promise = $client->request($this->action);
            $this->promise->then(function (\Clue\React\Ami\Protocol\Response $response) {
                echo 'Result:' . PHP_EOL;
                var_dump($response);
            });
        });
    }

    public function createConfig()
    {
        $this->factoryCre->then(function (Client $client) {

            $this->sender = new ActionSender($client);
            $this->action = $client->createAction('CreateConfig', array(
                'ActionID' => uniqid(),
                'Filename' => $this->filename
            ));

            $this->promise = $client->request($this->action);
            $this->promise->then(function (\Clue\React\Ami\Protocol\Response $response) {
                echo 'Result:' . PHP_EOL;
                var_dump($response);
            });
        });
    }


    public function executeCommands()
    {
        $this->factoryCre->then(function (Client $client) {

            $this->sender = new ActionSender($client);
            $this->action = $client->createAction($this->actionG, $this->conf);

            $this->promise = $client->request($this->action);
            $this->promise->then(function (\Clue\React\Ami\Protocol\Response $response) {
                echo 'Result:' . PHP_EOL;
                var_dump($response);
            });
        });
    }

    public function loopRun()
    {
        $this->loop->run();
    }

    #endregion

    #region Events
    public function eventEvent()
    {
        $this->factoryCre->then(function (Client $client) {
            $client->on('event', function (\Clue\React\Ami\Protocol\Event $event) {
                echo $event->getName() . ' event fired!' . PHP_EOL;
            });
        });
    }

    public function eventReact()
    {
        $this->factoryCre->then(function (Client $client) {
            $client->on('event', function (\Clue\React\Ami\Protocol\Event $event) {
                if ($event->getName() === $this->eventName) {
                    echo $event->getName() . ' event fired!' . PHP_EOL;
                    var_dump($event);
                }
            });
        });
    }

    public function eventError()
    {
        $this->factoryCre->then(function (Client $client) {
            $client->on('error', function (Exception $e) {
                echo 'Error: ' . $e->getMessage() . PHP_EOL;
            });
        });
    }

    public function eventClose()
    {
        $this->factoryCre->then(function (Client $client) {
            $client->on('close', function () {
                echo 'Connection closed' . PHP_EOL;
            });
        });
    }


#endregion


}
