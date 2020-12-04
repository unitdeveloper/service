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

use zetsoft\service\valid\ZValidator;
use yii\base\Component;



/**
 *
* @property ZValidator $zValidator

 */

class Valid extends Component
{

    
    private $_zValidator;

    
    public function getZValidator()
    {
        if ($this->_zValidator === null)
            $this->_zValidator = new ZValidator();

        return $this->_zValidator;
    }
    


}
