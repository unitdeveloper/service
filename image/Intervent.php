<?php

/**
 * Author:  Xolmat Ravshanov
 * Date: 31.05.2020
 *
 */

namespace zetsoft\service\image;


use Intervention\Image as Image;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\kernels\ZFrame;


class Intervent extends ZFrame
{

    public $id;
    public ?string $modelClassName = null;
    public $attribute;

    public $width;
    public $height;
    public $image;
    public $url;
    private $path;

    public function init()
    {
        parent::init();



    }



    public function example(){
        // open an image file
        $img = Image::make('D:/image/1.jpg');

        // now you are able to resize the instance
        $img->resize(320, 240);

        // and insert a watermark for example
        $img->insert('D:/image/4.png');

        // finally we save the image as a new file
        $img->save('D:/image/4.jpg');
    }




    public function path(){
        $this->path = Root . '/upload/uploaz/eyuf/';
        switch (true) {
            case empty($this->id) && empty($this->attribute):
                $this->path .= "$this->modelClassName/";
                break;
            case empty($this->id):
                $this->path .= "$this->modelClassName/$this->attribute/";
                break;
            default:
                $this->path .= "$this->modelClassName/$this->attribute/$this->id/";
        }
        return $this->path;
    }

    public function scan($path){
        if(is_dir($path))
        return ZFileHelper::findFiles($path);
      
    }


    public function resize($filePath)
    {
   
        $files = $this->scan($filePath);
        if (empty($files))
            return null;

        if(file_exists($this->path())){
            foreach($files as $file){
                $fileName = bname($file);
                $info = pathinfo($fileName);
                $file_name =  bname($fileName,'.'.$info['extension']);
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                $allowed = array('gif', 'png', 'jpg', 'webp');
                if (in_array($ext, $allowed)) {

                    Image::make($file)->resize(1024, 1024)->save($this->path.$file_name."_1024"."_"."1024.".$info['extension']);


                    Image::make($file)->resize(400, 400)->save($this->path.$file_name."_400"."_"."400.".$info['extension']);


                    Image::make($file)->resize(80, 80)->save($this->path.$file_name."_80"."_"."80.".$info['extension']);

                }else{
                    continue;
                }

            }
        }

    }


}
