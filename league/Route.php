<?php declare(strict_types=1);
/**
 * class Route
 * @package zetsoft/service/league
 * @author UzakbaevAxmet
 * class Route orqali controllerni chaqiradi
 */

namespace zetsoft\service\league;

use Laminas\Diactoros\ResponseFactory;
use League\Route\RouteCollectionTrait;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use League\Route\Router;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\Response;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use League\Route\Strategy\JsonStrategy;
use League\Container\Container;
use Symfony\Component\HttpFoundation\Request;
use League\Route\RouteCollection;
use zetsoft\service\league\SomeController;
class Route extends ZFrame
{

    #region Test

    public function test()
    {
       // $this->definerouteTest();
        $this->urlTest();
    }

    #endregion

    public function definerouteTest()
    {
        $path='/';
        $route=$this->defineroute($path);
        vd($route);
    }

    /**
     *
     * Function  defineroute
     * @param $path
     * @author Shax
     */
    public function defineroute($path) {
        $request = ServerRequestFactory::fromGlobals(
            $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
        );

        $router = new Router;

// map a route
        $router->map('GET', $path, function (ServerRequestInterface $request): ResponseInterface {
            $response = new Response;
            $response->getBody()->write('<h1>Hello, World!</h1>');
            return $response;
        });

        $response = $router->dispatch($request);

// send the response to the browser
        (new SapiEmitter)->emit($response);
    }
    public function urlTest() {
        $path='/';
        $controller=[SomeController::class,'someMethod'];
        $get=$this->url($path,$controller);
        vd($get);
    }
    public function url($path,$controller) {
        $request = ServerRequestFactory::fromGlobals(
            $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
        );

        $responseFactory = new ResponseFactory();

        $strategy = new JsonStrategy($responseFactory);
        $router   = (new Router)->setStrategy($strategy);

// map a route
        $router->map('GET', $path, $controller);

        $response = $router->dispatch($request);

// send the response to the browser
        (new SapiEmitter)->emit($response);
    }

}
