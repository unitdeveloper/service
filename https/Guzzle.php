<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\https;

require Root . '/vendors/netapp/vendor/autoload.php';

use GuzzleHttp\Client;
use zetsoft\system\kernels\ZFrame;

class Guzzle extends ZFrame
{

    public const types = [
        'GET' => 'GET',
        'POST' => 'POST',
        'PUT' => 'PUT',
    ];
    
    public function request($type, $url, $args = null)
    {
        $client = new Client();
        if ($args === null)
        {
            $res = $client->request($type, $url);
        }else
        {
            $res = $client->request($type, $url, $args);
        }


        return $res;
    }
}
