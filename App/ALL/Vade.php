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

namespace zetsoft\service\App\ALL;

use zetsoft\service\App\vade\ZCode;
use zetsoft\service\App\vade\ZQrCode;
use yii\base\Component;



/**
 *
* @property ZCode $zCode
* @property ZQrCode $zQrCode

 */

class Vade extends Component
{

    
    private $_zCode;
    private $_zQrCode;

    
    public function getZCode()
    {
        if ($this->_zCode === null)
            $this->_zCode = new ZCode();

        return $this->_zCode;
    }
    

    public function getZQrCode()
    {
        if ($this->_zQrCode === null)
            $this->_zQrCode = new ZQrCode();

        return $this->_zQrCode;
    }
    


}
