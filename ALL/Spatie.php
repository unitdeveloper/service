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

use zetsoft\service\spatie\ArrayFunction;
use zetsoft\service\spatie\ImageOptim;
use zetsoft\service\spatie\MyTask;
use zetsoft\service\spatie\RegExcp;
use zetsoft\service\spatie\SpatieString;
use zetsoft\service\spatie\SpatieTyped;
use zetsoft\service\spatie\SpatieUrl;
use zetsoft\service\spatie\TsetUrl;
use yii\base\Component;



/**
 *
* @property ArrayFunction $arrayFunction
* @property ImageOptim $imageOptim
* @property MyTask $myTask
* @property RegExcp $regExcp
* @property SpatieString $spatieString
* @property SpatieTyped $spatieTyped
* @property SpatieUrl $spatieUrl
* @property TsetUrl $tsetUrl

 */

class Spatie extends Component
{

    
    private $_arrayFunction;
    private $_imageOptim;
    private $_myTask;
    private $_regExcp;
    private $_spatieString;
    private $_spatieTyped;
    private $_spatieUrl;
    private $_tsetUrl;

    
    public function getArrayFunction()
    {
        if ($this->_arrayFunction === null)
            $this->_arrayFunction = new ArrayFunction();

        return $this->_arrayFunction;
    }
    

    public function getImageOptim()
    {
        if ($this->_imageOptim === null)
            $this->_imageOptim = new ImageOptim();

        return $this->_imageOptim;
    }
    

    public function getMyTask()
    {
        if ($this->_myTask === null)
            $this->_myTask = new MyTask();

        return $this->_myTask;
    }
    

    public function getRegExcp()
    {
        if ($this->_regExcp === null)
            $this->_regExcp = new RegExcp();

        return $this->_regExcp;
    }
    

    public function getSpatieString()
    {
        if ($this->_spatieString === null)
            $this->_spatieString = new SpatieString();

        return $this->_spatieString;
    }
    

    public function getSpatieTyped()
    {
        if ($this->_spatieTyped === null)
            $this->_spatieTyped = new SpatieTyped();

        return $this->_spatieTyped;
    }
    

    public function getSpatieUrl()
    {
        if ($this->_spatieUrl === null)
            $this->_spatieUrl = new SpatieUrl();

        return $this->_spatieUrl;
    }
    

    public function getTsetUrl()
    {
        if ($this->_tsetUrl === null)
            $this->_tsetUrl = new TsetUrl();

        return $this->_tsetUrl;
    }
    


}
