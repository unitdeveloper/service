<?php
/**
 * Class    RouteSymfony
 * @package zetsoft\service\league
 *
 * @author DilshodKhudoyarov
 *
 * https://symfony.com/doc/current/routing.html
 * RouteSymfony Sizning ilovangiz so'rovni qabul qilganda, javobni yaratish uchun kontroller harakatini amalga oshiradi.
 * Yo'naltirish konfiguratsiyasi har bir kiruvchi URL uchun qaysi amalni bajarish kerakligini aniqlaydi
 */

namespace zetsoft\service\league;
use Acme\BlogController;
use zetsoft\system\kernels\ZFrame;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RouteSymfony extends ZFrame
{
    public function test_case() {
        $this->maps_requestsTest();
    }

    public function maps_requests($path, $path_info) {
        $route = new Route($path, ['_controller' => BlogController::class]);
        $routes = new RouteCollection();
        $routes->add('blog_show', $route);

        $context = new RequestContext();

// Routing can match routes with incoming requests
        $matcher = new UrlMatcher($routes, $context);
        $parameters = $matcher->match($path_info);
// $parameters = [
//     '_controller' => 'App\Controller\BlogController',
//     'slug' => 'lorem-ipsum',
//     '_route' => 'blog_show'
// ]
// Routing can also generate URLs for a given route
        $generator = new UrlGenerator($routes, $context);
        $url = $generator->generate('blog_show', [
            'slug' => 'my-blog-post',
        ]);
        vd($generator);
// $url = '/blog/my-blog-post'
    }

     public function maps_requestsTest() {
         $route = new Route('/blog/{slug}', ['_controller' => BlogController::class]);
         $routes = new RouteCollection();
         $routes->add('blog_show', $route);

         $context = new RequestContext();

// Routing can match routes with incoming requests
         $matcher = new UrlMatcher($routes, $context);
         $parameters = $matcher->match('/blog/lorem-ipsum');
// $parameters = [
//     '_controller' => 'App\Controller\BlogController',
//     'slug' => 'lorem-ipsum',
//     '_route' => 'blog_show'
// ]

// Routing can also generate URLs for a given route
         $generator = new UrlGenerator($routes, $context);
         $url = $generator->generate('blog_show', [
             'slug' => 'my-blog-post',
         ]);
         vd($generator);
// $url = '/blog/my-blog-post'
     }
}