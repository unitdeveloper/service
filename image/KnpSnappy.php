<?php


namespace zetsoft\service\image;


use zetsoft\system\kernels\ZFrame;
use Knp\Snappy\Image;

class KnpSnappy extends ZFrame
{
    public function uploadImageFromBrowser()
    {
        $snappy = new Image('/upload/uploaz/eyuf/wkhtmltoimage');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="file.jpg"');
        
        echo $snappy->getOutput('http://www.github.com');
    }

    public function getImageToBrowser()
    {
        //
    }
}
