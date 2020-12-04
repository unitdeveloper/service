<?php

/**
 * Author:  Zoirjon Sobirov
 * @license  Zoirjon Sobirov
 * linkedIn: https://www.linkedin.com/in/zoirjon-sobirov/
 * Telegram: https://t.me/zoirjon_sobirov
 * @copyright zhead, zstart, zend
 */

namespace zetsoft\service\tests;



use zetsoft\system\helpers\ZTest;
use zetsoft\system\kernels\ZFrame;


class GeoSsl  extends ZFrame
{
    #region Vars

    #endregion

    #region TestCase
    public  function testCase(){
        return 'hello';
    }
    #endregion

    #region Init
    public function init(){
        parent::init();
    }
    #endregion

    public function addSSLOrder(){
        $CSR = "-----BEGIN CERTIFICATE REQUEST-----
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
-----END CERTIFICATE REQUEST-----";

        $request = [
            "product_id" => 68,
            "period" => 3,
            "csr" => $CSR,
            "server_count" => -1,
            "webserver_type" => -1,

            // CommonName - Domain Control Validation
            "dcv_method" => "email",
            "approver_email" => "admin@gogetssl.com",

            // SAN - Domain nameOn (coma separated string)
            "dns_names"       => implode(",", ["www.gogetssl.com",   "my.gogetssl.com", "support.gogetssl.com"]),
            // SAN - Domain validation methods (coma separated string)
            "approver_emails" => implode(",", ["admin@gogetssl.com", "dns",             "http"]),

            // Administrative Person info
            'admin_title' => "Mr.",
            'admin_firstname' => "John",
            'admin_lastname' => "Smith",
            'admin_phone' => "+37166164222",
            'admin_email' => "admin@gogetssl.com",

            // Technical Person info
            'tech_title' => "Mr.",
            'tech_firstname' => "John",
            'tech_lastname' => "Smith",
            'tech_phone' => "+37166164222",
            'tech_email' => "admin@gogetssl.com",
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://my.gogetssl.com/api/orders/add_ssl_order?auth_key=YOUR_AUTH_HASH");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        $response = curl_exec($ch);
    }


}
