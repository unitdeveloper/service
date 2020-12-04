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

use zetsoft\service\payer\Click;
use zetsoft\service\payer\ClickKeldiyor;
use zetsoft\service\payer\ClickTest;
use zetsoft\service\payer\Currency2;
use zetsoft\service\payer\Oson;
use zetsoft\service\payer\OsonKeldiyor;
use zetsoft\service\payer\Pay;
use zetsoft\service\payer\Payme;
use zetsoft\service\payer\PaymeQobil;
use zetsoft\service\payer\PaymeTest;
use zetsoft\service\payer\PaymeXolmat;
use zetsoft\service\payer\Paysys;
use zetsoft\service\payer\Uzcard;
use zetsoft\service\payer\UzcardNew;
use yii\base\Component;



/**
 *
* @property Click $click
* @property ClickKeldiyor $clickKeldiyor
* @property ClickTest $clickTest
* @property Currency2 $currency2
* @property Oson $oson
* @property OsonKeldiyor $osonKeldiyor
* @property Pay $pay
* @property Payme $payme
* @property PaymeQobil $paymeQobil
* @property PaymeTest $paymeTest
* @property PaymeXolmat $paymeXolmat
* @property Paysys $paysys
* @property Uzcard $uzcard
* @property UzcardNew $uzcardNew

 */

class Payer extends Component
{

    
    private $_click;
    private $_clickKeldiyor;
    private $_clickTest;
    private $_currency2;
    private $_oson;
    private $_osonKeldiyor;
    private $_pay;
    private $_payme;
    private $_paymeQobil;
    private $_paymeTest;
    private $_paymeXolmat;
    private $_paysys;
    private $_uzcard;
    private $_uzcardNew;

    
    public function getClick()
    {
        if ($this->_click === null)
            $this->_click = new Click();

        return $this->_click;
    }
    

    public function getClickKeldiyor()
    {
        if ($this->_clickKeldiyor === null)
            $this->_clickKeldiyor = new ClickKeldiyor();

        return $this->_clickKeldiyor;
    }
    

    public function getClickTest()
    {
        if ($this->_clickTest === null)
            $this->_clickTest = new ClickTest();

        return $this->_clickTest;
    }
    

    public function getCurrency2()
    {
        if ($this->_currency2 === null)
            $this->_currency2 = new Currency2();

        return $this->_currency2;
    }
    

    public function getOson()
    {
        if ($this->_oson === null)
            $this->_oson = new Oson();

        return $this->_oson;
    }
    

    public function getOsonKeldiyor()
    {
        if ($this->_osonKeldiyor === null)
            $this->_osonKeldiyor = new OsonKeldiyor();

        return $this->_osonKeldiyor;
    }
    

    public function getPay()
    {
        if ($this->_pay === null)
            $this->_pay = new Pay();

        return $this->_pay;
    }
    

    public function getPayme()
    {
        if ($this->_payme === null)
            $this->_payme = new Payme();

        return $this->_payme;
    }
    

    public function getPaymeQobil()
    {
        if ($this->_paymeQobil === null)
            $this->_paymeQobil = new PaymeQobil();

        return $this->_paymeQobil;
    }
    

    public function getPaymeTest()
    {
        if ($this->_paymeTest === null)
            $this->_paymeTest = new PaymeTest();

        return $this->_paymeTest;
    }
    

    public function getPaymeXolmat()
    {
        if ($this->_paymeXolmat === null)
            $this->_paymeXolmat = new PaymeXolmat();

        return $this->_paymeXolmat;
    }
    

    public function getPaysys()
    {
        if ($this->_paysys === null)
            $this->_paysys = new Paysys();

        return $this->_paysys;
    }
    

    public function getUzcard()
    {
        if ($this->_uzcard === null)
            $this->_uzcard = new Uzcard();

        return $this->_uzcard;
    }
    

    public function getUzcardNew()
    {
        if ($this->_uzcardNew === null)
            $this->_uzcardNew = new UzcardNew();

        return $this->_uzcardNew;
    }
    


}
