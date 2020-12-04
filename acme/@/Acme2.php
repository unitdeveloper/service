<?php

namespace zetsoft\service\acme;

/**
 * @author Umid Muminov
 * https://packagist.org/packages/stonemax/acme2
 */

require Root . '/vendori/acmes/vendor/autoload.php';

use stonemax\acme2\Client;
use stonemax\acme2\constants\CommonConstant;
use zetsoft\system\kernels\ZFrame;
use const WyriHaximus\Constants\Boolean\FALSE_;


class Acme2 extends ZFrame
{

    public function test()
    {
        $this->acme2test();
    }


    public function acme2test(){

        $domainInfo = [
            CommonConstant::CHALLENGE_TYPE_HTTP => [
                'market.zetsoft.uz'
            ],

            CommonConstant::CHALLENGE_TYPE_DNS => [
                '*.www.market.zetsoft.uz',
                'www.market.zetsoft.uz',
            ],
        ];

        $client = new Client(['spec7575@mail.ru'], Root . '/hoster/appssl/', FALSE);

        $order = $client->getOrder($domainInfo, CommonConstant::KEY_PAIR_TYPE_RSA, TRUE);

// $order = $client->getOrder($domainInfo, CommonConstant::KEY_PAIR_TYPE_EC, TRUE);    // Issue an ECC certificate

        $challengeList = $order->getPendingChallengeList();

        /* Get challenges info and set credentials */
        foreach ($challengeList as $challenge)
        {
            $challengeType = $challenge->getType();    // http-01 or dns-01
            $credential = $challenge->getCredential();

            // echo $challengeType."\n";
            // print_r($credential);

            /* http-01 */
            if ($challengeType == CommonConstant::CHALLENGE_TYPE_HTTP)
            {
                /* example purpose, create or update the ACME challenge file for this domain */
                setChallengeFile(
                    $credential['identifier'],
                    $credential['fileName'],
                    $credential['fileContent']
                );
            }

            /* dns-01 */
            else if ($challengeType == CommonConstant::CHALLENGE_TYPE_DNS)
            {
                /* example purpose, create or update the ACME challenge DNS record for this domain */
                setDNSRecore(
                    $credential['identifier'],
                    $credential['dnsContent']
                );
            }
        }

        /* Verify challenges */
        foreach ($challengeList as $challenge)
        {
            /* Infinite loop until the challenge status becomes valid */
            $challenge->verify();

            /* Or you can specify local verification timeout in seconds */
            // $challenge->verify(60);

            /* Or you can specify Let's Encrypt verification timeout in seconds */
            // $challenge->verify(0, 60);

            /* Or you can specify both timeouts at once */
            // $challenge->verify(60, 60);
        }

        $certificateInfo = $order->getCertificateFile();

        print_r($certificateInfo);
    }


    /*public function acme2test()
    {

        $emailList = ['spec7575@mial.ru'];
        $storagePath = Root . '/hoster/appssl/';
        $staging = TRUE;

        $client = new Client($emailList, $storagePath, $staging);

        $account = $client->getAccount();

        $account->updateAccountContact($emailList);
        $account->updateAccountKey();
        $account->deactivateAccount();



        $domainInfo = [
            CommonConstant::CHALLENGE_TYPE_HTTP => [
                'market.zetsoft.uz'
            ],

            CommonConstant::CHALLENGE_TYPE_DNS => [
                '*.www.market.zetsoft.uz',
                'www.market.zetsoft.uz',
            ],
        ];

        $algorithm = CommonConstant::KEY_PAIR_TYPE_RSA;

        $order = $client->getOrder($domainInfo, $algorithm, TRUE);

        $order->getPendingChallengeList();
        $order->getCertificateFile();
        $order->revokeCertificate('reason');

        $challengeList = $order->getPendingChallengeList();

        foreach ($challengeList as $challenge)
        {
            $challenge->getType();
            $challenge->getCredential();
            $challenge->verify();
        }

        echo $challenge->getType();

        print_r($challenge->getCredential());

        [
            'identifier' => 'www.market.zetsoft.uz',
            'fileName' => 'RzMY-HDa1P0DwZalmRyB7wLBNI8fb11LkxdXzNrhA1Y',
            'fileContent' => 'RzMY-HDa1P0DwZalmRyB7wLBNI8fb11LkxdXzNrhA1Y.CNWZAGtAHIUpstBEckq9W_-0ZKxO-IbxF9Y8J_svbqo',
        ];
    }*/

}
