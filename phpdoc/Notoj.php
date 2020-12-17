<?php

namespace zetsoft\service\phpdoc;


require Root.'/vendors/phpdc/vendor/autoload.php';


use Notoj\ReflectionClass;
use zetsoft\service\App\eyuf\User;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\kernels\ZFrame;

/*
 * class Notoj
 * @package zetsoft/service/phpdoc
 * https://packagist.org/packages/crodas/notoj
 * Annotation parser. Uses reflection and provides cache out of the box.
 * @author Keldiyor
 */

class Notoj extends ZFrame
{
   #region example

    public function fileDoc(){

        $parser = new \Notoj\File("D:\Develop\Projects\ALL\asrorz\zetsoft\cncmd\\tester\XolmatController.php");

        $something =   $parser->getAnnotations();

        vd($something);
    }

    /**
     *
     * Function  example
     * @param int $arg1
     * @param string $arg2
     * @param \zetsoft\models\user\User $user
     */

    public function example(){

       $dirPath =   ZFileHelper::scanFolder('D:\Develop\Projects\ALL\asrorz\zetsoft\vendors\somefiles');
            
        $parser = new \Notoj\Dir( $dirPath); // The parser is recursive
//        vdd($parser);

        $parser->setFilter(function($file) {
            vdd($file);
            return true;
        });
        
        $annotations = $parser->getAnnotations();
        //vdd($annotations);

        foreach ($annotations->get('vendors\somefiles\nimadir.php') as $annotations) {
            foreach ($annotations as $annotation) {
                vd(
                    "found @Foo\Bar at " . $annotation['file']
                    . ($annotation->isClass() ? ' on a class ' : ' on something else other than a class')
                );
            }
        }
        
    }






    #endregion
}
