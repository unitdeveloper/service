<?php


namespace zetsoft\service\office;

use Jupitern\Docx\DocxMerge;
use zetsoft\system\kernels\ZFrame;

require Root . '/vendori/fileapp/office/vendor/autoload.php';


class JupiternDocs extends ZFrame
{

    // Merge Docx files
    public function mergeDocsFromArray($files)
    {
        $path_arr = [];

        $dir_path = explode('\\', $files[0]);
        foreach ($dir_path as $key => $str) {

            if ($key != count($dir_path) - 1)
                $path_arr[] = $str;

        }

        $path = implode('\\', $path_arr) . '\\';

        $random_name = 'mergedfile-' .$this->myId();
        $filepath = $path.$random_name;

        $docxMerge = DocxMerge::instance()
            // add array of files to merge
            ->addFiles($files)
            // output filepath and pagebreak param
            ->save($filepath.'.docx', true);

        return [
            'filepath' => $filepath,
            'random_name' => $random_name
        ];


    }

}


