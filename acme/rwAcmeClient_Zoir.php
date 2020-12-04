<?php
/**
 * Author:  Zoirjon Sobirov
 * @license  Zoirjon Sobirov
 * linkedIn: https://www.linkedin.com/in/zoirjon-sobirov/
 * Telegram: https://t.me/zoirjon_sobirov
 * @copyright zhead, zstart, zend
 */

namespace zetsoft\service\acme;

require Root . '/vendori/rwacme/autoload.php';

use zetsoft\system\kernels\ZFrame;
use Rogierw\RwAcme\Api;
use League\Flysystem\Adapter\Local;
use Afosto\Acme\Client;

class rwAcmeClient_Zoir  extends ZFrame
{
    private $mailAccount = 'zoirbek.sobirov@mail.ru';
    public string $filePath = Root . '/hoster/appssl/';

    public function getssl($domain)
    {
        var_dump("Creating an account\n");
        //Creating an account
        $client = new Api($this->mailAccount, __DIR__ . '/__account');
        if (!$client->account()->exists()) {
            $account = $client->account()->create();
        }else{
            // get an existing account.
              $account = $client->account()->get();
        }
        var_dump("Account is created\n");


        var_dump("Creating an order\n");

        //Creating an order
        $order = $client->order()->new($account, [$domain]);
        var_dump("Order is created\n");

         //Getting an order
        $order = $client->order()->get($order->id);

        var_dump("Domain validation is started\n");
        //Domain validation && Getting the DCV status
        $validationStatus = $client->domainValidation()->status($order);
        var_dump("Domain validation is ended\n");

        var_dump("Validation data\n");
        // Get the data for the HTTP challenge; filename and content.
        $validationData = $client->domainValidation()->getFileValidationData($validationStatus);
        var_dump($validationData);
        /* @todo
         *
         * Dns Validation
         * Check validation status
         * */

         //Start domain validation
        try {
            $client->domainValidation()->start($account, $validationStatus[0]);
        } catch (DomainValidationException $exception) {
            // The local HTTP challenge test has been failed...
        }

        //getting CRS key
        $privateKey = \Rogierw\RwAcme\Support\OpenSsl::generatePrivateKey();
        $csr = \Rogierw\RwAcme\Support\OpenSsl::generateCsr([$domain], $privateKey);

        //finalizing
        if ($order->isReady() && $client->domainValidation()->challengeSucceeded($order, DomainValidation::TYPE_HTTP)) {
            $client->order()->finalize($order, $csr);
        }

        //  Getting the actual certificate
        if ($order->isFinalized()) {
            $certificateBundle = $client->certificate()->getBundle($order);
        }
         vd($certificateBundle);
        //Revoke a certificate
        /*if ($order->isValid()) {
            $client->certificate()->revoke($certificateBundle->fullchain);
        } */

    }
}
