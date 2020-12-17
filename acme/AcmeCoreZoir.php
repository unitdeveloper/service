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

use AcmePhp\Core\Http\Base64SafeEncoder;
use AcmePhp\Core\Http\SecureHttpClientFactory;
use AcmePhp\Core\Http\ServerErrorHandler;
use AcmePhp\Ssl\CertificateRequest;
use AcmePhp\Ssl\DistinguishedName;
use AcmePhp\Ssl\KeyPair;
use AcmePhp\Ssl\PrivateKey;
use AcmePhp\Ssl\PublicKey;
use AcmePhp\Ssl\Parser\KeyParser;
use AcmePhp\Ssl\Signer\DataSigner;
use GuzzleHttp\Client as GuzzleHttpClient;
use AcmePhp\Ssl\Generator\KeyPairGenerator;
use AcmePhp\Core\AcmeClient;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

class AcmeCoreZoir extends ZFrame
{

    public $subdomain;
    public $domain;
    public $basename;
    public $email = 'zoirbek.sobirov@mail.ru';
    public $acme_keys_path = Root . '/storing';
    public $mode = 'https://acme-v02.api.letsencrypt.org/directory';
    public $path_to_vhost_confs = Root . '/hoster';


    public function addSSL($app, $subdomain, $sslConf) {
    
        global $boot;
        
        $this->basename = $app;
        $this->subdomain = "$app.$subdomain";

        $path = $this->acme_keys_path . '/' . $this->basename;
        if(!file_exists($path)) $boot->mkdir($path);

        $privateKeyPath = $path . '/account.pem';
        $publicKeyPath = $path . '/account.pub.pem';

        if (!file_exists($privateKeyPath)) {
            $keyPairGenerator = new KeyPairGenerator();
            $keyPair = $keyPairGenerator->generateKeyPair();

            file_put_contents($publicKeyPath, $keyPair->getPublicKey()->getPEM());
            file_put_contents($privateKeyPath, $keyPair->getPrivateKey()->getPEM());
        } else {
            $publicKey = new PublicKey(file_get_contents($publicKeyPath));
            $privateKey = new PrivateKey(file_get_contents($privateKeyPath));

            $keyPair = new KeyPair($publicKey, $privateKey);
        }

        $secureHttpClientFactory = new SecureHttpClientFactory(
            new GuzzleHttpClient(),
            new Base64SafeEncoder(),
            new KeyParser(),
            new DataSigner(),
            new ServerErrorHandler()
        );
        // $keyPair instance of KeyPair
        $secureHttpClient = $secureHttpClientFactory->createSecureHttpClient($keyPair);
        var_dump("secure Http Client s created");
        //var_dump($secureHttpClient);
        // See AcmePhp\Core\Http\SecureHttpClient for all available methods.

        // Important, change to production LE directory for real certs!
        $acmeClient = new AcmeClient($secureHttpClient, $this->mode);
        $acmeClient->registerAccount(null, $this->email);
        var_dump('Account s created');

        // This will return a list of challenges that you can use to prove you own the domain.
        $authorizationChallenges = $acmeClient->requestAuthorization($app);
        var_dump("authorization challenge is finished successfully");

        file_put_contents($this->acme_keys_path . '/.well-known/acme-challenge/' . $authorizationChallenges[0]->getToken(), $authorizationChallenges[0]->getPayload());
         var_dump("authorizationChallenges");
         
        // You need to stage  your challenge response via DNS or HTTP before making the next call:
        $acmeClient->challengeAuthorization($authorizationChallenges[0]);
        var_dump("acmeClient challenge s runned");
        //var_dump($acmeClient);

        $dn = new DistinguishedName($app);
         var_dump("DistinguishedName");
        $keyPairGenerator = new KeyPairGenerator();
        // Make a new key pair. We'll keep the private key as our cert key
        $domainKeyPair = $keyPairGenerator->generateKeyPair();
        var_dump("domainKeyPair is generated keys");
        //var_dump($domainKeyPair);

        // Generate CSR
        $csr = new CertificateRequest($dn, $domainKeyPair);
        $certificateResponse = $acmeClient->requestCertificate($app, $csr);

        if ($certificateResponse) {
            if (!file_exists($this->path_to_vhost_confs . '/appssl/' . $app))
                $boot->mkdir($this->path_to_vhost_confs . '/appssl/' . $app);
            file_put_contents($this->path_to_vhost_confs . '/appssl/' . $app . '/ssl.crt', $certificateResponse->getCertificate()->getPEM() . "\r\n" . $certificateResponse->getCertificate()->getIssuerCertificate()->getPEM());
            file_put_contents($this->path_to_vhost_confs . '/appssl/' . $app . '/ssl.key', $certificateResponse->getCertificateRequest()->getKeyPair()->getPrivateKey()->getPEM());
                 var_dump("Certificates are successfully added to the APPSSL path !");
        } else {
            echo "Error while creating folder inside appsll !";
        }
        

        /* Pending To Update */
          if($sslConf){
              //serverda domenga ssl ni ulash
              $path_vhosts = $this->path_to_vhost_confs . '/domain/zetsoft/';
              $conf_file = file_get_contents($path_vhosts . $app . '.conf');

              $ssl_link = '	
                  listen 443 ssl http2;
                  ssl_certificate     	appssl/' .$app . '/ssl.crt;
                  ssl_certificate_key 	appssl/' . $app . '/ssl.key;
              }';

              $pos_end_file = strripos($conf_file, '}');

              $conf_file = substr_replace($conf_file, $ssl_link, $pos_end_file);
              file_put_contents($path_vhosts . $app . '.conf', $conf_file);

              echo "New certificates paths re added, please check your domain config file before resetting your NGINX server! \n" ;
              }
        echo "Please check your domain key config file that you should add certificates PATHs ! \n
        Please add these paths to your config file and u should change your domain name   
         ssl_certificate     		\"d:/Develop/Projects/ALL/asrorz/zetsoft/hoster/appssl/domain_name/ssl.crt\"; \n
         ssl_certificate_key 		\"d:/Develop/Projects/ALL/asrorz/zetsoft/hoster/appssl/domain_name/ssl.key \"; \n
        " ;

        Az::$app->acme->checkingExpirationDate->validate($app);
        echo "\nSuccess!\n";

    }

    public function removeSSL($subdomain) {
          echo $subdomain;
        $path = $this->path_to_vhost_confs . '/appssl/' . $subdomain;

        if (!file_exists($path . '/trashed'))
            mkdir($path . '/trashed', 0777, true);

        if (file_exists($path . '/ssl.crt'))
            rename($path . '/ssl.crt', $path . '/trashed/ssl.crt');
        if (file_exists($path . '/ssl.key'))
            rename($path . '/ssl.key', $path . '/trashed/ssl.key');
        echo "Success!";

    }

    public function updateSSL($subdomain, $basename, $sslConf) {
        $this->removeSSL($subdomain);
        $this->addSSL($subdomain, $basename, $sslConf);
    }

    

}
