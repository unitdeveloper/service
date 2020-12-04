<?php

/**
 * Author:  Zoirjon Sobirov
 * @license  Zoirjon Sobirov
 * linkedIn: https://www.linkedin.com/in/zoirjon-sobirov/
 * Telegram: https://t.me/zoirjon_sobirov
 * @copyright zhead, zstart, zend
 */

namespace zetsoft\service\acme;

require_once __DIR__ . '/GoGetSSLApiw.php';


use zetsoft\system\helpers\ZTest;
use zetsoft\system\kernels\ZFrame;


define('GOGETSSL_API_USER', 'zoirbek.sobirov@mail.ru');
define('GOGETSSL_API_PASS', 'L5tyZroTEm9M627');

class GeoSsl  extends ZFrame
{
    #region Vars

    //Customer Data

    #endregion

    #region TestCase
    public  function testCase(){
        return 'hello';
    }
    #endregion

    #region SampleReq
    public  function sampleReq(){

        $csr = <<<csr
-----BEGIN CERTIFICATE REQUEST-----
MIIC9TCCAd0CAQAwga8xCzAJBgNVBAYTAlVaMREwDwYDVQQIDAhUYXNoa2VudDER
MA8GA1UEBwwIVGFzaGtlbnQxHzAdBgNVBAoMFlpFVFNPRlQgRU5URVJQUklDRSBM
TEMxFTATBgNVBAsMDEthdHRhIERhcnhvbjEaMBgGA1UEAwwRbXBsYWNlLnpldHNv
ZnQudXoxJjAkBgkqhkiG9w0BCQEWF3pvaXJiZWsuc29iaXJvdkBtYWlsLnJ1MIIB
IjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAvbte0T5NrhvBA8A9quh78qwH
/51wADzb27Or73wIVZo5pCNIbPNhHrR02MZ5YiRtpMD4H5a4YM7z+unN31dg9L13
rLh+l3O0I6pD7v0tJR7EamTK53awcL4i7tSRGbnzT7NfsLNaZK6FNAqVSPqczXk8
JJtsbjjfyYxvNX08e02HwL/rGlX7mUWcO9/XPE02OJVe+/gjKGNVlC4PhrNlK8a5
0k6w12ENLWv4KPUwfS7AdueCf3p+gHALKmS80GBhkknJgK5WNmRdyAShBcFxF6xO
xHlxz1rIwxNo4if3iVlS5KAyRVaV0wTs2kWkal7TGFR40PFwnknOLGrpGU1CVQID
AQABoAAwDQYJKoZIhvcNAQEEBQADggEBAH/mV6zBbSukfAL81jBloJHT3VPKoiN7
K5ohnpTymQY3eRPNK8TxkS6wsEqevlkJe2IHKfsosUfCASgzCsgHx09Gkxch9yYJ
um9Jn1JkHX4D6OGYizYXhOL4hC1/jKkM2mRbfrz7DQXdnt4k+4JAC/0sUXrAFl/r
xU9PVsBZ/L+LQ1VZLpPRFCpTfhq2/JVlVjCPXz4WMLQfOLk2Oi/h7I9DT7oUTCd9
isZZIDVZywSnWM+wNrlZAYYmsZu41it0oN+0yaQYybuIwUR26764AmQnqiAFrxUX
RaJ4/tbyOw1EwSCA0uHq3GnOD8KYhLo0XkdiNzEZLwRoIY4IgOhoQKA=
-----END CERTIFICATE REQUEST-----
csr;


        $data = [
            'product_id'       => 65,
            'csr'              => $csr,
            'server_count'     => "-1",
            'period'           => 3,
            'approver_email'   => "zoirbek.sobirov@mail.ru",
            'webserver_type'   => "1",
            'admin_firstname'  => "Zoirjon",
            'admin_lastname'   => "Sobirov",
            'admin_phone'      => "998911981848",
            'admin_title'      => "Mr",
            'admin_email'      => "zoirbek.sobirov@mail.ru",
            'tech_firstname'   => "Zoirjon",
            'tech_lastname'    => "Sobirov",
            'tech_phone'       => "998911981848",
            'tech_title'       => "Mr",
            'tech_email'       => "zoirbek.sobirov@mail.ru",
            //'dns_names' => "domain.lv,domain2.lv,domani3.lv",
            'org_name'         => "AlexoMedia",
            'org_division'     => "Hosting",
            'org_addressline1' => "Valdeku street 55",
            'org_city'         => "Riga",
            'org_country'      => "LV",
            'org_phone'        => "37128216269",
            'org_postalcode'   => "LV-1056",
            'org_region'       => "None",
            'dcv_method'       => "dns",
            //'only_validate'    => true   // <-- Remove to place a real order
        ];

        try {

            $apiClient = new GoGetSSLApiw();
            $token = $apiClient->auth(GOGETSSL_API_USER, GOGETSSL_API_PASS);
            $newOrder = $apiClient->addSSLOrder($data);

            print_r($newOrder);

        } catch (Exception $e) {
            printf("%s: %s", get_class($e), $e->getMessage());
        }
    }
    #endregion

    #region Init
    public function init(){
        parent::init();
    }
    #endregion



}


