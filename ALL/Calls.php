<?php

/**
 *
 *
 * Author:  Asror Zakirov
 *
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\ALL;

use zetsoft\service\calls\AsteriskInfo;
use zetsoft\service\calls\AsteriskInfoD;
use zetsoft\service\calls\AutoDial;
use zetsoft\service\calls\CallCenter;
use zetsoft\service\calls\CallFile;
use zetsoft\service\calls\CreateUser;
use zetsoft\service\calls\CreateUser2;
use zetsoft\service\calls\DialCallFile;
use zetsoft\service\calls\FillCdr;
use zetsoft\service\calls\FillCell;
use zetsoft\service\calls\Fop;
use zetsoft\service\calls\FopSocket;
use zetsoft\service\calls\Freepbx;
use zetsoft\service\calls\FreepbxExtention;
use zetsoft\service\calls\FreepbxUser;
use zetsoft\service\calls\Fwconsole;
use zetsoft\service\calls\Hash;
use zetsoft\service\calls\MarceAMI;
use zetsoft\service\calls\NewService;
use zetsoft\service\calls\ReactAmi;
use zetsoft\service\calls\Stats;
use zetsoft\service\calls\StatsMerge;
use yii\base\Component;



/**
 *
* @property AsteriskInfo $asteriskInfo
* @property AsteriskInfoD $asteriskInfoD
* @property AutoDial $autoDial
* @property CallCenter $callCenter
* @property CallFile $callFile
* @property CreateUser $createUser
* @property CreateUser2 $createUser2
* @property DialCallFile $dialCallFile
* @property FillCdr $fillCdr
* @property FillCell $fillCell
* @property Fop $fop
* @property FopSocket $fopSocket
* @property Freepbx $freepbx
* @property FreepbxExtention $freepbxExtention
* @property FreepbxUser $freepbxUser
* @property Fwconsole $fwconsole
* @property Hash $hash
* @property MarceAMI $marceAMI
* @property NewService $newService
* @property ReactAmi $reactAmi
* @property Stats $stats
* @property StatsMerge $statsMerge

 */

class Calls extends Component
{

    
    private $_asteriskInfo;
    private $_asteriskInfoD;
    private $_autoDial;
    private $_callCenter;
    private $_callFile;
    private $_createUser;
    private $_createUser2;
    private $_dialCallFile;
    private $_fillCdr;
    private $_fillCell;
    private $_fop;
    private $_fopSocket;
    private $_freepbx;
    private $_freepbxExtention;
    private $_freepbxUser;
    private $_fwconsole;
    private $_hash;
    private $_marceAMI;
    private $_newService;
    private $_reactAmi;
    private $_stats;
    private $_statsMerge;

    
    public function getAsteriskInfo()
    {
        if ($this->_asteriskInfo === null)
            $this->_asteriskInfo = new AsteriskInfo();

        return $this->_asteriskInfo;
    }
    

    public function getAsteriskInfoD()
    {
        if ($this->_asteriskInfoD === null)
            $this->_asteriskInfoD = new AsteriskInfoD();

        return $this->_asteriskInfoD;
    }
    

    public function getAutoDial()
    {
        if ($this->_autoDial === null)
            $this->_autoDial = new AutoDial();

        return $this->_autoDial;
    }
    

    public function getCallCenter()
    {
        if ($this->_callCenter === null)
            $this->_callCenter = new CallCenter();

        return $this->_callCenter;
    }
    

    public function getCallFile()
    {
        if ($this->_callFile === null)
            $this->_callFile = new CallFile();

        return $this->_callFile;
    }
    

    public function getCreateUser()
    {
        if ($this->_createUser === null)
            $this->_createUser = new CreateUser();

        return $this->_createUser;
    }
    

    public function getCreateUser2()
    {
        if ($this->_createUser2 === null)
            $this->_createUser2 = new CreateUser2();

        return $this->_createUser2;
    }
    

    public function getDialCallFile()
    {
        if ($this->_dialCallFile === null)
            $this->_dialCallFile = new DialCallFile();

        return $this->_dialCallFile;
    }
    

    public function getFillCdr()
    {
        if ($this->_fillCdr === null)
            $this->_fillCdr = new FillCdr();

        return $this->_fillCdr;
    }
    

    public function getFillCell()
    {
        if ($this->_fillCell === null)
            $this->_fillCell = new FillCell();

        return $this->_fillCell;
    }
    

    public function getFop()
    {
        if ($this->_fop === null)
            $this->_fop = new Fop();

        return $this->_fop;
    }
    

    public function getFopSocket()
    {
        if ($this->_fopSocket === null)
            $this->_fopSocket = new FopSocket();

        return $this->_fopSocket;
    }
    

    public function getFreepbx()
    {
        if ($this->_freepbx === null)
            $this->_freepbx = new Freepbx();

        return $this->_freepbx;
    }
    

    public function getFreepbxExtention()
    {
        if ($this->_freepbxExtention === null)
            $this->_freepbxExtention = new FreepbxExtention();

        return $this->_freepbxExtention;
    }
    

    public function getFreepbxUser()
    {
        if ($this->_freepbxUser === null)
            $this->_freepbxUser = new FreepbxUser();

        return $this->_freepbxUser;
    }
    

    public function getFwconsole()
    {
        if ($this->_fwconsole === null)
            $this->_fwconsole = new Fwconsole();

        return $this->_fwconsole;
    }
    

    public function getHash()
    {
        if ($this->_hash === null)
            $this->_hash = new Hash();

        return $this->_hash;
    }
    

    public function getMarceAMI()
    {
        if ($this->_marceAMI === null)
            $this->_marceAMI = new MarceAMI();

        return $this->_marceAMI;
    }
    

    public function getNewService()
    {
        if ($this->_newService === null)
            $this->_newService = new NewService();

        return $this->_newService;
    }
    

    public function getReactAmi()
    {
        if ($this->_reactAmi === null)
            $this->_reactAmi = new ReactAmi();

        return $this->_reactAmi;
    }
    

    public function getStats()
    {
        if ($this->_stats === null)
            $this->_stats = new Stats();

        return $this->_stats;
    }
    

    public function getStatsMerge()
    {
        if ($this->_statsMerge === null)
            $this->_statsMerge = new StatsMerge();

        return $this->_statsMerge;
    }
    


}
