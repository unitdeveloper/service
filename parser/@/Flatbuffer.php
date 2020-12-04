<?php
/**
 * class Flatbuffer
 * @package zetsoft/service/parser
 * @author UzakbaevAxmet
 * Class maksimal xotira samaradorligi uchun arxivlangan xoch platformali seriyalashtirilgan kutubxona.
 */
namespace zetsoft\service\parser;

use zetsoft\system\kernels\ZFrame;

class Flatbuffer extends ZFrame
{


    public function test()
    {
        $this->flatbuffer();
    }

    public function flatbuffer()
    {
        require(__DIR__ . '/../../vendor/autoload.php');
        chdir(Root . '\vendor\google\flatbuffers\tests');
        require('phpTest.php');
// It is recommended that your use PSR autoload when using FlatBuffers in PHP.
// Here is an example:
        function __autoload($class_name)
        {
            // The last segment of the class name matches the file name.
            $class = substr($class_name, strrpos($class_name, "\\") + 1);
            $root_dir = join(DIRECTORY_SEPARATOR, array(dirname(dirname(__FILE__)))); // `flatbuffers` root.
            // Contains the `*.php` files for the FlatBuffers library and the `flatc` generated files.
            $paths = array(join(DIRECTORY_SEPARATOR, array($root_dir, "php")),
                join(DIRECTORY_SEPARATOR, array($root_dir, "tests", "MyGame", "Example")));
            foreach ($paths as $path) {
                $file = join(DIRECTORY_SEPARATOR, array($path, $class . ".php"));
                if (file_exists($file)) {
                    require($file);
                    break;
                }
            }
// Read the contents of the FlatBuffer binary file.
            $filename = "monster.dat";
            $handle = fopen($filename, "rb");
            $contents = fread($handle, filesize($filename));
            fclose($handle);
// Pass the contents to `GetRootAsMonster`.
            $monster = \MyGame\Example\Monster::GetRootAsMonster($contents);


            $hp = $monster->GetHp();
            $pos = $monster->GetPos();

        }
    }
}
