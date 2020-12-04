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

namespace zetsoft\service\office;

use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Yii;
use yii\helpers\Html;
use zetsoft\system\kernels\ZFrame;



class Pdf extends ZFrame
{
    
    public function convertHtmlToPdf(/*$html*/){

        $mpdf = new Mpdf();
        $mpdf->WriteHTML('utf-8', 4, '12', '');
        $mpdf->Output(__DIR__ . './full_cw_with_paceholder.pdf', Destination::FILE);

       /* header("Content-Type: application/pdf");
        header("Content-Disposition:inline;filename=\"invoice.pdf\"");
        header("Content-Transfer-Encoding: binary");

        ob_start();
        include 'file.php';
        $wav = ob_get_contents();
        ob_end_clean();

        include("mpdf60/mpdf.php");

        $mpdf = new mPDF('utf-8', 'A4', '12', '', 10, 10, 7, 7, 10, 10);

        $stylesheet = file_get_contents('css/invoice.css'); //подключаем css
        $mpdf->WriteHTML($html);

        $mpdf->WriteHTML($wav, 2); //формируем pdf
        $mpdf->Output('file.pdf', 'I');*/
        
    }

}

