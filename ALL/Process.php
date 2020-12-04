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

use zetsoft\service\process\Amphp;
use zetsoft\service\process\Csvs;
use zetsoft\service\process\Gits;
use zetsoft\service\process\StopWatch;
use zetsoft\service\process\SymfonyProcess;
use yii\base\Component;



/**
 *
* @property Amphp $amphp
* @property Csvs $csvs
* @property Gits $gits
* @property StopWatch $stopWatch
* @property SymfonyProcess $symfonyProcess

 */

class Process extends Component
{

    
    private $_amphp;
    private $_csvs;
    private $_gits;
    private $_stopWatch;
    private $_symfonyProcess;

    
    public function getAmphp()
    {
        if ($this->_amphp === null)
            $this->_amphp = new Amphp();

        return $this->_amphp;
    }
    

    public function getCsvs()
    {
        if ($this->_csvs === null)
            $this->_csvs = new Csvs();

        return $this->_csvs;
    }
    

    public function getGits()
    {
        if ($this->_gits === null)
            $this->_gits = new Gits();

        return $this->_gits;
    }
    

    public function getStopWatch()
    {
        if ($this->_stopWatch === null)
            $this->_stopWatch = new StopWatch();

        return $this->_stopWatch;
    }
    

    public function getSymfonyProcess()
    {
        if ($this->_symfonyProcess === null)
            $this->_symfonyProcess = new SymfonyProcess();

        return $this->_symfonyProcess;
    }
    


}
