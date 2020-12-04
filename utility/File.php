<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\utility;


use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\kernels\ZFrame;
use PhpOffice\PhpSpreadsheet\IOFactory;
use zetsoft\system\module\Models;

class File extends ZFrame
{



    /**
     * Function  readByLine
     * @param $file
     * @return  array
     */
    public function readByLine($file)
    {
        $s = fopen($file,"r");

        while(! feof($s))  {
            $str = trim(fgets($s));

            if(!empty($str))
                $result[] = $str;
        }

        fclose($s);

        return $result;
    }

    /**
     *
     * Function  removeString
     * @param $value
     * @param $arr
     * @param bool $reorder
     * @param bool $uniq
     * @return  array
     */
    public function removeString($value, $arr, $reorder = false, $uniq = false)
    {
        $k = array_search("$value", $arr);

        if($k)
            unset($arr[$k]);

        if($reorder)
            $arr = array_values($arr);

        if($uniq)
            $arr = array_unique($arr);

        return $arr;
    }

    /**
     *
     * Function  arrToFile
     * @param $file
     * @param $data
     */
    public function arrToFile($file, $data)
    {
        $content = implode("\r\n", $data);
        Az::debug($content, 'app.txt content');
        file_put_contents($file, "$content\r\n");
    }
}
