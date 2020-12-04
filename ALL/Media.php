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

use zetsoft\service\media\Execs;
use yii\base\Component;



/**
 *
* @property Execs $execs

 */

class Media extends Component
{

    
    private $_execs;

    
    public function getExecs()
    {
        if ($this->_execs === null)
            $this->_execs = new Execs();

        return $this->_execs;
    }
    


}
