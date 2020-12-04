<?php


namespace zetsoft\service\markup;


use zetsoft\system\kernels\ZFrame;
require_once Root . '/vendor/autoload.php';

class HtmlFormatter extends ZFrame
{
    public function test($code)
    {
        echo $this->format($code);
    }

    public function format($code)
    {

        $formater = new \Mihaeu\HtmlFormatter();
        if(substr($code, -5) === '.html'){
            $file = file_get_contents($code);
            $code = $file;
        }
        $formatedCode = $formater::format($code);

        return $formatedCode;
    }



}
