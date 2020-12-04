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

use zetsoft\service\App\All\Arbit;
use zetsoft\service\App\All\Eyuf;
use zetsoft\service\App\All\Vade;
use yii\base\Component;


/**
 *
* @property Arbit $arbit
* @property Eyuf $eyuf
* @property Vade $vade

 */

class App extends Component
{

    
    private $_arbit;
    private $_eyuf;
    private $_vade;

    
    public function getArbit()
    {
        if ($this->_arbit === null)
            $this->_arbit = new Arbit();

        return $this->_arbit;
    }
    

    public function getEyuf()
    {
        if ($this->_eyuf === null)
            $this->_eyuf = new Eyuf();

        return $this->_eyuf;
    }
    

    public function getVade()
    {
        if ($this->_vade === null)
            $this->_vade = new Vade();

        return $this->_vade;
    }
    


}
