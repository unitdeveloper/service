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

use LEClient\LEClient;
use LEClient\LEOrder;


class Youivwleclient
{
    public function addSSL($domain_name, $project_name)
    {
        $email = 'umid-berdiev82@mail.ru';
        $basename = $domain_name;
        $domains = [$domain_name];
        $type = LEOrder::CHALLENGE_TYPE_HTTP;
        $identifier = $project_name;

        // Initiating a LECLient and use the
        //LetsEncrypt staging URL
        $client = new LEClient($email, LEClient::LE_STAGING, LEClient::LOG_STATUS);

        // Get or create order. keyType can be set to "ec" to get ECDSA certificate. "rsa-4096" is default value. Accepts ALGO-SIZE format.
        $order = $client->getOrCreateOrder($basename, $domains);

        // Check whether all authorizations in this order instance are valid.
        if(!$order->allAuthorizationsValid())
        {
            // Get the HTTP challenges from the pending authorizations.
            $pending = $order->getPendingAuthorizations(LEOrder::CHALLENGE_TYPE_HTTP);

            // Walk the list of pending authorization HTTP challenges.
            if(!empty($pending))
            {
                foreach($pending as $challenge)
                {
                    // Define the folder in which to store the challenge. For the purpose of this example, a fictitious path is set.
                    $folder = Root . '/storing/acmev2/' . $identifier;
                    // Check if that directory yet exists. If not, create it.
                    if(!file_exists($folder)) mkdir($folder, 0777, true);
                    // Store the challenge file for this domain.
                    file_put_contents($folder . $challenge['filename'], $challenge['content']);
                    // Let LetsEncrypt verify this challenge.
                    $order->verifyPendingOrderAuthorization($challenge['identifier'], $type);
                }
            }
        }

        // Check once more whether all authorizations are valid before we can finalize the order.
        if($order->allAuthorizationsValid())
        {
            // Finalize the order first, if that is not yet done.
            if(!$order->isFinalized()) $order->finalizeOrder();
            // Check whether the order has been finalized before we can get the certificate. If finalized, get the certificate.
            if($order->isFinalized()) $order->getCertificate();
        }


    }
}
