<?php


namespace zetsoft\service\office;

use Jurosh\PDFMerge\PDFMerger;
use zetsoft\system\kernels\ZFrame;

require Root . '/vendors/fileapp/office/vendor/autoload.php';


class JuroshPdfMerge extends ZFrame
{

    public function mergePdfsFromArrayTest()
    {
        $files=[
            'D:\1.pdf',
            'D:\2.pdf'
        ];
        $this->mergePdfsFromArray($files);
    }


    // Merge Docx files
    public function mergePdfsFromArray($files, $type)
    {
        $pdf = new PDFMerger;

        $ids = '';
        foreach ($files as $key => $file)
        {
            if ($type === 'multiRouteList')
                $pdf->addPDF($file['pdf_full_path'],'all', 'horizontal');
            else
                $pdf->addPDF($file['pdf_full_path']);

            if ( $key < count($files)-1 )
                $ids .= $file['id'].'-';
            else
                $ids .= $file['id'];
        }
        $out_dir = Root . '/upload/uploaz/market/';
        $out_pdf = $out_dir.$type.'-'.$ids.'.pdf';

        // call merge, output format `file`
        $pdf->merge('file', $out_pdf);
        $merged_pdf_url = '/uploaz/market/'.$type.'-'.$ids.'.pdf';
        return $merged_pdf_url;

    }

}


