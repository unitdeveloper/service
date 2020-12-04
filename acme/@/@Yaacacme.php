<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\acme;

use Afosto\Acme\Client;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class Yaacacme
{
    public function addSSL($project_name, $domain_name) {
        //Prepare flysystem
        global $boot;
        $path = 'D:/Develop/Projects/ALL/asrorz/zetsoft/storing/acmev2/acme-challenge/';

        if (!file_exists($path . $project_name . '/data')) {
            $boot->mkdir($path . $project_name . '/data');
        }
        $adapter = new Local($path . $project_name . '/data');

        $filesystem = new Filesystem($adapter);

        //Construct the client
        $client = new Client([
            'username' => 'zetsoft@gmail.com',
            'fs'       => $filesystem,
            'mode'     => Client::MODE_LIVE,
        ]);

        $order = $client->createOrder([$domain_name]);

        $authorizations = $client->authorize($order);
        foreach ($authorizations as $authorization) {
            $file = $authorization->getFile();
            file_put_contents($path . $file->getFilename(), $file->getContents());

            if (!$client->selfTest($authorization, Client::VALIDATION_HTTP)) {
                throw new \Exception('Count not verify ownership via HTTP');
            }
        }

        foreach ($authorizations as $authorization) {
            $client->validate($authorization->getHttpChallenge(), 100);
        }

        if ($client->isReady($order)) {
            $certificate = $client->getCertificate($order);
            echo $certificate;
            echo "Success!";
            //    file_put_contents('D:/Develop/Projects/ALL/server/nginx/conf/appssl/sert.zetsoft.uz/certificate.cert', $certificate->getCertificate());
            //    file_put_contents('D:/Develop/Projects/ALL/server/nginx/conf/appssl/sert.zetsoft.uz/private.key', $certificate->getPrivateKey());
        } else {
            echo "Error!";
        }


    }
}
