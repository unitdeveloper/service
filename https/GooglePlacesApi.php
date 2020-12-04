<?php
/**
 * Class    GooglePlacesApi
 * @package zetsoft\service\https
 *
 * @author DilshodKhudoyarov
 *
 * https://github.com/SachinAgarwal1337/google-places-api
 * Class file Bu Google Places API Web Service uchun PHP o'rash vositasi.
 */

namespace zetsoft\service\https;
use zetsoft\system\kernels\ZFrame;
use SKAgarwal\GoogleApi\PlacesApi;

class GooglePlacesApi extends ZFrame
{
      public function google_place() {
          $googlePlaces = new PlacesApi('API_KEY');
          //Note: You can also set the API KEY after initiating the class using setKey('KEY') method
          //You can chain this with method with any other methods.
         $response = $googlePlaces->placeAutocomplete('some input');
         //$response can give a warning REQUEST_DENIED because of not configured settings of Google API in server side
         vd($response);
      }

      public function nearbySearch ($location,$radius,$params) {
          $googlePlaces = new PlacesApi('API_KEY');
          //Note: You can also set the API KEY after initiating the class using setKey('KEY') method
          //You can chain this with method with any other methods.
          $response = $googlePlaces->nearbySearch($location, $radius, $params);
          //$response can give a warning REQUEST_DENIED because of not configured settings of Google API in server side
          vd($response);
      }

    public function find_place ($input,$inputType, $params) {
        $googlePlaces = new PlacesApi('API_KEY');
        //Note: You can also set the API KEY after initiating the class using setKey('KEY') method
        //You can chain this with method with any other methods.
        $response = $googlePlaces->findPlace($input, $inputType, $params);
        //$response can give a warning REQUEST_DENIED because of not configured settings of Google API in server side
        vd($response);
    }

    public function place_details($placeId,$params) {
        $googlePlaces = new PlacesApi('API_KEY');
        //Note: You can also set the API KEY after initiating the class using setKey('KEY') method
        //You can chain this with method with any other methods.
        $response = $googlePlaces->placeDetails($placeId, $params);
        //$response can give a warning REQUEST_DENIED because of not configured settings of Google API in server side
        vd($response);
    }
}