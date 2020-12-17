<?php

namespace zetsoft\service\acme;

/*
 * @author SukhrobNuraliev
 * https://packagist.org/packages/afosto/yaac
 */

require Root . '/vendors/acme/vendor/autoload.php';

use zetsoft\system\kernels\ZFrame;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use Afosto\Acme\Client;

class Yaac extends ZFrame
{
    public string $filePath = Root . '/hoster/appssl/';

    public function getssl($domain, $domainwww)
    {
        //Prepare flysystem
        $adapter = new Local('data');
        $filesystem = new Filesystem($adapter);

        //Construct the client
        $client = new Client([
            'username' => 'zoirbek.sobirov@mail.ru',
            'fs'       => $filesystem,
            'mode'     => Client::MODE_STAGING,
        ]);


        $order = $client->createOrder([$domain, $domainwww]);
        $authorizations = $client->authorize($order);

        foreach ($authorizations as $authorization) {
            $file = $authorization->getFile();
            file_put_contents($file->getFilename(), $file->getContents());
        }

        foreach ($authorizations as $authorization) {
            $client->validate($authorization->getHttpChallenge(), 15);
        }

        if ($client->isReady($order)) {
            //The validation was successful.
            echo 'success';
        }
        var_dump($order);
        $certificate = $client->getCertificate($order);

        $path = $this->filePath . $domain;

        //Store the certificate and private key where you need it
        file_put_contents($path . '/certificateTest.cert', $certificate->getCertificate());

        file_put_contents($path . '/privateTest.key', $certificate->getPrivateKey());
    }
}
