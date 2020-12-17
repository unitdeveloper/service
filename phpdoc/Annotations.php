<?php

namespace zetsoft\service\phpdoc;


require Root.'/vendors/phpdc/vendor/autoload.php';

use Minime\Annotations\Cache\FileCache;
use Minime\Annotations\Reader;
use Minime\Annotations\Parser;
use Minime\Annotations\Cache\ArrayCache;
use zetsoft\system\kernels\ZFrame;

use Minime\Annotations\Interfaces\ParserInterface;
use Minime\Annotations\Types\IntegerType;



/*
 * class minime/annotations
 * @package zetsoft/service/phpdoc
 * https://packagist.org/packages/minime/annotations
 * The KISS PHP annotations library
 * @author by Keldiyor
 */



class Annotations extends ZFrame
{
    public function example(){

        //First grab an instance of the Minime\Annotations\Reader the lazy way:
        $reader = \Minime\Annotations\Reader::createFromDefaults();
        //vd($reader);

        //Or instantiate the annotations reader yourself with:
        $reader = new Reader(new Parser, new ArrayCache);
        vd($reader);


        //Let's use the Minime\Annotations\Reader instance to read annotations from classes,
        // properties and methods. Like so:
        $annotations = $reader->getClassAnnotations('ReflectionClass');
       // vd($annotations);
        $annotations->get('name');
       // vd($annotations);      // > string(3) "Foo"
        $annotations->get('accept');
    //    vd($annotations);   // > array(3){ [0] => "json" [1] => "xml" [2] => "csv" }
        $annotations->get('delta');
   //     vd($annotations);     // > double(0.60)
        $annotations->get('cache-duration');
   //     vd($annotations);    // > int(60)
        $annotations->get('undefined');  // > null
     //   vd($annotations);

        $reader->getCache()->clear();
       // vd($reader);


    }


}

