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

use zetsoft\service\acme\AcmeCoreZoir;
use zetsoft\service\acme\checkingExpirationDate;
use zetsoft\service\acme\rwAcmeClient_Zoir;
use zetsoft\service\acme\Yaac_Zoir;
use yii\base\Component;



/**
 *
* @property AcmeCoreZoir $acmeCoreZoir
* @property checkingExpirationDate $checkingExpirationDate
* @property rwAcmeClient_Zoir $rwAcmeClient_Zoir
* @property Yaac_Zoir $yaac_Zoir

 */

class Acme extends Component
{

    
    private $_acmeCoreZoir;
    private $_checkingExpirationDate;
    private $_rwAcmeClient_Zoir;
    private $_yaac_Zoir;

    
    public function getAcmeCoreZoir()
    {
        if ($this->_acmeCoreZoir === null)
            $this->_acmeCoreZoir = new AcmeCoreZoir();

        return $this->_acmeCoreZoir;
    }
    

    public function getCheckingExpirationDate()
    {
        if ($this->_checkingExpirationDate === null)
            $this->_checkingExpirationDate = new checkingExpirationDate();

        return $this->_checkingExpirationDate;
    }
    

    public function getRwAcmeClient_Zoir()
    {
        if ($this->_rwAcmeClient_Zoir === null)
            $this->_rwAcmeClient_Zoir = new rwAcmeClient_Zoir();

        return $this->_rwAcmeClient_Zoir;
    }
    

    public function getYaac_Zoir()
    {
        if ($this->_yaac_Zoir === null)
            $this->_yaac_Zoir = new Yaac_Zoir();

        return $this->_yaac_Zoir;
    }
    


}
