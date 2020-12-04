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

use zetsoft\service\soaps\LaminasSoap;
use zetsoft\service\soaps\Nusoap;
use zetsoft\service\soaps\SoapClient;
use zetsoft\service\soaps\Wsdl2phpgenerator;
use zetsoft\service\soaps\WsdlCreator;
use zetsoft\service\soaps\Wssecurity;
use yii\base\Component;



/**
 *
* @property LaminasSoap $laminasSoap
* @property Nusoap $nusoap
* @property SoapClient $soapClient
* @property Wsdl2phpgenerator $wsdl2phpgenerator
* @property WsdlCreator $wsdlCreator
* @property Wssecurity $wssecurity

 */

class Soaps extends Component
{

    
    private $_laminasSoap;
    private $_nusoap;
    private $_soapClient;
    private $_wsdl2phpgenerator;
    private $_wsdlCreator;
    private $_wssecurity;

    
    public function getLaminasSoap()
    {
        if ($this->_laminasSoap === null)
            $this->_laminasSoap = new LaminasSoap();

        return $this->_laminasSoap;
    }
    

    public function getNusoap()
    {
        if ($this->_nusoap === null)
            $this->_nusoap = new Nusoap();

        return $this->_nusoap;
    }
    

    public function getSoapClient()
    {
        if ($this->_soapClient === null)
            $this->_soapClient = new SoapClient();

        return $this->_soapClient;
    }
    

    public function getWsdl2phpgenerator()
    {
        if ($this->_wsdl2phpgenerator === null)
            $this->_wsdl2phpgenerator = new Wsdl2phpgenerator();

        return $this->_wsdl2phpgenerator;
    }
    

    public function getWsdlCreator()
    {
        if ($this->_wsdlCreator === null)
            $this->_wsdlCreator = new WsdlCreator();

        return $this->_wsdlCreator;
    }
    

    public function getWssecurity()
    {
        if ($this->_wssecurity === null)
            $this->_wssecurity = new Wssecurity();

        return $this->_wssecurity;
    }
    


}
