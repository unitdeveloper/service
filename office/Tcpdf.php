<?php
/**
 * Class    Tcpdf
 * @package zetsoft\service\office
 *
 * @author UzakbaevAxmet
 * Class docx fileni pdfga convert qiladi
 */

namespace zetsoft\service\office;

use zetsoft\system\kernels\ZFrame;

class Tcpdf extends ZFrame
{

    public function test_case() {
        $this->docxPdfTest();
    }

      public function docxPdfTest() {
          $docx='D:/test.docx'; // here havo to  be path
          $res=$this->docxPdf($docx);
          vd($res);
      }
    public function docxPdf($path)
    {
        $objReader = \PhpOffice\PhpWord\IOFactory::createReader('Word2007');
        $contents = $objReader->load($path);

        $rendername = \PhpOffice\PhpWord\Settings::PDF_RENDERER_TCPDF;
        chdir(Root . '\vendor\tecnickcom');
        $renderLibrary="TCPDF";
        $renderLibraryPath=''.$renderLibrary;
        if (!\PhpOffice\PhpWord\Settings::setPdfRenderer($rendername, $renderLibrary)) {
            die("Provide Render Library And Path");
        }
        $renderLibraryPath = '' . $renderLibrary;
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($contents, 'PDF');
        $objWriter->save("D:/office.pdf");
    }


}
