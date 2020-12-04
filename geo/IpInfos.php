<?php

/**
 *
 *
 *  Author:  Axrorbek Nisonboyev
 * API site: ipinfo.io
 *
 */

namespace zetsoft\service\geo;

use ipinfo\ipinfo\IPinfo;
use zetsoft\system\kernels\ZFrame;

class IpInfos extends ZFrame
{

    #region Vars

    public $token = 'afc8df53ebde24';

    #endregion


    #region Event

    public function test($ip)
    {
        echo $this->getinfo($ip);
    }


    public function getinfo($ip)
    {


        $access_token = $this->token;
        $client = new IPinfo($access_token);
        $details = $client->getDetails($ip);
        $details->all;

        //$details->loc;
        return $details;

    }

    #endregion


}
