<?php
/**
 * Author:  Xolmat Ravshanov
 */
namespace zetsoft\service\calls;

use League\Flysystem\Filesystem;
use League\Flysystem\Sftp\SftpAdapter;
use phpseclib\Net\SFTP;
use zetsoft\models\App\eyuf\db2\Cdr;
use zetsoft\models\App\eyuf\db2\Cel;
use zetsoft\models\calls\CallsCdr;
use zetsoft\system\kernels\ZFrame;
use function Dash\unique;

class AsteriskInfoD extends ZFrame
{
    #region Vars
    public $sftp;
    private $path = [
        'outgoing' => '/var/spool/asterisk/outgoing/',
        'outgoing_move' => '/var/spool/asterisk/outgoing_move/',
        'tmp' => '/var/spool/asterisk/tmp/',
        'outgoing_done' => '/var/spool/asterisk/outgoing_done/',
        'root' => '/',
        'monitoring' => '/var/spool/asterisk/monitor/',
        'local' => 'D:/monitoring/'
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
    #endregion
    public function test()
    {
      /*  $this->getRecordFileByName('internal-701-202-20200428-163524-1588073724.122.wav');*/
        $this->retomeToLocal();

    }

    public function init()
    {
        parent::init();
        $this->sftp = new SFTP($this->ip);
        if (!$this->sftp->login($this->credentials['username'], $this->credentials['password'])) {
            exit('bad login');
        }
    }

    public function absolutePath($name){
        if (!empty($name)) {
            $arrayExplode = explode('-', $name);
            $time = strtotime($arrayExplode[3]);
            $date = date('Y-m-d', $time);

            $FolderNames = explode('-', $date);
            $absolutePath = $FolderNames[0] . '/' . $FolderNames[1] . '/' . $FolderNames[2];
            //vdd($absolutePath);
            return $absolutePath;
        }
        else
            return $name;
    }


  
    public function getRecordFileByName($name)
    {
        if (!empty($this->absolutePath($name))) {
            $fullPath = $this->path['monitoring'] . $this->absolutePath($name) . '/' . $name;
            $localPath = Root . '/upload/audioz/eyuf/call/' . $this->absolutePath($name) . '/' . $name;
            $localPath1 = Root . '/upload/audioz/eyuf/call/' . $this->absolutePath($name) . '/';
            $localPath1 = str_replace('\\', '/', $localPath1);
            if (!file_exists($localPath1))
                mkdir($localPath1, 0777, true);
            $this->sftp->get($fullPath, $localPath);
            return 0;
        }
        return false;


    }



    public function getRecordByOrderId($order_id)
    {
        $cdr = CallsCdr::findOne(['src' => $order_id]);
        $this->getRecordFileByName($cdr->recordingfile);
    }

    public function retomeToLocal()
    {

        $cdr = Cdr::find()
        ->all();
     
        foreach ($cdr as $record){
            if(empty($record->recordingfile) || $record->recordingfile == null || $record->recordingfile == ' ' )
            continue;
            else
                $this->getRecordFileByName($record->recordingfile);

        }
    }

    
    /**
     *
     * Function  getRecordFileByExt
     * @param $ext
     * @param $name
     * @return  string Local path of the file
     */
     
    public function getRecordFileByExt($ext, $name){
        $arrayExplode = explode('-', $name);
        $time = strtotime($arrayExplode[3]);
        $date = date('Y-m-d', $time);
        $ext1 = $arrayExplode[1];
        $ext2 = $arrayExplode[1];
        $FolderNames = explode('-', $date);
        $FolderNames[0];
        $FolderNames[1];
        $FolderNames[2];
        if($ext1 == $ext || $ext2 === $ext)
        {
            $fullPath = $this->path['monitoring'].$FolderNames[0].'/'.$FolderNames[1].'/'.$FolderNames[2].'/'. $name;
            $localPath = $this->path['local'].$name;
            $this->sftp->get($fullPath, $localPath);
            return $localPath;
        }
    }



    /**
     *
     * Function  getExtentionRecordFiles
     * @param $ext
     * @return  array  files path
     * @throws \Exception
     */

    public function getExtentionRecordFiles($ext)
    {
        $localFilesPath = [];
        $cdr = CallsCdr::find()->all();
        foreach ($cdr as $record)
            $localFilesPath[] = $this->getRecordFileByExt($ext, $record->recordingfile);

        return $localFilesPath;
    }

    public function getRecordByOrderId1($order_id)
    {
       $cel = Cel::findOne(['cid_num' => $order_id]);
       $cdr = CallsCdr::findOne(['uniqueid' => $cel->uniqueid]);
       $this->getRecordFileByName($cdr->recordingfile);
    }





}
