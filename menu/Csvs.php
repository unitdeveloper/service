<?php
/**
 *
 * Author:  Asror Zakirov
 * Created: 29.06.2017 19:06
 * https://www.linkedin.com/in/asror-zakirov-167961a9
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 */

namespace zetsoft\service\blogic\shop;


use ParseCsv\Csv;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\kernels\ZFrame;


class Csvs extends ZFrame
{
    /**
     *
     * Function  actionRun
     * @throws \Exception
     */

    public function run()
    {
        //   $this->testMove();
        $this->all();
    }


    public function testMove()
    {
        $src = 'D:\Develop\Interface\___\Form\01e9 adaptive-switch';
        $dest = 'D:\Develop\Interface\Inputs\Switch\CSSJS\@ Dead\01e9 Adaptive-Switch 2 stars';

        $this->move($src, $dest);
    }


    public function all()
    {
        $file = 'd:\Develop\Interface\___\duplicates.csv';

        $csv = new Csv();
        $csv->conditions = 'author does not contain dan brown';
        $csv->auto($file);

        $all = [];

        // print_r($csv->data);

        foreach ($csv->data as $data) {
            $all[$data['Group']][] = $data;
        }


//        print_r($all);

        foreach ($all as $items) {
            $this->procs($items);
        }
    }


    public function procs($datas)
    {
        $destapp = '';
        $sources = [];

        foreach ($datas as $data) {
            $path = $data['Is Marked'];

            if (ZStringHelper::find($path, '___'))
                $sources[] = $path;
            else
                $destapp = $path;


            /*

            if (ZStringHelper::find($path, '___'))
                $sources[] = $path;
            else
                $destapp = $path;
            
             */


        }

        if (empty($destapp))
            $destapp = $datas[0]['Is Marked'];

        foreach ($sources as $source) {
            $this->move($source, $destapp);
        }

        return Az::debug($destapp, 'Processed: ');
    }

    public function move($src, $dest)
    {
        if (!file_exists($src))
            return false;

        $files = ZFileHelper::findFiles($src);

        foreach ($files as $file) {
            $fileName = bname($file);
            $destFile = "$dest/$fileName";
            Az::debug("Moving $file to $destFile");
            rename($file, $destFile);
        }

        $files = ZFileHelper::findFiles($src);
        if (empty($files))
            ZFileHelper::removeDirectory($src);

        return true;

    }


}
