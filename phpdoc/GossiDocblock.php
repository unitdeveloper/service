<?php

namespace zetsoft\service\phpdoc;


require Root.'/vendori/phpdc/vendor/autoload.php';


use zetsoft\system\kernels\ZFrame;
use Phpactor\Docblock\DocblockFactory;
use Phpactor\Docblock\Tag\VarTag;
use gossi\docblock\Docblock;
use gossi\docblock\tags\AuthorTag;


/*
 * class PhpactorDocblock
 * @package zetsoft/service/phpdoc
 * https://packagist.org/packages/gossi/docblock
 * PHP Docblock parser and generator. An API to read and write Docblocks.
 * @author by Keldiyor
 */

class GossiDocblock extends ZFrame
{
    public function example()
    {

        /*
         * 1. Generate a Docblock instance
         * a) Simple:
         */
//        $docblock = new Docblock();
//        vd($docblock);


        /*
         * b) Create from string:
         */
//        $docblock = new Docblock('/**
// * Short Description.
// *
// * Long Description.
// *
// * @author gossi
// */'
//        );
//        vd($docblock);

            /*
             * 2 Manipulate tags
             *  Get the tags:
             */
//        $docblock = new Docblock();
//        $tags = $docblock->getTags();
//        vd($tags);

            /*
             * Get tags by name:
             */
//        $docblock = new Docblock();
//        $tags = $docblock->getTags('author');
//        vd($tags);


        /*
         * Append a tag:
         */
//        $docblock = new Docblock();
//        $author = new AuthorTag();
//        $author->setName('gossi');
//        $docblock->appendTag($author);
//        vd($docblock);


            /*
             * or with fluent API:
             */
//        $docblock = new Docblock();
//        $docblock->appendTag(AuthorTag::create()
//            ->setName('gossi'));
//        vd($docblock);


            /*
             * Check tag existence:
             */
//        $docblock = new Docblock();
//        $docblock->hasTag('author');
//        vd($docblock);


        /*
         * 3. Get back the string
         * Call toString():
         */
//        $docblock = new Docblock();
//        $docblock->toString();
//        vd($docblock);


    }

}
