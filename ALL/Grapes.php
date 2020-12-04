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

use zetsoft\service\grapes\Grape;
use zetsoft\service\grapes\One;
use yii\base\Component;



/**
 *
* @property Grape $grape
* @property One $one

 */

class Grapes extends Component
{

    
    private $_grape;
    private $_one;

    
    public function getGrape()
    {
        if ($this->_grape === null)
            $this->_grape = new Grape();

        return $this->_grape;
    }
    

    public function getOne()
    {
        if ($this->_one === null)
            $this->_one = new One();

        return $this->_one;
    }
    


}
