<?php
namespace zetsoft\service\utility;


use Spatie\string\Integrations\Underscore;
use spatie\string\Exceptions;
use zetsoft\system\kernels\ZFrame;


class Spatie extends ZFrame
{
    public function slug($argument)
    {
        /* echo string($argument)->contains($second);*/
        echo string($argument)->slugify();

    }

    public function slugBetween($argument, $second, $third)
    {
        /* echo string($argument)->contains($second);*/

        echo string($argument)->slugify()->between($second, $third);
    }

    public function stringValue($argument)
    {
        /* echo string($argument)->contains($second);*/
        return string($argument)->isEmail(); // returns true;
    }

    public function toUpper($argument)
    {
        echo string($argument)->toUpper();

    }

    public function toLowerBetween($argument, $first, $second)
    {
        echo 'stuck in the ' . string($argument)->between($first, $second)->toLower() . ' with you';
    }

    public function toLower($argument)
    {
        echo string($argument)->toLower();
    }

    public function tease($length, $moreTextIndicator)
    {

        $longText = 'Now that there is the Tec-9, a crappy spray gun from South Miami.
         This gun is advertised as the most popular gun in American crime. Do you believe that shit?
          It actually says that in the
         little book that comes with it: the most popular gun in American crime.';
         echo string($longText)->tease(10);

    }

    public function replaceFirst($search, $replace){
        $sentence = 'A good thing is not a good thing.';
        echo string($sentence)->replaceFirst($search, $replace);
    }

    public function replaceLast($search, $replace){
        $sentence = 'A good thing is not a good thing.';
        echo string($sentence)->replaceLast($search, $replace); // A good thing is not a bad thing.
    }
    public function prefix($strings,$second){
        echo string($strings)->prefix($second); //hello world
    }
    public function suffix($string, $second){
        echo string($string)->suffix($second);
    }
    public function possessive($argument, $second){
        echo string($argument)->possessive(); // Bob's
        echo  string($second)->possessive(); // Charles'
    }

    public function lastSegment($delimiter,$index){

       echo string('foo/bar/baz')->segment($delimiter,$index);
    }
};



   /*    private $token;
       private $chatId;

     public function sendTelegram($token, $chatId, $text)
     {
            $this->token = $token;
            $this->chatId = $chatId;

            $telegram = new Api($this->token, true);
             $telegram
                 ->setAsyncRequest(true);

             $response = $telegram->sendMessage([
                'chat_id' => $this->chatId,
                 'text' => $text
             ]);

            
     }*/





