<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\calls;

use League\Flysystem\Filesystem;
use League\Flysystem\Sftp\SftpAdapter;
use phpseclib\Net\SFTP;


use zetsoft\system\kernels\ZFrame;
use function Dash\unique;

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
        '701', '204', '233', '333', '444', '445', '555', '535', '5544', '222', '202', '645', '4234', '111', '2222'
    ];

    private $callerId = [
        '1111' => '1111',

    ];

    private $context = [
        'from-internal' => 'from-internal',
        'from-trunk' => 'from-trunk'
    ];

    private $path = [
        'outgoing' => '/var/spool/asterisk/outgoing/',
        'outgoing_move' => '/var/spool/asterisk/outgoing_move/',
        'outgoing_done' => '/var/spool/asterisk/outgoing_done/',
        'root' => '/'
    ];


    public const ip = [
        '41' => '10.10.3.41',
        '30' => '10.10.3.30',
        '31' => '10.10.3.31',
    ];


    public $options = [
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
        $this->check();
    }

    #endregion
    private function write($extension, $callerId)
    {
        $this->fileName = $extension . "-" . $callerId . "-" . uniqid();
        $this->accountCode = uniqid();
        $filedest = $this->path['outgoing_move'] . $this->fileName . ".call";
        $content = "Channel: SIP/{$extension}\n";
        $content .= "Callerid: {$this->callerId['1111']}\n";
        $content .= "WaitTime: {$this->waitTime}\n";
        $content .= "MaxRetries: {$this->maxRetries}\n";
        $content .= "RetryTime: {$this->retryTime}\n";
        $content .= "Context: {$this->context['from-internal']}\n";
        $content .= "Extension: {$this->callerId['1111']}\n";
        $content .= "Priority: {$this->priority}\n";
        $content .= "Account: {$this->accountCode}\n";
        $content .= "Archive: {$this->archive}\n";
        $this->filesystem->write($filedest, $content);
    }

    private function count($path)
    {
        $contents = $this->filesystem->listContents($path);
        $count = count($contents);
        return $count;

    }

    public function moveFiles()
    {
        // Use sftp to make an mv
        if (!$this->filesystem->has($this->path['outgoing_move']))
            $this->filesystem->createDir($this->path['outgoing']);


        $this->sftp->exec("mv {$this->path['outgoing_move']}* {$this->path['outgoing']}");
    }

    public function check()
    {
        $dataCount = count($this->database);
        $k = 0;
        while (true) {
            foreach ($this->database as $key => $val) {
                $outMove = $this->count($this->path['outgoing_move']);
                $out = $this->count($this->path['outgoing']);
                if ($k == $key) {
                    if ($outMove <= $this->maxFiles) {
                        $this->write($val, $this->callerId['1111']);
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


    public function callFileGenete()
    {
        $dataCount = count($this->database);
        $k = 0;
        while (true) {
            foreach ($this->database as $key => $val) {
                $outMove = $this->count($this->path['outgoing_move']);
                $out = $this->count($this->path['outgoing']);
                if ($k == $key) {
                    if ($outMove <= $this->maxFiles) {
                        $this->write($val, $this->callerId['1111']);
                        echo $val.'<br>';
                        if($val === end($this->database))
                            die();

                        if ($k < $dataCount) {
                            ++$k;
                        } else {
                            $k = 0;

                        }

                    }
                }
                if ($out <= $this->maxFiles)
                    $this->moveFiles();
            }
        }

    }

    public function callFileGenete1()
    {
        $limit = 100;
        $i = 0;
        while($i < $limit){
            switch(true){
                case $this->count($this->path['outgoing_move']) < $limit:
                    foreach ($this->database as $number) {
                         $this->write($number, $this->callerId['1111']);
                    }
                break;
                case $this->count($this->path['outgoing']) < $limit:
                    $this->moveFiles();
                break;
            }
         ++$i;
        }



    }
}
