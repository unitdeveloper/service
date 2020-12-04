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

use zetsoft\service\fronts\Select2;
use yii\base\Component;



/**
 *
* @property Select2 $select2

 */

class Fronts extends Component
{

    
    private $_select2;

    
    public function getSelect2()
    {
        if ($this->_select2 === null)
            $this->_select2 = new Select2();

        return $this->_select2;
    }
    


}
