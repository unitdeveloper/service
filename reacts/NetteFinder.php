<?php


namespace zetsoft\service\reacts;


use zetsoft\system\kernels\ZFrame;

class NetteFinder extends ZFrame
{
    public function test(){
        foreach (Finder::findFiles('*.txt')->in($dir) as $key => $file) {
            echo $key;
            echo $file;
        }
    }
}
