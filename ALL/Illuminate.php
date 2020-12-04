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

use zetsoft\service\illuminate\ZLCollection;
use zetsoft\service\illuminate\ZLSchedule;
use yii\base\Component;



/**
 *
* @property ZLCollection $zLCollection
* @property ZLSchedule $zLSchedule

 */

class Illuminate extends Component
{

    
    private $_zLCollection;
    private $_zLSchedule;

    
    public function getZLCollection()
    {
        if ($this->_zLCollection === null)
            $this->_zLCollection = new ZLCollection();

        return $this->_zLCollection;
    }
    

    public function getZLSchedule()
    {
        if ($this->_zLSchedule === null)
            $this->_zLSchedule = new ZLSchedule();

        return $this->_zLSchedule;
    }
    


}
