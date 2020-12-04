<?php

/**
 * Author:
 * Xolmat Ravshanov
 * Date: 31.06.2020
 */

namespace zetsoft\service\filemanager;


use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\kernels\ZFrame;


class ElfinderTools extends ZFrame
{


    public function filterPath($path, $arrayEx)
    {
    
        $files = ZFileHelper::findFiles($path);

        foreach ($arrayEx as $array) {
            $files = array_filter($files, function ($k) use ($array) {
                $k = bname($k);
                if ($k === $array || !ZStringHelper::endsWith($k, '.php'))
                    return false;
                else
                    return true;

            });
        }
        $filteredFiles = [];
        foreach ($files as $file)
            $filteredFiles[] = trim(bname($file));

        $filteredFiles = implode("|", $filteredFiles);

        return $filteredFiles;

    }

    public function copyPasteFile($path, $dest)
    {

         $path = Root.'/webhtm/'.$path;

        $status = false;

        $fileName = strtr(bname($path), [
            '.php'=> ''
        ]);
        
        $fileName = $fileName.'_' . 'eyuf.php';

        if (file_exists($dest . $fileName))
            return $status;

        if (!copy($path, $dest . $fileName))
            return $status;
        else
            $status = true;

            return $status;

    }

}


