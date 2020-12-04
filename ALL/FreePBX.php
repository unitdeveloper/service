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

use zetsoft\service\freePBX\PBXExtension;
use zetsoft\service\freePBX\PBXwebdriver;
use zetsoft\service\freePBX\PBXwebdriver2;
use zetsoft\service\freePBX\PBXwebdriverIsmet;
use yii\base\Component;



/**
 *
* @property PBXExtension $pBXExtension
* @property PBXwebdriver $pBXwebdriver
* @property PBXwebdriver2 $pBXwebdriver2
* @property PBXwebdriverIsmet $pBXwebdriverIsmet

 */

class FreePBX extends Component
{

    
    private $_pBXExtension;
    private $_pBXwebdriver;
    private $_pBXwebdriver2;
    private $_pBXwebdriverIsmet;

    
    public function getPBXExtension()
    {
        if ($this->_pBXExtension === null)
            $this->_pBXExtension = new PBXExtension();

        return $this->_pBXExtension;
    }
    

    public function getPBXwebdriver()
    {
        if ($this->_pBXwebdriver === null)
            $this->_pBXwebdriver = new PBXwebdriver();

        return $this->_pBXwebdriver;
    }
    

    public function getPBXwebdriver2()
    {
        if ($this->_pBXwebdriver2 === null)
            $this->_pBXwebdriver2 = new PBXwebdriver2();

        return $this->_pBXwebdriver2;
    }
    

    public function getPBXwebdriverIsmet()
    {
        if ($this->_pBXwebdriverIsmet === null)
            $this->_pBXwebdriverIsmet = new PBXwebdriverIsmet();

        return $this->_pBXwebdriverIsmet;
    }
    


}
