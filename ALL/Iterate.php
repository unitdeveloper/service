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

use zetsoft\service\iterate\Collection1;
use zetsoft\service\iterate\CollectionTest;
use zetsoft\service\iterate\Immutable;
use zetsoft\service\iterate\KnapsakTest;
use zetsoft\service\iterate\MacrosTest;
use yii\base\Component;



/**
 *
* @property Collection1 $collection1
* @property CollectionTest $collectionTest
* @property Immutable $immutable
* @property KnapsakTest $knapsakTest
* @property MacrosTest $macrosTest

 */

class Iterate extends Component
{

    
    private $_collection1;
    private $_collectionTest;
    private $_immutable;
    private $_knapsakTest;
    private $_macrosTest;

    
    public function getCollection1()
    {
        if ($this->_collection1 === null)
            $this->_collection1 = new Collection1();

        return $this->_collection1;
    }
    

    public function getCollectionTest()
    {
        if ($this->_collectionTest === null)
            $this->_collectionTest = new CollectionTest();

        return $this->_collectionTest;
    }
    

    public function getImmutable()
    {
        if ($this->_immutable === null)
            $this->_immutable = new Immutable();

        return $this->_immutable;
    }
    

    public function getKnapsakTest()
    {
        if ($this->_knapsakTest === null)
            $this->_knapsakTest = new KnapsakTest();

        return $this->_knapsakTest;
    }
    

    public function getMacrosTest()
    {
        if ($this->_macrosTest === null)
            $this->_macrosTest = new MacrosTest();

        return $this->_macrosTest;
    }
    


}
