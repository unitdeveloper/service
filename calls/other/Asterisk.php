<?php

namespace zetsoft\service\calls;

use phpseclib\Net\SFTP;
use phpseclib\Net\SSH2;
use \zetsoft\system\kernels\ZFrame;
use zetsoft\models\App\eyuf\db2\CallsCdr;
use zetsoft\models\App\eyuf\db2\Cel;
use zetsoft\service\ALL\Asteriskk;
use zetsoft\system\actives\ZActiveRecord;

class Asterisk extends ZFrame
{
    private $localStructure;
    private $serverStructure;
    public $sftp;
    //public function getDb(){
    //    return $this->get('db2');
    //}
    public function celfind($id)
    {
        $rows = Cel::findBySql('SELECT * FROM cel WHERE uniqueid=:id', [':id' => $id])->all();
        return $rows;
    }

    public function getDataFromMainDb($usernumber)
    {
        $rows = CallsCdr::findBySql('SELECT * FROM cdr WHERE src=:usernumber',
            [':usernumber' => $usernumber])->all();
        return $rows;
    }

    public function getUserFromMainDB($uniqueid)
    {
        $rows = CallsCdr::findBySql('SELECT * FROM cdr WHERE uniqueid=:uniqueid',
            [':uniqueid' => $uniqueid])->all();
        return $rows;
    }

    public function getUserSearchintime($usernumber, $date1, $date2)
    {
        $rows = CallsCdr::findBySql('SELECT *
        FROM (SELECT * FROM cdr WHERE src=:usernumber) as T  WHERE calldate BETWEEN :time1 AND :time2 ',
            [':usernumber' => $usernumber, ':time1' => $date1, ':time2' => $date2])->all();
        return $rows;
    }

//SELECT linkedid FROM users GROUP BY linkedid HAVING (COUNT(linkedid)=1)
    public function getTransferCall()
    {
        $rows = CallsCdr::findBySql('SELECT * FROM cdr GROUP BY linkedid HAVING (COUNT(linkedid)=2)')->all();

        return $rows;
    }

    public function getFromToCall($from, $to)
    {
        $rows = CallsCdr::findBySql('SELECT * FROM cdr WHERE src=:from and dst=:to',
            [':from' => $from, ':to' => $to])->all();
        return $rows;
    }

    /*
    public function getAutoDialerData(){
        $data = Cdr::findBySql('select * from cdr where recordingfile like "q%"')->all();
        return $data;
    } */
    public function startsWith($string, $startString)
    {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }

    public function getDataFromUnansweredDb($usernumber)
    {
        $noanswer = 'NO ANSWER';
        $busy = 'BUSY';
        $data = CallsCdr::findBySql('SELECT *
        FROM (SELECT * FROM cdr WHERE src=:usernumber) as T WHERE disposition=:noanswer OR disposition=:busy',
            [':usernumber' => $usernumber, ':noanswer' => $noanswer, ':busy' => $busy])->all();
        return $data;
    }

    public function sftpConn()
    {
        $this->sftp = new SFTP('10.10.3.41');
        if (!$this->sftp->login('root', 'Formula1')) {
            exit('Login Failed');
        }
    }

    public function getServerStructure($filename)
    {
        $filename = bname($filename);
        $arrayExplode = explode('-', $filename);
        $time = strtotime($arrayExplode[3]);
        $date = date('Y-m-d', $time);
        $pathExplode = explode('-', $date);
        $pathExplode[0];
        $pathExplode[1];
        $pathExplode[2];
        $serverStructure = '/var/spool/asterisk/monitor/' . $pathExplode[0] . '/' . $pathExplode[1] . '/' . $pathExplode[2] . '/';
        $this->serverStructure = $serverStructure;
        return $this->serverStructure;
    }

    public function getSmallPath($filename)
    {
        $filename = bname($filename);
        $arrayExplode = explode('-', $filename);
        $time = strtotime($arrayExplode[3]);
        $date = date('Y-m-d', $time);
        $pathExplode = explode('-', $date);
        $pathExplode[0];
        $pathExplode[1];
        $pathExplode[2];
        $localStructure = '\videoz\eyuf\audio\\' . $pathExplode[0] . '\\' . $pathExplode[1] . '\\' . $pathExplode[2] . '\\' . $filename;
        return $localStructure;
    }

    public function getLocalStructure($filename)
    {
        $filename = bname($filename);
        $arrayExplode = explode('-', $filename);
        $time = strtotime($arrayExplode[3]);
        $date = date('Y-m-d', $time);
        $pathExplode = explode('-', $date);
        $pathExplode[0];
        $pathExplode[1];
        $pathExplode[2];
        $localStructure = Root . '/upload/videoz/eyuf/audio/' . $pathExplode[0] . '/' . $pathExplode[1] . '/' . $pathExplode[2] . '/';
        $this->localStructure = $localStructure;
        return $this->localStructure;
    }

    public function makeDirAndTransfer($serverFile, $localFile, $filename)
    {
        $this->sftpConn();
        if (!file_exists($localFile)) {
            mkdir($localFile, 0777, true);
        }
        if (!file_exists($localFile . $filename)) {
            $this->sftp->get($serverFile . $filename, $localFile . $filename);
        }
    }

    public function transferAtOnce()
    {
        $empty = '';
        $this->sftpConn();
        $query = CallsCdr::findBySql('SELECT recordingfile FROM cdr WHERE recordingfile!=:empty',
            [':empty' => $empty])->all();
        foreach ($query as $val) {
            $filename = bname($val['recordingfile']);
            $arrayExplode = explode('-', $filename);
            $time = strtotime($arrayExplode[3]);
            $date = date('Y-m-d', $time);
            $pathExplode = explode('-', $date);
            $pathExplode[0];
            $pathExplode[1];
            $pathExplode[2];
            $serverFile = '/var/spool/asterisk/monitor/' . $pathExplode[0] . '/' . $pathExplode[1] . '/' . $pathExplode[2] . '/' . $filename;
            $localFile = Root . '/upload/videoz/eyuf/audio/' . $pathExplode[0] . '/' . $pathExplode[1] . '/' . $pathExplode[2] . '/';
            if (!file_exists($localFile)) {
                mkdir($localFile, 0777, true);
            }
            if (!file_exists($localFile . $filename)) {
                $this->sftp->get($serverFile, $localFile . $filename);
            }
        }
    }
}
