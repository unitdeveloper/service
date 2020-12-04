<?php
namespace zetsoft\service\socket;

use Workerman\Worker;
use Workerman\WebServer;
use Workerman\Autoloader;
use PHPSocketIO\SocketIO;
use zetsoft\system\kernels\ZFrame;

// composer autoload

class Phpiosocket_3 extends ZFrame
{
    public function run(){
        $io = new SocketIO(2020);

        $io->on('connection', function ($socket) use ($io) {
            $socket->addedUser = false;
            // when the client emits 'new message', this listens and executes
            $socket->on('new message', function ($data) use ($socket) {
                // we tell the client to execute 'new message'
                $socket->broadcast->emit('new message', array(
                    'username' => $socket->username,
                    'message' => $data,
                ));
            });

            // when the client emits 'add user', this listens and executes
            $socket->on('add user', function ($username) use ($socket, $io) {
                global $usernames, $numUsers, $statuses;
                // we store the username in the socket session for this client
                $socket->username = $username;
                // add the client's username to the global list
                $usernames[$username] = $username;
                $statuses[] = ['username' => $socket->username, 'status' => 'online', 'lastActiveTime' => time()];
                ++$numUsers;
                $socket->addedUser = true;
                $io->emit('login', array(
                    'numUsers' => $numUsers,
                    'users' => $statuses
                ));
                // echo globally (all clients) that a person has connected
                $socket->broadcast->emit('user joined', array(
                    'username' => $socket->username,
                    'numUsers' => $numUsers,
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

            $socket->on('update status', function () use ($socket, $io) {
                global $statuses;
                $data = $statuses;
                if ($data) {
                    foreach ($data as $status) {
                        if ($status['username'] == $socket->username) {
                            $status['lastActiveTime'] = time();
                        }
                        $updatedStatuses[] = $status;
                    }
                }
                $statuses = $updatedStatuses;
                $io->emit('change status', array(
                    'status' => $updatedStatuses
                ));
            });

            $socket->on('change status', function () use ($socket, $io) {
                global $statuses;
                $data = $statuses;

                foreach ($data as $status) {
                    if ($status['username'] == $socket->username) {
                        if ((time() - (int)$status['lastActiveTime']) < 10) {
                            $status['status'] = 'online';
                        } else if ((time() - (int)$status['lastActiveTime']) >= 10 && (time() - (int)$status['lastActiveTime']) <= 30) {
                            $status['status'] = 'away';
                        } else {
                            $status['status'] = 'offline';
                        }
                    }
                    $updatedStatuses[] = $status;
                }
                $statuses = $updatedStatuses;
                $io->emit('change status', $updatedStatuses);
            });

            // when the user disconnects.. perform this
            $socket->on('disconnect', function () use ($socket) {
                global $usernames, $numUsers, $statuses;
                $data = $statuses;
                // remove the username from global usernames list
                if ($socket->addedUser) {
                    unset($usernames[$socket->username]);
                    --$numUsers;
                    foreach ($data as $status) {
                        if ($status['username'] != $socket->username) {
                            $updatedStatuses[] = $status;
                        }

                    }
                    $statuses = $updatedStatuses;
                    // echo globally that this client has left
                    $socket->broadcast->emit('user left', array(
                        'username' => $socket->username,
                        'numUsers' => $numUsers,
                    ));
                }
            });

        });

        if (!defined('GLOBAL_START')) {
            Worker::runAll();
        }
    }
}
