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
require Root . '/vendors/netter/ALL/vendor/autoload.php';

/**
 *
 * @author Shahzod Gulomqodirov
 *
 * @license Jakhongir Kudratov
 */

use cogpowered\FineDiff\Render\Html;
use GuzzleHttp\Client;
use React\EventLoop\Factory;
use zetsoft\dbitem\core\CpasTrackerItem;
use zetsoft\models\cpas\CpasCompany;
use zetsoft\models\cpas\CpasLand;
use zetsoft\models\cpas\CpasOffer;
use zetsoft\models\cpas\CpasStreamItem;
use zetsoft\models\cpas\CpasTracker;
use zetsoft\models\cpas\CpasStream;
use zetsoft\models\place\PlaceCountry;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\user\User;
use zetsoft\system\actives\ZActiveRecord;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZStringHelper;
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
        $this->client = new Client(['headers' => ['content-type' => 'application/x-www-form-urlencoded', 'Accept' => 'application/json', 'charset' => 'utf-8']]);

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
        if ($model->cpas_track) {
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

                if ($this->status_cpa) {
                    $token = 'qvhFgB9YC4UUHfZNG6Pd8WZ6k7H6waw5Vg2UeAXJgKe44fFxSwzStCMQ3jmv2gtz';
                    $this->requestStatus($token, $model->cpas_track);
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

    public function userPostback($track_id, $status = 'new')
    {
        if (!$track_id)
            return false;
        $url = Az::$app->cpas->cpa->urlReplace($track_id, $status);

        $cpasTrack = CpasTracker::findOne($track_id);
        if ($cpasTrack === null) {
            return null;
        }
        $user_id = CpasTracker::findOne($track_id)->user_id;
        $user = User::findOne($user_id)->email;
        $streamItem = CpasStreamItem::findOne($cpasTrack->cpas_stream_item_id);
        if ($this->emptyOrNullable($url)) {
            $this->logUserPostbacks($url, "элемент потока не найден. ид заказа: {$cpasTrack->shop_order_id} ", 404, $user);
            return null;
        }
        $stream = CpasStream::findOne($streamItem->cpas_stream_id);

        if ($this->emptyOrNullable($url)) {
            $this->logUserPostbacks($url, "urlNotGiven. ид заказа: {$cpasTrack->shop_order_id}", 404, $user);
            return null;
        }
        if (!strpos($url, '?'))
            $url = $url . '?';

        if (ZStringHelper::find($url, 'http')) {
            $fullUrl = $url . '&track_id=' . $track_id;
            /** @var Client $response */
            $response = $this->client->get($fullUrl);
            $body = json_decode($response->getbody());
            $body = $body . PHP_EOL . 'ид потока; ' . $stream->id . PHP_EOL . "Имя потока:" . $stream->name;
            $this->logUserPostbacks($url, $body, $response->getStatusCode(), $user);
            return $body;
        }
    }

    public function companyPostback($track_id)
    {

    }


    public function createTrack(CpasTracker &$cpasTrack, &$user_agent)
    {
        if (empty($cpasTrack->ip)) {
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
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $cpasTrack->device_model = isset($_SERVER['HTTP_USER_AGENT']) ? Az::$app->geo->geodecoder->getModelPhone($_SERVER['HTTP_USER_AGENT'])['device_model'] : '';
            $cpasTrack->device_os = isset($_SERVER['HTTP_USER_AGENT']) ? Az::$app->geo->geodecoder->getOs($_SERVER['HTTP_USER_AGENT']) : '';
            $cpasTrack->browser = isset($_SERVER['HTTP_USER_AGENT']) ? Az::$app->geo->geodecoder->getBrowser($_SERVER['HTTP_USER_AGENT']) : '';
        } elseif ($user_agent !== null) {
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
            $item->click++;
        else
            $item->uniclick++;

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
            $track->cpas_offer_id = $cpasTrack->cpas_offer_id;
            //vdd($track);
            $track->save();
        }
        return $track->id;
    }


    //start|JakhongirKudratov

    public function addNewOrder($track_id, $catalog = '')
    {
        $track = CpasTracker::findOne($track_id);
        $cpas_stream_item_id = $track->cpas_stream_item_id;
        $cpas_stream_item = CpasStreamItem::findOne($cpas_stream_item_id);

        if ($cpas_stream_item === null) {
            $track->shop_order_id = 'Элемент потока не найден';
            $track->status = 'trash';
            return $track->save();
        }

        $cpas_stream = CpasStream::findOne($cpas_stream_item->cpas_stream_id);
        $cpas_offer = CpasOffer::findOne($cpas_stream->cpas_offer_id);
        $catalog = $cpas_offer->catalog;
//        vdd($cpas_offer->attributes);
        $company = CpasCompany::findOne($cpas_offer->cpas_company_id);
        if ($company) {
            $token = $company->auth_code;
            $name = urldecode($track->contact_name);
            $tel = $track->contact_phone;
            switch ($company->id) {
                case 2:
                    $url = "http://websoap.t9.uz/service/index.php";
                    if (preg_match('/[А-Яа-яЁё]/u', $name)) {
                        $name = transliterator_transliterate('Russian-Latin/BGN', $name);
                    }
                    $param = "?tovarid={$catalog}&customer={$name}&telephone={$tel}&token={$token}";
                    $url = $url . $param;
                    $url = str_replace('\n', '', $url);
                    $url = trim($url);

                    $response = $this->client->request($this->methods['get'], $url, ['connect_timeout' => 0]);
                    $order_id = json_decode($response->getbody());

                    vd($url);
                    vd($order_id);
                    $track->cpas_company_id = $company->id;
                    $track->shop_order_id = $order_id;
                    $track->status = CpasTracker::status['new'];
                    return $track->save();
                    break;
                case 3:
                    $url = "http://api.cpa.tl/api/lead/send";
                    $data = array(
                        'key' => $token,
                        'id' => microtime(true), // тут лучше вставить значение, по которому вы сможете идентифицировать свой лид; можно оставить microtime если у вас нет своей crm
                        'offer_id' => $catalog,
                        'name' => $name,
                        'phone' => $tel,
                        'country' => $track->country, // формат ISO 3166-1 Alpha-2 - https://ru.wikipedia.org/wiki/ISO_3166-1
                        'web_id' => $company->id,
                        'ip_address' => $track->ip,
                        'user_agent' => $track->browser,
                    );

                    $options = array(
                        'http' => array(
                            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                            'method' => 'POST',
                            'content' => http_build_query($data),
                            'ignore_errors' => true,
                        )
                    );

                    $context = stream_context_create($options);
                    $result = file_get_contents($url, false, $context);

                    $obj = json_decode($result);

                    if (null === $obj) {
                        // Ошибка в полученном ответе
                        $track->shop_order_id = "Invalid JSON";
                        $track->status = CpasTracker::status['trash'];

                    } else if (!empty($obj->errmsg)) {
                        // Ошибка в отправленном запросе
                        $track->shop_order_id = $obj->errmsg;
                        $track->status = CpasTracker::status['trash'];
                    } else {
                        $track->shop_order_id = $obj->id;
                    }
                    $track->cpas_company_id = $company->id;
                    $track->save();
                    return $track->shop_order_id;
                    break;
                case 4:
                    $url = 'https://api.adbees.com/lead/add';
                    $data = [
                        'api_key' => $token,
                        'phone' => $tel,
                        'fio' => $name,
                        'ip' => $track->ip,
                        'mark' => $catalog,
                    ];
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    $data = curl_exec($ch);

                    $result = json_decode($data);
                    if (is_object($result) && !isset($result->error)) {
                        if ($result->success) {
                            $track->shop_order_id = $result->transaction_id;
                            $track->cpas_company_id = $company->id;
                            $track->save();
                            return true;
                        }
                    } else {
                        $track->shop_order_id = $result;
                        $track->cpas_company_id = $company->id;
                        $track->save();
                        return false;
                    }
                    break;
            }
        }
        return false;
    }

    public function checkStatusCrm()
    {

        $loop = Factory::create();

        $loop->addPeriodicTimer(300, function () {
            $model = CpasTracker::find()->where(['status' => CpasTracker::status['new']])->andWhere(['cpas_company_id' => 2])->andWhere(['!=', 'shop_order_id', ''])->andWhere(['not', ['shop_order_id' => null]])->all();
            foreach ($model as $item) {
                /** @var CpasTracker $item */

                $guid = $item->shop_order_id;
                $url = "http://websoap.t9.uz/service/getstatus.php?guid=$guid&token=29ea492504d9efe6461511a3b02679ca4eb1da5d";
                $response = $this->client->request($this->methods['get'], $url);
                $result = json_decode($response->getbody());
                if ($this->emptyOrNullable($result)) {
                    vd($url);
                }
                Az::debug($result);
                echo '--------------------------' . PHP_EOL;
                if (!$this->emptyOrNullable($result)) {
                    if (isset($result[0])) {
                        switch ($result[0]->stausid) {
                            case 2: //Approved
                            case 11: //Approved
                            case 12: //Approved
                            case 13: //Approved
                                $item->status = CpasTracker::status['accept'];
                                break;
                            case 0: //New
                            case 1: //Telephone call
                                $item->status = CpasTracker::status['new'];
                                break;
                            case 3: // Renouncement
                            case 4:
                                $item->status = CpasTracker::status['cancel'];
                                break;
                            default: //OtherStatuses
                                $item->status = CpasTracker::status['trash'];
                        }
                        if ($item->status !== CpasTracker::status['new']) {
                            Az::$app->cpas->cpasLead->userPostback($item->id, CpasTracker::status[$item->status]);
                        }
                        $item->save();
                    }
                }
            }

            $errors = CpasTracker::find()->where(['shop_order_id' => null])->orWhere(['shop_order_id' => ''])->all();

            /** @var CpasTracker $error */
            foreach ($errors as $error) {
                if ($error->cpas_offer_id) {
                    $this->addNewOrder($error->id);
                } else {
                    if ($error->cpas_stream_item_id) {
                        $cpasStreamItem = CpasStreamItem::findOne($error->cpas_stream_item_id);
                        if ($cpasStreamItem === null) {
                            $error->shop_order_id = 'Element of stream does not exists';
                            $error->status = CpasTracker::status['trash'];
                            $error->save();
                        } else {
                            $cpasStream = CpasStream::findOne($cpasStreamItem->cpas_stream_id);
                            if ($cpasStream === null) {
                                $error->shop_order_id = 'Stream does not exists';
                                $error->status = CpasTracker::status['trash'];
                                $error->save();
                            } else {
                                $cpasOffer = CpasOffer::findOne($cpasStream->cpas_offer_id);
                                if ($cpasOffer === null) {
                                    $error->shop_order_id = 'Offer does not exists';
                                    $error->status = CpasTracker::status['trash'];
                                    $error->save();
                                } else {
                                    $error->cpas_offer_id = $cpasStream->cpas_offer_id;
                                    $error->save();
                                    $this->addNewOrder($error->id);
                                }
                            }
                        }
                    } else {
                        $error->shop_order_id = 'Element of stream not given';
                        $error->status = CpasTracker::status['trash'];
                        $error->save();
                    }
                }

            }
        });

        $loop->run();
    }

    public function offerUrlReplace($track_id)
    {      //url
//        $track = CpasTracker::findOne($track_id);
//        $cpas_stream_item_id = $track->cpas_stream_item_id;
//        $cpas_stream_item = CpasStreamItem::findOne($cpas_stream_item_id);
//        $cpas_stream = CpasStream::findOne($cpas_stream_item->cpas_stream_id);
//        $cpas_offer = CpasOffer::findOne($cpas_stream->cpas_offer_id);
//        $company = CpasCompany::findOne($cpas_offer->cpas_company_id);
//        $token = $company->auth_code;
//        $pbback = $company->postback;
//        $method = $pbback['method'];
//        $url = $pbback['new'];
//        $urlReplace = strtr($url, [
//            '{status}' => $track->status,
//            '{token}' => $token,
//            '{offer_id}' => $cpas_offer->id,
//            '{offer_name}' => $cpas_offer->title,
//            '{flow_id}' => $cpas_stream->id,
//            '{track_id}' => $track->id,
//            '{customer}' => $track->contact_name,
//            '{phone}' => $track->contact_phone,
//            '{created_at}' => $track->created_at,
//            '{ip_address}' => $track->ip,
//            '{sub1}' => $cpas_stream->sub1,
//            '{sub2}' => $cpas_stream->sub2,
//            '{sub3}' => $cpas_stream->sub3,
//            '{sub4}' => $cpas_stream->sub4,
//            '{sub5}' => $cpas_stream->sub5,
//            '{utm_source}' => $cpas_stream->utm_source,
//            '{utm_term}' => $cpas_stream->utm_term,
//            '{utm_content}' => $cpas_stream->utm_content,
//            '{utm_campaign}' => $cpas_stream->utm_company,
//            '{source}' => $track->referrer,
//            '{country}' => $track->country,
//            '{city}' => $track->city,
//            '{region}' => $track->region,
//            '{timezone}' => $track->timezone,
//            '{utc}' => $track->utc,
//            '{lat}' => $track->lat,
//            '{lon}' => $track->lon,
//            '{device_model}' => $track->device_model,
//            '{device_os}' => $track->device_os,
//            '{browser}' => $track->browser,
//            '{revenue}' => $track->revenue,
//            '{keyword}' => $track->keyword,
//            '{cost}' => $track->cost,
//            '{currency}' => $track->currency,
//            '{external_id}' => $track->external_id,
//            '{creative_id}' => $track->creative_id,
//            '{ad_campaign_id}' => $track->ad_campaign_id,
//        ]);
//
//
//        return [
//            'url' => $urlReplace,
//            'method' => $method,
//            'source_id' =>$cpas_offer->source,
//            'token' => $token
//        ];
    }

    //end|JakhongirKudratov


    public function testRequest()
    {
        $token = 'ZHw4C69VRxvZpE9bRxRabaxXZbSkPGYzvSmTcZpm8Depsz7ERb9WgR5pGNC4qXSP';
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
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


    public function testCrylic()
    {

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

    public function logUserPostbacks($url, $response, $statusCode, $user)
    {
        //Something to write to txt log
        $log = "User: " . $user . ' - ' . date("H:i:s") . PHP_EOL .
            "Url: $url" . PHP_EOL .
            "Response: $response" . PHP_EOL .
            "Code: $statusCode" . PHP_EOL .
            "User: $user" . PHP_EOL .
            "-------------------------" . PHP_EOL;
//Save string to log, use FILE_APPEND to append.
        file_put_contents(Root . '/storing/userPostbacks/log_' . date("j.n.Y") . '.log', $log, FILE_APPEND);
    }

    #endregion
}

