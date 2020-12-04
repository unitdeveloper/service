<?php declare(strict_types=1);

namespace zetsoft\service\league;

use Httpful\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SomeController
{
    /**
     * Controller.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function someMethod(ServerRequestInterface $request) : Array
    {
    $get= '{"a":1,"b":2,"c":3,"d":4,"e":5}';
        $get=[1,2];
       // $arr = array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5);

        return  $get;
    }
}