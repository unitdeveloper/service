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

use zetsoft\service\inputs\DepDrops;
use zetsoft\service\inputs\DepDrops_;
use zetsoft\service\inputs\Depend;
use zetsoft\service\inputs\Fileinput;
use zetsoft\service\inputs\Ravshan;
use zetsoft\service\inputs\test_ammo;
use zetsoft\service\inputs\Typeaheads;
use yii\base\Component;



/**
 *
* @property DepDrops $depDrops
* @property DepDrops_ $depDrops_
* @property Depend $depend
* @property Fileinput $fileinput
* @property Ravshan $ravshan
* @property test_ammo $test_ammo
* @property Typeaheads $typeaheads

 */

class Inputs extends Component
{

    
    private $_depDrops;
    private $_depDrops_;
    private $_depend;
    private $_fileinput;
    private $_ravshan;
    private $_test_ammo;
    private $_typeaheads;

    
    public function getDepDrops()
    {
        if ($this->_depDrops === null)
            $this->_depDrops = new DepDrops();

        return $this->_depDrops;
    }
    

    public function getDepDrops_()
    {
        if ($this->_depDrops_ === null)
            $this->_depDrops_ = new DepDrops_();

        return $this->_depDrops_;
    }
    

    public function getDepend()
    {
        if ($this->_depend === null)
            $this->_depend = new Depend();

        return $this->_depend;
    }
    

    public function getFileinput()
    {
        if ($this->_fileinput === null)
            $this->_fileinput = new Fileinput();

        return $this->_fileinput;
    }
    

    public function getRavshan()
    {
        if ($this->_ravshan === null)
            $this->_ravshan = new Ravshan();

        return $this->_ravshan;
    }
    

    public function getTest_ammo()
    {
        if ($this->_test_ammo === null)
            $this->_test_ammo = new test_ammo();

        return $this->_test_ammo;
    }
    

    public function getTypeaheads()
    {
        if ($this->_typeaheads === null)
            $this->_typeaheads = new Typeaheads();

        return $this->_typeaheads;
    }
    


}
