<?php
namespace zetsoft\service\utility;


use Spatie\string\Integrations\Underscore;
use spatie\string\Exceptions;
use zetsoft\system\kernels\ZFrame;


class Geocoder extends ZFrame
{
        public function coder(){

         echo '123';

        }
   
        public function  coderExample()
        {

            $coordinate = new Coordinate($geocoderResult, Ellipsoid::createFromName(Ellipsoid::AIRY));
            // or in an array of latitude/longitude coordinate within GRS 1980 ellipsoid
            $coordinate = new Coordinate([48.8234055, 2.3072664], Ellipsoid::createFromName(Ellipsoid::GRS_1980));
            // or in latitude/longitude coordinate within WGS84 ellipsoid
            $coordinate = new Coordinate('48.8234055, 2.3072664');
            // or in degrees minutes seconds coordinate within WGS84 ellipsoid
            $coordinate = new Coordinate('48°49′24″N, 2°18′26″E');
            // or in decimal minutes coordinate within WGS84 ellipsoid
            $coordinate = new Coordinate('48 49.4N, 2 18.43333E');
            // the result will be:
            printf("Latitude: %F\n", $coordinate->getLatitude()); // 48.8234055
            printf("Longitude: %F\n", $coordinate->getLongitude()); // 2.3072664
            printf("Ellipsoid name: %s\n", $coordinate->getEllipsoid()->getName()); // WGS 84
            printf("Equatorial radius: %F\n", $coordinate->getEllipsoid()->getA()); // 6378136.0
            printf("Polar distance: %F\n", $coordinate->getEllipsoid()->getB()); // 6356751.317598
            printf("Inverse flattening: %F\n", $coordinate->getEllipsoid()->getInvF()); // 298.257224
            printf("Mean radius: %F\n", $coordinate->getEllipsoid()->getArithmeticMeanRadius()); // 6371007.772533
// it's also possible to modify the coordinate without creating an other coodinate
            $coordinate->setFromString('40°26′47″N 079°58′36″W');
            printf("Latitude: %F\n", $coordinate->getLatitude()); // 40.446388888889
            printf("Longitude: %F\n", $coordinate->getLongitude()); // -79.976666666667

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





