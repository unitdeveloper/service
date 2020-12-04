<?php

namespace zetsoft\service\office;

use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use zetsoft\system\kernels\ZFrame;


class PhpWordOb extends ZFrame
{
    public function test()
    {

        $this->WordToPdf();

    }

    private function read_docx($docx_file){
        $striped_content = '';
        $content = '';
        $zip = zip_open($docx_file);
        if (!$zip || is_numeric($zip)) return false;
        while ($zip_entry = zip_read($zip)) {
            if (zip_entry_open($zip, $zip_entry) == FALSE) continue;
            if (zip_entry_name($zip_entry) !== "word/document.xml") continue;
            $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
            zip_entry_close($zip_entry);
        }// end while
        zip_close($zip);
        $content = str_replace(array('</w:r></w:p></w:tc><w:tc>', '</w:r></w:p>'), array(" ", "\r\n"), $content);
        $striped_content = strip_tags($content);
        
        return $striped_content;
    }

    public function wordToPdf()
    {

        $FilePath = Root . '\binary\words\1C\Акт_передачи\example.docx';
        $FilePathPdf = Root . '\binary\words\1C\Акт_передачи\example1.pdf';

        $pdf = fopen($FilePathPdf, 'wb') or die('Unable to open file!');
        $word = fopen($FilePath, 'rb');

        //$domPdfPath = dirname(realpath(__DIR__)) . '\vendor\dompdf\dompdf';

        fwrite($pdf, $this->read_docx($FilePath));

        fclose($pdf);

        //Settings::setPdfRendererPath($domPdfPath);

        //Settings::setPdfRendererName('DOMPDF');

        /*$phpWord = IOFactory::load($FilePath);
        try {
            $pdfWriter = IOFactory::createWriter($phpWord, 'PDF');
            vdd($pdfWriter);
            $pdfWriter->save($FilePathPdf);
        } catch (Exception $e) {
            print ('error in save pdf file');
        }*/

    }

}
