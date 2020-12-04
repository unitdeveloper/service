<?php

/**
 *
 *
 * Author:  Asror Zakirov
 *
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\utility;


use zetsoft\dbitem\core\CpasTrackerItem;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\kernels\ZFrame;

class Assetz extends ZFrame
{
    public const dir = [
        'widgets'   =>  '@zetsoft/widgets',
        'cache'     =>  '@zetsoft/assetz'
    ];

    private const ignore = [
        'other' => '@',
        'weak'  => '@ Weak'
    ];

    private const cdn = [
        'cf'        => 'cdnjs.cloudflare.com',
        'max'       => 'maxcdn.bootstrapcdn.com',
        'deliver'   => 'cdn.jsdelivr.net',
        'unpkg'     => 'unpkg.com',
        'rawgit'    => 'cdn.rawgit.com',
        'ionic'     => 'code.ionicframework.com',
        'bootstrap' => 'stackpath.bootstrapcdn.com',
        'rgit'      => 'rawgit.org',
        'githack'   => 'raw.githack.com',
        'static'    => 'statically.io',
    ];

    public function run()
    {
        $dir = \Yii::getAlias(self::dir['widgets']);
        $files = ZFileHelper::scanFiles($dir, true, ['*.php']);

        $urls = [];
        foreach ($files as $file) {

            if (strpos($file, self::ignore['other']) !== false || strpos($file, self::ignore['weak']) !== false) {
                echo 'skipping file: ' . $file . "\r\n";
                continue;
            }

            $fileContent = file_get_contents($file);
           // vdd($fileContent);
            // http://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css
            // https://cdn.jsdelivr.net/npm/ajaxq@0.0.7/ajaxq.js
            // https://cdnjs.cloudflare.com/ajax/libs/dompurify/2.0.8/purify.js
            // https://unpkg.com/leaflet@1.6.0/dist/leaflet.js
            // https://code.jquery.com/jquery.min.js
            $pattern = 'https?:\/\/[\w]+\.[\w]+\.[\w]+\/[\w\@\.\/\-]+';

            $data = Az::$app->utility->pregs->pregMatchAll($fileContent, $pattern);

            foreach ($data[0] as $n => $url) {
                $remoteFile = bname($url);
                $isRequired = (strpos($remoteFile, '.js') || strpos($remoteFile, '.css'));
                if ($isRequired) $urls[] = $url;
            }
        }

        $urls = array_unique($urls);

        echo 'Total scripts to download: ' . count($urls) . "\r\n";

        foreach ($urls as $n => $url) {

            $destination = \Yii::getAlias(self::dir['cache']);

            $filename = bname($url);

            try {
                $urlData = parse_url($url);

                if(!$this->isAcceptableUrl($urlData['host']))  {
                    echo "\r\n skip: ' . $url . ' \r\n";
                    echo " wrong cdn host \r\n\r\n";
                    continue;
                }

                $data = $this->scanUrlPath($urlData['path']);
                $destination .= '/' . $data->dir;

                if(!is_dir($destination))
                    mkdir($destination, '0777', true);

                $file = $destination . '/' . $filename;

                $r = fopen($url, 'r');
                if(!file_put_contents($file, $r))
                    echo "File from ' . $url . ' - was NOT loaded.\r\n";
                else {
                    echo $url . " - done.\r\n";
                    echo " File $n: $file \r\n";
                }

            } catch (\ErrorException $errorException) {
                echo $errorException->getMessage();
                echo "\r\n Url: ' . $url . '\r\n";
                echo "\r\n dir: ' . $destination . '\r\n";
                continue;
            }
        }
    }

    public function scanUrlPath($path)
    {
        $updatedPath = str_replace('@', '/', $path);
        $pathParts = explode('/', $updatedPath);
        $filename = array_pop($pathParts);
        $dirPath = implode('/', $pathParts);

        $item = new CpasTrackerItem();
        $item->dir = $dirPath;
        $item->file = $filename;

        return $item;
    }

    public function isAcceptableUrl($host)
    {
        return in_array($host, self::cdn);
    }
}
