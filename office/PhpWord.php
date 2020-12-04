<?php
namespace zetsoft\service\office;

use zetsoft\system\kernels\ZFrame;

require Root . '/vendori/image/ALL/vendor/autoload.php';

class PhpWord extends ZFrame
{
    public function test(){

        $this->WordToPdf();


        /* this is the copy pase read example */
//        require_once Root . '\vendor\autoload.php';
//
//
//        $objReader = \PhpOffice\PhpWord\IOFactory::createReader('Word2007');
//        $contents = $objReader->load("D:\helloWorld.docx");
//
//        $rendername = \PhpOffice\PhpWord\Settings::PDF_RENDERER_TCPDF;
//
//        $renderLibrary = "TCPDF";
//        $renderLibraryPath = '' . $renderLibrary;
//        if (!\PhpOffice\PhpWord\Settings::setPdfRenderer($rendername, $renderLibrary)) {
//            die("Provide Render Library And Path");
//        }
//        $renderLibraryPath = '' . $renderLibrary;
//        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($contents, 'PDF');
//        $objWriter->save("D:\test.pdf");
    }
    public function WordToPdf (){

        require_once Root . '\vendor\autoload.php';

        $objReader = \PhpOffice\PhpWord\IOFactory::createReader('Word2007');
        $phpWord = $objReader->load("D:\test.docx");

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();

        $section->addText('test'. 'test'. 'test');

        $rendererName = \PhpOffice\PhpWord\Settings::PDF_RENDERER_TCPDF;
//        $rendererName = \PhpExcel\PhpWor d\Settings::PDF_RENDERER_MPDF;
//        $rendererName = \PhpExcel\PhpWord\Settings::PDF_RENDERER_DOMPDF;
        $rendererLibrary = 'tcpdf';
        $rendererLibraryPath = '' . $rendererLibrary;
        if (!\PhpOffice\PhpWord\Settings::setPdfRenderer(
            $rendererName,
            $rendererLibraryPath
        )) {
            die(
                'Error1'
            );
        }

        $renderLibraryPath = '' . $rendererLibrary;

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'PDF');
        $objWriter->save("D:\test.pdf");
    }

}
