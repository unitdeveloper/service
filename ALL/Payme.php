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

use zetsoft\service\payme\Click;
use zetsoft\service\payme\Uzcard;
use yii\base\Component;



/**
 *
* @property Click $click
* @property Uzcard $uzcard

 */

class Payme extends Component
{

    
    private $_click;
    private $_uzcard;

    
    public function getClick()
    {
        if ($this->_click === null)
            $this->_click = new Click();

        return $this->_click;
    }
    

    public function getUzcard()
    {
        if ($this->_uzcard === null)
            $this->_uzcard = new Uzcard();

        return $this->_uzcard;
    }
    


}
