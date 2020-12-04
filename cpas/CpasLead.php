<?


/**
 *
 *
 * Author:  Asror Zakirov
 * Date:    9/20/2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\cpas;

/**
 *
 * @author Shahzod Gulomqodirov
 *
 * @license Jakhongir Kudratov
 */

use GuzzleHttp\Client;
use zetsoft\models\cpas\CpasCompany;
use zetsoft\models\cpas\CpasLand;
use zetsoft\models\cpas\CpasOffer;
use zetsoft\models\cpas\CpasStreamItem;
use zetsoft\models\cpas\CpasTracker;
use zetsoft\models\cpas\CpasStream;
use zetsoft\models\place\PlaceCountry;
use zetsoft\models\shop\ShopOrder;
use zetsoft\system\actives\ZActiveRecord;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;

class CpasLead extends ZFrame
{


    #region vars


    public $client;
    public $id;
    public $status_cpa;
    public $status = [
        'new' => 'new',
        'trash' => 'trash',
        'cancel' => 'cancel',
        'accept' => 'accept',
    ];
    public $methods = [
        'post' => "POST",
        'get' => "GET",
        'delete' => "DELETE",
        'put' => "PUT",
        'patch' => "PATCH"
    ];

    public $model = [
        'id' => 1,
        'status_callcenter' => 'double',
        'status_logistics' => 'completed'
    ];

    #endregion

    #region Cores


    public function init()
    {
        parent::init();
        $this->client = new Client();

    }

    #endregion

    #region test


    public function test()
    {
        $stream_id = 21;
        $user_name = 'Orders new';
        $user_phone = '998909877665';
        $amount = 3;
        vdd($this->addShopOrder($stream_id, $user_name, $user_phone, $amount));

        //$this->testCreateLead();
    }


    #endregion


    #region processingStatus


    public function processingStatus(ShopOrder $model)
    {
        if ($model->isNewRecord)
            return null;
        $this->id = $model->id;
        $statuscallcenter = $model->status_callcenter;
        vdd($statuscallcenter);
        $status_logistics = $model->status_logistics;
        vdd($status_logistics);
        $oldStatusCallCenter = ZArrayHelper::getValue($model->oldAttributes, 'status_callcenter');
        vdd($oldStatusCallCenter);
        $oldStatusLogistics = ZArrayHelper::getValue($model->oldAttributes, 'status_logistics');
        vdd($oldStatusLogistics);
        if ($model->cpas_track){
            if ($oldStatusCallCenter !== $model->status_callcenter || $oldStatusLogistics !== $model->status_logistics) {
            if ($statuscallcenter === 'new') {
                $this->status_cpa = $this->status['new'];
            } elseif ($statuscallcenter === 'cancel')
                $this->status_cpa = $this->status['cancel'];
            elseif ($statuscallcenter === 'approved') {
                $this->status_cpa = $this->status['accept'];
                if ($status_logistics === 'cancel')
                    $this->status_cpa = $this->status['cancel'];
                elseif ($status_logistics === 'completed')
                    $this->status_cpa = $this->status['accept'];
            } elseif ($statuscallcenter === 'not_ordered' || $statuscallcenter === 'double' || $statuscallcenter === 'incorrect')
                $this->status_cpa = $this->status['trash'];

            if ($this->status_cpa){
                $token = 'qvhFgB9YC4UUHfZNG6Pd8WZ6k7H6waw5Vg2UeAXJgKe44fFxSwzStCMQ3jmv2gtz';
                $this->requestStatus($token,$model->cpas_track);
                return true;
            }
        } else
                return false;
        }

        return null;

    }



    #endregion

    



    #region saveStatus


    public function saveStatus($model)
    {
        if ($this->processingStatus($model))
            $this->requestStatus();

    }

    #endregion

    #region addShopOrder


    public function addShopOrder($stream_id, $user_name, $user_phone, $amount)
    {
        $stream = CpasStream::findOne($stream_id);
        if ($stream) {
            $offer_id = $stream->cpas_offer_id;
            $offer = CpasOffer::findOne($offer_id);
            $catalog_id = $offer->shop_catalog_id;
        }

        $response = $this->client->request($this->methods['post'], '/api/shop/cart/add-order.aspx', [
            'form_params' => [
                'catalog_id' => $catalog_id,
                'user_name' => $user_name,
                'user_phone' => $user_phone,
                'amount' => $amount,
            ]
        ]);
        return json_decode($response->getbody(), true, 512, JSON_THROW_ON_ERROR);
    }


    #endregion


    #region createLead


    public function createLead($user_name, $user_phone, $amount, $catalog_id)
    {

        $lead = new \zetsoft\models\cpas\CpasLead();
        $lead->contact_name = $user_name;
        $lead->contact_phone = $user_phone;
        $lead->amount = $amount;
        $lead->shop_catalog_id = $catalog_id;
        $lead->status = \zetsoft\models\cpas\CpasLead::status['new'];


        if ($lead->save()) {
            return true;
        }
        return false;

    }


    public function testCreateLead()
    {
        $auth_key = 'BT4NjadCRbMcKqQW5f267gPvhA5q8Zj8QvKVW5vSBDECF2Tkf9HbynGzjr4Xvzgz';
        $user_name = 'kimdir';
        $user_phone = '9989009876765';
        $shop_order_id = 705;
        vdd($this->createLead($user_name, $user_phone, $shop_order_id, $auth_key));
    }

    #endregion

    #region
    public function createLeadNotUser($user_name, $user_phone, $shop_order_id, $stream_id)
    {
        $lead = new \zetsoft\models\cpas\CpasLead();
        $lead->contact_name = $user_name;
        $lead->contact_phone = $user_phone;
        $lead->shop_order_id = $shop_order_id;
        $lead->cpas_streams_id = $stream_id;
        $lead->status = \zetsoft\models\cpas\CpasLead::status['new'];
        if ($lead->save(false))
            return true;

        return false;
    }
    #endregion

    #region CpasStatistic

    public function saveShopOrderId($id, $shop_order_id)
    {
        $track = CpasTracker::find()->where(['id' => $id])->one();

        $track->shop_order_id = $shop_order_id;
        $track->save(false);

    }

    public function userPostback($track_id)
    {
        if (!$track_id)
            return false;
        $url = Az::$app->cpas->cpa->urlReplace($track_id);
        if (!$url)
            return null;
        if(!strpos($url, '?'))
            $url = $url.'?';


        $fullUrl = $url.'&track_id='.$track_id;
        $response = $this->client->get($fullUrl);
        return json_decode($response->getbody());

    }
    

    public function createTrack(CpasTracker &$cpasTrack, &$user_agent)
    {
        if (empty($cpasTrack->ip)){
            $ip = Az::$app->request->userIP;
            $cpasTrack->ip = $ip;
        }
        $value = Az::$app->geo->sypex->getInformationByIp($cpasTrack->ip);
        $cpasTrack->country = ZArrayHelper::getValue($value, 'iso');
        $cpasTrack->city = ZArrayHelper::getValue($value, 'city');
        $cpasTrack->region = ZArrayHelper::getValue($this->httpPost(), 'region');
        $cpasTrack->timezone = ZArrayHelper::getValue($value, 'region');
        $cpasTrack->utc = ZArrayHelper::getValue($value, 'timezon');
        $cpasTrack->lat = ZArrayHelper::getValue($value, 'utc');
        $cpasTrack->lon = ZArrayHelper::getValue($value, 'lat');
        $cpasTrack->ad_campaign_id = ZArrayHelper::getValue($this->httpPost(), 'ad_campaign_id');
        $cpasTrack->cost = ZArrayHelper::getValue($this->httpPost(), 'cost');
        $cpasTrack->currency = ZArrayHelper::getValue($this->httpPost(), 'currency');
        $cpasTrack->external_id = ZArrayHelper::getValue($this->httpPost(), 'external_id');
        $cpasTrack->creative_id = ZArrayHelper::getValue($this->httpPost(), 'creative_id');
        $cpasTrack->sub_id_1 = ZArrayHelper::getValue($this->httpPost(), 'sub_id_1');
        $cpasTrack->sub_id_2 = ZArrayHelper::getValue($this->httpPost(), 'sub_id_2');
        $cpasTrack->sub_id_3 = ZArrayHelper::getValue($this->httpPost(), 'sub_id_3');
        $cpasTrack->sub_id_4 = ZArrayHelper::getValue($this->httpPost(), 'sub_id_4');
        $cpasTrack->sub_id_5 = ZArrayHelper::getValue($this->httpPost(), 'sub_id_5');
        $cpasTrack->sub_id_6 = ZArrayHelper::getValue($this->httpPost(), 'sub_id_6');
        if (isset($_SERVER['HTTP_USER_AGENT'])){
            $cpasTrack->device_model = isset($_SERVER['HTTP_USER_AGENT']) ? Az::$app->geo->geodecoder->getModelPhone($_SERVER['HTTP_USER_AGENT'])['device_model'] : '';
            $cpasTrack->device_os = isset($_SERVER['HTTP_USER_AGENT']) ? Az::$app->geo->geodecoder->getOs($_SERVER['HTTP_USER_AGENT']) : '';
            $cpasTrack->browser = isset($_SERVER['HTTP_USER_AGENT']) ? Az::$app->geo->geodecoder->getBrowser($_SERVER['HTTP_USER_AGENT']) : '';
        }
        elseif ($user_agent !== null){
            $cpasTrack->device_model = Az::$app->geo->geodecoder->getModelPhone($user_agent)['device_model'];
            $cpasTrack->device_os = Az::$app->geo->geodecoder->getOs($user_agent);
            $cpasTrack->browser = Az::$app->geo->geodecoder->getBrowser($user_agent);
        }
        $item = CpasStreamItem::findOne($cpasTrack->cpas_stream_item_id);
        $track = CpasTracker::find()->where(['ip' => $cpasTrack->ip])->exists();

        $cpasTrack->user_id = $item->user_id;
        $cpasTrack->status = 'new';
        $cpasTrack->save();

        if (!empty($track))
            $item->click ++;
        else
            $item->uniclick ++;
            
        $item->save();
        
        return $cpasTrack->id;

    }

    public function saveTrackLand(CpasTracker &$cpasTrack)
    {
        $track = CpasTracker::findOne($cpasTrack->id);
        if ($track !== null) {
            $track->contact_phone = $cpasTrack->contact_phone;
            $track->contact_name = $cpasTrack->contact_name;
            $track->amount = $cpasTrack->amount;
            //vdd($track);
            $track->save();
        }
        return $track->id;
    }


    //start|JakhongirKudratov

    public function addNewOrder($track_id, $catalog)
    {
        $track = CpasTracker::findOne($track_id);
        $return = $this->offerUrlReplace($track_id);
        $method = $return['method'];
        $url = $return['url'];
        if(!strpos($url, '?'))
            $url = $url.'?';
        $source_id = $return['source_id'];
        $token = $return['token'];
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ];
        if($method === 'post')
        {
            $response = $this->client->request($this->methods['post'], $url, [
                'headers' => $headers,
                'form_params' => [
                    'offer_id' => $catalog,
                    'user_name' => $track->contact_name,
                    'user_phone' => $track->contact_phone,
                    'amount' => $track->amount,
                    'cpas_track_id' => $track->id,
                    'source_id' => $source_id,
                    'ip_address' => $track->ip,
                    'country' => $track->country,
                    'tz' => $track->timezone,

                ]
            ]);
        }
        else
        {

            $response = $this->client->request($this->methods['get'], $url, [
                'headers' => $headers,
                'query' => [
                    'offer_id' => $catalog,
                    'user_name' => $track->contact_name,
                    'user_phone' => $track->contact_phone,
                    'amount' => $track->amount,
                    'cpas_track_id' => $track->id,
                    'source_id' => $source_id,
                    'ip_address' => $track->ip,
                    'country' => $track->country,
                    'tz' => $track->timezone
                ]
            ]);


        }
            return json_decode($response->getbody());

    }
    
    public function offerUrlReplace($track_id)
    {      //url
        $track = CpasTracker::findOne($track_id);
        $cpas_stream_item_id = $track->cpas_stream_item_id;
        $cpas_stream_item = CpasStreamItem::findOne($cpas_stream_item_id);
        $cpas_stream = CpasStream::findOne($cpas_stream_item->cpas_stream_id);
        $cpas_offer = CpasOffer::findOne($cpas_stream->cpas_offer_id);
        $company = CpasCompany::findOne($cpas_offer->cpas_company_id);
        $token = $company->auth_code;
        $pbback = $company->postback;
        $method = $pbback['method'];
        $url = $pbback['new'];
        $urlReplace = strtr($url, [
            '{status}' => $track->status,
            '{offer_id}' => $cpas_offer->id,
            '{offer_name}' => $cpas_offer->title,
            '{flow_id}' => $cpas_stream->id,
            '{track_id}' => $track->id,
            '{created_at}' => $track->created_at,
            '{ip_address}' => $track->ip,
            '{sub1}' => $cpas_stream->sub1,
            '{sub2}' => $cpas_stream->sub2,
            '{sub3}' => $cpas_stream->sub3,
            '{sub4}' => $cpas_stream->sub4,
            '{sub5}' => $cpas_stream->sub5,
            '{utm_source}' => $cpas_stream->utm_source,
            '{utm_term}' => $cpas_stream->utm_term,
            '{utm_content}' => $cpas_stream->utm_content,
            '{utm_campaign}' => $cpas_stream->utm_company,
            '{source}' => $track->referrer,
            '{country}' => $track->country,
            '{city}' => $track->city,
            '{region}' => $track->region,
            '{timezone}' => $track->timezone,
            '{utc}' => $track->utc,
            '{lat}' => $track->lat,
            '{lon}' => $track->lon,
            '{device_model}' => $track->device_model,
            '{device_os}' => $track->device_os,
            '{browser}' => $track->browser,
            '{revenue}' => $track->revenue,
            '{keyword}' => $track->keyword,
            '{cost}' => $track->cost,
            '{currency}' => $track->currency,
            '{external_id}' => $track->external_id,
            '{creative_id}' => $track->creative_id,
            '{ad_campaign_id}' => $track->ad_campaign_id,


        ]);


        return [
            'url' => $urlReplace,
            'method' => $method,
            'source_id' =>$cpas_offer->source,
            'token' => $token
        ];
    }

    //end|JakhongirKudratov


    public function testRequest()
    {
        $token = 'ZHw4C69VRxvZpE9bRxRabaxXZbSkPGYzvSmTcZpm8Depsz7ERb9WgR5pGNC4qXSP';
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ];
        $url = 'http://market.zetsoft.uz/api/shop/cart/add-order.aspx';
        $response = $this->client->request($this->methods['get'], $url, [
            'headers' => $headers,
            'query' => [
                'offer_id' => 12365,
                'user_name' => 'Botir',
                'user_phone' => '9989098778',
                'amount' => 1,
                'cpas_track_id' => '',
                'source_id' => 247,
                'ip_address' => '',
                'country' => '',
                'tz' => ''
            ]
        ]);
        return json_decode($response->getBody());
    }


    #region

    public function postbackTlight()
    {
        $api_key = 'SrToc1s2Pj5EsylUMVERxrYguYgcAAEV8KrlJe8VTUjZUT';
        $apiUrl = 'http://api.cpa.tl/api/lead/send';

        $response = $this->client->request($this->methods['post'], $apiUrl, [
            'form_params' => [
                 'key' => $api_key,
            ]
        ]);
        return json_decode($response->getbody());

    }
    public function postbackTlightBalance()
    {
        $api_key = 'SrToc1s2Pj5EsylUMVERxrYguYgcAAEV8KrlJe8VTUjZUT';
        $apiUrl = 'http://api.cpa.tl/api/user/balance';
        $offer_id = 121;
        $stream_hid = '';
        $ip = '';
        $source = '';
        $data_post = [];
        $response = $this->client->request($this->methods['get'], $apiUrl, [
            'query' => [
                 'key' => $api_key,
                'id' => microtime(true), // тут лучше вставить значение, по которому вы сможете идентифицировать свой лид; можно оставить microtime если у вас нет своей crm
                'offer_id' => $offer_id,    // id offer uladagi
                'stream_hid' => $stream_hid, // id stream uladagi
                'name' => $data_post['name'],  // name
                'phone' => $data_post['phone'],  // phone
                'comments' => $data_post['comments'],
                'country' => $data_post['country'], // формат ISO 3166-1 Alpha-2 - https://ru.wikipedia.org/wiki/ISO_3166-1
                'address' => $data_post['address'],  // ?
                'tz' => $data_post['timezone_int'], // очень желательно получать его с ленда, но если никак лучше оставить пустым или 3 (таймзона мск)
                'web_id' => '', // userni idsi
                'ip_address' => $ip,     //ip address
                'user_agent' => $source,
            ]
        ]);
        //vdd($response);
        return json_decode($response->getbody());

    }

    public function postbackTlightFeed()
    {
        $api_key = 'SrToc1s2Pj5EsylUMVERxrYguYgcAAEV8KrlJe8VTUjZUT';
        $apiUrl = 'http://api.cpa.tl/api/lead/feed';

        $response = $this->client->request($this->methods['get'], $apiUrl, [
            //'headers' => $headers,
            'query' => [
                 'key' => $api_key,
                 //'id' => ['1'],
                 //'date' => ['2020-07-01', '2020-07-15'],


            ]
        ]);
        //vdd($response);
        return json_decode($response->getbody());

    }

    #endregion



    #region testCrylic


    public function testCrylic(){

        $response = $this->client->request($this->methods['get'], 'http://arbit.zetsoft.uz/api/cpas/lead/set-lead.aspx', [
            'query' => [
                'offer_id' => 'мишршгавми',
                'user_name' => 'мывпм',
                'user_phone' => 'имвами',
                'amount' => 'мва',
                'cpas_track_id' => 'мва',
                'source_id' => 'ваааааааа',
                'ip_address' => 'ауваааы',
                'country' => 'ываппппкае',
                'tz' => 'упекпощшкеощ',

            ]
        ]);

        return json_decode($response->getbody());

    }


    #endregion
}

