<?php
/**
 * Author:  Xolmat Ravshanov
 */
namespace zetsoft\service\calls;

use League\Flysystem\Filesystem;
use League\Flysystem\Sftp\SftpAdapter;
use PAMI\Client\Impl\ClientImpl;
use PAMI\Message\Action\ExtensionStateAction;
use phpseclib\Net\SFTP;
use React\EventLoop\Factory;
use zetsoft\dbitem\App\eyuf\AutoDialItem;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\user\User;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

class DialCallFile extends ZFrame
{

    #region Vars
    public $maxRetries = '1';
    public $retryTime = '15';
    public $waitTime = '25';
    public $priority = '1';
    public $archive = 'yes';
    public $maxFiles = 5;
    public $accountCode;
    public $fileName;

    public $database = [
        '301', '204', '205', '233', '333', '444', '445', '555', '535', '5544', '222', '202', '645', '4234', '111', '2222'
    ];

    public $callerId = '203';

    private $context = [
        'from-internal' => 'from-internal',
        'from-trunk' => 'from-trunk'
    ];

    private $path = [
        'outgoing' => '/var/spool/asterisk/outgoing/',
        'outgoing_move' => '/var/spool/asterisk/outgoing_move/',
        'tmp' => '/var/spool/asterisk/tmp/',
        'outgoing_done' => '/var/spool/asterisk/outgoing_done/',
        'root' => '/'
    ];


    public const ip = [
        '41' => '10.10.3.41',
        '30' => '10.10.3.30',
        '31' => '10.10.3.31',
    ];


    private $options = [
        'port' => 22,
        'timeout' => 10,
    ];

    private $credentials = [
        'username' => 'root',
        'password' => 'Formula1'
    ];

    public $ip = self::ip['31'];
    public $remoteIp;
    public $currentIp;

    private $adapter;
    private $filesystem;
    private $sftp;
    #endregion
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


    public $ext = '204';
    public $client;
    public $response;
    public $orgStatus;
    public $agentId;
    public $time = 300;
    public $sleepTime = 30;


    public function init()
    {
        parent::init();

        if (empty($this->remoteIp))
            $this->currentIp = $this->ip;
        else
            $this->currentIp = $this->remoteIp;

        $this->adapter = new SftpAdapter([
            'host' => $this->currentIp,
            'port' => $this->options['port'],
            'username' => $this->credentials['username'],
            'password' => $this->credentials['password'],
            'root' => $this->path['root'],
            'timeout' => $this->options['timeout'],
        ]);
        $this->filesystem = new Filesystem($this->adapter);
        $this->adapter->connect();

        $this->sftp = new SFTP($this->ip);
        if (!$this->sftp->login($this->credentials['username'], $this->credentials['password']))
            exit('bad login');

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

    public function test()
    {

        $this->coreShowChannels();

    }



    public function extStatus($ext, $context)
    {
        $this->response = $this->client->send(new ExtensionStateAction($ext, $context));
        $this->orgStatus = $this->response->getKeys();
        return $this->orgStatus['status'];
    }


    public function autoWrite($client, $operator, $order_id)
    {
        $this->fileName = $operator;
        $filedest = $this->path['outgoing'] . $this->fileName . ".call";
        $content = "Channel: SIP/{$client}\n";
        $content .= "Callerid: {$order_id}\n";
        $content .= "WaitTime: {$this->waitTime}\n";
        $content .= "MaxRetries: {$this->maxRetries}\n";
        $content .= "RetryTime: {$this->retryTime}\n";
        $content .= "Context: {$this->context['from-internal']}\n";
        $content .= "Extension: {$operator}\n";
        $content .= "Priority: {$this->priority}\n";
        $content .= "Account: {$order_id}\n";
        //$content .= "Archive: {$this->archive}\n";
        $this->filesystem->write($filedest, $content);
        $this->sftp->exec("chown asterisk {$filedest}");
    }


    public function count($path)
    {
        $contents = $this->filesystem->listContents($path);
        $count = count($contents);
        return $count;
    }

    /**
     *
     * Function  hasDir
     */
    public function hasDir()
    {
        // Use sftp to make an mv
        if (!$this->filesystem->has($this->path['tmp']))
            $this->filesystem->createDir($this->path['tmp']);
    }

    /**
     *
     * Function  moveFiles
     */

    public function moveFiles()
    {
        $this->sftp->exec("mv {$this->path['tmp']}* {$this->path['outgoing']}");
    }

    public function moveFile($filename)
    {
        $this->sftp->exec("mv {$this->path['tmp']}.$filename.'.call' {$this->path['outgoing']}");
    }


    public function call($agentId)
    {

        $numbers = Az::$app->calls->autoDial->callAgent($agentId);

        /* @var AutoDialItem[] $numbers */
        if (empty($numbers))
            return Az::error($agentId, 'Empty');


        foreach ($numbers as $key => $number) {

            while (true) {

                vd($number->operator);

                $agentStatus = User::findOne($agentId);

                vd($agentStatus);

                if ($agentStatus->status !== 'online' || !$agentStatus->autodial)
                    sleep(10);
                else {
                    // move
                    $fileExistsOutgoing = $this->filesystem->has
                    ($this->path['outgoing'].$number->operator.'.call');

                    $status = $this->extStatus($number->operator, $this->context['from-internal']);
                    $status = (int)$status;

                    vd($fileExistsOutgoing . 'Status Outgoing status');
                    vd('________________');
                    vd($fileExistsOutgoing);
                    vd('________________');

                    vd($status . 'Status Extention');
                    vd('________________');
                    vd($status);
                    vd('________________');


                    if ($fileExistsOutgoing || $status !== 0)
                        sleep(40);
                    else
                        break;
                }
            }

            vd('________Sleep');
            sleep($this->sleepTime);
            vd('________Sleep');

            $this->autoWrite($number->client, $number->operator, $number->order_id);
            vd('written');

            $this->moveFile($number->operator);
            vd('moved');

            $number->order->status_callcenter = ShopOrder::status_callcenter['ring'];
            $number->order->save();
            vd('___________');

        }

    }


    public function call22222($agentId)
    {
        $numbers = Az::$app->calls->autoDial->callAgent($agentId);

        /* @var AutoDialItem[] $numbers */

        if (empty($numbers))
            return Az::error($agentId, 'Empty');

        foreach ($numbers as $key => $number) {

            while (true) {

                vd($number->operator);

                $agentStatus = User::findOne($agentId)->status;

                vd($agentStatus);

                if ($agentStatus !== 'online') {
                    sleep(30);
                } else {
                    // move
                    $status = $this->extStatus($number->operator, $this->context['from-internal']);
                    $status = (int)$status;

                    if ($key !== 0)
                        $fileExistsOutgoing = $this->filesystem->has
                        ($this->path['outgoing'] . $numbers[$key - 1]->order_id . '.call');
                    else {
                        if ($status !== 0)
                            sleep(40);
                        else
                            break;
                    }


                    if ($fileExistsOutgoing && $status !== 0)
                        sleep(40);
                    else
                        break;


                }
            }
            
            vd('________Sleep');
            sleep($this->sleepTime);
            vd('________Sleep');

            $this->autoWrite($number->client, $number->operator, $number->order_id);
            vd('written');

            $this->moveFile($number->operator);
            vd('moved');

            $number->order->status_callcenter = ShopOrder::status_callcenter['ring'];
            $number->order->save();
            vd('___________');
        }

    }


    public function run()
    {
     
            $ids = User::find()
                ->where([
                    'role' => 'operator',
                    'autodial' => true
                ])
                ->asArray()
                ->all();

            foreach ($ids as $id) {

                $file = Root . '/scripts/runner/Call.exe';

                Az::$app->utility->execs->exec($file . ' "5 D:/Develop/Projects/ALL/server/php7/7_44/php.exe" D:/Develop/Projects/ALL/asrorz/zetsoft/excmd/asrorz.php caller/call-file/call --agentId=' . $id['id'], true);

                vd($id['id']);

            }

    }

    public function start()
    {

        $file = Root . '/scripts/runner/Call.exe';

        Az::$app->utility->execs->exec($file . ' "5 D:/Develop/Projects/ALL/server/php7/7_44/php.exe" D:/Develop/Projects/ALL/asrorz/zetsoft/excmd/asrorz.php caller/call-file/run', true);


    }

    public function runold()
    {
        $loop = Factory::create();
        $loop->addPeriodicTimer($this->time, function (\React\EventLoop\TimerInterface $timer) use ($loop) {

            $ids = User::find()
                ->where([
                    'role' => 'operator',
                    'autodial' => true
                ])
                ->asArray()
                ->all();

            foreach ($ids as $id) {

                $file = Root . '/scripts/runner/Call.exe';

                Az::$app->utility->execs->exec($file . ' "D:/Develop/Projects/ALL/server/php7/7_44/php.exe" D:/Develop/Projects/ALL/asrorz/zetsoft/excmd/asrorz.php caller/call-file/call --agentId=' . $id['id'], true);

                vd($id['id']);

            }
        });

        $loop->run();
    }







    // #for-auto-operator
    public function callOld($agentId)
    {
        //$calls   = Az::$app->calls->autoDial->callAgent1();
        $calls = Az::$app->calls->autoDial->callAgent($agentId);

        /* @var AutoDialItem[] $numbers */
        $numbers = $calls;
        while (true) {

            if ($this->extStatus($agentId, $this->context['from-internal']) == '-1' || $this->extStatus($agentId, $this->context['from-internal']) == '4')
                return Az::error($agentId, 'Extention offline');

            foreach ($numbers as $key => $number) {
                $writePathCount = $this->count($this->path['tmp']);
                $callPathCount = $this->count($this->path['outgoing']);

                if ($writePathCount == 0 && $callPathCount == 0 && $this->extStatus($this->agentId, $this->context['from-internal']) == '0') {
                    $this->autoWrite($number->client, $number->operator, $number->order_id);

                    /*$order = ShopOrder::findOne($number->order_id);
                    $order->status_autodial = ShopOrder::status_autodial['dial-success'];
                    $order->save();*/

                    $number->order->status_autodial = ShopOrder::status_autodial['dial-success'];
                    $number->order->save();

                    //unset($numbers[$key]);
                    vd($number->client);
                    vd('written');
                }

                if ($callPathCount == 0) {
                    $this->moveFiles();
                    vd('moved');
                }

            }

        }

    }


    // #for-auto-operator
    public function call111($agentId)
    {
        $numbers = Az::$app->calls->autoDial->callAgent($agentId);

        /* @var AutoDialItem[] $numbers */

        if (empty($numbers))
            return Az::error($agentId, 'Empty');

        foreach ($numbers as $key => $number) {

            while (true) {

                vd($number->operator);

                $agentStatus = User::findOne($agentId)->status;

                vd($agentStatus);

                if ($agentStatus !== 'online') {
                    sleep(30);
                } else {
                    $fileExistsTmp = $this->filesystem->has
                    ($this->path['tmp'] . $number->operator . 'call');

                    // write
                    if (!$fileExistsTmp) {
                        $this->autoWrite($number->client, $number->operator, $number->order_id);
                        vd('written');
                    } else {
                        sleep(40);
                    }

                    // move
                    $fileExistsOutgoing = $this->filesystem->has
                    ($this->path['outgoing'] . $number->operator . 'call');

                    if (!$fileExistsOutgoing) {
                        $this->moveFile($number->operator);
                        vd('moved');
                        break;
                    } else
                        sleep(40);


                }
            }


        }

    }




}
