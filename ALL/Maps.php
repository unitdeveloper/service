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

use zetsoft\service\maps\Matrix;
use zetsoft\service\maps\MatrixGoogle;
use zetsoft\service\maps\navigation;
use zetsoft\service\maps\textToSpeech;
use yii\base\Component;



/**
 *
* @property Matrix $matrix
* @property MatrixGoogle $matrixGoogle
* @property navigation $navigation
* @property textToSpeech $textToSpeech

 */

class Maps extends Component
{

    
    private $_matrix;
    private $_matrixGoogle;
    private $_navigation;
    private $_textToSpeech;

    
    public function getMatrix()
    {
        if ($this->_matrix === null)
            $this->_matrix = new Matrix();

        return $this->_matrix;
    }
    

    public function getMatrixGoogle()
    {
        if ($this->_matrixGoogle === null)
            $this->_matrixGoogle = new MatrixGoogle();

        return $this->_matrixGoogle;
    }
    

    public function getNavigation()
    {
        if ($this->_navigation === null)
            $this->_navigation = new navigation();

        return $this->_navigation;
    }
    

    public function getTextToSpeech()
    {
        if ($this->_textToSpeech === null)
            $this->_textToSpeech = new textToSpeech();

        return $this->_textToSpeech;
    }
    


}
