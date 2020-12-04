<?php

namespace zetsoft\service\phpdoc;


require Root.'/vendori/phpdc/vendor/autoload.php';


use zetsoft\system\kernels\ZFrame;
use Phpactor\Docblock\DocblockFactory;
use Phpactor\Docblock\Tag\VarTag;

/*
 * class PhpactorDocblock
 * @package zetsoft/service/phpdoc
 *https://packagist.org/packages/phpdocumentor/reflection-docblock
 * With this component, a library can provide support for annotations
 * via DocBlocks or otherwise retrieve information that is embedded in a DocBlock.
 * @author by Keldiyor
 */

class PhpactorDocblock extends ZFrame
{
        public function example(){

            /*
             * In order to parse the DocBlock one needs a DocBlockFactory
             * that can be instantiated using its createInstance factory method like this:
             */
            $factory  = \phpDocumentor\Reflection\DocBlockFactory::createInstance();
            //vd($factory);
///////////////////////////////////////////////////////////////////////////////////////////////////////////////

            /*
             * Then we can use the create method of the factory to interpret the DocBlock.
             * Please note that it is also possible to provide a class that has the getDocComment() method,
             * such as an object of type ReflectionClass, the create method will read that if it exists.
             */

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
            //vd($docblock);
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
            /*
             * The create method will yield an object of type \phpDocumentor\Reflection\DocBlock
             * whose methods can be queried:
             */

            // Contains the summary for this DocBlock
            $summary = $docblock->getSummary();

// Contains \phpDocumentor\Reflection\DocBlock\Description object
            $description = $docblock->getDescription();

// You can either cast it to string
            $description = (string) $docblock->getDescription();

// Or use the render method to get a string representation of the Description.
            $description = $docblock->getDescription()->render();
        //    vd($description);
///////////////////////////////////////////////////////////////////////////////////////////////////////////


        }
}
