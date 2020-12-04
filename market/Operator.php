<?php

/**
 * Author: Jobir
 * Date:    07.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 */

namespace zetsoft\service\market;

use Mpdf\Tag\Time;
use Nette\Utils\DateTime;
use zetsoft\dbitem\shop\CompanyCardItem;
use zetsoft\dbitem\shop\ProductItem;
use zetsoft\dbitem\shop\PropertyItem;
use zetsoft\dbitem\data\ALLApp;
use zetsoft\dbitem\data\Config;
use zetsoft\dbitem\data\Form;
use zetsoft\models\calls\CallsStats;
use zetsoft\models\calls\CallsStatus;
use zetsoft\models\calls\CallsStatusTime;
use zetsoft\models\shop\ShopBrand;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopCategory;
use zetsoft\models\user\UserCompany;
use zetsoft\models\place\PlaceCountry;
use zetsoft\models\shop\ShopElement;
use zetsoft\models\shop\ShopOption;
use zetsoft\models\shop\ShopOptionType;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopProduct;
use zetsoft\models\place\PlaceRegion;
use zetsoft\models\shop\ShopShipment;
use zetsoft\models\user\User;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZUrl;
use zetsoft\system\kernels\ZFrame;
use zetsoft\widgets\former\ZFormWidget;
use zetsoft\widgets\incores\ZIRadioGroupWidget;
use zetsoft\widgets\incores\ZMCheckboxGroupWidget;
use zetsoft\widgets\incores\ZMCheckboxGroupWidget2;
use zetsoft\widgets\incores\ZMRadioWidget;
use zetsoft\widgets\inputes\ZHCheckboxButtonGroupWidget;
use zetsoft\widgets\inputes\ZHInputWidget;
use zetsoft\widgets\inputes\ZHRadioButtonGroupWidget;
use zetsoft\widgets\inputes\ZKSelect2Widget;
use zetsoft\widgets\inputes\ZKSliderIonWidget;
use zetsoft\widgets\navigat\ZGAccordionWidget;
use zetsoft\widgets\values\ZFormViewWidget;
use function PHPUnit\Framework\returnArgument;
use function Spatie\array_keys_exist;
use zetsoft\former\shop\ShopOperatorForm;

class Operator extends ZFrame
{
    #region shipmentData
    public function testshipmentData()
    {
        $data = $this->shipmentData();
        vd($data);
    }

    public function shipmentData($operator_id)
    {
        if ($operator_id === null) return [];
        $orders = ShopOrder::find()
            ->where([
                'operator' => $operator_id
            ])
            ->all();
        if (empty($orders)) return [];
        $data = [];
        /** @var ShopOrder $order */
        foreach ($orders as $order) {
            $shipment = ShopShipment::findOne(['order_id' => $order->id]);
            $user = User::findOne($order->user_id);
            if ($user === null) continue;
            if ($shipment === null) continue;
            $form = new ShopOperatorForm();
            $form->user_id = $user->name;
            //$form->contact_info = $order->contact_info;
            $form->full_name = $order->contact_info['full_name'];
            $form->phone = $order->contact_info['phone'];
            $form->email = $order->contact_info['email'];
            $form->comment = $order->comment;
            $form->core_adress_id = $order->core_adress_id;
            $form->shipment_type = $shipment->shipment_type;
            $form->payment_type = $shipment->payment_type;
            $form->courier_id = $shipment->courier_id;
            $form->price = $order->price;
            $form->total_price = $shipment->price;
            $form->shop_coupon_id = $order->shop_coupon_id;
            $data[] = $form;

        }

        return $data;
    }

    #endregion

    #region getInfoOrder Javohir
    public function setOrdersToAgent($agent, $checkList, $status = null)
    {

        $checkArr = json_decode($checkList, true);

        $result_array = [];

        foreach ($checkArr as $each_number)
            $result_array[] = (int)$each_number;

        if (empty($result_array))
            return $agent . $checkList . 'none';

        $ids = ShopOrder::find()
            ->where(['id' => $result_array])
            ->all();

        foreach ($ids as $id) {
            $id->operator = (int)$agent;
            $id->status_callcenter = $status;
            $id->save();
        }

        return $agent . $checkList;
    }
    #endregion

    #region getUserByRole   Javohir
    public function getUserByRole($role)
    {
        $operators = User::find()
            ->where([
                'role' => $role,
            ])->asArray()->all();

        return $operators;
    }
    #endregion


    #region
    public function swagentStatusChange($model)
    {


        $Newtime = new CallsStats();

        $start = new DateTime(date('Y-m-d h:i:s', time()));
        $stop = new DateTime($model->modified_at);


        $difference = $start->diff($stop);

        $difTime = $difference->h . ":" . $difference->i . ":" . $difference->s;

        $Newtime->user_id = $model->id;
        $Newtime->status = $model->status;
        $Newtime->time = $difTime;

        $Newtime->save();

    }

    #endregion Xolmat Ravshanov
    public function setCallTime($orderId)
    {
        $orderId = (int)$orderId;
        $shop_order = ShopOrder::findOne($orderId);
        $shop_order->called_time = date('Y-m-d H:i:s');
        $shop_order->save();
        return true;
    }
    #region Statistics

    /**
     *
     * Function  beforeSave
     * @param User $model
     * @todo save new record on user status change in CallsStatus
     * @author Daho
     */
    public function beforeSave(User $model)
    {
        $stat = new CallsStatus();
        $oldStat = $model->oldAttributes['status'];
        if ($oldStat === $model->status)
            return null;
        $user_id = $model->id;
        $oldModel = CallsStatus::find()
            ->where([
                'user_id' => $user_id,
                'status' => $oldStat
            ])
            ->orderBy(['id' => SORT_ASC])
            ->limit(1)
            ->one();

        $stat->user_id = $model->id;
        $stat->status = $model->oldAttributes['status'];
        if ($oldModel !== null) {
            $current = new DateTime(date('Y-m-d H:i:s', time()));
            // vdd($current);
            $modified_at = new DateTime($oldModel->modified_at);
            $diff = $current->diff($modified_at);
            $stat->time = $diff->h . ':' . $diff->m . ':' . $diff->s;
        } else
            $stat->time = '00:00:00';
        $stat->save();

    }

    public function callsStatusTimeTest()
    {
        $model = User::findOne(103);
        $this->callsStatusTime($model);
    }

    /**
     *
     * Function  callsStatusTime
     * @todo save/update record on user status change in CallsStatusTime
     * @author Daho
     *
     */
    public function callsStatusTime(User $model)
    {
        $old_stat =
        /*if ($model->status === $model->oldAttributes['status'])
           return null;*/

        /** @var DateTime $date */
        $date = new DateTime();

        $stat = CallsStatusTime::findOne([
            'date' => $date,
            'user_id' => $model->id
        ]);

        $status = $model->status;

        if ($stat === null) {
            $oldStat = CallsStatusTime::findOne([
                'date' => Az::$app->cores->date->dateTime('-24 hours'),
                'user_id' => $model->id
            ]);

            $stat = new CallsStatusTime();
            $stat->date = $date;
            $stat->user_id = $model->id;
            $stat->online = '00:00:00';
            $stat->offline = '00:00:00';
            $stat->dnd = '00:00:00';
            $stat->lunch = '00:00:00';
            $stat->away = '00:00:00';

            if ($oldStat !== null) {
                $oldStat->$status = $this->calculateTime($oldStat->modified_at, $oldStat->$status, Az::$app->cores->date->dateTime_Day_End('-1 days'));

                $stat->$status = $this->calculateTime(Az::$app->cores->date->dateTime_Day_Start(), $stat->$status, $date);
                $oldStat->save();
            }
            $stat->save();
            return null;
        }

        $stat->$status = $this->calculateTime($stat->modified_at, $stat->$status, $date);
        $stat->update();


    }

    public function calculateTime($dateTime1, $dateTime2, $current)
    {
        $current = new DateTime($current);
        $diff = $current->diff(new DateTime($dateTime1));

        $sec = $diff->h * 3600 + $diff->i * 60 + $diff->s;

        $before = new DateTime($dateTime2);

        $before->add(new \DateInterval('PT' . $sec . 'S'));

        return $before;
    }

    #endregion

    #region Xolmat Ravshanov
    public function setAgentStatus($agentId, string $status)
    {

        $agent = User::findOne($agentId);

        $agent->configs->rules = [
            [
                validatorSafe
            ]
        ];

        $agent->status = $status;

        $agent->save();
    }
    #endregion
}
