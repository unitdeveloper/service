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

use zetsoft\service\select\DepdropDb;
use zetsoft\service\select\Select2;
use zetsoft\service\select\Select2Xolmat;
use yii\base\Component;



/**
 *
* @property DepdropDb $depdropDb
* @property Select2 $select2
* @property Select2Xolmat $select2Xolmat

 */

class Select extends Component
{

    
    private $_depdropDb;
    private $_select2;
    private $_select2Xolmat;

    
    public function getDepdropDb()
    {
        if ($this->_depdropDb === null)
            $this->_depdropDb = new DepdropDb();

        return $this->_depdropDb;
    }
    

    public function getSelect2()
    {
        if ($this->_select2 === null)
            $this->_select2 = new Select2();

        return $this->_select2;
    }
    

    public function getSelect2Xolmat()
    {
        if ($this->_select2Xolmat === null)
            $this->_select2Xolmat = new Select2Xolmat();

        return $this->_select2Xolmat;
    }
    


}
