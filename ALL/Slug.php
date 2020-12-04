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

use zetsoft\service\slug\CocurSlugify;
use zetsoft\service\slug\VokuUrlify;
use yii\base\Component;



/**
 *
* @property CocurSlugify $cocurSlugify
* @property VokuUrlify $vokuUrlify

 */

class Slug extends Component
{

    
    private $_cocurSlugify;
    private $_vokuUrlify;

    
    public function getCocurSlugify()
    {
        if ($this->_cocurSlugify === null)
            $this->_cocurSlugify = new CocurSlugify();

        return $this->_cocurSlugify;
    }
    

    public function getVokuUrlify()
    {
        if ($this->_vokuUrlify === null)
            $this->_vokuUrlify = new VokuUrlify();

        return $this->_vokuUrlify;
    }
    


}
