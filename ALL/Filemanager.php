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

use zetsoft\service\filemanager\Elfinder;
use zetsoft\service\filemanager\Elfinder2;
use zetsoft\service\filemanager\ElfinderTools;
use yii\base\Component;



/**
 *
* @property Elfinder $elfinder
* @property Elfinder2 $elfinder2
* @property ElfinderTools $elfinderTools

 */

class Filemanager extends Component
{

    
    private $_elfinder;
    private $_elfinder2;
    private $_elfinderTools;

    
    public function getElfinder()
    {
        if ($this->_elfinder === null)
            $this->_elfinder = new Elfinder();

        return $this->_elfinder;
    }
    

    public function getElfinder2()
    {
        if ($this->_elfinder2 === null)
            $this->_elfinder2 = new Elfinder2();

        return $this->_elfinder2;
    }
    

    public function getElfinderTools()
    {
        if ($this->_elfinderTools === null)
            $this->_elfinderTools = new ElfinderTools();

        return $this->_elfinderTools;
    }
    


}
