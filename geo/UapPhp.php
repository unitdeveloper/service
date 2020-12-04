<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\geo;
require_once Root . '/vendor/autoload.php';
use UAParser\Parser;
use zetsoft\system\kernels\ZFrame;



class UapPhp extends ZFrame
{

    public $dbase = Root . '\vendor\ua-parser\uap-php\resources\regexes.php';
    public function test($ip){

        vdd($this->getIp($ip));

    }

    public function getIp($ua)
    {
        $res = '????';
        //$ua = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7) Gecko/20040803 Firefox/0.9.3";

       /* $parser = Parser::create($this->dbase);
        $result = $parser->parse($ua);*/
        $result = file_get_contents($this->dbase);
        foreach ($result as $k => $v){
            if(preg_match($k, $ua)){
               $res = $v;
            }
        }
        return  $res;
       /* echo $result->ua->family;
        echo $result->ua->major;             // 6
        echo $result->ua->minor;             // 0
        echo $result->ua->patch;             // 2
        echo $result->ua->toString();        // Safari 6.0.2
        echo $result->ua->toVersion();       // 6.0.2


        echo $result->device->family;        // Other

        echo $result->toString();            // Safari 6.0.2/Mac OS X 10.7.5
        echo $result->os->family;            // Mac OS X
        echo $result->os->major;             // 10
        echo $result->os->minor;             // 7
        echo $result->os->patch;             // 5
        echo $result->os->patchMinor;        // [null]
        echo $result->os->toString();        // Mac OS X 10.7.5
        echo $result->os->toVersion();       // 10.7.5
        echo $result->ua->major;*/

    }


}
