<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\reacts;



class FileSystem
{
   public function dirRename() {
       $loop = \React\EventLoop\Factory::create();
       $filesystem = \React\Filesystem\Filesystem::create($loop);
       $dir = $filesystem->dir('new');

       $dir->rename('new_name')->then(function(\React\Filesystem\Node\DirectoryInterface $newDir){
           echo 'Renamed to ' . $newDir->getPath() . PHP_EOL;
       }, function(\Exception $e) {
           echo 'Error: ' . $e->getMessage('') . PHP_EOL;
       });

       $loop->run();
   }

}
