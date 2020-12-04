<?php

namespace zetsoft\service\office;

/*
 * @author Shahzod G'ulomqodirov
 */
require Root . '/vendori/fileapp/office/vendor/autoload.php';

use zetsoft\models\cpas\CpasOffer;
use zetsoft\models\cpas\CpasOfferItem;
use zetsoft\models\cpas\CpasLand;
use zetsoft\models\cpas\CpasStream;
use zetsoft\models\place\PlaceCountry;
use zetsoft\system\kernels\ZFrame;

class ZipArchive extends ZFrame
{
    public function unzip($open, $paste)
    {
        if ($this->getFormat($open) === 'tar') {
            $phar = new PharData($open);
            if (!file_exists($paste))
                mkdir($paste, 0777, true);
            $phar->extractTo($paste);
            return false;
        } else {
            $zip = new \ZipArchive();
            $res = $zip->open($open);
            if ($res === TRUE) {
                if (!file_exists($paste))
                    mkdir($paste, 0777, true);
                $zip->extractTo($paste);
                $zip->close();
                return true;
            }
        }

        //return 'test';
    }

    public static function dirToZip($folder, &$zipFile, $exclusiveLength)
    {

        $handle = opendir($folder);

        while (FALSE !== $f = readdir($handle)) {
            // Check for local/parent path or zipping file itself and skip
            if ($f != '.' && $f != '..' && $f != bname(__FILE__)) {
                $filePath = "$folder/$f";
                // Remove prefix from file path before add to zip
                $localPath = substr($filePath, $exclusiveLength);

                if (is_file($filePath)) {
                    $zipFile->addFile($filePath, $localPath);
                } elseif (is_dir($filePath)) {
                    // Add sub-directory
                    $zipFile->addEmptyDir($localPath);
                    self::dirToZip($filePath, $zipFile, $exclusiveLength);
                }
            }
        }

        closedir($handle);
    }

    public function toZip($flowId, $siteId)
    {

        $stream = CpasStream::findOne($flowId);

        $zipname = CpasStream::findOne($flowId)->name . '.zip';

        $pathdir = Root . '/render/cpanet/' . CpasOffer::findOne($stream->cpas_offer_id)->title . '/' . PlaceCountry::findOne(CpasOfferItem::findOne(CpasLand::findOne($siteId)->cpas_offer_item_id)->place_country_id)->alpha2 . '/' . CpasLand::findOne($siteId)->type . '/';

        if (file_exists($pathdir . 'call.php')) {
            $fil = file_get_contents($pathdir . 'call.php');
            $content = strtr($fil, [
                '{subid}' => CpasStream::findOne($flowId)->cpas_offer_id,
            ]);
            file_put_contents($pathdir . 'call.php', $content);
        }


        $path = $pathdir . 'thanks.php';
        if (file_exists($path)) {

            $file = file_get_contents($path);
            $cont = "\n" . $stream->yandex;
            $cont .= "\n" . $stream->google;
            $cont .= "\n" . $stream->facebook;
            $cont .= "\n" . $stream->mail;

            $content = strtr($file, [
                '</body>' => $cont . "\n</body>",
            ]);

            file_put_contents($path, $content);
            $outZipPath = Root . '\render\cpanet\\' . $zipname;

            $pathInfo = pathinfo($pathdir);

            $parentPath = $pathInfo['dirname'];

            $dirName = $pathInfo['basename'];

            $z = new \ZipArchive();
            $z->open($outZipPath, \ZipArchive::CREATE);
            $z->addEmptyDir($dirName);
            if ($pathdir === $dirName) {
                self::dirToZip($pathdir, $z, 0);
            } else {
                self::dirToZip($pathdir, $z, strlen("$parentPath/"));
            }
            $z->close();
            $url = '/render/cpanet/' . $this->getName($outZipPath) . '.zip';
            file_put_contents($path, $file);
            file_put_contents($pathdir . 'call.php', $fil);

            return $url;
        }


    }


    public function toZipNew($flowId, $siteId)
    {
        $stream = CpasStream::findOne($flowId);

        $offer = CpasOffer::findOne($stream->cpas_offer_id);

        $zipname = $stream->name . '.zip';

        $site = CpasLand::findOne($siteId);

        $offer_item = CpasOfferItem::findOne($site->cpas_offer_item_id);

        $country = PlaceCountry::findOne($offer_item->place_country_id);


        $pathdir = Root . '/render/cpanet/' . $offer->title . '/' . $country->alpha2 . '/'  ;

        $path = $pathdir . 'thanks.php';
        if (file_exists($path)) {

            $file = file_get_contents($path);

            $cont = "\n" . $stream->yandex;

            $cont .= "\n" . $stream->google;

            $cont .= "\n" . $stream->facebook;

            $cont .= "\n" . $stream->mail;


            $content = strtr($file, [
                '</body>' => $cont . "\n</body>",
            ]);

            file_put_contents($path, $content);
        }


        $outZipPath = Root . '\render\cpanet\\' . $zipname;

        $pathInfo = pathinfo($pathdir);

        $parentPath = $pathInfo['dirname'];
        $dirName = $pathInfo['basename'];

        $z = new \ZipArchive();

        $z->open($outZipPath, \ZipArchive::CREATE);

        $z->addEmptyDir($dirName);

        if ($pathdir === $dirName) {
            self::dirToZip($pathdir, $z, 0);
        } else {
            self::dirToZip($pathdir, $z, strlen("$parentPath/"));
        }

        $z->close();

        $url = '/render/cpanet/' . $this->getName($outZipPath) . '.zip';

        return $url;
    }


    public function deleteZip()
    {
        $dir = Root . '/render/cpanet/';

        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..')
                if (substr($file, -4) === '.zip')
                    \Amp\File\unlink($dir . $file);
        }


    }

    public function replace($path, $catalog_id)
    {
        if (!file_exists($path))
            return false;

        $file = file_get_contents($path);

        $content = strtr($file, [
            '{catalog_id}' => $catalog_id,
        ]);

        file_put_contents($path, $content);

        return true;
    }

    /**
     * Function file extension
     * @author Fozil Zayniddionv
     */
    public function getFormat($name)
    {
        return pathinfo($name, PATHINFO_EXTENSION);
    }

    /**
     * @param $name
     * @return string|string[]
     */
    public function getName($name)
    {
        return pathinfo($name, PATHINFO_FILENAME);
    }
}
