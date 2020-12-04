<?php

namespace zetsoft\service\acme;

/**
 * @author Umid Muminov
 * https://packagist.org/packages/afosto/yaac
 */

require Root . '/vendori/acme/vendor/autoload.php';

use zetsoft\system\kernels\ZFrame;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use Afosto\Acme\Client;

class Yaac_u extends ZFrame
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
            var_dump($authorization);
            file_put_contents($file->getFilename(), $file->getContents());

            if (!$client->selfTest($authorization, Client::VALIDATION_HTTP)) {
                throw new \Exception('Could not verify ownership via HTTP');
            }
        }

        /*foreach ($authorizations as $authorization) {
            $txtRecord = $authorization->getTxtRecord();

            //To get the name of the TXT record call:
            $txtRecord->getName();

            //To get the value of the TXT record call:
            $txtRecord->getValue();
        }*/

        foreach ($authorizations as $authorization) {
            $client->validate($authorization->getHttpChallenge(), 15);
        }

        if ($client->isReady($order)) {
            //The validation was successful.
            vdd('success');
        }

        $certificate = $client->getCertificate($order);

        $path = $this->filePath . $domain;

        //Store the certificate and private key where you need it
        file_put_contents($path . '/certificateTest.cert', $certificate->getCertificate());

        file_put_contents($path . '/privateTest.key', $certificate->getPrivateKey());
    }
}
