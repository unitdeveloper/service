<?php


namespace zetsoft\service\office;

require Root . '/vendors/fileapp/office/vendor/autoload.php';

use zetsoft\system\kernels\ZFrame;

class Mpdf extends ZFrame
{

    public $generatedFile;
    #region GetDocFile
    public function getDocFile($path){
        $mpdf = new \Mpdf\Mpdf();
        $text = $mpdf->writeHTML($path);
        $vi = $mpdf->output($text);
        vdd($vi);

    }
}   #endRegion
