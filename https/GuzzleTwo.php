<?php


namespace zetsoft\service\https;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

class GuzzleTwo extends ZFrame
{
#region Vars
    public const types = [
        'GET' => 'GET',
        'HEAD' => 'HEAD',
        'POST' => 'POST',
        'PUT' => 'PUT',
        'PATCH'=>'PATCH',
        'DELETE'=>'DELETE',
        'OPTIONS'=>'OPTIONS',
    ];
    public $method = 'GET';


    /**
     * @var $base_uri
     *  Base URI is used with relative requests
     *
     */
    public $base_uri;

    /**
     * @var
     * Example: $uri = '/test/something'  or 'test'
     */
    public $uri;

    /**
     * @var
     */
    public $fullUri;


    
    /**
     * @var $timeout
     * You can set any number of default request options.
     *
     */
     public $timeout = 0;

    /**
     * @var $allow_redirects
     * Describes the redirect behavior of a request
     * Types : bool, array
     * Set to false to disable redirects.
     * Set to true (the default setting) to enable normal redirects with a maximum number of 5 redirects.
     */
     public $allow_redirects = [
                 'max'             => 5,
                 'strict'          => false,
                 'referer'         => false,
                 'protocols'       => ['http', 'https'],
                 'track_redirects' => false
     ];

    /**
     * @var
     * Pass an array of HTTP authentication parameters to use with the request.
     * The array must contain the username in index [0],
     * the password in index [1],
     * and you can optionally provide a built-in authentication type in index [2].
     * Pass null to disable authentication for a request.
     *
     *
     * Types: array, string, null
     */
     public $auth;

    /**
     * @var $body
     * The body option is used to control the body of an entity enclosing request (e.g., PUT, POST, PATCH).
     * Types: string, fopen() resource,  Psr\Http\Message\StreamInterface
     * Note: This option cannot be used with form_params, multipart, or json
     */
     public $body;


    /**
     * @var $cert
     * Set to a string to specify the path to a file containing a PEM formatted client side certificate. If a password is required, then set to an array containing the path to the PEM file in the first array element followed by the password required for the certificate in the second array element.
     *
     * Types: string, array
     */
    public $cert;


    /**
     * @var $cookies
     * Specifies whether or not cookies are used in a request or what cookie jar to use or what cookies to send.
     * Types : GuzzleHttp\Cookie\CookieJarInterface
     */
    public $cookies;


    /**
     * @var int $connect_timeout
     * 
     * Types : float
     */
    public $connect_timeout = 0;


    /**
     * @var $debug
     * Set to true or set to a PHP stream returned by fopen() to enable debug output with the handler used to send a request. For example, when using cURL to transfer requests, cURL's verbose of CURLOPT_VERBOSE will be emitted. When using the PHP stream wrapper, stream wrapper notifications will be emitted. If set to true, the output is written to PHP's STDOUT. If a PHP stream is provided, output is written to the stream.
     * Types : bool, fopen() resource
     *
     */
    public $debug;


    /**
     * @var bool  $decode_conten
     * Specify whether or not Content-Encoding responses (gzip, deflate, etc.) are automatically decoded.
     * Types: string, bool
     *
     * This option can be used to control how content-encoded response bodies are handled. By default, decode_content is set to true, meaning any gzipped or deflated response will be decoded by Guzzle.
        When set to false, the body of a response is never decoded, meaning the bytes pass through the handler unchanged.
        When set to a string, the bytes of a response are decoded and the string value provided to the decode_content option is passed as the Accept-Encoding header of the request.
     */
    public $decode_conten = true;


    /**
     * @var null  $delay
     * The number of milliseconds to delay before sending the request.
     * Types: integer, float
     */
    public $delay = null;


    /**
     * @var int $expect
     * Controls the behavior of the "Expect: 100-Continue" header.
     */
    public $expect  = 1048576;

    /**
     * @var null $force_ip_resolve
     * Set to "v4" if you want the HTTP handlers to use only ipv4 protocol or "v6" for ipv6 protocol.
     * Types: string
     */
    public $force_ip_resolve = null;


    /**
     * @var array  $form_params
     * Used to send an application/x-www-form-urlencoded POST request.
     * Types :array
     */
    public $form_params = [];


    /**
     * @var $headers
     * Associative array of headers to add to the request. Each key is the name of a header, and each value is a string or array of strings representing the header field values.
     * Types : array
     */
    public $headers = [];

    /**
     * @var bool
     * Set to false to disable throwing exceptions on an HTTP protocol errors (i.e., 4xx and 5xx responses). Exceptions are thrown by default when HTTP protocol errors are encountered
     * Types : bool
     */
    public $http_errors = true;

    /**
     * @var bool
     * Internationalized Domain Name (IDN) support (enabled by default if intl extension is available).
     * Types : bool, int
     */
    public $idn_conversion = true;


    /**
     * @var $json
     * The json option is used to easily upload JSON encoded data as the body of a request. A Content-Type header of application/json will be added if no Content-Type header is already present on the message.
     */
    public $json;


    /**
     * @var array $multipart
     * Sets the body of the request to a multipart/form-data form.
     * Types: array
     */
    public $multipart = [];


    /**
     * @var  $on_headers
     * A callable that is invoked when the HTTP headers of the response have been received but the body has not yet begun to download.
     *
     * Types: callable
     */
    public $on_headers;


    /**
     * @var
     * on_stats allows you to get access to transfer statistics of a request and access the lower level transfer details of the handler associated with your client. on_stats is a callable that is invoked when a handler has finished sending a request. The callback is invoked with transfer statistics about the request, the response received, or the error encountered. Included in the data is the total amount of time taken to send the request.
     *
     * Types : callable
     * 
     */
    public $on_stats;

    /**
     * @var $progress
     * Defines a function to invoke when transfer progress is made.
     *
     * Types : callable
     */
    public $progress;


    /**
     * @var $proxy
     * Pass a string to specify an HTTP proxy, or an array to specify different proxies for different protocols.
     *
     * Types: string, array
     */
    public $proxy;

    /**
     * @var
     * Associative array of query string values or query string to add to the request
     * Types : array, string
     */
    public $query;

    /**
     * @var   $read_timeout
     * Float describing the timeout to use when reading a streamed body
     * Types : float
     * Default : Defaults to the value of the default_socket_timeout PHP ini setting
     */
    public $read_timeout;

    /**
     * @var
     * Specify where the body of a response will be saved.
     * Types: string (path to file on disk), fopen() resource, Psr\Http\Message\StreamInterface
     *
     * Default : PHP temp stream
     */
    public $sink;

    /**
     * @var
     * Specify the path to a file containing a private SSL key in PEM format. If a password is required, then set to an array containing the path to the SSL key in the first array element followed by the password required for the certificate in the second element.
     *
     * Types: string, array
     *
     * ssl_key is implemented by HTTP handlers. This is currently only supported by the cURL handler, but might be supported by other third-part handlers.
     *
     *
     * Note : ssl_key is implemented by HTTP handlers. This is currently only supported by the cURL handler, but might be supported by other third-part handlers.
     *
     */
    public $ssl_key;

    /**
     * @var bool
     *
     * Set to true to stream a response rather than download it all up-front.
     * Types: bool
     */
    public $stream = false;

    /**
     * @var
     * Set to true to inform HTTP handlers that you intend on waiting on the response. This can be useful for optimizations.
     * Types : bool
     */
    public $synchronous;

    /**
     * @var bool
     * Describes the SSL certificate verification behavior of a request.

        Set to true to enable SSL certificate verification and use the default CA bundle provided by operating system.
        Set to false to disable certificate verification (this is insecure!).
        Set to a string to provide the path to a CA bundle to enable verification using a custom certificate.
     *
     * Types : bool, string
     */
    public $verify = true;


    /**
     * @var float
     * Protocol version to use with the request.
     * Types: string, float
     */
    public $version = 1.1;


     
#endregion
#region Client

   
    public function createClient(): Client
    {

        if($this->base_uri === null) {
            return new Client();
        }

        return new Client(['base_uri' => $this->base_uri, $this->timeout]);
    }

    public function createRequest(): Request
    {
        return new Request($this->method, $this->base_uri . $this->uri, $this->headers, $this->body, $this->version);
    }

    public function sendRequest()
    {
         return $this->createClient()->send($this->createRequest(),  ['timeout' => $this->timeout]);
    }

    public function sendAsyncRequest(){
        return $this->createClient()->sendAsync($this->createRequest());
    }

    public function requestAsync(){
       return $this->createClient()->requestAsync($this->method, $this->fullUri);
    }


    public function asyn(){
        $client = new Client();
        $requests = function ($total) {
            $uri = $this->fullUri;
            for ($i = 0; $i < $total; $i++) {
                yield new Request('GET', $uri, ['synchronous'=>false]);
            }
        };


        $pool = new Pool($client, $requests(1), [
            'concurrency' => 5,
            'fulfilled' => function (Response $response, $index) {
                // this is delivered each successful response
            },
            'rejected' => function (RequestException $reason, $index) {
                // this is delivered each failed request
            },
        ]);
        $pool->promise()->wait();


    }


    
#endregion

#region Test
public function testOne(){
    $str =  "/admin/core-category/queue.aspx?namespace=cores&service=product&method=afterEditCorecategory&args=35";
    $this->setFullUri($str);
    $this->asyn();
}


#endregion



#region Setters
    /**
     * @param mixed $base_uri
     */
    public function setBaseUri($base_uri): bool
    {
        $this->base_uri = $base_uri;
        return true;
    }


    /**
     * @param string $method
     */
    public function setMethod(string $method): bool
    {
        $this->method = $method;
        return true;
    }

    /**
     * @param mixed $uri
     */
    public function setUri($uri): bool
    {
        $this->uri = $uri;
        return true;
    }

    /**
     * @param mixed $timeout
     */
    public function setTimeout(int $timeout): bool
    {
        $this->timeout = $timeout;
        return true;
    }

    /**
     * @param mixed $fullUri
     */
    public function setFullUri($fullUri): bool
    {
        $this->fullUri = $fullUri;
        return true;
    }

#endregion

    /**
     * @param mixed $headers
     */
    public function setHeaders(Array $headers): bool
    {
        $this->headers = $headers;
        return true;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body): void
    {
        $this->body = $body;
    }
}
