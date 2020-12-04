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

use zetsoft\service\jsonb\ExportToJson;
use zetsoft\service\jsonb\ExportToJson_U;
use yii\base\Component;



/**
 *
* @property ExportToJson $exportToJson
* @property ExportToJson_U $exportToJson_U

 */

class Jsonb extends Component
{

    
    private $_exportToJson;
    private $_exportToJson_U;

    
    public function getExportToJson()
    {
        if ($this->_exportToJson === null)
            $this->_exportToJson = new ExportToJson();

        return $this->_exportToJson;
    }
    

    public function getExportToJson_U()
    {
        if ($this->_exportToJson_U === null)
            $this->_exportToJson_U = new ExportToJson_U();

        return $this->_exportToJson_U;
    }
    


}
