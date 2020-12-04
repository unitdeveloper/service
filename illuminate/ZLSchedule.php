<?php

/**
 * Author: Sardor
 * Date:    07.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 */

namespace zetsoft\service\illuminate;


                                    

use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

use Illuminate\Console\Scheduling\Schedule;
class ZLSchedule extends ZFrame
{

//    protected function schedule(Schedule $schedule){
//        $schedule->exec('ping 8.8.8.8')->everyMinute();
//    }

    public function index(){
        $schedule = new Schedule();
        $schedule->exec('ping 8.8.8.8')->everyMinute();
    }
    
}
