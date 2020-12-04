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

use zetsoft\service\guid\PascaldevinkShortUUID;
use zetsoft\service\guid\RamseyUuid;
use zetsoft\service\guid\SGuid;
use yii\base\Component;



/**
 *
* @property PascaldevinkShortUUID $pascaldevinkShortUUID
* @property RamseyUuid $ramseyUuid
* @property SGuid $sGuid

 */

class Guid extends Component
{

    
    private $_pascaldevinkShortUUID;
    private $_ramseyUuid;
    private $_sGuid;

    
    public function getPascaldevinkShortUUID()
    {
        if ($this->_pascaldevinkShortUUID === null)
            $this->_pascaldevinkShortUUID = new PascaldevinkShortUUID();

        return $this->_pascaldevinkShortUUID;
    }
    

    public function getRamseyUuid()
    {
        if ($this->_ramseyUuid === null)
            $this->_ramseyUuid = new RamseyUuid();

        return $this->_ramseyUuid;
    }
    

    public function getSGuid()
    {
        if ($this->_sGuid === null)
            $this->_sGuid = new SGuid();

        return $this->_sGuid;
    }
    


}
