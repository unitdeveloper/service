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

use zetsoft\service\phpdoc\Annotations;
use zetsoft\service\phpdoc\GossiDocblock;
use zetsoft\service\phpdoc\Notoj;
use zetsoft\service\phpdoc\PhpactorDocblock;
use zetsoft\service\phpdoc\ReflectionDocblock;
use yii\base\Component;



/**
 *
* @property Annotations $annotations
* @property GossiDocblock $gossiDocblock
* @property Notoj $notoj
* @property PhpactorDocblock $phpactorDocblock
* @property ReflectionDocblock $reflectionDocblock

 */

class Phpdoc extends Component
{

    
    private $_annotations;
    private $_gossiDocblock;
    private $_notoj;
    private $_phpactorDocblock;
    private $_reflectionDocblock;

    
    public function getAnnotations()
    {
        if ($this->_annotations === null)
            $this->_annotations = new Annotations();

        return $this->_annotations;
    }
    

    public function getGossiDocblock()
    {
        if ($this->_gossiDocblock === null)
            $this->_gossiDocblock = new GossiDocblock();

        return $this->_gossiDocblock;
    }
    

    public function getNotoj()
    {
        if ($this->_notoj === null)
            $this->_notoj = new Notoj();

        return $this->_notoj;
    }
    

    public function getPhpactorDocblock()
    {
        if ($this->_phpactorDocblock === null)
            $this->_phpactorDocblock = new PhpactorDocblock();

        return $this->_phpactorDocblock;
    }
    

    public function getReflectionDocblock()
    {
        if ($this->_reflectionDocblock === null)
            $this->_reflectionDocblock = new ReflectionDocblock();

        return $this->_reflectionDocblock;
    }
    


}
