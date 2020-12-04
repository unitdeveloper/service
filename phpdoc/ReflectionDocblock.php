<?php

namespace zetsoft\service\phpdoc;


require Root.'/vendori/phpdc/vendor/autoload.php';

use Minime\Annotations\Cache\FileCache;
use Minime\Annotations\Reader;
use Minime\Annotations\Parser;
use Minime\Annotations\Cache\ArrayCache;
use zetsoft\system\kernels\ZFrame;

use Minime\Annotations\Interfaces\ParserInterface;
use Minime\Annotations\Types\IntegerType;



/*
 * class  ReflectionDocblock
 * @package zetsoft/service/phpdoc
 * https://packagist.org/packages/phpdocumentor/reflection-docblock
 * Ushbu komponent yordamida kutubxona DocBlocks orqali izohlarni qo'llab-quvvatlashi yoki
 * boshqa usul bilan DocBlock-ga kiritilgan ma'lumotlarni olishlari mumkin.
 * @author by Keldiyor
 */



class ReflectionDocblock extends ZFrame
{
    public function example(){

        $factory  = \phpDocumentor\Reflection\DocBlockFactory::createInstance();
       // vd($factory);

        $docComment = <<<DOCCOMMENT
/**
 * This is an example of a summary.
 *
 * This is a Description. A Summary and Description are separated by either
 * two subsequent newlines (thus a whiteline in between as can be seen in this
 * example), or when the Summary ends with a dot (`.`) and some form of
 * whitespace.
 */
DOCCOMMENT;

        $docblock = $factory->create($docComment);
       // vd($docblock);


        // Contains the summary for this DocBlock
        $summary = $docblock->getSummary();

// Contains \phpDocumentor\Reflection\DocBlock\Description object
        $description = $docblock->getDescription();

// You can either cast it to string
        $description = (string) $docblock->getDescription();

// Or use the render method to get a string representation of the Description.
        $description = $docblock->getDescription()->render();
       // vd($description);

    }


}

