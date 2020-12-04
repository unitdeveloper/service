<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\office;


use DateTime;
use zetsoft\system\kernels\ZFrame;
use Ellumilel\ExcelWriter;

class PhpExcelWriter extends ZFrame
{
    public function test(){
        $this->ExcelWriter('1st month', '2nd month', '3rd month',1,2,3,'D:/qwerty','.xlsx');
    }

    public function ExcelWriter($header1, $header2, $header3, $data1, $data2, $data3, $path, $ext = 'xlsx'){
        $start = microtime(true);
        $header = [
            'Date' => 'YYYY-MM-DD HH:MM:SS',
            $header1 => 'string',
            $header2 => 'string',
            $header3 => 'string',
        ];



        $wExcel = new ExcelWriter();
        $wExcel->writeSheetHeader('Sheet1', $header);
        $wExcel->setAuthor('Your name here');
        for ($i = 0; $i < 5000; $i++) {
            $wExcel->writeSheetRow('Sheet1', [
                (new DateTime())->format('Y-m-d H:i:s'),
                $data1,
                $data2,
                $data3
            ]);
        }
        $wExcel->writeToFile($path.$ext);
        $time = microtime(true) - $start;
        echo round(memory_get_usage() / 1048576, 2)." megabytes";
        printf("Complete after %.4F sec.\n", $time);
    }



    public function TwoSheet($header1, $header2, $header3, $header4, $data1, $data2, $data3, $data4, $path, $ext = 'xlsx'){
        $start = microtime(true);
        $data1 = [
            [$header1, $header2],
            [$data1, $data2],
        ];
        $data2 = [
            [$header3, $header4 ],
            [$data3, $data4 ],
        ];
        $wExcel = new ExcelWriter();
        $wExcel->setAuthor('Tester');
        $wExcel->writeSheet($data1, 'Sheet11');
        $wExcel->writeSheet($data2, 'Sheet22');
        $wExcel->writeToFile($path.$ext);
        $time = microtime(true) - $start;
        printf("Complete after: %.4F sec.\n", $time);
    }
    public function One ($path, $ext = 'xlsx') {
        $start = microtime(true);
        $header = [
            'test_order' => 'date',
            'test2' => 'string',
            'test3' => 'euro',
            'test4' => 'dollar',
            'test5' => 'float',
            'test6' => 'float_with_sep',
            'test7' => 'string',
        ];
        $wExcel = new Ellumilel\ExcelWriter();
        $wExcel->setAuthor('Tester');
        $wExcel->writeSheetHeader('Sheet1', $header);
        for ($j = 0; $j < 100; $j++) {
            $wExcel->writeSheetRow('Sheet1', [
                (new DateTime())->format('Y-m-d'),
                rand(1000, 10000),
                rand(1000, 10000),
                rand(1000, 10000),
                rand(1000, 10000),
                rand(1000, 10000),
                '=HYPERLINK("http://yandex.ru/asd'.rand(1000, 10000).'/sdf='.rand(1000, 10000).'","ссылка")',
            ]);
        }
        $wExcel->writeToFile($path.$ext);
        $time = microtime(true) - $start;
        printf("Complete after: %.4F sec.\n", $time);
    }
}
