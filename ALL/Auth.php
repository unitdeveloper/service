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

use zetsoft\service\auth\OpenAuth;
use zetsoft\service\auth\Swift;
use yii\base\Component;



/**
 *
* @property OpenAuth $openAuth
* @property Swift $swift

 */

class Auth extends Component
{

    
    private $_openAuth;
    private $_swift;

    
    public function getOpenAuth()
    {
        if ($this->_openAuth === null)
            $this->_openAuth = new OpenAuth();

        return $this->_openAuth;
    }
    

    public function getSwift()
    {
        if ($this->_swift === null)
            $this->_swift = new Swift();

        return $this->_swift;
    }
    


}
