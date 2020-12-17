<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 * https://github.com/stonemax/acme2
 */

namespace zetsoft\service\acme;

use zetsoft\system\kernels\ZFrame;

include Root . '/vendors/acmes/vendor/autoload.php';

use stonemax\acme2\Client;
use stonemax\acme2\constants\CommonConstant;

class Acme2 extends ZFrame
{

    #region Vars
    public $emailList = [];
    public $storagePath = '';
    public $staging = false;
    public $client;
    public $path_appsll = 'D:/Develop/Projects/ALL/asrorz/zetsoft/hoster/appssl/';


    public $app_name = ''; //eyuf
    public $domain_name = '';  //eyuf.zetsoft.uz
    public $email = ''; //jobiryusupov0@gmail.com

    #endregion


    public function example(){



    }

    public function file_force_contents($fullPath, $contents, $flags = 0)
    {
        global $boot;
        $parts = explode('/', $fullPath);
        array_pop($parts);
        $dir = implode('/', $parts);

        if (!file_exists($dir))
            $boot->mkdir($dir);

        file_put_contents($fullPath, $contents, $flags);
    }


    /**
     *
     * Function  add
     * @param $app_name
     * @param $domain_name
     * @throws \stonemax\acme2\exceptions\AccountException
     * @throws \stonemax\acme2\exceptions\AuthorizationException
     * @throws \stonemax\acme2\exceptions\NonceException
     * @throws \stonemax\acme2\exceptions\OrderException
     * @throws \stonemax\acme2\exceptions\RequestException
     * @throws \stonemax\acme2\exceptions\timeout\VerifyCATimeoutException
     * @throws \stonemax\acme2\exceptions\timeout\VerifyLocallyTimeoutException
     */
    public function addSSL($app_name, $domain_name)
    {
        //$this->removeSSL($domain_name);
        $this->app_name = $app_name;
        $this->domain_name = $domain_name;
        $this->email = 'zetsoft.uz@gmail.com';

        $this->storagePath = Root . '/storing/acmev2/' . $this->app_name;

        $this->emailList[] = $this->email;

        $this->client = new Client($this->emailList, $this->storagePath, $this->staging);


        $domainInfo = [
            CommonConstant::CHALLENGE_TYPE_HTTP => [
                $this->domain_name
            ]
        ];

        $algorithm = CommonConstant::KEY_PAIR_TYPE_RSA;

        $order = $this->client->getOrder($domainInfo, $algorithm, TRUE);      // Get an order service instance

        $challengeList = $order->getPendingChallengeList();

        foreach ($challengeList as $challenge) {
            $challengeType = $challenge->getType();
            // echo $challengeType."\n";
            if ($challengeType == CommonConstant::CHALLENGE_TYPE_HTTP) {
                $credential = $challenge->getCredential();

                $this->file_force_contents($this->storagePath . $credential['fileName'], $credential['fileContent'], LOCK_EX);
            }

        }

        foreach ($challengeList as $challenge) {
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

        $this->file_force_contents($this->path_appsll . $this->domain_name . '/ssl.key', file_get_contents($certificateInfo['privateKey']), LOCK_EX);

        $this->file_force_contents($this->path_appsll . $this->domain_name . '/public.pem', file_get_contents($certificateInfo['publicKey']), LOCK_EX);

        $this->file_force_contents($this->path_appsll . $this->domain_name . '/certificate.crt', file_get_contents($certificateInfo['certificate']), LOCK_EX);

        $this->file_force_contents($this->path_appsll . $this->domain_name . '/ssl.crt', file_get_contents($certificateInfo['certificateFullChained']), LOCK_EX);

        //domenga ssl ni ulash
        $path_vhosts = 'D:/Develop/Projects/ALL/server/nginx/conf/vhosts/';
        $conf_file = file_get_contents($path_vhosts . $this->domain_name . '.conf');

        $ssl_link = '	
            listen 443 ssl http2;
            ssl_certificate     	appssl/' . $this->domain_name . '/ssl.crt;
            ssl_certificate_key 	appssl/' . $this->domain_name . '/ssl.key;
        }';

        $pos_end_file = strripos($conf_file, '}');

        $conf_file = substr_replace($conf_file, $ssl_link, $pos_end_file);
        file_put_contents($path_vhosts . $this->domain_name . '.conf', $conf_file);

    }

    public function removeSSL($domain_name)
    {
        $this->domain_name = $domain_name;
        $dir_trashed = $this->path_appsll . $this->domain_name . '/trashed';
        if (!is_dir($dir_trashed))
            mkdir($dir_trashed, 0777, true);

        if (file_exists($this->path_appsll . $this->domain_name . '/ssl.key'))
            rename($this->path_appsll . $this->domain_name . '/ssl.key', $dir_trashed . '/ssl.key');
        if (file_exists($this->path_appsll . $this->domain_name . '/public.pem'))
            rename($this->path_appsll . $this->domain_name . '/public.pem', $dir_trashed . '/public.pem');
        if (file_exists($this->path_appsll . $this->domain_name . '/certificate.crt'))
            rename($this->path_appsll . $this->domain_name . '/certificate.crt', $dir_trashed . '/certificate.crt');
        if (file_exists($this->path_appsll . $this->domain_name . '/ssl.crt'))
            rename($this->path_appsll . $this->domain_name . '/ssl.crt', $dir_trashed . '/ssl.crt');


        $path_vhosts = 'D:/Develop/Projects/ALL/server/nginx/conf/vhosts/';
        $conf_file = file_get_contents($path_vhosts . $this->domain_name . '.conf');


        $conf_file = str_replace('listen 443 ssl http2;', '', $conf_file);

        $conf_file = str_replace('appssl/' . $this->domain_name . '/ssl.crt;', '', $conf_file);
        $conf_file = str_replace('appssl/' . $this->domain_name . '/ssl.key;', '', $conf_file);

        $conf_file = str_replace('ssl_certificate', '', $conf_file);
        $conf_file = str_replace('ssl_certificate_key', '', $conf_file);
        $conf_file = str_replace('_key', '', $conf_file);

        file_put_contents($path_vhosts . $this->domain_name . '.conf', $conf_file);
    }

    public function updateSSL($domain_name, $app_name)
    {
        $this->removeSSL($domain_name);
        $this->addSSL($domain_name, $app_name);
    }

}
