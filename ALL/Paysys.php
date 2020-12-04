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

use zetsoft\service\paysys\Oson;
use zetsoft\service\paysys\Pay;
use zetsoft\service\paysys\Paysysnew;
use yii\base\Component;



/**
 *
* @property Oson $oson
* @property Pay $pay
* @property Paysysnew $paysysnew

 */

class Paysys extends Component
{

    
    private $_oson;
    private $_pay;
    private $_paysysnew;

    
    public function getOson()
    {
        if ($this->_oson === null)
            $this->_oson = new Oson();

        return $this->_oson;
    }
    

    public function getPay()
    {
        if ($this->_pay === null)
            $this->_pay = new Pay();

        return $this->_pay;
    }
    

    public function getPaysysnew()
    {
        if ($this->_paysysnew === null)
            $this->_paysysnew = new Paysysnew();

        return $this->_paysysnew;
    }
    


}
