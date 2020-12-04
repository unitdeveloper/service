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

use zetsoft\service\temps\Themes;
use yii\base\Component;



/**
 *
* @property Themes $themes

 */

class Temps extends Component
{

    
    private $_themes;

    
    public function getThemes()
    {
        if ($this->_themes === null)
            $this->_themes = new Themes();

        return $this->_themes;
    }
    


}
