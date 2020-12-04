<?php
/**
 * Author:  Xolmat Ravshanov
 */

namespace zetsoft\service\calls;

use League\Flysystem\Filesystem;
use League\Flysystem\Sftp\SftpAdapter;
use phpseclib\Net\SFTP;


use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

class CallFile extends ZFrame
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

    public $callerId = '309';

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

    public $ip = self::ip['41'];

    private $adapter;
    private $filesystem;
    private $sftp;
    #endregion

    #region Cores
    public function init()
    {
        parent::init();

        $this->adapter = new SftpAdapter([
            'host' => $this->ip,
            'port' => $this->options['port'],
            'username' => $this->credentials['username'],
            'password' => $this->credentials['password'],
            'root' => $this->path['root'],
            'timeout' => $this->options['timeout'],
        ]);
        $this->filesystem = new Filesystem($this->adapter);
        $this->adapter->connect();

        $this->sftp = new SFTP($this->ip);
        if (!$this->sftp->login($this->credentials['username'], $this->credentials['password'])) {
            exit('bad login');
        }
        $this->accountCode = uniqid();
    }

    public function test()
    {
        $this->write1();
    }
    #endregion

    /**
     *
     * Function  write
     * @param $extension
     * @param $callerId
     *
     */
    public function write($extension, $callerId)
    {
        $this->fileName = $extension . "-" . $callerId . "-" . uniqid();
        $this->accountCode = uniqid();
        $filedest = $this->path['tmp'] . $this->fileName . ".call";
        $content = "Channel: SIP/{$extension}\n";
        $content .= "Callerid: {$this->callerId}\n";
        $content .= "WaitTime: {$this->waitTime}\n";
        $content .= "MaxRetries: {$this->maxRetries}\n";
        $content .= "RetryTime: {$this->retryTime}\n";
        $content .= "Context: {$this->context['from-internal']}\n";
        $content .= "Extension: {$this->callerId}}\n";
        $content .= "Priority: {$this->priority}\n";
        $content .= "Account: {$this->accountCode}\n";
        $content .= "Archive: {$this->archive}\n";
        $this->filesystem->write($filedest, $content);
        $this->sftp->exec("chown asterisk {$filedest}");
    }


    public function write1()
    {
        $this->fileName = '300303' . "-" . '309' . "-" . uniqid();
        $this->accountCode = uniqid();
        $filedest = $this->path['outgoing'] . $this->fileName . ".call";
        $content = "Channel: SIP/etc/'934631525/'\n";
        $content .= "Callerid: {$this->callerId}\n";
        $content .= "WaitTime: {$this->waitTime}\n";
        $content .= "MaxRetries: {$this->maxRetries}\n";
        $content .= "RetryTime: {$this->retryTime}\n";
        $content .= "Context: {$this->context['from-internal']}\n";
        $content .= "Extension: {$this->callerId}}\n";
        $content .= "Priority: {$this->priority}\n";
        $content .= "Account: {$this->accountCode}\n";
        $content .= "Archive: {$this->archive}\n";
        $this->filesystem->write($filedest, $content);
        $this->sftp->exec("chown asterisk {$filedest}");
    }


    public function autoWrite($client, $operator, $order_id)
    {
        $this->fileName = $client . "-" . $operator . "-" . uniqid();
        $this->accountCode = uniqid();
        $filedest = $this->path['outgoing'] . $this->fileName . ".call";
        $content = "Channel: SIP/{$client}\n";
        $content .= "Callerid: {$order_id}\n";
        $content .= "WaitTime: {$this->waitTime}\n";
        $content .= "MaxRetries: {$this->maxRetries}\n";
        $content .= "RetryTime: {$this->retryTime}\n";
        $content .= "Context: {$this->context['from-internal']}\n";
        $content .= "Extension: {$operator}\n";
        $content .= "Priority: {$this->priority}\n";
        $content .= "Account: {$this->accountCode}\n";
        $content .= "Archive: {$this->archive}\n";
        $this->filesystem->write($filedest, $content);
        $this->sftp->exec("chown asterisk {$filedest}");
    }


    /**
     *
     * Function  count
     * @param $path
     * @return  int
     */
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

    /**
     *
     * Function  check
     *
     */
    public function check()
    {
        $dataCount = count($this->database);
        $k = 0;
        while (true) {
            foreach ($this->database as $key => $val) {
                $outMove = $this->count($this->path['tmp']);
                $out = $this->count($this->path['outgoing']);
                if ($k == $key) {
                    if ($outMove <= $this->maxFiles) {
                        $this->write($val, $this->callerId);
                        echo $val;
                        if ($k < $dataCount) {
                            ++$k;
                        } else {
                            $k = 0;
                            die();
                        }
                    }
                }
                if ($out <= $this->maxFiles)
                    $this->moveFiles();
            }

        }

    }


    public function auto($calls, $limit = 1)
    {
        $dataCount = count($calls);
        $k = 0;
        while (true) {
            foreach ($calls as $key => $val) {
                $outMove = $this->count($this->path['tmp']);
                $out = $this->count($this->path['outgoing']);
                if ($k == $key) {
                    if ($outMove <= $limit) {
                        $this->autoWrite($val->operator, $val->client, $val->order_id);
                        echo $val;
                        if ($k < $dataCount) {
                            ++$k;
                        } else {
                            $k = 0;
                            die();
                        }
                    }
                }
                if ($out <= $limit)
                    $this->moveFiles();
            }

        }

    }

    // #for-auto-operator
    public function call()
    {
        $calls = Az::$app->calls->autoDial->callAgent1();
        $numbers = $calls;
        while (true) {
            if (empty($numbers))
                die();

            foreach ($numbers as $key => $number) {
                $writePathCount = $this->count($this->path['tmp']);
                $callPathCount = $this->count($this->path['outgoing']);

                /*  $extStatus =  Az::$app->calls->marceAmi->extStatus($number->operator, 'from-internal');*/

                if ($writePathCount == 0 && $callPathCount == 0) {
                    $this->autoWrite($number->client, $number->operator, $number->order_id);
                    unset($numbers[$key]);
                    vd($number->operator);
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
        $calls = Az::$app->calls->autoDial->callAgent($agentId);


        if (empty($numbers))
            return Az::error($agentId, 'Empty');

        foreach ($numbers as $key => $number) {
            while (true) {


                $writePathCount = $this->count($this->path['tmp']);
                $callPathCount = $this->count($this->path['outgoing']);

                /*  $extStatus =  Az::$app->calls->marceAmi->extStatus($number->operator, 'from-internal');*/

                if ($writePathCount == 0 && $callPathCount == 0) {
                    $this->autoWrite($number->client, $number->operator, $number->order_id);
                    unset($numbers[$key]);
                    vd($number->operator);
                    vd('written');
                }
                if ($callPathCount == 0) {
                    $this->moveFiles();
                    vd('moved');
                }
            }

        }

    }


}




