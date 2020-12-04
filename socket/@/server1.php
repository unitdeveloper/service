<?php

namespace zetsoft\service\socket;

require Root . '/vendori/netapp/vendor/autoload.php';

use Workerman\Worker;
use PHPSocketIO\SocketIO;
use zetsoft\system\kernels\ZFrame;

class server1 extends ZFrame{
    public function run(){

        $io = new SocketIO(2020);
        $io->on('connection', function ($socket) {
            $socket->addedUser = false;

            // When the client emits 'new message', this listens and executes
            $socket->on('new message', function ($data) use ($socket) {
                // We tell the client to execute 'new message'
                $socket->broadcast->emit('new message', array(
                    'username' => $socket->username,
                    'message' => $data
                ));
            });

            // When the client emits 'add user', this listens and executes
            $socket->on('add user', function ($username) use ($socket) {
                global $usernames, $numUsers;

                // We store the username in the socket session for this client
                $socket->username = $username;
                // Add the client's username to the global list
                $usernames[$username] = $username;
                ++$numUsers;

                $socket->addedUser = true;
                $socket->emit('login', array(
                    'numUsers' => $numUsers
                ));

                // echo globally (all clients) that a person has connected
                $socket->broadcast->emit('user joined', array(
                    'username' => $socket->username,
                    'numUsers' => $numUsers
                ));
            });

            // When the client emits 'typing', we broadcast it to others
            $socket->on('typing', function () use ($socket) {
                $socket->broadcast->emit('typing', array(
                    'username' => $socket->username
                ));
            });

            // When the client emits 'stop typing', we broadcast it to others
            $socket->on('stop typing', function () use ($socket) {
                $socket->broadcast->emit('stop typing', array(
                    'username' => $socket->username
                ));
            });

            // When the user disconnects, perform this
            $socket->on('disconnect', function () use ($socket) {
                global $usernames, $numUsers;

                // Remove the username from global usernames list
                if ($socket->addedUser) {
                    unset($usernames[$socket->username]);
                    --$numUsers;

                    // echo globally that this client has left
                    $socket->broadcast->emit('user left', array(
                        'username' => $socket->username,
                        'numUsers' => $numUsers
                    ));
                }
            });
        });

        Worker::runAll();

    }
}
