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

use zetsoft\service\webs\Errors;
use zetsoft\service\webs\Retur;
use zetsoft\service\webs\Verify;
use yii\base\Component;



/**
 *
* @property Errors $errors
* @property Retur $retur
* @property Verify $verify

 */

class Webs extends Component
{

    
    private $_errors;
    private $_retur;
    private $_verify;

    
    public function getErrors()
    {
        if ($this->_errors === null)
            $this->_errors = new Errors();

        return $this->_errors;
    }
    

    public function getRetur()
    {
        if ($this->_retur === null)
            $this->_retur = new Retur();

        return $this->_retur;
    }
    

    public function getVerify()
    {
        if ($this->_verify === null)
            $this->_verify = new Verify();

        return $this->_verify;
    }
    


}
