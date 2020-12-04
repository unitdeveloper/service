<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\maps;


use zetsoft\models\place\PlaceAdress;
use zetsoft\models\shop\ShopProduct;
use zetsoft\models\user\User;
use zetsoft\system\actives\ZActiveData;
use zetsoft\system\actives\ZActiveQuery;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\dbitem\map\DistanceItem;

class matrix extends ZFrame
{

    #region Vars


    public $addressFrom;


    public $addressTo;

    public $unit = self::unit['m'];
    public $units = self::unit['metric'];
    public const unit = [
        'km' => 'K',
        'm' => 'M'
    ];
    public const units = [
        'metric' => 'metric',
        'imperial' => 'imperial'
    ];
    public $mode = self::type['driving'];
    public const type = [
        'driving' => 'driving',
        'walking' => 'walking',
        'bicycling' => 'bicycling',
        'transit' => 'transit',
    ];
    public $language ='en';
    public $region;
    public $avoid=self::avoid['tolls'];
    public const avoid = [
        'tolls' => 'tolls',
        'highways' => 'highways',
        'ferries' => 'ferries',
        'indoor' => 'indoor',
    ];
    public $arrival_time;
    public $departure_time;
    public $traffic_model=self::traffic['best_guess'];
    public const traffic = [
        'best_guess' => 'best_guess',
        'pessimistic' => 'pessimistic',
        'optimistic' => 'optimistic',
    ];
    public $transit_mode=self::transit['bus'];
    public const transit = [
        'bus' => 'bus',
        'subway' => 'subway',
        'train' => 'train',
        'tram' => 'tram',
        'rail' => 'rail',
    ];
    public $transit_routing_preference=self::preference['less_walking'];
    public const preference = [
        'less_walking' => 'less_walking',
        'fewer_transfers' => 'fewer_transfers',
    ];


    public $api_key = 'AIzaSyBkxS5l87lclaC6MIWSGejdCXL13wSShRo';
    public $formattedAddrFrom;
    public $formattedAddrTo;
    public $duration;
    #endregion

    #region Test

    public function test()
    {
       return $this->testMultiDots();
    }
    public function testMultiDots()
    {
    return $this->multicore(1,[4,3,2]);
    }
    public function testtwo()
    {
       return $this->core(1,4);
    }
    #endregion

    #region Main

    public function core(int $from, int $to)
    {
        $FromAddress = PlaceAdress::findOne($from);
        $ToAddress = PlaceAdress::findOne($to);
        if (empty($FromAddress) && empty($ToAddress)) {
            return null;
        } else {
            $FromAddress = $FromAddress->location['location'][0];
            $ToAddress = $ToAddress->location['location'][0];
        }

        $this->addressFrom = $FromAddress['lat'] . ',' . $FromAddress['lng'];
        $this->addressTo = $ToAddress['lat'] . ',' . $ToAddress['lng'];

        $this->formattedAddrFrom = str_replace(' ', '+', $this->addressFrom);
        $this->formattedAddrTo = str_replace(' ', '+', $this->addressTo);

        $item = new DistanceItem();
        $item->distance_in_meter = $this->distance_street();
        $item->time = $this->duration;
        $item->distance_in_meter .= Az::l('meters');

        return $item;
    }

    public function multicore(int $from, $destinations)
    {
        $FromAddress = PlaceAdress::findOne($from);

        $FromAddress = $FromAddress->location['location'][0];
        $this->addressFrom = $FromAddress['lat'] . ',' . $FromAddress['lng'];
        $this->formattedAddrFrom = str_replace(' ', '+', $this->addressFrom);

        foreach ($destinations as $to) {
            $ToAddress = PlaceAdress::findOne($to);
            $ToAddress = $ToAddress->location['location'][0];
            $this->addressTo = $ToAddress['lat'] . ',' . $ToAddress['lng'];
            $this->formattedAddrTo .= str_replace(' ', '+', $this->addressTo);

            $this->formattedAddrTo .= '|';
        }

        $this->formattedAddrTo = rtrim($this->formattedAddrTo, '|');

        $item = new DistanceItem();
        $item->distance_in_meter = $this->distance_street();
        $item->time = $this->duration;
        $item->distance_in_meter .= Az::l('meters');

        return $item;
    }

    public function getprice(int $from, int $to, $price)
    {
        $item = $this->core($from, $to);
        $item->price = $this->distance_street() / 1000 * $price;
        return $item->price;
    }

    public function distance_line()
    {
        // Google API key
        $apiKey = $this->api_key;
        // Geocoding API request with start address
        $geocodeFrom = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address=' . $this->formattedAddrFrom . '&sensor=false&key=' . $apiKey);
        $outputFrom = json_decode($geocodeFrom);
        if (!empty($outputFrom->error_message)) {
            return $outputFrom->error_message;
        }

        // Geocoding API request with end address
        $geocodeTo = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address=' . $this->formattedAddrTo . '&sensor=false&key=' . $apiKey);
        $outputTo = json_decode($geocodeTo);
        if (!empty($outputTo->error_message)) {
            return $outputTo->error_message;
        }

        // Get latitude and longitude from the geodata
        $latitudeFrom = $outputFrom->results[0]->geometry->location->lat;
        $longitudeFrom = $outputFrom->results[0]->geometry->location->lng;
        $latitudeTo = $outputTo->results[0]->geometry->location->lat;
        $longitudeTo = $outputTo->results[0]->geometry->location->lng;

        // Calculate distance between latitude and longitude
        $theta = $longitudeFrom - $longitudeTo;
        $dist = sin(deg2rad($latitudeFrom)) * sin(deg2rad($latitudeTo)) + cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;

        // Convert unit and return distance
        $unit = strtoupper($this->unit);
        if ($unit == "K") {
            return round($miles * 1.609344, 2) . ' km';
        } elseif ($unit == "M") {
            return round($miles * 1609.344, 2) . Az::l('meters');
        } else {
            return round($miles, 2) . ' miles';
        }
    }

    public function distance_street()
    {
        // Google API key
        $apiKey = $this->api_key;

        $distance_data = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins=' . urlencode($this->formattedAddrFrom) . '&destinations=' . urlencode($this->formattedAddrTo) . '&mode=' . $this->mode . '&key=' . $apiKey);
        $distance_arr = json_decode($distance_data);
        
        if ($distance_arr->status == 'OK') {
            $elements = $distance_arr->rows[0]->elements;
            $min = $elements[0]->distance->value;
            foreach ($elements as $element) {
            $distance = $element->distance->value;//. Az::l('meters');

            if($distance<$min){
                $min=$distance;
                $this->duration = $element->duration->text;
            }

            }
            $distance=$min;
            return $distance;
        } else {
            return 'Error';
        }
    }
    #endregion
}
