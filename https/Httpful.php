<?php
/**
 * class Httpful
 * @package zetsoft/service/https
 * @author UzakbaevAxmet
 * class Http Client kutubxonasidir
 */

namespace zetsoft\service\App\eyuf;
namespace zetsoft\service\https;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;
class Httpful extends ZFrame
{
    #region Test
    public function test()
    {
        $this->jsonTest();
        $this->xmlTest();
        $this->json_authTest();
        $this->custom_headerTest();
    }

    #endregion
    public function jsonTest()
    {
        $url = "https://api.github.com/users/nategood";
        $result = $this->json($url);
        vd($result);
    }

    public function json($url)
    {
        // Make a request to the GitHub API with a custom
// header of "X-Trvial-Header: Just as a demo".

        $response = \Httpful\Request::get($url)->expectsJson()
            ->withXTrivialHeader('Just as a demo')
            ->send();
            
        return $response;
    }

    public function xmlTest()
    {
        $url = 'http://www-db.deis.unibo.it/courses/TW/DOCS/w3schools/xml/note.xml';
        $result = $this->xml($url);
        vd($result);
    }

    public function xml($url)
    {
        // Make a request to the GitHub API with a custom
        $response = \Httpful\Request::get($url) //here might use post request
        ->expectsXml()
            ->withXTrivialHeader('Just as a demo')
            ->send();
        return $response;
    }

    public function json_authTest()
    {
        $url = "https://api.github.com/users/nategood";
        $result = $this->json_auth($url);
        vd($result);
    }

    public function json_auth($url)
    {
        $response = \Httpful\Request::put($url)                  // Build a PUT request...
        ->sendsJson()                               // tell it we're sending (Content-Type) JSON...
        ->authenticateWith('', '')  // authenticate with basic auth...
        ->body('{"json":"is awesome"}')             // attach a body/payload...
        ->send();                                   // and finally, fire that thing off!
        return $response;
    }

    public function custom_headerTest()
    {
        $url = "https://api.github.com/users/nategood";
        $result = $this->custom_header($url);
        vd($result);
    }

    public function custom_header($url)
    {
        $response = \Httpful\Request::get($url)
            ->xExampleHeader("My Value")                // Add in a custom header X-Example-Header
            ->withXAnotherHeader("Another Value")       // Sugar: You can also prefix the method with "with"
            ->addHeader('X-Or-This', 'Header Value')    // Or use the addHeader method
            ->addHeaders(array(
                'X-Header-1' => 'Value 1',              // Or add multiple headers at once
                'X-Header-2' => 'Value 2',              // in the form of an assoc array
            ))
            ->send();
        return $response;
    }


}
