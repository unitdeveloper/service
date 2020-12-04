<?php

namespace zetsoft\service\office;
use zetsoft\system\kernels\ZFrame;


class textToPdf extends ZFrame
{
    public function txt_html($path)
    {
        $get_txt = explode('.',$path)[0].'.html';
        $old_path = getcwd();
        chdir('../../scripts/convert/');
        $output = shell_exec('pandoc ' .$path. ' -o ' . $get_txt);
        chdir($old_path);
        return $output;
    }
    public function txt_tex($path)
    {
        $get_txt = explode('.',$path)[0].'.tex';
        $old_path = getcwd();
        chdir('../../scripts/convert/');
        $output = shell_exec('pandoc -s ' . $path . ' -o ' . $get_txt);
        chdir($old_path);
        return $output;
    }
    public function tex_txt($path)
    {
        $get_txt = explode('.',$path)[0].'.txt';
        $old_path = getcwd();
        chdir('../../scripts/convert/');
        $output = shell_exec('pandoc -s ' . $path . ' -o ' . $get_txt);
        chdir($old_path);
        return $output;
    }
    public function txt_docx($path)
    {
        $get_txt = explode('.',$path)[0].'.docx';
        $old_path = getcwd();
        chdir('../../scripts/convert/');
        $output = shell_exec('pandoc -s ' . $path . ' -o ' . $get_txt);
        chdir($old_path);
        return $output;

    }
    public function docx_txt($path)
    {
        $get_txt = explode('.',$path)[0].'.txt';
        $old_path = getcwd();
        chdir('../../scripts/convert/');
        $output = shell_exec('pandoc -s ' . $path . ' -o ' . $get_txt);
        chdir($old_path);
        return $output;

    }
    public function  txt_pdf()
    {
        //$get_txt = explode('.','D:/text.txt')[0].'.txt';
        $old_path = getcwd  ();
        chdir('../../scripts/convert/');
        $output = shell_exec('pandoc D:/MANUAL.txt  --pdf-engine=weasyprint -o example13.pdf');
        chdir($old_path);
        return $output;
    }
    public function txt_xml($path)
    {
        $get_txt = explode('.',$path)[0].'.xml';
        $old_path = getcwd();
        chdir('../../scripts/convert/');
        $output = shell_exec('pandoc ' . $path . ' -s -t opendocument -o ' . $get_txt);
        chdir($old_path);
        return $output;
    }

    public function txt_epub($path)
    {
        $get_txt = explode('.',$path)[0].'.epub';
        $old_path = getcwd();
        chdir('../../scripts/convert/');
        $output = shell_exec('pandoc '. $path .' -o ' . $get_txt);
        chdir($old_path);
        return $output;
    }
    public function epub_txt($path)
    {
        $get_txt = explode('.',$path)[0].'.txt';
        $old_path = getcwd();
        chdir('../../scripts/convert/');
        $output = shell_exec('pandoc '. $path .' -o ' . $get_txt);
        chdir($old_path);
        return $output;
    }

    public function txt_rtf($path)
    {
        $get_txt = explode('.',$path)[0].'.rtf';
        $old_path = getcwd();
        chdir('../../scripts/convert/');
        $output = shell_exec('pandoc -s ' . $path . ' -o ' . $get_txt);
        chdir($old_path);
        return $output;
    }
    //pandoc MANUAL.txt --pdf-engine=xelatex -o example13.pdf
    public function doc_pdf()
    {
       // $doc = explode('.',$path)[0].'.pdf';
        $old_path = getcwd();
        chdir('../../scripts/convert/');
        $output = shell_exec('pandoc D:/Zoir.docx --pdf-engine=weasyprint -o Zoir.pdf');
        chdir($old_path);
        return $output;
    }
}
