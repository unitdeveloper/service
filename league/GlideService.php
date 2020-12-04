<?php
/**
 * Class    GlideService
 * @package zetsoft\service\league
 *
 * @author DilshodKhudoyarov
 *
 * Documentation - https://glide.thephpleague.com/
 *
 * Glide - bu tezkor va samarali ochiladigan ommaviy axborot vositalarini boshqarish
 * va Android uchun rasmlarni yuklash asosi bo'lib,
 * u ommaviy axborotni dekodlash, xotira va disk keshini o'zgartiradi.
 */

namespace zetsoft\service\league;

use League\Glide\ServerFactory;
use zetsoft\system\kernels\ZFrame;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class GlideService extends ZFrame
{
    public function test_case() {
        $this->get_image_responseTest();
        $this->get_image_flysystemTest();
    }

    public function get_image_responseTest() {
        $file_folder ='Give a file folder!';
        $cache_folder ='Give a cache folder!';
        $output_path ='Give a path for the output!';
        $result=$this->get_image_response($file_folder, $cache_folder, $output_path);
        vd($result);
    }

    //Next, within your routes, setup your Glide server. Configure where the source images can be found,
    // as well as where the manipulated images should be saved (the cache).
    // Finally pass the server the request. It will handle all the image manipulations and will output the image.

     public function get_image_response($file_folder, $cache_folder, $output_path) {
         // Setup Glide server
         $server = ServerFactory::create([
             'source' => $file_folder,
             'cache' => $cache_folder,
         ]);

         // You could manually pass in the image path and manipulations options
         $server->getImageResponse($output_path, ['w' => 300, 'h' => 400]);

         // But, a better approach is to use information from the request
         $server->getImageResponse($output_path, $_GET);
     }

    public function get_image_flysystemTest() {
        $file_folder ='Give a file folder!';
        $cache_folder ='Give a cache folder!';
        $output_path ='Give a path for the output!';
        $result=$this->get_image_flysystem($file_folder, $cache_folder, $output_path);
        vd($result);
    }

//To set your source and cache locations, simply pass an instance of League\Flysystem\Filesystem
// for each. See the Flysystem website for a complete list of available adapters.

     public function get_image_flysystem($file_folder, $cache_folder, $output_path) {
         $server = ServerFactory::create([
             'source' => new Filesystem(new Local($file_folder)),
             'cache' => new Filesystem(new Local($cache_folder)),
         ]);

         // You could manually pass in the image path and manipulations options
         $server->outputImage($output_path, ['w' => 200, 'h' => 300]);

         // But, a better approach is to use information from the request
         $server->outputImage($output_path, $_GET);
     }
}
