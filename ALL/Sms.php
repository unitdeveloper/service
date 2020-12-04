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

use zetsoft\service\sms\Eskiz;
use zetsoft\service\sms\Eskiz2;
use zetsoft\service\sms\EskizXolmat;
use zetsoft\service\sms\misollar;
use yii\base\Component;



/**
 *
* @property Eskiz $eskiz
* @property Eskiz2 $eskiz2
* @property EskizXolmat $eskizXolmat
* @property misollar $misollar

 */

class Sms extends Component
{

    
    private $_eskiz;
    private $_eskiz2;
    private $_eskizXolmat;
    private $_misollar;

    
    public function getEskiz()
    {
        if ($this->_eskiz === null)
            $this->_eskiz = new Eskiz();

        return $this->_eskiz;
    }
    

    public function getEskiz2()
    {
        if ($this->_eskiz2 === null)
            $this->_eskiz2 = new Eskiz2();

        return $this->_eskiz2;
    }
    

    public function getEskizXolmat()
    {
        if ($this->_eskizXolmat === null)
            $this->_eskizXolmat = new EskizXolmat();

        return $this->_eskizXolmat;
    }
    

    public function getMisollar()
    {
        if ($this->_misollar === null)
            $this->_misollar = new misollar();

        return $this->_misollar;
    }
    


}
