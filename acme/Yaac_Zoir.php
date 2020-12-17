<?php
/**
 * Author:  Zoirjon Sobirov
 * @license  Zoirjon Sobirov
 * linkedIn: https://www.linkedin.com/in/zoirjon-sobirov/
 * Telegram: https://t.me/zoirjon_sobirov
 * @copyright zhead, zstart, zend
 */

namespace zetsoft\service\acme;

require Root . '/vendors/acme/vendor/autoload.php';

use zetsoft\system\kernels\ZFrame;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use Afosto\Acme\Client;

class Yaac_Zoir  extends ZFrame
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

        //Create an order
        $order = $client->createOrder([$domain, $domainwww]);

        //Prove ownership
        $authorizations = $client->authorize($order);
         var_dump($authorizations);
        //HTTP validation . HTTP validation (where serve specific content at a specific url on the domain, like: example.org/.well-known/acme-challenge/*) is done
       /* foreach ($authorizations as $authorization) {
            $file = $authorization->getFile();
            file_put_contents($file->getFilename(), $file->getContents());
        }
            var_dump("file\n");
            var_dump($file);

        //  Self test. After exposing the challenges (made accessible through HTTP or DNS) we should perform a self test just to be sure it works before asking Let's Encrypt to validate ownership.
        if (!$client->selfTest($authorization, Client::VALIDATION_HTTP)) {
            throw new \Exception('Could not verify ownership via HTTP');
        } */
        
        // DNS validation
        foreach ($authorizations as $authorization) {
            $txtRecord = $authorization->getTxtRecord();

            //To get the name of the TXT record call:
            $txtRecord->getName();

            //To get the value of the TXT record call:
            $txtRecord->getValue();
        var_dump($txtRecord);
        }
        
        //  Self test. After exposing the challenges (made accessible through HTTP or DNS) we should perform a self test just to be sure it works before asking Let's Encrypt to validate ownership.
        if (!$client->selfTest($authorization, Client::VALIDATION_DNS)) {
            throw new \Exception('Could not verify ownership via DNS');
        }
        sleep(60); // this further sleep is recommended, depending on your DNS provider, see below
       
       
        // HTTP validation
        /*foreach ($authorizations as $authorization) {
            $client->validate($authorization->getHttpChallenge(), 15);
        }
        //Validation Status
        if ($client->isReady($order)) {
            //The validation was successful.
            vdd('success');
        }

        //Obtain Certificate
        $certificate = $client->getCertificate($order);

        $path = $this->filePath . $domain;

        //Store the certificate and private key where you need it
        file_put_contents($path . '/certificateTest.cert', $certificate->getCertificate());

        file_put_contents($path . '/privateTest.key', $certificate->getPrivateKey()); */
    }
}
