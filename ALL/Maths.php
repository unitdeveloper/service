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

use zetsoft\service\maths\Combine;
use zetsoft\service\maths\Complex;
use zetsoft\service\maths\Math;
use zetsoft\service\maths\MathExecutor;
use zetsoft\service\maths\MathPhp;
use yii\base\Component;



/**
 *
* @property Combine $combine
* @property Complex $complex
* @property Math $math
* @property MathExecutor $mathExecutor
* @property MathPhp $mathPhp

 */

class Maths extends Component
{

    
    private $_combine;
    private $_complex;
    private $_math;
    private $_mathExecutor;
    private $_mathPhp;

    
    public function getCombine()
    {
        if ($this->_combine === null)
            $this->_combine = new Combine();

        return $this->_combine;
    }
    

    public function getComplex()
    {
        if ($this->_complex === null)
            $this->_complex = new Complex();

        return $this->_complex;
    }
    

    public function getMath()
    {
        if ($this->_math === null)
            $this->_math = new Math();

        return $this->_math;
    }
    

    public function getMathExecutor()
    {
        if ($this->_mathExecutor === null)
            $this->_mathExecutor = new MathExecutor();

        return $this->_mathExecutor;
    }
    

    public function getMathPhp()
    {
        if ($this->_mathPhp === null)
            $this->_mathPhp = new MathPhp();

        return $this->_mathPhp;
    }
    


}
