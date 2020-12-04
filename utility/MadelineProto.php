<?php

namespace zetsoft\service\utility;


class MadelineProto{

public function sendMessage(){/*

    if (!file_exists('madeline.php')) {
        copy('https://phar.madelineproto.xyz/madeline.php', 'madeline.php');
    }
    include 'madeline.php';


    $MadelineProto = new \danog\MadelineProto\API('session.madeline');
    $MadelineProto->start();

    $Updates = $MadelineProto->messages->sendMessage(['peer' => '@xolmat98', 'message' => 'something']);


 */



/*

    $MadelineProto = new \danog\MadelineProto\API('session.madeline');
    $MadelineProto->async(true);
    $MadelineProto->loop(function () use ($MadelineProto) {
        yield $MadelineProto->start();

        $me = yield $MadelineProto->botLogin('280253273:AAG5oiNEFPvTpy8LdnX4RPL1reeZCVx4uKM');;

        $MadelineProto->logger($me);

        if (!$me['bot']) {
            yield $MadelineProto->messages->sendMessage(['peer' => '@danogentili', 'message' => "Hi!\nThanks for creating MadelineProto! <3"]);
            yield $MadelineProto->channels->joinChannel(['channel' => '@MadelineProto']);
       }
        yield $MadelineProto->echo('OK, done!');
    });


  */

}




}

/*
 *
 *
 * 'bot_api' => '280253273:AAG5oiNEFPvTpy8LdnX4RPL1reeZCVx4uKM',
        'chat_id' => '-1001176048898',
 */

