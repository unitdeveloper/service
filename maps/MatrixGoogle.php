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
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopProduct;
use zetsoft\models\user\User;
use zetsoft\models\user\UserCompany;
use zetsoft\system\actives\ZActiveData;
use zetsoft\system\actives\ZActiveQuery;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\dbitem\map\DistanceItem;

class MatrixGoogle extends ZFrame
{

    #region Vars

    /* @var ZActiveQuery $addressFrom */
    public $addressFrom;

    /* @var ZActiveData $addressTo */
    public $addressTo;
    /* @var ZActiveData $unit */
    public $unit = self::unit['m'];
    public const unit = [
        'km' => 'K',
        'm' => 'M'
    ];

    public $mode = self::mode['driving'];
    public const mode = [
        'driving' => 'driving',
        'walking' => 'walking',
        'bicycling' => 'bicycling',
        'transit' => 'transit',
    ];

    public $api_key = 'AIzaSyBkxS5l87lclaC6MIWSGejdCXL13wSShRo';
    public $formattedAddrFrom;
    public $formattedAddrTo;
    public $duration;
    #endregion

    #region Test

    public function tests()
    {
        vdd('sadf');
        return $this->testMultiDots();
    }

    public function testMultiDots()
    {
        vdd('saf');
        return $this->multicore(1, [4, 3, 2]);
    }

    public function testtwo()
    {
        return $this->core(1, 4);
    }
    #endregion


    #region Deliver

    public function deliverPrice($orderId = null, $companyId = null)
    {
       $company = UserCompany::findOne($companyId);

       $order = ShopOrder::findOne($orderId);

       $company_address = PlaceAdress::findOne($company->place_adress_id);

       $order_address = PlaceAdress::findOne($order->place_adress_id);

       $distance = $this->cores($company_address->id, $order_address->id);

       $price = (float) $distance->distance_in_km * (float) $company->delivery_price_per_km;

       return $price;
    }

    #endregion

    #region Main

    public function cores(int $from = null, int $to = null)
    {
        $fromAddress = PlaceAdress::findOne($from);
        $toAddress = PlaceAdress::findOne($to);
        if ($fromAddress === null && $toAddress === null) {
            return null;
        }

        $fromAddress = ZArrayHelper::getValue($fromAddress->location, '0');
        $toAddress = ZArrayHelper::getValue($toAddress->location, '0');

        $this->addressFrom = ZArrayHelper::getValue($fromAddress, 'lat'). ',' . ZArrayHelper::getValue($fromAddress, 'lng');

        $this->addressTo = ZArrayHelper::getValue($toAddress, 'lat'). ',' . ZArrayHelper::getValue($toAddress, 'lng');


        $this->formattedAddrFrom = str_replace(' ', '+', $this->addressFrom);
        $this->formattedAddrTo = str_replace(' ', '+', $this->addressTo);

        $item = new DistanceItem();
        $item->distance_in_meter = $this->distanceStreet();
        $item->distance_in_km = $this->distanceStreet() / 1000;
        $item->time = $this->duration;
      //  $item->distance_in_meter .= Az::l('meters');

        return $item;
    }

    public function multicore(int $from = null, $destinations = null)
    {
        if ($from === null && $destinations === null){
            return null;
        }
        $fromAddress = PlaceAdress::findOne($from);

        //ZArrayHelper::getValue();
        //$fromAddress = $fromAddress->location['location'][0];
        $fromAddress = ZArrayHelper::getValue($fromAddress->location, '0');

        //$this->addressFrom = $fromAddress['lat'] . ',' . $fromAddress['lng'];

        $this->addressFrom = ZArrayHelper::getValue($fromAddress, 'lat'). ',' . ZArrayHelper::getValue($fromAddress, 'lng');

        $this->formattedAddrFrom = str_replace(' ', '+', $this->addressFrom);

        foreach ($destinations as $to) {

            $toAddress = PlaceAdress::findOne($to);

            //$toAddress = $toAddress->location['location'][0];
            $toAddress = ZArrayHelper::getValue($toAddress->location, '0');

           // $this->addressTo = $toAddress['lat'] . ',' . $toAddress['lng'];

            $this->addressTo = ZArrayHelper::getValue($toAddress, 'lat'). ',' . ZArrayHelper::getValue($toAddress, 'lng');



            $this->formattedAddrTo .= str_replace(' ', '+', $this->addressTo);

            $this->formattedAddrTo .= '|';
        }

        $this->formattedAddrTo = rtrim($this->formattedAddrTo, '|');

        $item = new DistanceItem();

        $item->distance_in_meter = $this->distanceStreet();

        $item->distance_in_km = $this->distanceStreet() / 1000;

        $item->time = $this->duration;

        $item->distance_in_meter .= ' '.Az::l('meters');

        return $item;
    }

    public function getprice(int $from, int $to, $price)
    {
        $item = $this->core($from, $to);
        $item->price = $this->distanceStreet() / 1000 * $price;
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


        if ($unit === "K") {
            return round($miles * 1.609344, 2) . ' km';
        } elseif ($unit === "M") {
            return round($miles * 1609.344, 2) . Az::l('meters');
        } else {
            return round($miles, 2) . ' miles';
        }
    }

    public function distanceStreet()
    {
        // Google API key
        $apiKey = $this->api_key;

        $distance_data = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins=' . urlencode($this->formattedAddrFrom) . '&destinations=' . urlencode($this->formattedAddrTo) . '&mode=' . $this->mode . '&key=' . $apiKey);
        $distance_arr = json_decode($distance_data);

        if ($distance_arr->status === 'OK') {
            $elements = $distance_arr->rows[0]->elements;
            $min = $elements[0]->distance->value;
            foreach ($elements as $element) {
                $distance = $element->distance->value;//. Az::l('meters');

                if ($distance < $min) {
                    $min = $distance;
                    $this->duration = $element->duration->text;
                }

            }
            $distance = $min;
            return $distance;
        } else {
            return 'Error';
        }
    }
    #endregion

    #region GoogleWidgetData
    public function widgetData()
    {
        $warehouses = PlaceAdress::find()->all();
        return [
            'WH' => $warehouses,
            'customer' => [],
        ];
    }


    ##endregion
}
