<?php

/**
 *
 * Author: Xolmat Ravshanov
 *
 */
namespace zetsoft\service\calls;


use Ratchet\Http\HttpServer;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Server\IpBlackList;
use zetsoft\system\kernels\ZFrame;
use Ratchet\Server\FlashPolicy;
use Ratchet\Http\OriginCheck;


class FopSocket extends ZFrame implements MessageComponentInterface
{
    protected $clients;

    public $route = [
        'main' => '/core/fop/fop.aspx',
    ];


    public function init()
    {
        parent::init();
        $this->clients = new \SplObjectStorage;
    }


    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo 'Someone Connected '.PHP_EOL;
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        
        foreach ($this->clients as $client) {
            vd($client);
            if ($from != $client) {
                $client->send($msg);
            }
        }

        

    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();
    }


    public function run(){
    
        // Run the server application through the WebSocket protocol on port 8080
        $server  = new \Ratchet\App('localhost', 9312);
        $server->route($this->route['main'], new FopSocket, ['*']);
        $server->route('/echo', new \Ratchet\Server\EchoServer, ['*']);
        $server->run();


    }


    public function run2(){
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new FopSocket()
                )
            ),
            9312,
            'localhost'
        );
        $server->run();

    }



    private function blockIp(){
            // Your shell script
            $blackList = new IpBlackList(new FopSocket());
            $blackList->blockAddress('74.125.226.46');
            // Stop Google from connecting to our server

            $server = IoServer::factory($blackList, 8080);
            $server->run();
    }




    private function flashPolicy(){

        $flash = new FlashPolicy;
        $flash->addAllowedAccess('*', 8080); // Allow all Flash Sockets from any domain to connect on port 8080

        $server = IoServer::factory($flash, 843);
        $server->run();

    }

    private function checkOrigin(){
        $checkedApp = new OriginCheck(new FopSocket(), ['localhost']);
        $checkedApp->allowedOrigins[] = 'mydomain.com';
        $server = IoServer::factory(new HttpServer($checkedApp));
        $server->run();
    }


    

}



