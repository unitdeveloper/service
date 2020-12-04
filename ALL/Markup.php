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

use zetsoft\service\markup\CssTidy;
use zetsoft\service\markup\HtmlFormatter;
use yii\base\Component;



/**
 *
* @property CssTidy $cssTidy
* @property HtmlFormatter $htmlFormatter

 */

class Markup extends Component
{

    
    private $_cssTidy;
    private $_htmlFormatter;

    
    public function getCssTidy()
    {
        if ($this->_cssTidy === null)
            $this->_cssTidy = new CssTidy();

        return $this->_cssTidy;
    }
    

    public function getHtmlFormatter()
    {
        if ($this->_htmlFormatter === null)
            $this->_htmlFormatter = new HtmlFormatter();

        return $this->_htmlFormatter;
    }
    


}
