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

use zetsoft\service\string\CocurSlugify;
use yii\base\Component;



/**
 *
* @property CocurSlugify $cocurSlugify

 */

class String extends Component
{

    
    private $_cocurSlugify;

    
    public function getCocurSlugify()
    {
        if ($this->_cocurSlugify === null)
            $this->_cocurSlugify = new CocurSlugify();

        return $this->_cocurSlugify;
    }
    


}
