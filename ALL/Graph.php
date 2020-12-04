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

use zetsoft\service\graph\InputUserType;
use zetsoft\service\graph\MutationType;
use zetsoft\service\graph\QueryType;
use zetsoft\service\graph\StartGraph;
use zetsoft\service\graph\Types;
use zetsoft\service\graph\UserType;
use yii\base\Component;



/**
 *
* @property InputUserType $inputUserType
* @property MutationType $mutationType
* @property QueryType $queryType
* @property StartGraph $startGraph
* @property Types $types
* @property UserType $userType

 */

class Graph extends Component
{

    
    private $_inputUserType;
    private $_mutationType;
    private $_queryType;
    private $_startGraph;
    private $_types;
    private $_userType;

    
    public function getInputUserType()
    {
        if ($this->_inputUserType === null)
            $this->_inputUserType = new InputUserType();

        return $this->_inputUserType;
    }
    

    public function getMutationType()
    {
        if ($this->_mutationType === null)
            $this->_mutationType = new MutationType();

        return $this->_mutationType;
    }
    

    public function getQueryType()
    {
        if ($this->_queryType === null)
            $this->_queryType = new QueryType();

        return $this->_queryType;
    }
    

    public function getStartGraph()
    {
        if ($this->_startGraph === null)
            $this->_startGraph = new StartGraph();

        return $this->_startGraph;
    }
    

    public function getTypes()
    {
        if ($this->_types === null)
            $this->_types = new Types();

        return $this->_types;
    }
    

    public function getUserType()
    {
        if ($this->_userType === null)
            $this->_userType = new UserType();

        return $this->_userType;
    }
    


}
