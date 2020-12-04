<?php


namespace zetsoft\service\tests;


use zetsoft\system\kernels\ZFrame;

class Micro extends ZFrame
{
    public static $timeStart = 0;

    public static function start()
    {
        self::$timeStart = microtime(true);
        return self::$timeStart;
    }

    public static function end()
    {
        $timeEnd = microtime(true);
        if (self::$timeStart === 0)
            return 'Timer Not started';
        else
            return number_format($timeEnd - self::$timeStart, 3);
    }
}
