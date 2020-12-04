<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\App\eyuf;

use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Yii;
use yii\helpers\Html;
use zetsoft\system\kernels\ZFrame;



class Pdf extends ZFrame
{
    
    public function convertHtmlToPdf($html){
        
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->Output(__DIR__ . './full_cw_with_paceholder.pdf', Destination::FILE);
        
    }

}

