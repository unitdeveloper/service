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

use zetsoft\service\calls\Ad;
use zetsoft\service\calls\Adp;
use zetsoft\service\calls\AmiAppMultiple;
use zetsoft\service\calls\AppAmi;
use zetsoft\service\calls\AppAmi1EventTest;
use zetsoft\service\calls\Asterisk;
use zetsoft\service\calls\CallFile;
use zetsoft\service\calls\CallFile1;
use zetsoft\service\calls\Cc;
use zetsoft\service\calls\Fop;
use zetsoft\service\calls\Fop2;
use zetsoft\service\calls\fulltest;
use zetsoft\service\calls\MarceAMI;
use zetsoft\service\calls\MarceAMIEvents;
use zetsoft\service\calls\Pami;
use zetsoft\service\calls\ReactAmi;
use zetsoft\service\calls\ReactAmiC;
use zetsoft\service\calls\ReactAmiF;
use zetsoft\service\calls\ReactAmiFarrukh;
use zetsoft\service\calls\ReactAmiF_Jamoliddin;
use zetsoft\service\calls\ReactAmiS;
use zetsoft\service\calls\SSH2;
use zetsoft\service\calls\ZActive;
use zetsoft\service\calls\ZActiveCall;
use yii\base\Component;



/**
 *
* @property Ad $ad
* @property Adp $adp
* @property AmiAppMultiple $amiAppMultiple
* @property AppAmi $appAmi
* @property AppAmi1EventTest $appAmi1EventTest
* @property Asterisk $asterisk
* @property CallFile $callFile
* @property CallFile1 $callFile1
* @property Cc $cc
* @property Fop $fop
* @property Fop2 $fop2
* @property fulltest $fulltest
* @property MarceAMI $marceAMI
* @property MarceAMIEvents $marceAMIEvents
* @property Pami $pami
* @property ReactAmi $reactAmi
* @property ReactAmiC $reactAmiC
* @property ReactAmiF $reactAmiF
* @property ReactAmiFarrukh $reactAmiFarrukh
* @property ReactAmiF_Jamoliddin $reactAmiF_Jamoliddin
* @property ReactAmiS $reactAmiS
* @property SSH2 $sSH2
* @property ZActive $zActive
* @property ZActiveCall $zActiveCall

 */

class Asteriskk extends Component
{

    
    private $_ad;
    private $_adp;
    private $_amiAppMultiple;
    private $_appAmi;
    private $_appAmi1EventTest;
    private $_asterisk;
    private $_callFile;
    private $_callFile1;
    private $_cc;
    private $_fop;
    private $_fop2;
    private $_fulltest;
    private $_marceAMI;
    private $_marceAMIEvents;
    private $_pami;
    private $_reactAmi;
    private $_reactAmiC;
    private $_reactAmiF;
    private $_reactAmiFarrukh;
    private $_reactAmiF_Jamoliddin;
    private $_reactAmiS;
    private $_sSH2;
    private $_zActive;
    private $_zActiveCall;

    
    public function getAd()
    {
        if ($this->_ad === null)
            $this->_ad = new Ad();

        return $this->_ad;
    }
    

    public function getAdp()
    {
        if ($this->_adp === null)
            $this->_adp = new Adp();

        return $this->_adp;
    }
    

    public function getAmiAppMultiple()
    {
        if ($this->_amiAppMultiple === null)
            $this->_amiAppMultiple = new AmiAppMultiple();

        return $this->_amiAppMultiple;
    }
    

    public function getAppAmi()
    {
        if ($this->_appAmi === null)
            $this->_appAmi = new AppAmi();

        return $this->_appAmi;
    }
    

    public function getAppAmi1EventTest()
    {
        if ($this->_appAmi1EventTest === null)
            $this->_appAmi1EventTest = new AppAmi1EventTest();

        return $this->_appAmi1EventTest;
    }
    

    public function getAsterisk()
    {
        if ($this->_asterisk === null)
            $this->_asterisk = new Asterisk();

        return $this->_asterisk;
    }
    

    public function getCallFile()
    {
        if ($this->_callFile === null)
            $this->_callFile = new CallFile();

        return $this->_callFile;
    }
    

    public function getCallFile1()
    {
        if ($this->_callFile1 === null)
            $this->_callFile1 = new CallFile1();

        return $this->_callFile1;
    }
    

    public function getCc()
    {
        if ($this->_cc === null)
            $this->_cc = new Cc();

        return $this->_cc;
    }
    

    public function getFop()
    {
        if ($this->_fop === null)
            $this->_fop = new Fop();

        return $this->_fop;
    }
    

    public function getFop2()
    {
        if ($this->_fop2 === null)
            $this->_fop2 = new Fop2();

        return $this->_fop2;
    }
    

    public function getFulltest()
    {
        if ($this->_fulltest === null)
            $this->_fulltest = new fulltest();

        return $this->_fulltest;
    }
    

    public function getMarceAMI()
    {
        if ($this->_marceAMI === null)
            $this->_marceAMI = new MarceAMI();

        return $this->_marceAMI;
    }
    

    public function getMarceAMIEvents()
    {
        if ($this->_marceAMIEvents === null)
            $this->_marceAMIEvents = new MarceAMIEvents();

        return $this->_marceAMIEvents;
    }
    

    public function getPami()
    {
        if ($this->_pami === null)
            $this->_pami = new Pami();

        return $this->_pami;
    }
    

    public function getReactAmi()
    {
        if ($this->_reactAmi === null)
            $this->_reactAmi = new ReactAmi();

        return $this->_reactAmi;
    }
    

    public function getReactAmiC()
    {
        if ($this->_reactAmiC === null)
            $this->_reactAmiC = new ReactAmiC();

        return $this->_reactAmiC;
    }
    

    public function getReactAmiF()
    {
        if ($this->_reactAmiF === null)
            $this->_reactAmiF = new ReactAmiF();

        return $this->_reactAmiF;
    }
    

    public function getReactAmiFarrukh()
    {
        if ($this->_reactAmiFarrukh === null)
            $this->_reactAmiFarrukh = new ReactAmiFarrukh();

        return $this->_reactAmiFarrukh;
    }
    

    public function getReactAmiF_Jamoliddin()
    {
        if ($this->_reactAmiF_Jamoliddin === null)
            $this->_reactAmiF_Jamoliddin = new ReactAmiF_Jamoliddin();

        return $this->_reactAmiF_Jamoliddin;
    }
    

    public function getReactAmiS()
    {
        if ($this->_reactAmiS === null)
            $this->_reactAmiS = new ReactAmiS();

        return $this->_reactAmiS;
    }
    

    public function getSSH2()
    {
        if ($this->_sSH2 === null)
            $this->_sSH2 = new SSH2();

        return $this->_sSH2;
    }
    

    public function getZActive()
    {
        if ($this->_zActive === null)
            $this->_zActive = new ZActive();

        return $this->_zActive;
    }
    

    public function getZActiveCall()
    {
        if ($this->_zActiveCall === null)
            $this->_zActiveCall = new ZActiveCall();

        return $this->_zActiveCall;
    }
    


}
