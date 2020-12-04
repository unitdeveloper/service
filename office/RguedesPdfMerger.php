<?php


namespace zetsoft\service\office;

use PDFMerger;
use zetsoft\system\kernels\ZFrame;

require Root . '/vendori/fileapp/office/vendor/autoload.php';


class RguedesPdfMerger extends ZFrame
{

    public function mergePdfsFromArrayTest()
    {
        $files=[
            'D:\Example_package\1.pdf',
            'D:\Example_package\2.pdf'
        ];
        $this->mergePdfsFromArray($files);
    }

    /*public function mergePdfsFromArray($files, $type)
    {
        $path_arr = [];

        $dir_path = explode('\\', $files[0]);
        foreach ($dir_path as $key => $str) {
            if ($key != count($dir_path) - 1)
                $path_arr[] = $str;
        }

        $path = implode('\\', $path_arr) . '\\';

        $random_name = 'mergedfile-' .$this->myId();
        $filepath = $path.$random_name.'.pdf';

        $pdf = new PDFMerger;

        $pdf->addPDF($files[0],'all');
        $pdf->addPDF($files[1],'all');

        // add as many pdfs as you want
//        foreach ($files as $file)
//            $pdf->addPDF($file);



        // call merge, output format `file`
        $pdf->merge('file', $filepath);

        return $filepath;

    }*/










    public function mergePdfsFromArray($files)
    {
        $pdf = new PDFMerger();

        $out_dir = 'D:\Example_package';
        $out_pdf = $out_dir.'pdf';

        // call merge, output format `file`
        $pdf->merge('file', $out_pdf);
        $merged_pdf_url = 'D:\Example_package'.'.pdf';
        return $merged_pdf_url;

    }


}


