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

use zetsoft\service\chat\Main;
use yii\base\Component;



/**
 *
* @property Main $main

 */

class Chat extends Component
{

    
    private $_main;

    
    public function getMain()
    {
        if ($this->_main === null)
            $this->_main = new Main();

        return $this->_main;
    }
    


}
