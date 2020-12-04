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

use Dompdf\Dompdf;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Yii;
use yii\helpers\Html;
use zetsoft\models\user\UserCompany;
use zetsoft\system\kernels\ZFrame;



class GeneratePdf extends ZFrame
{
    public function init()
    {
        parent::init();


    }
    public function createPdf()

    {

        $html =
            '<html><body>'.
            '<p>Some text</p>'.
            '</body></html>';

        $dompdf = new Dompdf();

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'landscape');

        $dompdf->render();
        /*$output = $dompdf->output();
        file_put_contents('/other/testSalohiddin.pdf', $output);*/
        $dompdf->stream('/upload/excelz/eyuf/testSalohiddin'.".pdf");
         $dompdf->setBasePath("/other/testSalohiddin'.\".pdf");

    }

}

