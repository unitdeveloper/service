<?php

/**
 *
 *
 * Author:  Asror Zakirov, (Niyozbek Obidov)
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\utility;
use Workerman\Events\EventInterface;
use Workerman\Worker;

class Workerman
{
    #A websocket server
    #An http server
    #A WebServer server doesnt exist
    #A tcp server
    const DEFAULT_PROTOCOL = 'DefaultProtocol';
    const DEFAULT_PROTOCOL_FILE = self::DEFAULT_PROTOCOL.'.php';
    private static $connection_types = ['websocket', 'http', 'tcp'];
    private $connection_type;
    private $connection_ip;
    private $connection_port;
    private $custom_protocol = false;
    private $connection_config = [];

    public function __construct($connection_type, $connection_ip, $connection_port, $connection_config = []){
        //check if it is custom protocol
        if(!in_array($connection_type, self::$connection_types)){
            $this->custom_protocol = true;
            $fileName = $connection_type.'.php';
            if(file_exists($fileName)){
                echo "Loading {$fileName} protocol file...\n";
                $this->connection_type = $connection_type;
                require $fileName;
            }else{
                echo "{$fileName} not found. Please copy ".self::DEFAULT_PROTOCOL_FILE." as {$fileName}.\n Loading default protocol file...\n";
                $this->connection_type = self::DEFAULT_PROTOCOL;
                require self::DEFAULT_PROTOCOL_FILE;
            }
        }else{
            echo "Loading {$connection_type}...\n";
            $this->connection_type = $connection_type;
        }
        $this->connection_ip = $connection_ip;
        $this->connection_port = $connection_port;
        $this->connection_config = $connection_config;
    }
    public function run(){
        // Check connection type and direct to methodo
        if(!$this->custom_protocol){
            $method = $this->connection_type.'Server';
            $this->$method();
        }else{
            $this->customProtocolServer();
        }
    }

    private function websocketServer(){
        /*Properties
            count
            name
            user
            reloadable
            transport
            connections
            daemonize
            stdoutFile
            pidFile
            globalEvent
        Callbacks
            onWorkerStart
            onWorkerStop
            onConnect
            onMessage
            onClose
            onBufferFull
            onBufferDrain
            onError*/
        $worker = new Worker("{$this->connection_type}://{$this->connection_ip}:{$this->connection_port}");

        //load connection_config parameters to the worker properties
        $properties = [
            //property name => default value
            'count' => 4,
            'name' => NULL,
            'user' => NULL,
            'reloadable' => NULL,
            'transport' => NULL,
            'daemonize' => NULL,
            'stdoutFile' => NULL,
            'pidFile' => NULL,
            'globalEvent' => NULL,
        ];
        $properties = self::loadProperties($properties, $this->connection_config);
        foreach($properties as $property => $value){
            if($properties[$property]){
                $worker->$property = $value;
            }
        }
        // Emitted when new connection come
        $worker->onConnect = function ($connection)
        {
            echo "New connection\n";
        };

        // Emitted when data received
        $worker->onMessage = function ($connection, $data)
        {
            // Send hello $data
            echo "New message:{$data}\n";
            $connection->send('hello ' . $data);
        };

        // Emitted when connection closed
        $worker->onClose = function ($connection)
        {
            echo "Connection closed\n";
        };

        // Run worker
        Worker::runAll();
    }
    private function httpServer(){
        $worker = new Worker("{$this->connection_type}://{$this->connection_ip}:{$this->connection_port}");
        /*Properties
            count
            name
            user
            reloadable
            transport
            connections
            daemonize
            stdoutFile
            pidFile
            globalEvent
        Callbacks
            onWorkerStart
            onWorkerStop
            onConnect
            onMessage
            onClose
            onBufferFull
            onBufferDrain
            onError*/
        // 4 processes default
        $properties = [
            //property name => default value
            'count' => 4,
            'name' => NULL,
            'user' => NULL,
            'reloadable' => NULL,
            'transport' => NULL,
            'daemonize' => NULL,
            'stdoutFile' => NULL,
            'pidFile' => NULL,
            'globalEvent' => NULL,
        ];
        $properties = self::loadProperties($properties, $this->connection_config);
        foreach($properties as $property => $value){
            if($properties[$property]){
                $worker->$property = $value;
            }
        }


        // Emitted when new connection come
        $worker->onConnect = function ($connection)
        {
            echo "New connection\n";
        };

        // Emitted when data received
        $worker->onMessage = function ($connection, $data)
        {
            // $_GET, $_POST, $_COOKIE, $_SESSION, $_SERVER, $_FILES are available
            var_dump($_GET, $_POST, $_COOKIE, $_SESSION, $_SERVER, $_FILES);
            // send data to client
            $connection->send("hello world \n");
        };

        // Run worker
        Worker::runAll();
    }
    private function tcpServer(){
        $tcp_worker = new Worker("{$this->connection_type}://{$this->connection_ip}:{$this->connection_port}");
        /*Properties
            id
            protocol
            worker
            maxSendBufferSize
            maxPackageSize
        Callbacks
            onMessage
            onClose
            onBufferFull
            onBufferDrain
            onError
        Methods
            send
            getRemoteIp
            getRemotePort
            close
            destroy
            pauseRecv
            resumeRecv     */
        $properties = [
            //property name => default value
            'id' => NULL,
            'protocol' => NULL,
            'worker' => NULL,
            'maxSendBufferSize' => NULL,
            'maxPackageSize' => NULL,
            'globalEvent' => NULL,
        ];
        $properties = self::loadProperties($properties, $this->connection_config);
        foreach($properties as $property => $value){
            if($properties[$property]){
                $tcp_worker->$property = $value;
            }
        }

        // Emitted when new connection come
        $tcp_worker->onConnect = function ($connection)
        {
            echo "New connection\n";
        };

        // Emitted when data received
        $tcp_worker->onMessage = function ($connection, $data)
        {
            // Send hello $data
            echo "New message:{$data}\n";
            $connection->send('hello ' . $data);
        };

        // Emitted when connection closed
        $tcp_worker->onClose = function ($connection)
        {
            echo "Connection closed\n";
        };

        // Run worker
        Worker::runAll();
    }
    private function customProtocolServer(){
        $worker = new Worker("{$this->connection_type}://{$this->connection_ip}:{$this->connection_port}");

        // 4 processes default
        $worker->count = $this->count;


        // Emitted when new connection come
        $worker->onConnect = function ($connection)
        {
            echo "New connection\n";
        };

        // Emitted when data received
        $worker->onMessage = function ($connection, $data)
        {
            // Send hello $data
            echo "New message:{$data}\n";
            $connection->send('hello ' . $data);
        };

        // Emitted when connection closed
        $worker->onClose = function ($connection)
        {
            echo "Connection closed\n";
        };

        // Run worker
        Worker::runAll();
    }
    private static function loadProperties($properties, $connection_config){
        foreach($connection_config as $property=>$value){
            //check if property exists
            if(array_key_exists($property, $properties)){
                $properties[$property] = $value;
            }else{
                echo "Wrong connection_config  `{$property}`. Property is not valid.";
                die();
            }
        }
        return $properties;
    }
}
