<?php

/**
 *
 *
 * Author:  Asror Zakirov
 *
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\process;

use Symfony\Component\Stopwatch\StopwatchEvent;
use zetsoft\system\kernels\ZFrame;

class StopWatch extends ZFrame
{
    public $event;

    #region Test

    public function test()
    {
        $this->startStop();
    }

    public function startStop()
    {
        $this->watchStart('test');
        sleep(3);

        $this->event->lap('test');
        sleep(1);
        $this->event->lap('test');
        sleep(2);
        $gg = $this->watchStop('test');
        vd($gg->getPeriods()[1]->getDuration());
//        vd((string)$this->stop('test'));
//        vd($gg->getEndTime());
//        vd($gg->getDuration());
//        vd($gg->getCategory());
    }

    #endregion

    public function start($event, $category = null)
    {
        $this->event = new \Symfony\Component\Stopwatch\Stopwatch($event);
        return $this->event->start($event, $category);
    }

    public function lap($event)
    {
        return $this->event->lap($event);
    }

    public function stop($event)
    {
        /** @var StopwatchEvent $data */
        $data = $this->event->stop($event);
        $time = $data->getDuration();
        return $time;
    }

}
