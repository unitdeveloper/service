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
require Root . '/vendors/fileapp/office/vendor/autoload.php';
require Root . '/vendors/netter/ALL/vendor/autoload.php';

/**
 *
 * @author Shahzod Gulomqodirov
 *
 * @license Jakhongir Kudratov
 */


use PhpOffice\PhpWord\Shared\ZipArchive;
use PHPUnit\Util\Exception;
use PhpZip\ZipFile;
use React\EventLoop\Factory;
use Telegram\Bot\Helpers\Emojify;
use yii\rbac\DbManager;
use zetsoft\dbcore\ALL\CoreRoleCore;
use zetsoft\former\cpas\CpasFilterForm;
use zetsoft\former\cpas\CpasBalanceHistoryForm;
use zetsoft\former\cpas\CpasUserFlowsForm;
use zetsoft\models\core\CoreRole;
use zetsoft\models\cpas\CpasLand;
use zetsoft\models\cpas\CpasOffer;
use zetsoft\models\cpas\CpasOfferItem;
use zetsoft\models\cpas\CpasPaysHistory;
use zetsoft\models\cpas\CpasSource;
use zetsoft\models\cpas\CpasStream;
use zetsoft\models\cpas\CpasStreamItem;
use zetsoft\models\cpas\CpasTracker;
use zetsoft\models\pays\PaysPayment;
use zetsoft\models\pays\PaysWithdraw;
use zetsoft\models\place\PlaceCountry;
use zetsoft\service\ALL\Cpas;
use zetsoft\service\App\eyuf\User;
use zetsoft\system\Az;
use zetsoft\system\except\ZException;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\kernels\ZFrame;
use function GuzzleHttp\Psr7\str;
use function MongoDB\BSON\fromJSON;

class Cpa extends ZFrame
{
    public $offerPath = Root . '/render/cpanet/';

    public $sitePath = Root . '/render/cpasite/';

    public $zipPath = Root . '/render/cpazips/';

    public $indexFile = Root . '/render/cpanet/index.php';
    public $thanksFile = Root . '/render/cpanet/thanks.php';
    public $indexPre = Root . '/render/cpanet/indexPre.php';
    public $indexLink = Root . '/render/cpanet/indexForm.php';
    public $mainFileP = Root . '/render/cpanet/Main.php';

    public function createOfferFolder($name)
    {
        return ZFileHelper::createDirectory($this->offerPath . $name);
    }

    public function createOfferItemFolder(CpasOfferItem $model)
    {
        $cpaOffer = CpasOffer::findOne($model->cpas_offer_id);
        $placeCountry = PlaceCountry::findOne($model->place_country_id);
        $path = "{$this->offerPath}{$cpaOffer->title}/{$placeCountry->alpha2}";
        return ZFileHelper::createDirectory($path);
    }


    public function createCpasSite(CpasStreamItem $model)
    {

        $stream = CpasStream::findOne($model->cpas_stream_id);
        $offer = CpasOffer::findOne($stream->cpas_offer_id);
        $faceBookPixel = '';
        $googleAnlytics = '';
        $yandexMetrics = '';
        $mailMetrics = '';

        if ($stream->counter) {
            if ($stream->counter['facebook'])
                $faceBookPixel = $stream->counter['facebook'];
            if ($stream->counter['google'])
                $googleAnlytics = $stream->counter['google'];
            if ($stream->counter['yandex'])
                $yandexMetrics = $stream->counter['yandex'];
            if ($stream->counter['mail'])
                $mailMetrics = $stream->counter['mail'];

        }

        if (file_exists($this->thanksFile)) {
            $thanks = file_get_contents($this->thanksFile);
            $thanks = strtr($thanks, [
                '{facebookPixel}' => $faceBookPixel,
                '{googleAnlytics}' => $googleAnlytics,
                '{yandexMetrics}' => $yandexMetrics,
                '{mailMetrics}' => $mailMetrics,
            ]);

        }


        if (!empty($model->cpas_land_id) && empty($model->cpas_trans)) {
            $land = CpasLand::findOne($model->cpas_land_id);
            $copyFrom = Root . $land->path;
            $copyTo = $this->sitePath . $stream->id . "/{$model->id}" . '/' . $land->title;

            ZFileHelper::cloneDir($copyFrom, $copyTo);
            $index = file_get_contents($this->indexFile);
            $index = strtr($index, [
                '{offer_id}' => $offer->catalog,
                '{auth_key}' => $this->userIdentity()->auth_key,
                '{item_id}' => $model->id
            ]);
            if (is_dir($copyTo)) {
                copy($this->mainFileP, $copyTo . '/Main.php');
                file_put_contents($copyTo . '/index.php', $index);
                file_put_contents($copyTo . '/thanks.php', $thanks);
            }

        }

        if (!empty($model->cpas_trans)) {
            if ($model->cpas_trans_form)
                $land = CpasLand::findOne($model->cpas_trans_form);
            if ($model->cpas_land_id)
                $land = CpasLand::findOne($model->cpas_land_id);
            $preland = CpasLand::findOne($model->cpas_trans);
            $copyToPreland = $this->sitePath . $stream->id . "/{$model->id}" . '/' . $preland->title . '_pre';
            $prelendPath = Root . $preland->path;

            ZFileHelper::cloneDir($prelendPath, $copyToPreland);
            $link = $this->urlGetBase() . '/render/cpasite/' . $stream->id . '/' . $model->id . '/' . $land->title . '/index.php';
            $indexPreland = file_get_contents($this->indexLink);
            $replaceindex = strtr($indexPreland, [
                '{form_link}' => $model->trans_link ? $model->trans_link : $link,
                '{offer_id}' => $offer->catalog,
                '{auth_key}' => $this->userIdentity()->auth_key,
                '{item_id}' => $model->id
            ]);

            $copyToPreland_land = $copyToPreland . '/land';
            $land_path = Root . $land->path;
            ZFileHelper::cloneDir($land_path, $copyToPreland_land);

            if (is_dir($copyToPreland)) {
                copy($this->mainFileP, $copyToPreland . '/Main.php');
                file_put_contents($copyToPreland . '/index.php', $replaceindex);
                file_put_contents($copyToPreland . '/thanks.php', $thanks);
            }
            $index = file_get_contents($this->indexFile);
            $index = strtr($index, [
                '{offer_id}' => $offer->catalog,
                '{auth_key}' => $this->userIdentity()->auth_key,
                '{item_id}' => $model->id
            ]);

            if (is_dir($copyToPreland_land)) {
                copy($this->mainFileP, $copyToPreland_land . '/Main.php');
                file_put_contents($copyToPreland_land . '/index.php', $index);
                file_put_contents($copyToPreland_land . '/thanks.php', $thanks);
            }

        }
        if (!empty($model->cpas_trans_form) && empty($model->cpas_trans)) {

            $preland = CpasLand::findOne($model->cpas_trans_form);
            $copyToPreland = $this->sitePath . $stream->id . "/{$model->id}" . '/' . $preland->title . '_form';
            $prelendPath = Root . $preland->path;
            ZFileHelper::cloneDir($prelendPath, $copyToPreland);
            $index = file_get_contents($this->indexFile);
            $index = strtr($index, [
                '{offer_id}' => $offer->catalog,
                '{auth_key}' => $this->userIdentity()->auth_key,
                '{item_id}' => $model->id
            ]);
            if (is_dir($copyToPreland)) {
                copy($this->mainFileP, $copyToPreland . '/Main.php');
                file_put_contents($copyToPreland . '/index.php', $index);
                file_put_contents($copyToPreland . '/thanks.php', $thanks);
            }

        }


        $zipAll = $this->sitePath . $stream->id . "/{$model->id}";
        $zipTo = $this->zipPath . $model->user_id . "/{$model->id}/";
        $zipFile = new ZipFile();
        ZFileHelper::createDirectory($zipTo);
        $zipFile->addDirRecursive($zipAll) // add files from the directory
        ->saveAsFile($zipTo . $model->id . '.zip') // save the archive to a file
        ->close();
    }


    /**
     *
     * Function  editStream
     *  delete old cpasite and cpazip and create new cpasite with updated params
     *
     * @param CpasStream $model
     * @param null $user_id
     * @throws \Exception
     *
     * @author JakhongirKudratov
     * @license JakhongirKudratov
     *
     */


    public function editStream(CpasStream $model, $user_id = null)
    {
        $items = CpasStreamItem::find()
            ->where([
                'cpas_stream_id' => $model->id
            ])
            ->all();
        foreach ($items as $item) {
            $dirto = $this->sitePath . $model->id . '/' . $item->id;
            $dirName = $this->zipPath . $model->user_id . '/' . $item->id;

            ZFileHelper::removeDir($dirName);
            ZFileHelper::removeDir($dirto);
            $this->createCpasSite($item);
        }
    }


    public function getCountry($id)
    {
        $data = [];
        $model = CpasLand::find()
            ->where(['cpas_offer_id' => $id])
            ->all();
        foreach ($model as $item) {

            $cont = PlaceCountry::findOne($item->place_country_id);
            if ($cont !== null)
                $data[$item->place_country_id] = $cont->name;
        }
        return $data;

    }


    public function getStrim($id)
    {
        $model = CpasStream::find($id)
            ->asArray()
            ->all();
        return $model;
    }

    /**
     *
     * Function  trafficNames
     * @param $model
     * @return  mixed|string|null
     *
     * @author AzimjonToirov
     * Recommended and not recommended traffics
     */
    public function trafficNames($model)
    {
        $checkIcon = '<i class="fas fa-check-circle text-success mr-3"></i>';
        $unCheckIcon = '<i class="fas fa-times-circle text-danger mr-3"></i>';
        $notTraffic = Az::l('not traffics');

        if ($model === null)
            return $notTraffic;

        if (!empty($model->recommended_trafic))
            foreach ($model->recommended_trafic as $spacOfferId) {
                $recomended_trafficName = CpasSource::findOne($spacOfferId)->name;
                if ($recomended_trafficName !== null)
                    echo $checkIcon . $recomended_trafficName . '<br>';
            }

        if (!empty($model->not_recommended_traffic))
            foreach ($model->not_recommended_traffic as $spacOfferId) {
                $not_recommended_trafficName = CpasSource::findOne($spacOfferId)->name;
                if ($not_recommended_trafficName !== null)
                    echo $unCheckIcon . $not_recommended_trafficName . '<br>';
            }

        return null;
    }


    public function setTeaserUrls(CpasStreamItem $model)
    {
        $cpas_stream_item_id = $model->id;
        $teserform = $model->teaser;
        $url = 'http://arbit.zetsoft.uz/cpas/track/teaser.aspx?cpas_stream_id=' . $cpas_stream_item_id . '&';
    }

    public function getStreamsByUser()
    {
        $user_id = $this->userIdentity()->id;

        $streams = CpasStream::find()->where(['user_id' => $user_id])
            ->orderBy([
                'id' => SORT_DESC
            ])
            ->all();

        $needStreams = [];

        foreach ($streams as $key => $value) {
            $exists = CpasStreamItem::find()
                ->where([
                    'cpas_stream_id' => $value->id
                ])
                ->exists();
            if ($exists)
                $needStreams[] = $value;
        }

        return $needStreams;
    }


    /**
     *
     * Function  urlReplace
     * @param $track_id
     * @return  mixed|string|null
     *
     * @author jakhongirKudratov
     *
     */

    public function urlReplace($track_id, $status = 'new')
    {
        $track = CpasTracker::findOne($track_id);
        $cpas_stream_item_id = $track->cpas_stream_item_id;
        $cpas_stream_item = CpasStreamItem::findOne($cpas_stream_item_id);
        $urlReplace = '';
        if ($cpas_stream_item) {
            $cpas_stream_id = $cpas_stream_item->cpas_stream_id;
            $cpas_stream = CpasStream::findOne($cpas_stream_id);
            $pback = $cpas_stream->postback;
            if ($status === 'accept')
                $status = 'approve';
            $url = $pback[$status];
            if (!$url)
                return false;
            $urlReplace = strtr($url, [
                '{status}' => $track->status,
                '{source}' => $track->referrer,
                '{country}' => $track->country,
                '{city}' => $track->city,
                '{customer}' => $track->contact_name,
                '{order}' => $track->shop_order_id,
                '{phone}' => $track->contact_phone,
                '{region}' => $track->region,
                '{timezone}' => $track->timezone,
                '{utc}' => $track->utc,
                '{lat}' => $track->lat,
                '{lon}' => $track->lon,
                '{ip}' => $track->ip,
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
                '{sub1}' => $track->sub_id_1,
                '{sub2}' => $track->sub_id_2,
                '{sub3}' => $track->sub_id_3,
                '{sub4}' => $track->sub_id_4,
                '{sub5}' => $track->sub_id_5,
                '{sub6}' => $track->sub_id_6,
            ]);
        }
        return $urlReplace;
    }

    public function updateTrack($track_id, $order_id)
    {
        $track = CpasTracker::findOne($track_id);
        if ($track) {
            $track->shop_order_id = $order_id;
            if ($track->save())
                return true;
        }
        return false;
    }

    public function updateStatus($subId, $status)
    {
        $track = CpasTracker::findOne(['shop_order_id' => $subId]);
        if (empty($track))
            return false;
        $cpasStreamItem = CpasStreamItem::findOne($track->cpas_stream_item_id);
        if ($cpasStreamItem === null)
            return false;
        $user = \zetsoft\models\user\User::findOne($cpasStreamItem->user_id);
        if ($user !== null) {
            //vdd($this->sessionGet('bearer'));
            //if ($user->auth_key === $this->sessionGet('bearer')) {
            $track->status = $status;
            if ($track->save())
                return true;
            else
                return false;
        }

        return false;
        /*}
         Az::$app->response->setStatusCode('403');
         throw new ZException(Az::l('Forbidden'));*/
    }


    #region status and balance

    //start|JakhongirKudratov|2020-10-10


    /**
     *
     * Function  setBalance
     *  get status and changes balance
     * @param CpasTracker $model
     * @return  bool|null
     * @author JakhongirKudratov
     */
    public function setBalance(CpasTracker $model)
    {
        /*
                vd($model->oldAttributes);
                vdd($model->attributes);*/
        //start|JakhongirKudratov|2020-10-12

        if ($model->isNewRecord)
            return true;

        if ($model->oldAttributes['status'] === CpasTracker::status['accept']) {
            if ($this->balance($model, 'minus'))
                return true;
        }


        if ($model->status === CpasTracker::status['accept'])
            if ($this->balance($model, 'add'))
                return true;


        return null;

    }

    //end|JakhongirKudratov|2020-10-12

    /**
     *
     * Function  balance
     *  changes the balance when the status changes
     * @param $model
     * @param $practice
     * @return  bool|null
     * @author JakhongirKudratov
     */

    public function balance(CpasTracker $model, $practice)
    {
        $item = CpasStreamItem::findOne($model->cpas_stream_item_id);
        if (!empty($item->cpas_land_id))
            $land = CpasLand::findOne($item->cpas_land_id);
        elseif (!empty($item->cpas_trans_form))
            $land = CpasLand::findOne($item->cpas_trans_form);
        elseif (!empty($item->cpas_trans))
            $land = CpasLand::findOne($item->cpas_trans);
        else
            return null;
        if ($land === null) {
            $cpasStream = CpasStream::findOne($item->cpas_stream_id);
            $user = \zetsoft\models\user\User::findOne($cpasStream->user_id);
            $cpasOffer = CpasOffer::findOne($cpasStream->cpas_offer_id);
            $log = "User: " . $user->email . ' - ' . date("H:i:s") . PHP_EOL .
                "ид заказа: {$model->shop_order_id}" .PHP_EOL .
                "action $practice" . PHP_EOL .
                "tovar: {$cpasOffer->name} , id: {$cpasOffer->id}" . PHP_EOL .
                "-------------------------" . PHP_EOL;
//Save string to log, use FILE_APPEND to append.
            file_put_contents(Root . '/storing/usersNeedToGveMoney/log_' . date("j.n.Y") . '.log', $log, FILE_APPEND);
            return null;
        }
        $offer_item = CpasOfferItem::findOne($land->cpas_offer_item_id);
        $money = $offer_item->pay;
        $stream = CpasStream::findOne($item->cpas_stream_id);
        $user = \zetsoft\models\user\User::findOne($stream->user_id);
        if ($practice === 'add')
            $user->balance += $money;
        if ($practice === 'minus')
            $user->balance -= $money;
        if ($user->save())
            return true;

        return null;
    }

    //end|JakhongirKudratov|2020-10-10

    #endregion

    public function openArchive($id, CpasLand $model)
    {
        //vd($id);
        $item = CpasOfferItem::findOne($id);
        $offer = CpasOffer::findOne($item->cpas_offer_id);
        $offerName = $offer->title;
        $country = PlaceCountry::findOne($item->place_country_id);
        $lang = $country->alpha2;
        $paste = Root . '/render/cpanet/' . $offerName . '/' . $lang . '/' . $model->title . '/';
        $model->path = '/render/cpanet/' . $offerName . '/' . $lang . '/' . $model->title . '/';

        if ($model->type === 'trans') {
            $paste = Root . '/render/cpanet/' . $offerName . '/' . $lang . '-pre/' . $model->title . '/';
            $model->path = '/render/cpanet/' . $offerName . '/' . $lang . '-pre/' . $model->title . '/';
        }

        if ($model->type === 'trans_form') {
            $paste = Root . '/render/cpanet/' . $offerName . '/' . $lang . '-form/' . $model->title . '/';
            $model->path = '/render/cpanet/' . $offerName . '/' . $lang . '-form/' . $model->title . '/';
        }

        $open = Root . '/upload/uploaz/arbit/' . $model->className . '/archive/' . $model->id . '/' . $model->archive[0];
        $model->place_country_id = $item->place_country_id;
        $model->save();
        $unzip = Az::$app->office->zipArchive->unzip($open, $paste);
        return $unzip;
    }


    #region getBalanceHistory

    public function getBalanceHistory()
    {
        $users = \zetsoft\models\user\User::find()
            ->where([
                'not',
                [
                    'balance_history' => null
                ]
            ])
            ->all();
        $paysWithdraw = collect(PaysWithdraw::find()
            ->where([
                'status' => 'ok'
            ])
            ->all());
        $pays_payment = collect(PaysPayment::find()->all());
        $data = [];

        foreach ($users as $user) {
            $histories = $user->balance_history;
            foreach ($histories as $history) {
                $userBy = \zetsoft\models\user\User::findOne($history['user_id']);
                $historyBalance = new CpasBalanceHistoryForm();
                $historyBalance->user = $user->email;
                $historyBalance->date = $history['date'];

                $pays = $paysWithdraw->filter(function ($value, $key) use ($user, $history) {
                    if ($value->modified_at == $history['date'] && $value->user_id === $user->id)
                        return $value;
                })->first();

                if (empty($pays))
                    continue;

                if (!empty($pays->pays_payment_id)) {
                    $payment = $pays_payment->where('id', $pays->pays_payment_id)->first();
                    if (!empty($payment))
                        $historyBalance->pays_payment = $payment->name;
                }

                if ($pays)
                    $historyBalance->balance = $pays->amount;
                $historyBalance->userBy = isset($userBy->email) ? $userBy->email : '';

                $data [] = $historyBalance;
            }


        }

        return $data;
    }

    #endregion


    /**
     *
     * Function  getUserBalanceHistory
     * get user balance history
     * @param $user_id
     * @return  array
     * @throws \Exception
     */

    //start|JakhongirKudratov|2020-10-20

    public function getUserBalanceHistory($user_id)
    {
        $user = \zetsoft\models\user\User::findOne($user_id);
        $paysWithdraw = collect(PaysWithdraw::find()
            ->where([
                'status' => 'ok'
            ])
            ->andWhere([
                'user_id' => $user_id
            ])
            ->all());
        $pays_payment = collect(PaysPayment::find()->where([
            'user_id' => $user_id
        ])->all());
        $data = [];
        $histories = $user->balance_history;
        //vdd($histories);
        if (!empty($histories))
            foreach ($histories as $history) {
                $userBy = \zetsoft\models\user\User::findOne($history['user_id']);
                $historyBalance = new CpasBalanceHistoryForm();
                $historyBalance->user = $user->email;
                $historyBalance->date = $history['date'];
                $pays = $paysWithdraw->filter(function ($value, $key) use ($user, $history) {
                    if ($value->modified_at == $history['date'] && $value->user_id === $user->id)
                        return $value;
                })->first();

                if (!empty($pays->pays_payment_id)) {
                    $payment = $pays_payment->where('id', $pays->pays_payment_id)->first();
                    $historyBalance->pays_payment = $payment->name;
                }

                if (empty($pays))
                    continue;

                if ($pays)
                    $historyBalance->balance = $pays->amount;

                $historyBalance->userBy = isset($userBy->email) ? $userBy->email : '';

                $data [] = $historyBalance;
            }

        return $data;

    }

    //end|JakhongirKudratov|2020-10-20

    #region getFilters

    public function getFilters()
    {
        $filter = new CpasFilterForm();
        if ($this->httpIsGet()) {
            if (empty($this->httpGet()))
                return $filter;
            $filter->selectedBtnValue = ZArrayHelper::getValue($this->httpGet(), 'selectedBtnValue');
            $filter->startdate = ZArrayHelper::getValue($this->httpGet(), 'startdate');
            $filter->enddate = ZArrayHelper::getValue($this->httpGet(), 'enddate');
            $filter->selectedOffer = ZArrayHelper::getValue($this->httpGet(), 'selectedOffer');
            $filter->selectedFlow = ZArrayHelper::getValue($this->httpGet(), 'selectedFlow');
            $filter->selectedland = ZArrayHelper::getValue($this->httpGet(), 'selectedland');
            $filter->selectedPreland = ZArrayHelper::getValue($this->httpGet(), 'selectedPreland');
            $filter->selectedCountry = ZArrayHelper::getValue($this->httpGet(), 'selectedCountry');
            $filter->selectedSub = ZArrayHelper::getValue($this->httpGet(), 'selectedSub');
            $filter->selectedUtm = ZArrayHelper::getValue($this->httpGet(), 'selectedUtm');
            $filter->selectTimes = ZArrayHelper::getValue($this->httpGet(), 'selectTimes');

            return $filter;
        }
    }

    #endregion


    #region getFilterData

    public function getFilterData($user_id)
    {
        $streams = CpasStream::find()
            ->where([
                'user_id' => $user_id
            ])
            ->all();
        $selectStreams = ZArrayHelper::map($streams, 'id', 'title');
        $streamIds = ZArrayHelper::map($streams, 'id', 'id');
        //vdd($streamIds);
        $ids = ZArrayHelper::map($streams, 'cpas_offer_id', 'cpas_offer_id');
        $offers = CpasOffer::find()
            ->where([
                'id' => $ids
            ])
            ->all();
        $selectoffer = ZArrayHelper::map($offers, 'id', 'title');
        $items = CpasStreamItem::find()
            ->where([
                'cpas_stream_id' => $streamIds
            ])
            ->all();
        //vdd($items);
        $land_ids = [];
        $preland_ids = [];
        foreach ($items as $item) {
            if ($item->cpas_land_id)
                $land_ids[] = $item->cpas_land_id;

            if ($item->cpas_trans)
                $preland_ids[] = $item->cpas_trans;

        }
        //$land_ids = ZArrayHelper::map($items, 'cpas_land_id', 'cpas_land_id');
        //$preland_ids = ZArrayHelper::map($items, 'cpas_trans', 'cpas_trans');
        //vdd(array_unique($land_ids));
        $cpas_lands = CpasLand::find()
            ->where([
                'id' => array_unique($land_ids)
            ])
            ->all();
        $cpas_prelands = CpasLand::find()
            ->where([
                'id' => array_unique($land_ids)
            ])
            ->all();
        $lands = ZArrayHelper::map($cpas_lands, 'id', 'title');
        $prelands = ZArrayHelper::map($cpas_prelands, 'id', 'title');
        return [
            'selectStreams' => $selectStreams,
            'lands' => $lands,
            'prelands' => $prelands,
            'selectOffers' => $selectoffer
        ];

    }

    #endregion


    #region getDayByName

    public function getDayByName()
    {
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime($today . ' -1 day'));
        $previousday = date('Y-m-d', strtotime($today . ' -2 day'));
        $week = date('Y-m-d', strtotime($today . ' -7 day'));
        list($y, $m, $d) = explode('-', $today);
        $first_day_month = $y . '-' . $m . '-01';
        $month = date('Y-m-d', strtotime($today . ' -30 day'));
        $getYear = date('Y' . '-01-01');


        return [
            'today' => $today,
            'yesterday' => $yesterday,
            'week' => $week,
            'month' => $month,
            'year' => $getYear,
            'yesterdayEnd' => $yesterday
        ];


    }



    #endregion


    #region editStreamItem

    /**
     *
     * Function  editStreamItem
     * @param CpasStreamItem $model
     * @param null $user_id
     * @return  |null
     *
     * @author JakhongirKudratov
     */


    //start|JakhongirKudratov|2020-10-11


    public function editStreamItem(CpasStreamItem $model, $user_id = null)
    {
        if ($model->isNewRecord)
            return null;

        $stream = CpasStream::findOne($model->cpas_stream_id);

        $dirto = $this->sitePath . $stream->id . '/' . $model->id;
        $dirName = $this->zipPath . $stream->user_id . '/' . $model->id;

        ZFileHelper::removeDir($dirName);
        ZFileHelper::removeDir($dirto);
        $this->createCpasSite($model);

    }

    //end|JakhongirKudratov|2020-10-11

    #endregion


    #region createStreamItem

    //start|JakhongirKudratov|2020-10-14

    /**
     *
     * Function  createStreamItem
     * created checked stream items
     * @param $ids
     * @param $user_id
     * @param $model
     * @return  bool
     * @throws \Exception
     * @author JakhongirKudratov
     *
     */
    public function createStreamItem($ids, $user_id, $model)
    {
        if (!is_array($ids))
            return false;
        $lands = collect(CpasLand::find()->all());
        $cpas_lands = $lands->whereIn(
            'id', $ids
        )->where(
            'type', 'land'
        );
        $cpas_trans = $lands->whereIn(
            'id', $ids
        )->where(
            'type', 'trans'
        );
        $cpas_trans_form = $lands->whereIn(
            'id', $ids
        )->where(
            'type', 'trans_form'
        );
        if (empty($cpas_lands->toArray()) && empty($cpas_trans_form->toArray()))
            return false;
        foreach ($cpas_lands as $land) {
            if (!empty($cpas_trans->count())) {
                foreach ($cpas_trans as $pre) {
                    $item = new CpasStreamItem();
                    $item->title = $pre->title . '_pre_and_land';
                    $item->cpas_trans = $pre->id;
                    $item->cpas_land_id = $land->id;
                    $item->user_id = $user_id;
                    $item->cpas_stream_id = $model;
                    $item->save();

                }
            } else {
                $item = new CpasStreamItem();
                $item->title = $land->title . '-land';
                $item->cpas_land_id = $land->id;
                $item->user_id = $user_id;
                $item->cpas_stream_id = $model;
                $item->save();
            }
        }
        if (!empty($cpas_trans_form->toArray())) {
            foreach ($cpas_trans_form as $form) {
                if (!empty($cpas_trans->count()) && empty($cpas_lands->toArray())) {
                    foreach ($cpas_trans as $pre) {
                        $item = new CpasStreamItem();
                        $item->title = $pre->title . '-pre';
                        $item->cpas_trans = $pre->id;
                        $item->cpas_trans_form = $form->id;
                        $item->user_id = $user_id;
                        $item->cpas_stream_id = $model;
                        $item->save();
                    }
                } else {
                    $item = new CpasStreamItem();
                    $item->title = $form->title . '-form';
                    $item->cpas_trans_form = $form->id;
                    $item->user_id = $user_id;
                    $item->cpas_stream_id = $model;
                    $item->save();
                }
            }
        }

        return true;

    }

    //end|JakhongirKudratov|2020-10-14

    #endregion

    #region setPaymentName

    public function setPaymentName(PaysPayment $model)
    {
        $value = $model->value;
        $values = '';
        foreach ($value as $key => $val) {
            $values = $key . ' ' . $val;
        }
        $model->name = $values;
        if ($model->save())
            return true;
    }

    #endregion


    #region 

    /**
     *
     * Function  generateFlowsByUser
     *userlar bo'yicha patoklani yig'ib olish
     * @return  array
     * @throws \Exception
     */

    //start|JakhongirKudratov|2020-10-21

    public function generateFlowsByUser()
    {
        $users = collect(\zetsoft\models\user\User::find()->all());
        $flows = collect(CpasStream::find()->all());
        $flowsGroup = $flows->groupBy('user_id');
        $data = [];
        foreach ($flowsGroup as $key => $value) {
            $flowsUserBy = new CpasUserFlowsForm();
            $user = $users->where('id', $key)->first();
            if (empty($user))
                continue;

            $flowsUserBy->user = $user->email;
            $flowsUserBy->amount = $value->count();
            $data[] = $flowsUserBy;

        }


        return $data;
    }

    //end|JakhongirKudratov|2020-10-21

    #endregion

    #region getAmountToUser

    /**
     *
     * Function  getAmountToUser
     * userni balance dan mablag'ni olib tashlash
     * @param PaysWithdraw $model
     * @return  bool|null
     */

    //start|JakhongirKudratov|2020-10-21

    public function getAmountToUser(PaysWithdraw $model)
    {
        if (!$this->paramGet('withdrawed')) {
            $user = \zetsoft\models\user\User::findOne($model->user_id);
            if ($user === null)
                return null;
            if ($model->amount > 0) {
                $user->balance = $user->balance - $model->amount;
                if ($user->save())
                    return true;
                return null;
            }
            return false;
        }
    }

    //end|JakhongirKudratov|2020-10-21

    #endregion

    #region

    public function generateHistory(PaysWithdraw $model)
    {

        if ($model->status === 'ok') {
            $history = new CpasPaysHistory();
            $history->user_id = $model->user_id;
            $history->balance = $model->amount;
            $history->pays_payment_id = $model->pays_payment_id;
            $history->userBy = $this->userIdentity()->id;
            if ($history->save())
                return true;

        }

        return null;
    }


    #endregion


    #region deleteAllRelations

    /**
     *
     * Function  deleteItemRelations
     * delete all CpasOfferItem relations
     * @param CpasOfferItem $model
     * @return  bool
     * @throws \Exception
     */

    //start|JakhongirKudratov|2020-10-22

    public function deleteItemRelations(CpasOfferItem $model)
    {
        $cpas_stream_items = collect(CpasStreamItem::find()->all());
        $lands = CpasLand::find()
            ->where([
                'cpas_offer_item_id' => $model->id
            ])->all();

        foreach ($lands as $land) {
            $streamItems = $cpas_stream_items->filter(function ($value, $key) use ($land) {
                if (($value->cpas_land_id == $land->id || $value->cpas_trans === $land->id || $value->cpas_trans_form === $land->id))
                    return $value;
            });

            foreach ($streamItems as $item) {
                $item->delete();
            }
            $land->delete();
        }

        return true;
    }

    //end|JakhongirKudratov|2020-10-22


    public function testDeleteAllRelations()
    {
        $model = CpasOfferItem::findOne(58);
        $this->deleteItemRelations($model);
    }
    #endregion
}

