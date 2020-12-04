<?php
namespace zetsoft\service\socket;

use Workerman\Worker;
use Workerman\WebServer;
use Workerman\Autoloader;
use PHPSocketIO\SocketIO;
use zetsoft\system\kernels\ZFrame;

// composer autoload

class Phpiosocket_2 extends ZFrame
{
    const DEFAULT_PORT=2020;
    private $port;

    private $chatList = [
        #unique = username/room
        ['chat_id'=>'Haloyiq',      'type'=>'public', 'name'=>'Haloyiq'],
        #['chat_id'=>'Group 1',     'type'=>'group'],
        #['chat_id'=>'Bill',        'type'=>'private'],
    ];
    private static function findChat($chat_id, $chatList){
        foreach($chatList as $row){
            if($row['chat_id'] == $chat_id){
                return $row;
            }
        }
        return false;
    }

    private static function removeChat($name, $chatList){
        $result = [];
        foreach($chatList as $row) {
            if($row['name'] != $name) {
                $result[] = $row;
            }
        }
        return $result;
    }

    public function __construct($port=self::DEFAULT_PORT){
        $this->port = $port;
    }
    public function run(){
        $io = new SocketIO($this->port);
        $io->on('connection', function ($socket) use ($io) {
            $socket->addedUser = false;
            // when the client emits 'new message', this listens and executes
            #io.in('game').emit('big-announcement', 'the game will start soon');
            $socket->on('new message', function ($data) use ($socket, $io) {
                // we tell the client to execute 'new message'
                $chat = self::findChat($data['chat_id'], $this->chatList);
                $socket->broadcast->emit('new message', array(
                    'username' => $socket->username,
                    'message' => $data,
                    'time' => date("Y-m-d h:i:sa"),
                    'chat_id' => $data['chat_id']
                ));

            });

            // when the client emits 'add user', this listens and executes
            $socket->on('add user', function ($username) use ($socket, $io) {
                global $numUsers;
                $isExict = false;
                // we store the username in the socket session for this client
                $socket->username = $username;
                // add the client's username to the global list
                foreach ($this->chatList as $chat){
                    if ($chat['name'] == $username){
                        $isExict = true;
                    }
                }
                if (!$isExict){
                    $this->chatList[] = ['chat_id'=>$username, 'type'=>'private', 'name'=>$username];
                    ++$numUsers;
                    $socket->addedUser = true;
                    $socket->emit('login', array(
                        'numUsers' => $numUsers,
                        'isExict' => $isExict
                    ));

                    // echo globally (all clients) that a person has connected

                    $socket->broadcast->emit('user joined', array(
                        'username' => $socket->username,
                        'numUsers' => $numUsers,
                    ));
                }else{
                    $socket->emit('login', array(
                        'numUsers' => $numUsers,
                        'isExict' => $isExict
                    ));
                }
            });

            $socket->on('update chat list', function () use ($io) {
                $chatList = $this->chatList;
                $io->emit('update chat list', array(
                    'chatList' => $chatList,
                ));
            });



            // when the client emits 'typing', we broadcast it to others
            $socket->on('typing', function () use ($socket) {
                $socket->broadcast->emit('typing', array(
                    'username' => $socket->username
                ));
            });

            // when the client emits 'stop typing', we broadcast it to others
            $socket->on('stop typing', function () use ($socket) {
                $socket->broadcast->emit('stop typing', array(
                    'username' => $socket->username
                ));
            });

            // when the user disconnects.. perform this
            $socket->on('disconnect', function () use ($socket, $io) {
                global $numUsers;
                // remove the username from global usernames list
                if ($socket->addedUser) {
                    $this->chatList = self::removeChat($socket->username, $this->chatList);
                    --$numUsers;
                    // echo globally that this client has left
                    $socket->broadcast->emit('user left', array(
                        'username' => $socket->username,
                        'numUsers' => $numUsers,
                    ));

                    $io->emit('update chat list', array(
                        'chatList' => $this->chatList,
                    ));
                }
            });

        });

        if (!defined('GLOBAL_START')) {
            Worker::runAll();
        }
    }
}