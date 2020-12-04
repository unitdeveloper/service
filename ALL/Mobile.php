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

use zetsoft\service\mobile\DeviceDetection;
use zetsoft\service\mobile\MobileDetecting;
use yii\base\Component;



/**
 *
* @property DeviceDetection $deviceDetection
* @property MobileDetecting $mobileDetecting

 */

class Mobile extends Component
{

    
    private $_deviceDetection;
    private $_mobileDetecting;

    
    public function getDeviceDetection()
    {
        if ($this->_deviceDetection === null)
            $this->_deviceDetection = new DeviceDetection();

        return $this->_deviceDetection;
    }
    

    public function getMobileDetecting()
    {
        if ($this->_mobileDetecting === null)
            $this->_mobileDetecting = new MobileDetecting();

        return $this->_mobileDetecting;
    }
    


}
