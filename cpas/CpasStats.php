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
 *
 * @author Jakhongir Kudratov
 */

namespace zetsoft\service\cpas;


use DateTime;
use yii\rbac\DbManager;
use zetsoft\former\cpas\CpasStatsForm;
use zetsoft\former\cpas\CpasTrackForm;
use zetsoft\former\stat\StatHistoryForm;
use zetsoft\models\cpas\CpasLand;
use zetsoft\models\cpas\CpasOffer;
use zetsoft\models\cpas\CpasOfferItem;
use zetsoft\models\cpas\CpasStream;
use zetsoft\models\cpas\CpasStreamItem;
use zetsoft\models\cpas\CpasTracker;
use zetsoft\models\cpas\CpasTeaser;
use zetsoft\models\place\PlaceCountry;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\user\User;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;
use function Dash\Curry\all;

class CpasStats extends ZFrame
{

    public $stream_items;

    public $cpas_traks;
    public $streams;
    public $sub;

    public function init()
    {
        $this->stream_items = collect(CpasStreamItem::find()->all());
        $this->streams = collect(CpasStream::find()->all());
        $query = CpasTracker::find();
        if ($this->httpGet('sub_id_1')) {
            $query = $query->andWhere('sub_id_1=:param', [':param' => $this->httpGet('sub_id_1')]);
        }
        if ($this->httpGet('sub_id_2')) {
            $query = $query->andWhere('sub_id_2=:param', [':param' => $this->httpGet('sub_id_2')]);
        }
        if ($this->httpGet('sub_id_3')) {
            $query = $query->andWhere('sub_id_3=:param', [':param' => $this->httpGet('sub_id_3')]);
        }
        if ($this->httpGet('sub_id_4')) {
            $query = $query->andWhere('sub_id_4=:param', [':param' => $this->httpGet('sub_id_4')]);
        }
        if ($this->httpGet('sub_id_5')) {
            $query = $query->andWhere('sub_id_5=:param', [':param' => $this->httpGet('sub_id_5')]);
        }
        if ($this->httpGet('sub_id_6')) {
            $query = $query->andWhere('sub_id_6=:param', [':param' => $this->httpGet('sub_id_6')]);
        }
        $this->cpas_traks = collect($query->all());
    }


    public
    function create()
    {
        $tizer_tracker_id = $this->httpGet('trackId');
        $source = $this->httpGet('source');
        $ip = Az::$app->request->userIP;
        $value = Az::$app->geo->sypex->getInformationByIp($ip);

        $cpasTracker = CpasTeaser::findOne($tizer_tracker_id);
        $cpasTrackerStats = new CpasTracker();

        if ($cpasTracker === null) {
            die('Something went wrong');
        }
        $cpasTrackerStats->tizer_tracker_id = $tizer_tracker_id;
        $cpasTrackerStats->source = $source;
        $cpasTrackerStats->ip = $ip;
        $cpasTrackerStats->lat = $value['lat'];
        $cpasTrackerStats->lon = $value['lon'];
        $cpasTrackerStats->region = $value['region'];
        $cpasTrackerStats->city = $value['city'];
        $cpasTrackerStats->country = $value['country'];
        $cpasTrackerStats->utc = $value['utc'];
        $cpasTrackerStats->device_model = isset($_SERVER['HTTP_USER_AGENT']) ? Az::$app->geo->geodecoder->getModelPhone($_SERVER['HTTP_USER_AGENT'])['device_model'] : '';
        $cpasTrackerStats->device_os = isset($_SERVER['HTTP_USER_AGENT']) ? Az::$app->geo->geodecoder->getOs($_SERVER['HTTP_USER_AGENT']) : '';
        $cpasTrackerStats->browser = isset($_SERVER['HTTP_USER_AGENT']) ? Az::$app->geo->geodecoder->getBrowser($_SERVER['HTTP_USER_AGENT']) : '';
        $cpasTrackerStats->save();

        return $this->urlRedirect($cpasTracker->redirect_url . "$cpasTrackerStats->id", false);
    }


#region CreateStats
    public
    function createStats(bool $teaser = false, &$cpas_stream_item_id = null, &$ip = null, &$user_agent = null)
    {

        /*vd(Az::$app->request->userIP);
        vdd(Az::$app->getRequest()->getUserIP());*/
        //vdd($ip);
        if ($ip === null)
            $ip = Az::$app->request->userIP;

        //vdd($ip);

        if ($cpas_stream_item_id === null)
            $cpas_stream_item_id = ZArrayHelper::getValue($this->httpGet(), 'cpas_stream_item_id');


        $item = CpasStreamItem::findOne($cpas_stream_item_id);

        $track = CpasTracker::find()->where(['ip' => $ip])->exists();

        if (!empty($track))
            $item->click++;
        else
            $item->uniclick++;

        $item->save();

        if (!$source = ZArrayHelper::getValue($this->httpGet(), 'source')) {
            $source = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        }
        $value = Az::$app->geo->sypex->getInformationByIp($ip);

        $cpasTrackerStats = new CpasTracker();
        $cpasTrackerStats->cpas_stream_item_id = $cpas_stream_item_id;
        $cpasTrackerStats->user_id = $item->user_id;
        if ($teaser) {
            $cpasTrackerStats->ad_campaign_id = ZArrayHelper::getValue($this->httpGet(), 'ad_campaign_id');
            $cpasTrackerStats->cost = ZArrayHelper::getValue($this->httpGet(), 'cost');
            $cpasTrackerStats->currency = ZArrayHelper::getValue($this->httpGet(), 'currency');
            $cpasTrackerStats->external_id = ZArrayHelper::getValue($this->httpGet(), 'external_id');
            $cpasTrackerStats->creative_id = ZArrayHelper::getValue($this->httpGet(), 'creative_id');

            $cpasTrackerStats->sub_id_1 = ZArrayHelper::getValue($this->httpGet(), 'sub_id_1');
            $cpasTrackerStats->sub_id_2 = ZArrayHelper::getValue($this->httpGet(), 'sub_id_2');
            $cpasTrackerStats->sub_id_3 = ZArrayHelper::getValue($this->httpGet(), 'sub_id_3');
            $cpasTrackerStats->sub_id_4 = ZArrayHelper::getValue($this->httpGet(), 'sub_id_4');
            $cpasTrackerStats->sub_id_5 = ZArrayHelper::getValue($this->httpGet(), 'sub_id_5');
            $cpasTrackerStats->sub_id_6 = ZArrayHelper::getValue($this->httpGet(), 'sub_id_6');
        }

        $cpasTrackerStats->referrer = $source;
        $cpasTrackerStats->country = ZArrayHelper::getValue($value, 'iso');
        $cpasTrackerStats->city = ZArrayHelper::getValue($value, 'city');
        $cpasTrackerStats->region = ZArrayHelper::getValue($value, 'region');
        $cpasTrackerStats->timezone = ZArrayHelper::getValue($value, 'timezon');
        $cpasTrackerStats->utc = ZArrayHelper::getValue($value, 'utc');
        $cpasTrackerStats->lat = ZArrayHelper::getValue($value, 'lat');
        $cpasTrackerStats->lon = ZArrayHelper::getValue($value, 'lon');
        $cpasTrackerStats->ip = $ip;
        if ($user_agent !== null) {
            $cpasTrackerStats->device_model = Az::$app->geo->geodecoder->getModelPhone($user_agent)['device_model'];
            $cpasTrackerStats->device_os = Az::$app->geo->geodecoder->getOs($user_agent);
            $cpasTrackerStats->browser = Az::$app->geo->geodecoder->getBrowser($user_agent);
        } elseif (isset($_SERVER['HTTP_USER_AGENT'])) {
            $cpasTrackerStats->device_model = isset($_SERVER['HTTP_USER_AGENT']) ? Az::$app->geo->geodecoder->getModelPhone($_SERVER['HTTP_USER_AGENT'])['device_model'] : '';
            $cpasTrackerStats->device_os = isset($_SERVER['HTTP_USER_AGENT']) ? Az::$app->geo->geodecoder->getOs($_SERVER['HTTP_USER_AGENT']) : '';
            $cpasTrackerStats->browser = isset($_SERVER['HTTP_USER_AGENT']) ? Az::$app->geo->geodecoder->getBrowser($_SERVER['HTTP_USER_AGENT']) : '';
        }
        $cpasTrackerStats->status = 'new';
        $cpasTrackerStats->save();


        return $cpasTrackerStats->id;
    }

##endregion


#region ShopOrder Track
    public
    function createCpasTrackForm()
    {
        $return = [];
        $cpasTrack = CpasTeaser::find()->all();
        $i = 0;
        foreach ($cpasTrack as $item) {
            $stats = collect(CpasTracker::findAll(['cpas_teaser_id' => $item->id]));

            $form = new CpasTrackForm();
            $form->id = $item->id;
            $form->name = $item->title;
            $form->url = $item->created_url;
            $form->click = $stats->count();
            $form->unique_click = $this->getUniqueClick($stats);
            $form->approve = $this->getApprove($stats) . '%';
            $form->await = $this->getAwait($stats);
            $form->confirmed = $this->getByStats($stats, ShopOrder::status_callcenter['approved']);
            $form->trash = $this->getByStats($stats, ShopOrder::status_callcenter['incorrect']);
            $form->canceled = $this->getByStats($stats, ShopOrder::status_callcenter['cancel']);
            $return[] = $form;
        }
        return $return;
    }

    protected
    function getByStats($stats, $status)
    {
        $count = 0;
        if ($stats->isEmpty())
            return 0;
        foreach ($stats as $stat) {
            if (ShopOrder::find()->where(['id' => $stat->shop_order_id])->andWhere(['status_accept' => $status])->exists()) {
                $count++;
            }
        }
        return $count;
    }

    protected
    function getAwait($stats)
    {
        $count = 0;
        if (empty($stats))
            return 0;

        foreach ($stats as $stat) {
            if (ShopOrder::find()->where(['id' => $stat->shop_order_id])->andWhere(['or',
                ['status_callcenter' => ShopOrder::status_callcenter['new']],
                ['status_callcenter' => ShopOrder::status_callcenter['ring']],
                ['status_callcenter' => ShopOrder::status_callcenter['autodial']]
            ])->exists()) {
                $count++;
            }
        }

        return $count;
    }

    protected
    function getUniqueClick($stats)
    {
        $array = [];
        if ($stats->isEmpty())
            return 0;
        foreach ($stats as $stat) {
            if (!ZArrayHelper::isIn($stat->ip, $array)) {
                $array[] = $stat->ip;
            }
        }
        return count($array);
    }

    protected
    function getApprove($stats)
    {
        $approve = 0;
        if ($stats->isEmpty())
            return 0;
        foreach ($stats as $stat) {
            if (ShopOrder::find()->where(['id' => $stat->shop_order_id])->andWhere(['status_accept' => ShopOrder::status_accept['completed']])->exists()) {
                $approve++;
            }
        }
        if ($stats->count() === 0 || $approve === 0) {
            return 0;
        }
        return (int)($stats->count() / $approve) * 100;
    }

    protected
    function getCountByStatus($stats)
    {
        $count = 0;
        if ($stats->isEmpty())
            return 0;
        foreach ($stats as $stat) {
            if (ShopOrder::find()->where(['id' => $stat->shop_order_id])->andWhere(['status_accept' => ShopOrder::status_callcenter['completed']])->exists()) {
                $count++;
            }
        }
        return $count;
    }

    public
    function getStatsByTime()
    {
        $data = [];
        foreach ($data as $d) {
            $item = new StatHistoryForm();
            $item->name = 'ftuhihg';


            $data[] = item;
        }

        return $data;
    }

#endregion

    public
    function generateStats()
    {
        $return = [];
        $cpasTrack = CpasTeaser::find()->all();
        $i = 0;
        foreach ($cpasTrack as $item) {
            $stats = collect(CpasTracker::findAll(['cpas_teaser_id' => $item->id]));

            $form = new CpasTrackForm();
            $form->id = $item->id;
            $form->name = $item->title;
            $form->url = $item->created_url;
            $form->click = $stats->count();
            $form->unique_click = $this->getUniqueClick($stats);
            $form->approve = $this->getApprove($stats) . '%';
            $form->await = $this->getAwait($stats);
            $form->confirmed = $this->getByStats($stats, ShopOrder::status_callcenter['approved']);
            $form->trash = $this->getByStats($stats, ShopOrder::status_callcenter['incorrect']);
            $form->canceled = $this->getByStats($stats, ShopOrder::status_callcenter['cancel']);
            $return[] = $form;
        }
        return $return;
    }


//start|JakhongirKudratov

#region Stats
    public
    function generateAdminStats($users)
    {
        $data = [];
        foreach ($users as $user) {
            $items = $this->stream_items->where(
                'user_id', $user->id
            );
            if (empty($items->toArray()))
                continue;
            $items_ids = ZArrayHelper::getColumn($items, 'id');
            $traks = $this->cpas_traks->whereIn(
                'cpas_stream_item_id', $items_ids
            );
            $datas = $this->generateForm($traks, 'user', $user->email);
            if ($datas !== null)
                $data [] = $datas;
        }
        return $data;
    }

    public
    function generateClientStats($user_id, $filter = [])
    {
        if (!empty($filter)) {
            if (!empty($filter['selectedBtnValue']))
                switch ($filter['selectedBtnValue']) {
                    case 'time':
                        return $filter['startdate'] ? $this->getStatsAllByDay($user_id, $filter['startdate'], $filter['enddate']) : $this->getStatsAllByDay($user_id);
                        break;
                    case 'offer':
                        return $filter['startdate'] ? $this->getStatsByOffer($user_id, $filter['startdate'], $filter['enddate']) : $this->getStatsByOffer($user_id);
                        break;
                    case 'stream':
                        return $filter['startdate'] ? $this->getStatsByStreams($user_id, $filter['startdate'], $filter['enddate']) : $this->getStatsByStreams($user_id);
                        break;
                    case 'lands':
                        return $filter['startdate'] ? $this->getStatsByLands($user_id, 'cpas_land_id', $filter['startdate'], $filter['enddate']) : $this->getStatsByLands($user_id, 'cpas_land_id');
                        break;
                    case 'prelands':
                        return $filter['startdate'] ? $this->getStatsByLands($user_id, 'cpas_trans', $filter['startdate'], $filter['enddate']) : $this->getStatsByLands($user_id, 'cpas_trans');
                        break;
                    case 'preland_with_form':
                        return $filter['startdate'] ? $this->getStatsByLands($user_id, 'cpas_trans_form', $filter['startdate'], $filter['enddate']) : $this->getStatsByLands($user_id, 'cpas_trans_form');
                        break;
                    case 'country':
                        return $filter['startdate'] ? $this->getStatsByCountries($user_id, $filter['startdate'], $filter['enddate']) : $this->getStatsByCountries($user_id);
                        break;
                    case 'device':
                        return $filter['startdate'] ? $this->getStatsByDevices($user_id, $filter['startdate'], $filter['enddate']) : $this->getStatsByDevices($user_id);
                        break;
                    case 'sub':
                        return $this->getStatsBySubs($user_id);
                        break;
                    default:
                        return $filter['startdate'] ? $this->getStatsAllByDay($user_id, $filter['startdate'], $filter['enddate']) : $this->getStatsAllByDay($user_id);
                        break;
                }
            return ZArrayHelper::getValue($filter, 'startdate') ? $this->getStatsAllByDay($user_id, $filter['startdate'], $filter['enddate']) : $this->getStatsAllByDay($user_id);
        }
        return $this->getStatsAllByDay($user_id);
    }

#endregion
#region getStatsAllByDay
    public function getStatsAllByDay($user_id, $stratTime = null, $endTime = null)
    {
        $streams = CpasStream::find()
            ->where([
                'user_id' => $user_id
            ])
            ->all();
        $stream_ids = ZArrayHelper::getColumn($streams, 'id');
        $stream_items = CpasStreamItem::find()
            ->where([
                'cpas_stream_id' => $stream_ids
            ])
            ->all();
        $ids = ZArrayHelper::getColumn($stream_items, 'id');
        $cpasTrackerQuery = CpasTracker::find()
            ->where([
                'cpas_stream_item_id' => $ids
            ])
            ->orderBy('created_at asc');

        $cpasTracker = collect($cpasTrackerQuery->all());

        if (!$cpasTracker->count())
            return [];

        $first = $cpasTracker->first();
        $lastday = date('Y-m-d', strtotime($first->created_at . ' -1 day'));
        $day = date('Y-m-d');
        if ($endTime)
            $day = $endTime;
        if ($stratTime)
            $lastday = $stratTime;
        $toDay = 0;
        $data = [];
        $days = $this->daysBetween($lastday, $day);
        if ($lastday === $day)
            $days = 1;
        for ($i = 1; $i <= $days; $i++) {
            $current_day = date('Y-m-d', strtotime($day . ' -' . $toDay . ' day'));
            $orderByDay = $cpasTracker->where('created_at', '<=', $current_day . ' 23:59:59')->where('created_at', '>=', $current_day . ' 00:00:00');
            $toDay++;
            if (empty($orderByDay->toArray()))
                continue;
            $datas = $this->generateForm($orderByDay, 'day', $current_day);
            if ($datas !== null)
                $data [] = $datas;
        }
        return $data;
    }

#endregion
    public function getStatsBySubs($user_id)
    {

        $cpasTrackerQuery = CpasTracker::find()
            ->where([
                'user_id' => $user_id
            ])
            ->orderBy('created_at asc');

        $startDate = $this->httpGet('startdate');
        $endDate = $this->httpGet('enddate');
        if($startDate){
            $cpasTrackerQuery = $cpasTrackerQuery->andWhere(['between', 'created_at', $startDate. ' 00:00:00', $endDate .' 23:59:59' ]);
        }
        if($this->httpGet('selectedSub')){
            $val = $this->httpGet('selectedSub');
            $cpasTracker = collect($cpasTrackerQuery->andWhere("$val IS NOT NULL")->asArray()->all())->groupBy($val);
        }else{
            $cpasTracker = collect($cpasTrackerQuery->andWhere("sub_id_1 IS NOT NULL")->asArray()->all())->groupBy('sub_id_1');
        }

        $data = [];
        if (!$this->emptyOrNullable($cpasTracker)) {
            foreach ($cpasTracker as $key => $item) {
                $item = collect($item);
                $orders = $item->where('shop_order_id', '!=', null);
                $accepts = $orders->where('status', 'accept');
                $form = new CpasTrackForm();
                $form->approve = '0%';
                $form->sub = $key;
                $form->click = count($item->groupBy('ip'));
                $form->unique_click = count($item);
                $form->all = count($orders);
                $form->confirmed = count($orders->where('status', 'accept'));
                $form->trash = count($orders->where('status', 'trash'));
                $form->canceled = count($orders->where('status', 'cancel'));
                $form->await = count($orders->where('status', 'new'));
                $form->cr = round(($form->all / $form->click * 100), 2) . ' %';
                $form->Valid = abs($form->all - $form->trash);
                $form->earned_money = Az::$app->cpas->cpasStats->generateEarnedMoney(ZArrayHelper::getColumn($accepts, 'id'));
                $new_valid = $form->Valid - $form->await;
                if ($new_valid !== 0) {
                    $form->approve = round(($form->confirmed / $new_valid) * 100, 2) . ' %';
                }
                $form->EPC = round(($form->earned_money * $form->confirmed / $form->click), 2);
                $data[] = $form;
            }
        }
        return $data;
    }

#region daysBetween

    public function daysBetween($stratTime, $enddata)
    {
        return (int)date_diff(
            date_create($stratTime),
            date_create($enddata)
        )->format('%a');
    }

#endregion
#region getStatsByOffer
    public function getStatsByOffer($user_id, $startTime = null, $endTime = null)
    {
        $streams = collect(CpasStream::find()
            ->where([
                'user_id' => $user_id,

            ])->all());
        $offer_ids = ZArrayHelper::map($streams->toArray(), 'id', 'cpas_offer_id');
        $offers = CpasOffer::find()
            ->where([
                'id' => $offer_ids
            ])
            ->all();
        $data = [];
        if ($startTime)
            $startTime = date('Y-m-d', strtotime($startTime . ' -1 day'));
        foreach ($offers as $offer) {
            $streamByOffers = $streams->where(
                'cpas_offer_id', $offer->id
            );
            $all = 0;
            $trash = 0;
            $new = 0;
            $cancel = 0;
            $accept = 0;
            $earned_money = 0;
            $allIds = [];
            foreach ($streamByOffers as $stream) {
                $items = $this->stream_items->where(
                    'cpas_stream_id', $stream->id
                )->all();
                if (!empty($items)) {
                    $ids = ZArrayHelper::map($items, 'id', 'id');
                    $allIds = ZArrayHelper::merge($allIds, $ids);
                    $traks = $this->cpas_traks->whereIn(
                        'cpas_stream_item_id', $ids
                    )->where('created_at', '<=', $endTime . '23:59:59')->where('created_at', '>', $startTime . '00:00:00');
                    $notEmpty = $traks->whereNotNull('contact_name');
                    if (empty($notEmpty->toArray()))
                        continue;
                    $all += $notEmpty->count();
                    if ($all === 0)
                        continue;
                    $trash += $notEmpty->where('status', 'trash')->count();
                    $new += $notEmpty->where('status', 'new')->count();
                    $accepts = $notEmpty->where('status', 'accept');
                    $accept_1 = $notEmpty->where('status', 'accept')->count();
                    $cancel = $notEmpty->where('status', 'cancel')->count();
                    $accept_ids = ZArrayHelper::map($accepts, 'id', 'cpas_stream_item_id');
                    $earned_money_1 = 0;
                    if ($accept_1 !== 0)
                        $earned_money_1 = $this->generateEarnedMoney($accept_ids);
                    $accept += $accept_1;
                    $earned_money += $earned_money_1;
                }
            }
            $traksAll = $this->cpas_traks->whereIn(
                'cpas_stream_item_id', $allIds
            )->where('created_at', '<=', $endTime . '23:59:59')->where('created_at', '>', $startTime . '00:00:00');
            $trackGroup = $traksAll->groupBy('ip');
            $click = count($trackGroup);
            $notEmpty = $traksAll->whereNotNull('contact_name');
            $stats = new CpasTrackForm();
            $stats->offer = $offer->title;
            $stats->click = $click;
            $stats->unique_click = count($traksAll) - $stats->click;
            $stats->cr = '0 %';
            $stats->approve = 0 . ' %';
            if ($click === 0)
                continue;
            if (empty($notEmpty->toArray())) {
                $data [] = $stats;
                continue;
            }
            $stats->all = $notEmpty->count();
            $stats->canceled = $cancel;
            $stats->await = $new;
            $stats->trash = $trash;
            $stats->confirmed = $accept;
            $stats->earned_money = $earned_money;
            $stats->cr = round(($stats->all / $stats->click * 100), 2) . ' %';
            $stats->Valid = abs($stats->all - $stats->trash);
            $new_valid = $stats->Valid - $stats->await;
            if ($new_valid !== 0) {
                $stats->approve = round(($stats->confirmed / $new_valid) * 100, 2) . ' %';
            }
            $stats->EPC = round(($stats->earned_money * $stats->confirmed / $stats->click), 2);
            $data[] = $stats;
        }
        return $data;
    }
#endregion
#region getStatsByLands
    public function getStatsByLands($user_id, string $attr = null, $startTime = null, $endTime = null)
    {
        $streams = collect(CpasStream::find()
            ->where([
                'user_id' => $user_id,

            ])->all());

        $stream_ids = ZArrayHelper::map($streams, 'id', 'id');
        $items_all = $this->stream_items->whereIn(
            'cpas_stream_id', $stream_ids
        );

        $land_ids = array_filter(ZArrayHelper::map($items_all, $attr, $attr));

        $cpas_lands = CpasLand::find()
            ->where([
                'id' => $land_ids
            ])
            ->all();

        $data = [];

        if ($startTime)
            $startTime = date('Y-m-d', strtotime($startTime . ' -1 day'));
        foreach ($cpas_lands as $land) {
            $items = $items_all->where(
                $attr, $land->id
            )
                ->where(
                    'user_id', $user_id
                )
                ->all();
            if (!empty($items)) {

                $ids = ZArrayHelper::map($items, 'id', 'id');
                $traks = $this->cpas_traks->whereIn(
                    'cpas_stream_item_id', $ids
                )->where('created_at', '<=', $endTime . '23:59:59')->where('created_at', '>', $startTime . '00:00:00');


                $datas = $this->generateForm($traks, 'land', $land->title);

                if ($datas !== null)
                    $data [] = $datas;


            };


        }

        return $data;

    }
#endregion
#region getStatsByDevices

    public function getStatsByDevices($user_id, $startTime = null, $endTime = null)
    {
        $streams = CpasStream::find()
            ->where([
                'user_id' => $user_id
            ])
            ->all();
        $stream_ids = ZArrayHelper::map($streams, 'id', 'id');
        $stream_items = CpasStreamItem::find()
            ->where([
                'cpas_stream_id' => $stream_ids
            ])
            ->all();
        $ids = ZArrayHelper::map($stream_items, 'id', 'id');
        $traks = $this->cpas_traks->whereIn(
            'cpas_stream_item_id', $ids
        )->all();
        $check = $this->checkDevice($traks);
        $types = [
            'desktop' => 'Десктопные',
            'mobile' => 'Мобильные'
        ];
        $data = [];
        if ($startTime)
            $startTime = date('Y-m-d', strtotime($startTime . ' -1 day'));
        foreach ($types as $key => $type) {
            $traks = $this->cpas_traks->whereIn(
                'id', $check[$key]
            )->where('created_at', '<=', $endTime . '23:59:59')->where('created_at', '>', $startTime . '00:00:00');

            $datas = $this->generateForm($traks, 'device', $type);
            if ($datas !== null)
                $data [] = $datas;
        }
        return $data;
    }

#endregion
#region checkDevice

    public function checkDevice($traks)
    {
        $mobile_ids = [];
        $desktop_ids = [];
        $mobiles = [
            'iPhone',
            'iPod',
            'iPad',
            'Android',
            'BlackBerry',
            'Mobile'
        ];
        foreach ($traks as $trak) {
            if (in_array($trak->device_os, $mobiles))
                $mobile_ids[] = $trak->id;

            else
                $desktop_ids[] = $trak->id;
        }
        return [
            'mobile' => $mobile_ids,
            'desktop' => $desktop_ids
        ];
    }

#endregion
#region getStatsByStreams

    public function getStatsByStreams($user_id, $startTime = null, $endTime = null)
    {
        $streams = collect(CpasStream::find()
            ->where([
                'user_id' => $user_id
            ])
            ->all());
        $data = [];
        if ($startTime)
            $startTime = date('Y-m-d', strtotime($startTime . ' -1 day'));
        foreach ($streams as $stream) {
            $items = $this->stream_items->where(
                'cpas_stream_id', $stream->id
            )->all();
            if (!empty($items)) {
                $ids = ZArrayHelper::map($items, 'id', 'id');
                $traks = $this->cpas_traks->whereIn(
                    'cpas_stream_item_id', $ids
                )->where('created_at', '<=', $endTime . '23:59:59')->where('created_at', '>', $startTime . '00:00:00');
                $datas = $this->generateForm($traks, 'stream', $stream->title);
                if ($datas !== null)
                    $data [] = $datas;
            }
        }
        return $data;
    }

#endregion
#region getStatsByCountries

    public function getStatsByCountries($user_id, $startTime = null, $endTime = null)
    {
        $countries = collect(PlaceCountry::find()->all());
        $streams = collect(CpasStream::find()
            ->where([
                'user_id' => $user_id
            ])
            ->all());
        $stream_ids = ZArrayHelper::map($streams, 'id', 'id');
        $stream_items = $this->stream_items->whereIn(
            'cpas_stream_id', $stream_ids
        )
            ->where(
                'user_id', $user_id
            );
        $ids = ZArrayHelper::map($stream_items, 'id', 'id');
        if ($startTime)
            $startTime = date('Y-m-d', strtotime($startTime . ' -1 day'));

        $traks = $this->cpas_traks->whereIn(
            'cpas_stream_item_id', $ids
        )->where('created_at', '<=', $endTime . '23:59:59')->where('created_at', '>', $startTime . '00:00:00')->groupBy('country');
        $data = [];
        foreach ($traks as $key => $trackGroup) {
            $country_name = $countries->where(
                'alpha2', $key
            )->first();
            if (!empty($country_name))
                $name = $country_name->name;
            else
                $name = $key;

            $datas = $this->generateForm($trackGroup, 'country', $name);

            if ($datas !== null)
                $data [] = $datas;
        }
        return $data;
    }

#endregion
#region generateForm

    public function generateForm($cpasTracker, $attr, $val = null)
    {
        $notEmpty = $cpasTracker->whereNotNull('contact_name');
        if (empty(count($cpasTracker)))
            return null;
        $trackGroup = $cpasTracker->groupBy('ip');
        $click = count($trackGroup);
        $cpasStats = new CpasTrackForm();
        $cpasStats->$attr = $val;
        $cpasStats->click = $click;
        $cpasStats->unique_click = $cpasTracker->count() - $click;
        $cpasStats->approve = '0 %';
        $cpasStats->cr = '0 %';
        if (empty($notEmpty->toArray())) {
            return $cpasStats;
        }
        $trash = $notEmpty->where('status', 'trash')->count();
        $new = $notEmpty->where('status', 'new')->count();
        $accept = $notEmpty->where('status', 'accept');
        $accepts = $accept->toArray();
        $accept_ids = ZArrayHelper::map($accepts, 'id', 'cpas_stream_item_id');
        $confirmed = $accept->count();
        $cancel = $notEmpty->where('status', 'cancel')->count();
        $cpasStats->canceled = $cancel;
        $cpasStats->trash = $trash;
        $cpasStats->confirmed = $confirmed;
        $cpasStats->all = count($notEmpty);
        $cpasStats->await = $new;
        $cpasStats->earned_money = 0;
        if ($confirmed !== 0)
            $cpasStats->earned_money = $this->generateEarnedMoney($accept_ids);

        $cr = 0;
        $approve = 0;
        $epc = 0;
        $cpasStats->Valid = $cpasStats->all - $cpasStats->trash;
        if ($cpasStats->all) {
            $cr = round(($cpasStats->all / $cpasStats->click) * 100, 2);
            $epc = round($cpasStats->earned_money * $cpasStats->confirmed / $cpasStats->click, 2);
            $valid_new = $cpasStats->Valid - $cpasStats->await;
            if ($valid_new !== 0)
                $approve = round(($confirmed / $valid_new) * 100, 2);
        }
        $cpasStats->approve = $approve . ' %';
        $cpasStats->cr = $cr . ' %';
        $cpasStats->EPC = $epc;
        return $cpasStats;
    }

#endregion

#region generateEarnedMoney

    public
    function generateEarnedMoney(array $ids)
    {
        $amount = 0;
        foreach ($ids as $id) {
            $item = CpasStreamItem::findOne($id);

            if (!empty($item->cpas_land_id))
                $land = CpasLand::findOne($item->cpas_land_id);

            if (!empty($item->cpas_trans_form))
                $land = CpasLand::findOne($item->cpas_trans_form);


            if (!empty($land)) {
                $offer_item = CpasOfferItem::findOne($land->cpas_offer_item_id);
                if (!empty($offer_item))
                    $amount += $offer_item->pay;
            }
        }

        return $amount;
    }

#endregion

//end|JakhongirKudratov


#region generateClicks
    public
    function generateClicks($id)
    {
        $items = CpasStreamItem::find()
            ->where([
                'cpas_stream_id' => $id
            ])
            ->all();
        $clicks = 0;
        foreach ($items as $item) {
            $clicks += $item->click;
        }
        return $clicks;
    }

#endregion


#region generateClicks
    public
    function generateUniClicks($id)
    {
        $items = CpasStreamItem::find()
            ->where([
                'cpas_stream_id' => $id
            ])
            ->all();
        $clicks = 0;
        foreach ($items as $item) {
            $clicks += $item->uniclick;
        }
        return $clicks;
    }

#endregion


#region generateByStatus

    public
    function generateByStatus($item_id, $status)
    {
        $tracks = CpasTracker::find()
            ->where([
                'cpas_stream_item_id' => $item_id
            ])
            ->andWhere([
                'status' => $status
            ])
            ->count();
        if (!$tracks)
            return 0;
        return $tracks;
    }

#endregion

#region generateAproove

    public
    function generateAproove($item_id, $click)
    {
        $aprove = CpasTracker::find()
            ->where([
                'cpas_stream_item_id' => $item_id
            ])
            ->andWhere([
                'status' => 'accept'
            ])
            ->count();
        if (!$aprove || $aprove === 0)
            return 0;
        if ($click === 0)
            return 0;
        $return = round(($aprove / $click) * 100, 2);
        return $return;


    }

#endregion


#region generateEnrolled
    public
    function generateEnrolled($item)
    {
        $tracks = CpasTracker::find()
            ->where([
                'cpas_stream_item_id' => $item->id
            ])
            ->andWhere([
                'status' => 'accept'
            ])
            ->count();

        if (!$tracks)
            return 0;

        if (!empty($item->cpas_land_id))
            $land = CpasLand::findOne($item->cpas_land_id);
        if (!empty($item->cpas_trans_form))
            $land = CpasLand::findOne($item->cpas_trans_form);

        if (!$land)
            return 0;
        else {
            $offer_item = CpasOfferItem::findOne($land->cpas_offer_item_id);
            $money = $offer_item->pay;
            $return = $money * $tracks;
            //vdd($tracks);
            return $return;

        }


    }

#endregion

#region generateExpected

    public
    function generateExpected($item_id)
    {
        return 0;
    }

#endregion

#region generateAllStatus

    public
    function generateAllStatus($item_id)
    {
        $tracks = CpasTracker::find()
            ->where([
                'cpas_stream_item_id' => $item_id
            ])
            ->count();

        if ($tracks)
            return 0;

        return $tracks;


    }

#endregion

#region generateClientUniClicks

    public
    function generateClientUniClicks(array $ids, $attr)
    {
        $click = 0;
        $cpasStreamItems = CpasStreamItem::find()
            ->where([
                'id' => $ids
            ])
            ->all();
        if (empty($cpasStreamItems))
            return $click;

        foreach ($cpasStreamItems as $item) {
            $click += (int)$item->$attr;
        }

        return $click;
    }

#endregion
#region Test


    public
    function test()
    {
        $cpasTracker = collect(CpasTracker::find()->orderBy('created_at asc')->all());

        vdd($cpasTracker);
    }

#endregion

#region getAllOffersbyName

    public
    function getAllOffersbyName()
    {
        $all = CpasOffer::find()->all();
        $data = [];

        foreach ($all as $value) {
            $data[$value->title] = $value->title;
        }

        return $data;
    }

#endregion
    public function getAllSubs(){
        $streams = CpasStream::find()
            ->where([
                'user_id' => $this->userIdentity()->id
            ])->all();
        $stream_ids = ZArrayHelper::getColumn($streams, 'id');
        $stream_items = CpasStreamItem::find()
            ->where([
                'cpas_stream_id' => $stream_ids
            ])
            ->all();
        $ids = ZArrayHelper::getColumn($stream_items, 'id');
        $cpasTrackerQuery = CpasTracker::find()
            ->where([
                'cpas_stream_item_id' => $ids
            ])
            ->orderBy('created_at asc');
        if($this->httpGet('selectedSub')){
            $val = $this->httpGet('selectedSub');
            $cpasTracker = collect($cpasTrackerQuery->andWhere("$val IS NOT NULL")->asArray()->all())->groupBy($val);
        }else{
            $cpasTracker = collect($cpasTrackerQuery->andWhere("sub_id_1 IS NOT NULL")->asArray()->all())->groupBy('sub_id_1');
        }
        $data = [];
        foreach ($cpasTracker as $key => $item){
            $data[$key] = $key;
        }
        return $data;
    }

#region getStatsByCountriesOld

    public
    function getStatsByCountriesOld($startTime = null, $endTime = null)
    {
        $user_id = $this->userIdentity()->id;
        $countries = collect(PlaceCountry::find()->all());
        $streams = collect(CpasStream::find()
            ->where([
                'user_id' => $user_id
            ])
            ->all());
        $stream_ids = ZArrayHelper::map($streams, 'id', 'id');
        //vdd($stream_ids);
        $stream_items_1 = $this->stream_items->whereIn(
            'cpas_stream_id', $stream_ids
        )
            ->where(
                'user_id', $user_id
            );
        //vdd($stream_items_1);

        $land_ids = array_filter(ZArrayHelper::map($stream_items_1, 'cpas_land_id', 'cpas_land_id'));
        $trans_ids = array_filter(ZArrayHelper::map($stream_items_1, 'cpas_trans', 'cpas_trans'));
        $trans_form_ids = array_filter(ZArrayHelper::map($stream_items_1, 'cpas_trans_form', 'cpas_trans_form'));
        $all_ids = array_unique(ZArrayHelper::merge($land_ids, $trans_ids, $trans_form_ids));
        $lands = collect(CpasLand::find()
            ->where([
                'id' => $all_ids
            ])
            ->all());
        //vdd($lands);

        $country_ids = array_unique(ZArrayHelper::map($lands, 'id', 'place_country_id'));
        //vdd($country_ids);
        $data = [];
        if ($startTime)
            $startTime = date('Y-m-d', strtotime($startTime . ' -1 day'));


        foreach ($country_ids as $c_id) {
            $land_by_country = $lands->whereIn('place_country_id', $c_id);
            $all = 0;
            $click = 0;
            $approve = 0;
            $trash = 0;
            $new = 0;
            $cancel = 0;
            $accept = 0;
            $earned_money = 0;
            $unique_click = 0;
            $allIds = [];
            vd($land_by_country);

            foreach ($land_by_country as $land) {
                $items = $this->stream_items->filter(function ($value, $key) use ($land, $user_id) {
                    if (($value->cpas_land_id == $land->id || $value->cpas_trans === $land->id || $value->cpas_trans_form === $land->id) && $value->user_id == $user_id)
                        return $value;
                })->all();
                /* vd('item_1');
                 vd($items_1);*/
                //vd($items_1);

                /* $items = $this->stream_items->whereIn(
                         'cpas_land_id', $land->id
                     )
                     ->where(
                        'user_id', $user_id
                     )
                     ->all();
    
                 vd('item');
                 vd($items);*/

                if (!empty($items)) {

                    $ids = ZArrayHelper::map($items, 'id', 'id');
                    vd($ids);
                    $traks = $this->cpas_traks->whereIn(
                        'cpas_stream_item_id', $ids
                    )->where('created_at', '<=', $endTime . '23:59:59')->where('created_at', '>', $startTime . '00:00:00');

                    $item_ids = ZArrayHelper::map($traks, 'id', 'cpas_stream_item_id');
                    vd($item_ids);
                    $click_1 = $this->generateClientUniClicks($item_ids, 'uniclick');
                    $all += $traks->count();

                    $trash += $traks->where('status', 'trash')->count();
                    $new += $traks->where('status', 'new')->count();
                    $accepts = $traks->where('status', 'accept');

                    $accept_1 = $traks->where('status', 'accept')->count();
                    $cancel = $traks->where('status', 'cancel')->count();
                    $accept_ids = ZArrayHelper::map($accepts, 'cpas_stream_item_id', 'cpas_stream_item_id');
                    $unique_click_1 = $this->generateClientUniClicks($item_ids, 'uniclick');
                    $earned_money_1 = 0;

                    if ($accept_1 !== 0)
                        $earned_money_1 = $this->generateEarnedMoney($accept_ids);


                    if ($click_1 !== 0) {
                        $approve += round(($accept_1 / $click_1) * 100, 2);

                    }

                    $unique_click += $unique_click_1;
                    $accept += $accept_1;
                    $click += $click_1;
                    $earned_money += $earned_money_1;

                }


            }

            $traksAll = $this->cpas_traks->whereIn(
                'cpas_stream_item_id', $allIds
            )->where('created_at', '<=', $endTime . '23:59:59')->where('created_at', '>', $startTime . '00:00:00');

            if ($click === 0)
                continue;
            $country = $countries->where('id', $c_id)->first();
            $stats = new CpasTrackForm();
            $stats->country = $country->name;
            $stats->click = $click;
            $stats->all = $all;
            $stats->canceled = $cancel;
            $stats->await = $new;
            $stats->trash = $trash;
            $stats->confirmed = $accept;
            $stats->unique_click = $unique_click;
            $stats->earned_money = $earned_money;
            $stats->approve = $approve . ' %';
            $stats->cr = $this->generateCr($traksAll) . ' %';
            $stats->Valid = $click - $unique_click;
            $epc = round($earned_money / ($all), 2);
            $stats->EPC = $epc;
            //vd($stats);
            $data[] = $stats;

        }

        /*foreach ($lands as $land)
        {
            $items = $this->stream_items->whereIn(
                    'cpas_land_id', $land->id
                )
                ->all();
    
            $items_1 = $this->stream_items->filter(function ($value, $key) use ($land) {
                if ($value->cpas_land_id === $land->id || $value->cpas_trans === $land->id || $value->cpas_trans_form === $land->id)
                    return $value;
            });
            vd($items_1);
    
            if (!empty($items)){
    
                $ids = ZArrayHelper::map($items, 'id', 'id');
                $traks = $this->cpas_traks->whereIn(
                    'cpas_stream_item_id', $ids
                );
    
                if ($traks->count() === 0)
                    continue;
    
                $item_ids = ZArrayHelper::map($traks, 'cpas_stream_item_id', 'cpas_stream_item_id');
                $trash = $traks->where('status', 'trash')->count();
                $new = $traks->where('status', 'new')->count();
                $accepts = $traks->where('status', 'accept');
                $accept = $traks->where('status', 'accept')->count();
                $accept_ids = ZArrayHelper::map($accepts, 'cpas_stream_item_id', 'cpas_stream_item_id');
                $cancel = $traks->where('status' , 'cancel')->count();
                $country = $countries->where('id', $land->place_country_id)->first();
    
                $stats = new CpasTrackForm();
                $stats->country =$country->name;
                $stats->click = $traks->count();
                $stats->all = $traks->count();
                $stats->canceled = $cancel;
                $stats->await = $new;
                $stats->trash = $trash;
                $stats->confirmed = $accept;
                $stats->unique_click = $this->generateClientUniClicks($item_ids);
                $stats->earned_money = 0;
                if ($accept !== 0)
                    $stats->earned_money = $this->generateEarnedMoney($accept_ids);
    
                $cr = 0;
                $approve= 0;
                $epc = 0;
                if ($stats->click !== 0){
                    $cr = round(($stats->unique_click/$stats->click)*100, 2);
                    $approve = round(($stats->confirmed/$stats->click)*100, 2);
                    $epc = round($stats->earned_money/$stats->click, 2);
    
                }
                $stats->approve = $approve. ' %';
                $stats->cr = $cr. ' %';
                $stats->Valid = $stats->click - $stats->unique_click;
                $stats->EPC = $epc;
                $data[] = $stats;
    
            };
    
    
        }*/

        return $data;
    }

#endregion

#region generateCr

    public
    function generateCr($traks)
    {
        $count = $traks->count();
        $notOrdered = $traks->filter(function ($value, $key) {
            if (!empty($value->contact_phone) || !empty($value->contact_name))
                return null;
            return $value;
        })->count();

        $orderedCount = $count - $notOrdered;
        if (!empty($count)) {
            return round(($orderedCount / $count) * 100, 2);
        } else
            return 0;
    }

#endregion
#region old


    public
    function generateAdminStatsOld()
    {
        $users = User::find()->all();
        $data = [];
        foreach ($users as $user) {
            $streams = CpasStream::find()
                ->where([
                    'user_id' => $user->id
                ])
                ->all();
            foreach ($streams as $stream) {
                $items = CpasStreamItem::find()
                    ->where([
                        'cpas_stream_id' => $stream->id
                    ])
                    ->orderBy('id desc')
                    ->all();
                foreach ($items as $item) {
                    $cr = $item->click ? round(($item->uniclick / $item->click) * 100, 2) : 0;
                    $stats = new CpasTrackForm();
                    $stats->id = $item->id;
                    $stats->user = $user->email;
                    $stats->stream = $stream->title;
                    $stats->stream_item = $item->title;
                    $stats->click = $item->click ? $item->click : 0;
                    $stats->unique_click = $item->uniclick ? $item->uniclick : 0;
                    $stats->cr = $cr . ' %';
                    $stats->approve = $this->generateAproove($item->id, $stats->click) . ' %';
                    $stats->confirmed = $this->generateByStatus($item->id, 'accept');
                    $stats->await = $this->generateByStatus($item->id, 'new');
                    $stats->canceled = $this->generateByStatus($item->id, 'cancel');
                    $stats->all = $this->generateAllStatus($item->id);
                    $stats->trash = $this->generateByStatus($item->id, 'trash');
                    $stats->Valid = $stats->all - $stats->trash;
                    $stats->earned_money = $this->generateEnrolled($item);
                    $epc = $stats->click ? round($stats->earned_money / $stats->click, 2) : 0;
                    $stats->EPC = $epc;
                    $data[] = $stats;
                }
            }

        }
        return $data;

    }
#endregion
}

