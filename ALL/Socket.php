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

use zetsoft\service\socket\RatchetchatTest;
use zetsoft\service\socket\Server;
use zetsoft\service\socket\ZSocketIo;
use yii\base\Component;



/**
 *
* @property RatchetchatTest $ratchetchatTest
* @property Server $server
* @property ZSocketIo $zSocketIo

 */

class Socket extends Component
{

    
    private $_ratchetchatTest;
    private $_server;
    private $_zSocketIo;

    
    public function getRatchetchatTest()
    {
        if ($this->_ratchetchatTest === null)
            $this->_ratchetchatTest = new RatchetchatTest();

        return $this->_ratchetchatTest;
    }
    

    public function getServer()
    {
        if ($this->_server === null)
            $this->_server = new Server();

        return $this->_server;
    }
    

    public function getZSocketIo()
    {
        if ($this->_zSocketIo === null)
            $this->_zSocketIo = new ZSocketIo();

        return $this->_zSocketIo;
    }
    


}
