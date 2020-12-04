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

use zetsoft\service\image\Intervent;
use zetsoft\service\image\KnpSnappy;
use yii\base\Component;



/**
 *
* @property Intervent $intervent
* @property KnpSnappy $knpSnappy

 */

class Image extends Component
{

    
    private $_intervent;
    private $_knpSnappy;

    
    public function getIntervent()
    {
        if ($this->_intervent === null)
            $this->_intervent = new Intervent();

        return $this->_intervent;
    }
    

    public function getKnpSnappy()
    {
        if ($this->_knpSnappy === null)
            $this->_knpSnappy = new KnpSnappy();

        return $this->_knpSnappy;
    }
    


}
