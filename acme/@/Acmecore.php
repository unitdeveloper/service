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
use zetsoft\system\kernels\ZFrame;

class Acmecore  extends ZFrame
{

    public $subdomain;
    public $domain;
    public $basename;
    public $email = 'zoirbek.sobirov@mail.ru';
    public $acme_keys_path = Root . '/storing';
    public $mode = 'https://acme-v02.api.letsencrypt.org/directory';
    public $path_to_vhost_confs = Root . '/hoster';


    public function addSSL($app, $subdomain) {
    
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

        // See AcmePhp\Core\Http\SecureHttpClient for all available methods.

        // Important, change to production LE directory for real certs!
        $acmeClient = new AcmeClient($secureHttpClient, $this->mode);
        $acmeClient->registerAccount(null, $this->email);

        // This will return a list of challenges that you can use to prove you own the domain.
        $authorizationChallenges = $acmeClient->requestAuthorization($this->domain);

        file_put_contents($this->acme_keys_path . '/.well-known/acme-challenge/' . $authorizationChallenges[0]->getToken(), $authorizationChallenges[0]->getPayload());

        // You need to stage  your challenge response via DNS or HTTP before making the next call:
        $acmeClient->challengeAuthorization($authorizationChallenges[0]);

        $dn = new DistinguishedName($this->domain);

        $keyPairGenerator = new KeyPairGenerator();

        // Make a new key pair. We'll keep the private key as our cert key
        $domainKeyPair = $keyPairGenerator->generateKeyPair();

        // Generate CSR
        $csr = new CertificateRequest($dn, $domainKeyPair);

        $certificateResponse = $acmeClient->requestCertificate($this->domain, $csr);

        if ($certificateResponse) {
            if (!file_exists($this->path_to_vhost_confs . '/appssl/' . $this->domain))
                $boot->mkdir($this->path_to_vhost_confs . '/appssl/' . $this->domain);

            file_put_contents($this->path_to_vhost_confs . '/appssl/' . $this->domain . '/ssl.crt', $certificateResponse->getCertificate()->getPEM() . "\r\n" . $certificateResponse->getCertificate()->getIssuerCertificate()->getPEM());
            file_put_contents($this->path_to_vhost_confs . '/appssl/' . $this->domain . '/ssl.key', $certificateResponse->getCertificateRequest()->getKeyPair()->getPrivateKey()->getPEM());
        } else {
            echo "Error!";
        }

        //serverda domenga ssl ni ulash
        $path_vhosts = $this->path_to_vhost_confs . '/vhosts/';
        $conf_file = file_get_contents($path_vhosts . $this->domain . '.conf');

        $ssl_link = '	
            listen 443 ssl http2;
            ssl_certificate     	appssl/' . $this->domain . '/ssl.crt;
            ssl_certificate_key 	appssl/' . $this->domain . '/ssl.key;
        }';

        $pos_end_file = strripos($conf_file, '}');

        $conf_file = substr_replace($conf_file, $ssl_link, $pos_end_file);
        file_put_contents($path_vhosts . $this->domain . '.conf', $conf_file);

        echo "Success!";

    }

    public function removeSSL($subdomain) {
        
        $path = $this->path_to_vhost_confs . '/appssl/' . $subdomain;

        if (!file_exists($path . '/trashed'))
            mkdir($path . '/trashed', 0777, true);

        if (file_exists($path . '/ssl.crt'))
            rename($path . '/ssl.crt', $path . '/trashed/ssl.crt');
        if (file_exists($path . '/ssl.key'))
            rename($path . '/ssl.key', $path . '/trashed/ssl.key');
        echo "Success!";

    }

    public function updateSSL($subdomain, $basename) {
        $this->removeSSL($subdomain);
        $this->addSSL($subdomain, $basename);
    }

    

}
