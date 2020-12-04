<?php

/**
 * @param $is_pdf -> pdf formatiga otqazish (false bo'sa o'tmidi) return bool
 *
 * @License OtabekNosirov
 * @License JaloliddinovSalohiddin
 * @License AkromovAzizjon
 * @author JaloliddinovSalohiddin
 * @author AkromovAzizjon
 *
 * @author OtabekNosirov
 */

namespace zetsoft\service\office;

require Root . '/vendori/fileapp/office/vendor/autoload.php';


use DocxMerge\DocxMerge;
use zetsoft\system\kernels\ZFrame;

class MergeFiles extends ZFrame
{
    public bool $is_pdf = true;

    public function init()
    {
        parent::init();
    }


    #region MERGE DOCUMENTS VIA DIR

    /**
     * @param  : $file_dir -> folderni ichidagi hammi file ni merge qiladi yani bitta file ga birlashtiradi
     * @param  : $extension -> merge qilinadigan file larni (.pdf , .docx , etc.) o'lchamda saqlab qo'yadi
     * @author AkromovAzizjon
     *
     * @method MergeDocsViaDir
     * @author OtabekNosirov
     * @author JaloliddinovSalohiddin
     * @license OtabekNosirov
     * @license JaloliddinovSalohiddin
     * @license AkromovAzizjon
     *
     */
    public function mergeDocsViaDir($file_dir = Root . '/binary/words/1C/Акт_передачи/', $extension = '.docx')
    {
        $files = glob($file_dir . '*' . $extension);
        $filepath = $file_dir . 'mergedfile1' . $extension;

        $dm = new DocxMerge();
        $dm->merge($files, $filepath, true);

        return $filepath;
    }

    #endregion MERGE DOCUMENTS

    #region MERGE DOCUMENTS FROM ARRAY
    /**
     * @method  MergeDocsFromArray
     * @param  $files -> array type da keladigan filelarni pathi
     * @author OtabekNosirov
     * @author JaloliddinovSalohiddin
     * @author AkromovAzizjon
     */
    public function mergeDocsFromArray($files)
    {

        $path_arr = [];

        $dir_path = explode('\\', $files[0]);
        foreach ($dir_path as $key => $str) {

            if ($key != count($dir_path) - 1) {
                $path_arr[] = $str;
            }

        }

        $path = implode('\\', $path_arr) . '\\';

        $random_name = 'mergedfile-' . $this->myId();
        $filepath = $path.$random_name;

        $dm = new DocxMerge();

        $dm->merge($files, $filepath, true);

        return [
            'filepath' => $filepath,
            'random_name' => $random_name
        ];
    }

    #endregion MERGE DOCUMENTS

    #region docxToPdf

    public function docxToPdf($path)
    {

        $docx = fopen($path, 'rb');

        $dir_path = explode('\\', $path);

        foreach ($dir_path as $key => $str) {

            if ($key != count($dir_path) - 1)
                $path_arr[] = $str;

        }

        $path = implode('\\', $path_arr) . '\\';

        $pdf = fopen($path . '\\formated', 'wb');
    }

    #endregion docxToPdf

}
