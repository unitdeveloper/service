<?php
/**
 * Class    MashapeHttp
 * @package zetsoft\service\https
 *
 * @author DilshodKhudoyarov
 *
 * Class file MashapeHttp url request labilan ishlaydigan service, json formatga chiqarib beradi javobini
 */

namespace zetsoft\service\App\eyuf;
namespace zetsoft\service\https;
use Unirest\Request;
use Unirest\Request\Body;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

class MashapeHttp extends ZFrame
{
    public function test_case() {
       $this->making_requestTest();
       $this->form_requestsTest();
       $this->multipart_file_uploadTest();
       $this->request_objectTest();
       $this->multipart_requestsTest();
       $this->jsonTest();
    }

    public function making_requestTest() {
        $url = "http://mockbin.com/request";
        $result =  Az::$app->https->mashapeHttp->making_request($url);
        vd($result);
    }

    public function making_request($url) {
        $headers = array('Accept' => 'application/json');
        $query = array('foo' => 'hello', 'bar' => 'world');

        $response = Request::post($url, $headers, $query);

        $response->code;        // HTTP Status code
        $response->headers;     // Headers
        $response->body;        // Parsed body
        $response->raw_body;    // Unparsed body
        vd($response);
    }

    public function jsonTest() {
        $url = "http://mockbin.com/request";
        $result =  Az::$app->https->mashapeHttp->json($url);
        vd($result);
    }

    public function json($url) {
        $headers = array('Accept' => 'application/json');
        $data = array('name' => 'ahmad', 'company' => 'mashape');

        $body = Body::json($data);

        $response = Request::post($url, $headers, $body);
        vd($response);
    }

    public function form_requestsTest() {
        $url = "http://mockbin.com/request";
        $result =  Az::$app->https->mashapeHttp->form_requests($url);
        vd($result);
    }

    public function form_requests($url) {
        $headers = array('Accept' => 'application/json');
        $data = array('name' => 'ahmad', 'company' => 'mashape');

        $body = Body::form($data);

        $response = Request::post($url, $headers, $body);
        vd($response);
    }

    public function multipart_requestsTest() {
        $url = "http://mockbin.com/request";
        $result =  Az::$app->https->mashapeHttp->multipart_requests($url);
        vd($result);
    }

    public function multipart_requests($url) {
        $headers = array('Accept' => 'application/json');
        $data = array('name' => 'ahmad', 'company' => 'mashape');

        $body = Body::multipart($data);

        $response = Request::post($url, $headers, $body);
        vd($response);
    }

    public function multipart_file_uploadTest() {
        $url = "http://mockbin.com/request";
        $result =  Az::$app->https->mashapeHttp->multipart_file_upload($url);
        vd($result);
    }

    public function multipart_file_upload($url) {
        $headers = array('Accept' => 'application/json');
        $data = array('name' => 'ahmad', 'company' => 'mashape');
        $files = array('bio' => '/path/to/bio.txt', 'avatar' => '/path/to/avatar.jpg');

        $body = Body::multipart($data, $files);

        $response = Request::post($url, $headers, $body);
        vd($response);
    }

    public function request_objectTest() {
        $url = "http://mockbin.com/request";
        $result =  Az::$app->https->mashapeHttp->request_object($url);
        vd($result);
    }

    public function request_object($url) {
        Request::get($url, $headers = array(), $parameters = null);
        Request::post($url, $headers = array(), $body = null);
        Request::put($url, $headers = array(), $body = null);
        Request::patch($url, $headers = array(), $body = null);
        Request::delete($url, $headers = array(), $body = null);
        //url - Endpoint, address, or uri to be acted upon and requested information from.
        //headers - Request Headers as associative array or object
        //body - Request Body as associative array or object
    }
    //Previous versions of Unirest support Basic Authentication by providing the username and password arguments:
    //
    //$response = Unirest\Request::get('http://mockbin.com/request', null, null, 'username', 'password');
    //This has been deprecated, and will be completely removed in v.3.0.0 please use the Unirest\Request::auth() method instead
    //
    //Cookies
    //Set a cookie string to specify the contents of a cookie header. Multiple cookies are separated with a semicolon followed by a space (e.g., "fruit=apple; colour=red")
    //
    //Unirest\Request::cookie($cookie)
    //Set a cookie file path for enabling cookie reading and storing cookies across multiple sequence of requests.
    //
    //Unirest\Request::cookieFile($cookieFile)
    //$cookieFile must be a correct path with write permission.
}