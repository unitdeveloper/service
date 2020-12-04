<?php

/**
 *
 *
 * Author: Mirshod Ibodov && Dilmurod Axmadov
 *
 */

namespace zetsoft\service\calls;


use zetsoft\former\calls\CallsStatsAgentForm;
use zetsoft\former\calls\CallsStatsForm;
use zetsoft\former\eyuf\EyufStatsOrdersForm;
use zetsoft\models\calls\CallsCdr;
use zetsoft\models\calls\CallsStatusTime;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\user\UserCompany;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;


class Stats extends ZFrame
{

    /**
     * Function  agentWorkedTime
     * @param null $from
     * @param null $to
     * @return  array
     * @throws \Exception
     *
     * @todo Get data agents worked time for use Dyna and Chart
     *
     * @author Daho
     */

    public function agentWorkedTime($from = null, $to = null)
    {

        if ($from === null)
            $from = Az::$app->cores->date->dateTime('-24 hours');

        if ($to === null)
            $to = Az::$app->cores->date->dateTime();

        $data = [];

        $agents = \zetsoft\models\user\User::find()
            ->where([
                'role' => 'agent'
            ])
            ->all();

        foreach ($agents as $agent) {

            $elem = new CallsStatsAgentForm();
            $elem->name = $agent->title;
            $elem->online = $this->workHoursByStatus($agent->id, $from, $to, 'online');
            $elem->offline = $this->workHoursByStatus($agent->id, $from, $to, 'offline');
            $elem->away = $this->workHoursByStatus($agent->id, $from, $to, 'away');
            $elem->dnd = $this->workHoursByStatus($agent->id, $from, $to, 'dnd');
            $elem->lunch = $this->workHoursByStatus($agent->id, $from, $to, 'lunch');
            $elem->process = $this->workHoursByStatus($agent->id, $from, $to, 'process');

            $data[] = $elem;

        }

        return $data;
    }

    /**
     *
     * Function  workHoursByStatus
     * @param $user_id
     * @param $from
     * @param $to
     * @param $status
     * @return  string
     * @throws \Exception
     * @todo get an agent status change period
     * @author Daho
     */
    public function workHoursByStatus($user_id, $from = null, $to = null, $status)
    {

        if ($from === null)
            $from = Az::$app->cores->date->dateTime('-24 hours');

        if ($to === null)
            $to = Az::$app->cores->date->dateTime();

        $records = CallsStatusTime::find()
            ->where([
                'between', 'date', $from, $to
            ])
            ->andWhere([
                'user_id' => $user_id,
            ])
            ->asArray()
            ->all();

        $time = '00:00:00';

        foreach ($records as $record) {
            $time = Az::$app->cores->date->addTime($time, $record[$status]);
        }

        return $time;

    }


    /*
     * Author: Mirshod Ibodov && Dilmurod Axmadov
     * @return int  
     */

    public function agentCallStast($dateOne = null, $dateTwo = null)
    {

        $return = [];

        $operators = \zetsoft\models\user\User::find()
            ->where([
                'role' => 'agent'
            ])
            ->asArray()
            ->all();

        foreach ($operators as $operator) {

            $form = new CallsStatsForm();

            $form->name = $operator['title'];

            $form->number = $operator['number'];

            $form->answered = $this->agentCalls($operator['number'], 'ANSWERED', $dateOne, $dateTwo);

            $form->no_answer = $this->agentCalls($operator['number'], 'NO ANSWER', $dateOne, $dateTwo);

            //$form->cancel = $this->agentCalls($operator['number'], 'CANCEL', $dateOne, $dateTwo);

            $form->busy = $this->agentCalls($operator['number'], 'BUSY', $dateOne, $dateTwo);

            $return[] = $form;

        }

        return $return;
    }

    /*
     * Author: Xolmat Ravshanov
     * @return array
     */

    public function agentCalls($operatorNumber, $disposition = 'ANSWERED', $dateOne = null, $dateTwo = null)
    {
        if ($operatorNumber === null)
            return 0;

        if ($dateOne === null)
            $dateOne = Az::$app->cores->date->dateTime('-24 hours');

        if ($dateTwo === null)
            $dateTwo = Az::$app->cores->date->dateTime();

        $model = CallsCdr::find()
            ->where([
                'between', 'calldate', $dateOne, $dateTwo
            ])
            ->andWhere([
                'disposition' => $disposition,
                'number' => $operatorNumber,
            ])
            ->count();

        return $model;

    }

    /**
     *  Author: Dilmurod Axmadov
     * Function  operatorCalls
     * @param $operatorNumber
     * @param null $from
     * @param null $to
     * @return  array|\zetsoft\system\actives\ZActiveQuery[]
     * @throws \Exception
     */

    public function operatorCalls($operatorNumber, $disposition, $from = null, $to = null)
    {

        if ($from === null)
            $from = Az::$app->cores->date->dateTime('-24 hours');

        if ($to === null)
            $to = Az::$app->cores->date->dateTime();

        $query = CallsCdr::find()
            ->where([
                'between', 'calldate', $from, $to
            ])
            ->andWhere([
                'number' => $operatorNumber,
                'disposition' => $disposition
            ]);


        return $query;
    }

    public function getOrderCountByMarketTest()
    {

        $dateOne = Az::$app->cores->date->dateTime('-2400 hours');
        $dateTwo = Az::$app->cores->date->dateTime();
        vdd($this->getOrderCountByMarket($dateOne, $dateTwo));
    }

    public function getOrderCountByMarket($dateOne = null, $dateTwo = null, $agent_id = null)
    {

        if ($dateOne === null)
            $dateOne = Az::$app->cores->date->dateTime('-24 hours');

        if ($dateTwo === null)
            $dateTwo = Az::$app->cores->date->dateTime();


        $query = ShopOrder::find()
            ->where([
                'between', 'called_time', $dateOne, $dateTwo
            ]);

        if ($agent_id !== null)
            $query = $query->andWhere([
                'operator' => $agent_id
            ]);

        $shop_orders = $query->all();

        $ids = [];

        foreach ($shop_orders as $shop_order) {
            $ids[] = $shop_order->id;
        }

        $items = ShopOrderItem::find()
            ->where([
                'shop_order_id' => $ids
            ])
            ->asArray()
            ->all();

        $order_items = collect($items)->groupBy('user_company_id');

        $data = [];
        foreach ($order_items as $key => $order_item) {
            $stat = new EyufStatsOrdersForm();
            if (empty($key))
                continue;
            $stat->market = UserCompany::findOne($key)->title;
            $values = collect($order_item)->groupBy('shop_order_id');
            $stat->new = $this->countByStatus($values, 'new');
            $stat->approved = $this->countByStatus($values, 'approved');
            $stat->cancel = $this->countByStatus($values, 'cancel');
            $stat->not_ordered = $this->countByStatus($values, 'not_ordered');
            $stat->incorrect = $this->countByStatus($values, 'incorrect');
            $stat->ring = $this->countByStatus($values, 'ring');
            $stat->autodial = $this->countByStatus($values, 'autodial');
            $stat->on_performance = $this->countByStatus($values, 'on_performance');
            $stat->check = $this->countByStatus($values, 'check');
            $stat->all = count($order_item);

            $data[] = $stat;
        }


        return $data;

    }

    private function countByStatus($collection, $status)
    {
        $count = 0;
        foreach ($collection as $key => $value) {
            if ((ShopOrder::findOne($key))->status_callcenter === $status)
                $count += count($value);
        }

        return $count;
    }


}
