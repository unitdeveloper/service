<?php

/**
 *
 * @author OtabekNosirov
 * @author JaloliddinovSalohiddin
 * @author AkromovAzizjon
 *
 */

namespace zetsoft\service\geo;


use GeoIp2\Database\Reader;
use zetsoft\models\cpas\CpasTeaser;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\kernels\ZView;

class Geodecoder extends ZFrame
{



    public $cpTracks;

    #region init



    public function init()
    {
        $this->cpTracks = collect(CpasTeaser::find()->asArray()->all());

        parent::init();
    }

    #endregion init

    public function test($item)
    {
    $test = $this->getInfosById($item)->city->name ;
    $test1 = $this->getOs($item) ;
    $test = $this->getInfosById($item)->city->name ;

        //$this->exampleSetIp();
    }

    #region getInfosById

    public function exampleSetIp(){
        $country = [
            'continent' => [
                'code' => 'NA',
                'geoname_id' => 42,
                'nameOn' => ['en' => 'North America'],
            ],
            'country' => [
                'geoname_id' => 1,
                'iso_code' => 'US',
                'nameOn' => ['en' => 'United States of America'],
            ],
            'maxmind' => ['queries_remaining' => 11],
            'traits' => [
                'ip_address' => '1.2.3.4',
                'network' => '1.2.3.0/24',
            ],
        ];
    }

    /**
     *
     * Function  getInfosById
     * Getting about user's information by ip
     *
     * @param $ip
     * @return  \GeoIp2\Model\City
     * @throws \GeoIp2\Exception\AddressNotFoundException
     * @throws \MaxMind\Db\Reader\InvalidDatabaseException
     */

    public function getInfosById($ip)
    {
        //$reader = new Reader(Root . '\vendor\GeoLite2-City.mmdb');
        $reader = new Reader(Root . '\vendor\GeoLite2-City.mmdb');
        //$reader = new Reader(Root . '\vendor\lysenkobv\maxmind-geolite2-database\city.mmdb');
        //$reader = new Reader(Root . '/usr/local/share/GeoIP/GeoIP2-City.mmdb');
        $record = $reader->city($ip);
        
        print($record->country->isoCode . "\n"); // 'US'
        print($record->country->name . "\n"); // 'United States'
        print($record->country->nameOn['zh-CN'] . "\n"); // '美国'

        print($record->mostSpecificSubdivision->name . "\n"); // 'Minnesota'
        print($record->mostSpecificSubdivision->isoCode . "\n"); // 'MN'

        print($record->city->name . "\n"); // 'Minneapolis'

        print($record->postal->code . "\n"); // '55455'

        print($record->location->latitude . "\n"); // 44.9733
        print($record->location->longitude . "\n"); // -93.2323

        print($record->traits->network . "\n"); // '128.101.101.101/32'

        return $record;
    }

    #endregion getInfosById

    #region getOS

    public function getOs($item)
    {

        //$user_agent = $_SERVER['HTTP_USER_AGENT'] ;

        $os_array = array(
            '/windows nt 10/i' => 'Windows 10',
            '/windows nt 6.3/i' => 'Windows 8.1',
            '/windows nt 6.2/i' => 'Windows 8',
            '/windows nt 6.1/i' => 'Windows 7',
            '/windows nt 6.0/i' => 'Windows Vista',
            '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
            '/windows nt 5.1/i' => 'Windows XP',
            '/windows xp/i' => 'Windows XP',
            '/windows nt 5.0/i' => 'Windows 2000',
            '/windows me/i' => 'Windows ME',
            '/win98/i' => 'Windows 98',
            '/win95/i' => 'Windows 95',
            '/win16/i' => 'Windows 3.11',
            '/macintosh|mac os x/i' => 'Mac OS X',
            '/mac_powerpc/i' => 'Mac OS 9',
            '/linux/i' => 'Linux',
            '/ubuntu/i' => 'Ubuntu',
            '/iphone/i' => 'iPhone',
            '/ipod/i' => 'iPod',
            '/ipad/i' => 'iPad',
            '/android/i' => 'Android',
            '/blackberry/i' => 'BlackBerry',
            '/webos/i' => 'Mobile'
        );
        $os_platform = null;
        foreach ($os_array as $regex => $value)
            if (preg_match($regex, $item))
                $os_platform = $value;

        return $os_platform;


    }

    #endregion getOS

    #region getModelPhone

    /**
     *
     * Function  getModelPhone
     *
     * Getting information about user's model phone by browser Header
     *
     * @param $item
     * @return  mixed
     */

    public function getModelPhone($item)
    {

        $symbol_start = strpos($item, '(');
        $symbol_end = strpos($item, ')');
        $device_el = substr($item, $symbol_start + 1, $symbol_end - $symbol_start - 1);
        $device_arr = explode(';', $device_el);
        $device_model = $device_arr[count($device_arr) - 1];

        $data['device_model'] = $device_model;
        $data['data'] = $device_arr;

        return $data;

    }

    #endregion getModelPhone

    #region getBrowser

    /**
     *
     * Function  getBrowser
     *
     * Getting information about user's browser by Header
     *
     * @param $item
     * @return  string
     */

    public function getBrowser($item)
    {

        $browser = "Unknown Browser";

        $browser_array = array(
            '/msie/i' => 'Internet Explorer',
            '/firefox/i' => 'Firefox',
            '/safari/i' => 'Safari',
            '/chrome/i' => 'Chrome',
            '/edge/i' => 'Edge',
            '/opera/i' => 'Opera',
            '/netscape/i' => 'Netscape',
            '/maxthon/i' => 'Maxthon',
            '/konqueror/i' => 'Konqueror',
            '/mobile/i' => 'Handheld Browser'
        );

        foreach ($browser_array as $regex => $value)
            if (preg_match($regex, $item))
                $browser = $value;

        return $browser;
    }

    #endregion getBrowser

    #region subidGenerate

    /**
     *
     * Function  subidGenerate
     *
     * Generating subid
     *
     * @return  string
     */

    public function subidGenerate()
    {

        $from_db = $this->cpTracks->pluck('subid');
        $subid = Az::$app->cores->auth->generate(10);
        $isHave = false;

        while ($isHave) {
            if (!ZArrayHelper::isIn($subid, $from_db))
                $isHave = true;
            else
                $subid = Az::$app->cores->auth->generate(10);

        }

        return $subid;
    }

    #endregion subidGenerate



    #region
    public function subidGenerateExample()
    {

        $from_db = $this->cpTracks->pluck('subid');
        $subid = Az::$app->cores->auth->generate(10);
        $isHave = false;

        while ($isHave) {
            if (!ZArrayHelper::isIn($subid, $from_db))
                $isHave = true;
            else
                $subid = Az::$app->cores->auth->generate(10);

        }

        return $subid;
    }

    #endregion




}
