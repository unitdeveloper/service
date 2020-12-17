<?php

namespace zetsoft\service\acme;

/**
 * @author Umid Muminov
 * https://packagist.org/packages/yourivw/leclient
 */

require Root . '/vendors/acmes/vendor/autoload.php';

use zetsoft\system\kernels\ZFrame;
use LEClient\LEClient;
use LEClient\LEFunctions;
use LEClient\LEOrder;

class LeclientAcme extends ZFrame
{
    public function getssl()
    {
        $email = 'sukhrobnuralievv@gmail.com';

        $client = new LEClient($email);								// Initiating a basic LEClient with an array of string e-mail address(es).
//        $client = new LEClient($email, LEClient::LE_STAGING);					// Initiating a LECLient and use the LetsEncrypt staging URL.
//        $client = new LEClient($email, LEClient::LE_PRODUCTION);				// Initiating a LECLient and use the LetsEncrypt production URL.
//        $client = new LEClient($email, true);							// Initiating a LECLient and use the LetsEncrypt staging URL.
//        $client = new LEClient($email, true, $logger);						// Initiating a LEClient and use a PSR-3 logger (\Psr\Log\LoggerInterface).
//        $client = new LEClient($email, true, LEClient::LOG_STATUS);				// Initiating a LEClient and log status messages (LOG_DEBUG for full debugging).
//        $client = new LEClient($email, true, LEClient::LOG_STATUS, 'keys/');			// Initiating a LEClient and select custom certificate keys directory (string or array)
//        $client = new LEClient($email, true, LEClient::LOG_STATUS, 'keys/', '__account/');	// Initiating a LEClient and select custom account keys directory (string or array)


        $acct = $client->getAccount();  // Retrieves the LetsEncrypt Account instance created by the client.
        $acct->updateAccount($email);   // Updates the account with new contact information. Supply an array of string e-mail address(es).
        $acct->changeAccountKeys();     // Generates a new RSA keypair for the account and updates the keys with LetsEncrypt.
        $acct->deactivateAccount();     // Deactivates the account with LetsEncrypt.


        $order = $client->getOrCreateOrder($basename, $domains);                          	    // Get or create order. The basename is preferably the top domain name. This will be the directory in which the keys are stored. Supply an array of string domain nameOn to create a certificate for.
//        $order = $client->getOrCreateOrder($basename, $domains, $keyType);              	    // Get or create order. keyType can be set to "ec" to get ECDSA certificate. "rsa-4096" is default value. Accepts ALGO-SIZE format.
//        $order = $client->getOrCreateOrder($basename, $domains, $keyType, $notBefore);              // Get or create order. Supply a notBefore date as a string similar to 0000-00-00T00:00:00Z (yyyy-mm-dd hh:mm:ss).
//        $order = $client->getOrCreateOrder($basename, $domains, $keyType, $notBefore, $notAfter);   // Get or create order. Supply a notBefore and notAfter date as a string similar to 0000-00-00T00:00:00Z (yyyy-mm-dd hh:mm:ss).

        
        $valid      = $order->allAuthorizationsValid();                             // Check whether all authorizations in this order instance are valid.
        $pending    = $order->getPendingAuthorizations($type);                      // Get an array of pending authorizations. Performing authorizations is described further on. Type is LEOrder::CHALLENGE_TYPE_HTTP or LEOrder::CHALLENGE_TYPE_DNS.
        $verify     = $order->verifyPendingOrderAuthorization($identifier, $type);  // Verify a pending order. The identifier is a string domain name. Type is LEOrder::CHALLENGE_TYPE_HTTP or LEOrder::CHALLENGE_TYPE_DNS.
        $deactivate = $order->deactivateOrderAuthorization($identifier);            // Deactivate an authorization. The identifier is a string domain name.
        $finalize   = $order->finalizeOrder();                                      // Finalize the order and generate a Certificate Signing Request automatically.
        $finalize   = $order->finalizeOrder($csr);                                  // Finalize the order with a custom Certificate Signing Request string.
        $finalized  = $order->isFinalized();                                        // Check whether the order is finalized.
        $cert       = $order->getCertificate();                                     // Retrieves the certificate and stores it in the keys directory, under the specific order (basename).
        $revoke     = $order->revokeCertificate();                                  // Revoke the certificate without a reason.
        $revoke     = $order->revokeCertificate($reason);                           // Revoke the certificate with a reason integer as found in section 5.3.1 of RFC5280.


//        LEFunctions::RSAGenerateKeys($directory, $privateKeyFile, $publicKeyFile);	// Generate a RSA keypair in the given directory. Variables privateKeyFile and publicKeyFile are optional and have default values private.pem and public.pem.
//        LEFunctions::ECGenerateKeys($directory, $privateKeyFile, $publicKeyFile);	// Generate a EC keypair in the given directory (PHP 7.1+ required). Variables privateKeyFile and publicKeyFile are optional and have default values private.pem and public.pem.
//        LEFunctions::Base64UrlSafeEncode($input);					// Encode the input string as a base64 URL safe string.
//        LEFunctions::Base64UrlSafeDecode($input);					// Decode a base64 URL safe encoded string.
//        LEFunctions::log($data, $function);						// Print the data. The function variable is optional and defaults to the calling function's name.
//        LEFunctions::checkHTTPChallenge($domain, $token, $keyAuthorization);		// Checks whether the HTTP challenge is valid. Performing authorizations is described further on.
//        LEFunctions::checkDNSChallenge($domain, $DNSDigest);				// Checks whether the DNS challenge is valid. Performing authorizations is described further on.
//        LEFunctions::createhtaccess($directory);
    }
}
