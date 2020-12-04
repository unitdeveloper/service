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

namespace zetsoft\service\utility;

use ChromePhp;
use Exception;
use Monolog\Handler\ChromePHPHandler;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\PHPConsoleHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Logger;
use Monolog\Processor\ProcessIdProcessor;
use Monolog\Processor\TagProcessor;
use PC;
use PhpConsole\Handler;
use PhpConsole\Helper;
use Tracy\Debugger;
use Tracy\Dumper;
use yii\db\Command;
use yii\db\Transaction;
use zetsoft\cncmd\tester\Test12Controller;
use zetsoft\system\actives\ZActiveQuery;
use zetsoft\system\actives\ZActiveRecord;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\monolog\formatters\ZConsoleFormatter;
use zetsoft\system\monolog\formatters\ZLineFormatter;
use zetsoft\system\monolog\handlers\ZTelegramHandler;
use zetsoft\system\monolog\processors\ZMemoryUsageProcessor;
use zetsoft\system\monolog\processors\ZServerProcessor;
use zetsoft\system\monolog\processors\ZWebProcessor;
use zetsoft\system\targets\ZTelegram;


class TerrabaytsMonolog extends ZFrame
{

    #region Vars

    /**
     *
     * @var array Default config vars for all methods
     */
    public $continue;
    public $coreAll = [
        'name' => 'monolog', //The logging channel, a simple descriptive name that is attached to all log records
        'context' => [], //The log context
        'level' => Logger::INFO, //Default level
        'prefix' => null,
        'dateTimeFormat' => 'd-m-Y H:i:s', //datetime format
        'isExtra' => false,
        'isTrace' => true,
        'bubble' => true, //Whether the messages that are handled can bubble up the stack or not
        'filePermission' => null, //Optional file permissions (default (0644) are only for owner
        'useLocking' => false, //Try to lock log file before doing any writes
        'tags' => ['tags', 'for', 'logs'],
        'customProcessorKey' => null,
        'customProcessorVar' => [],
    ];
    public static $traceLevel = 5;
    public $logFormats = [
        'file' => "%message%\n%trace%\n\n%webdata%\n\n%serverdata%\n%prefix% | %level_name% | %datetime% | %processors%\n%extra%\n-------------------------------------------\n",
        'telegram' => "%app%\n<i>Message:</i> <b>%message%</b>\n\n<i>%trace%\n\n</i>%webdata%\n\n%serverdata%\n\n%prefix% | #%level_name% | %datetime% | %processors%\n<code>%extra%\n</code>",
        'cli' => "\n%message%\n%trace%\n%serverdata%\n%prefix% | %level_name% | %datetime% | %processors%\n%extra%\n",
    ];

    public $processors = [
        ZWebProcessor::class => true,
        ZServerProcessor::class => true,
        ProcessIdProcessor::class => true,
        ZMemoryUsageProcessor::class => true,
        [
            'enabled' => true,
            'tags' => true,
            'item' => TagProcessor::class,
        ],
    ];

    public $excepts = [
        Transaction::class,
        ZActiveRecord::class,
        ZActiveQuery::class,
        Test12Controller::class,
        "yii\db\BaseActiveRecord",
    ];

    public $levels = [
        Logger::EMERGENCY => 'emergency',
        Logger::CRITICAL => 'critical',
        Logger::ERROR => 'error',
        Logger::WARNING => 'warning',
        Logger::INFO => 'info',
        Logger::DEBUG => 'debug',
        Logger::API => 'api',
        Logger::ALERT => 'alert',
        Logger::NOTICE => 'notice',
    ];

    /**
     *
     * @var array Config vars for logging to file
     */
    public $logFile = [
        'enabled' => true,
        'enabledLevels' => [
            'web_emergency' => true,
            'web_critical' => true,
            'web_error' => true,
            'web_warning' => false,
            'web_info' => false,
            'web_debug' => false,

            'cmd_emergency' => true,
            'cmd_critical' => true,
            'cmd_error' => true,
            'cmd_warning' => false,
            'cmd_info' => false,
            'cmd_debug' => false,
        ],
        'enabledTraceLevels' => [
            'web_emergency' => 10,
            'web_critical' => 10,
            'web_error' => 10,
            'web_warning' => 3,
            'web_info' => 0,
            'web_debug' => 0,

            'cmd_emergency' => 10,
            'cmd_critical' => 10,
            'cmd_error' => 10,
            'cmd_warning' => 3,
            'cmd_info' => 0,
            'cmd_debug' => 0,
        ],
        'isTrace' => true,
        'isExtra' => false,
        'file' => null,
    ];

    /**
     *
     * @var array Config vars for logging to file
     */
    public $logConsole = [
        'enabled' => true,
        'enabledLevels' => [
            'cmd_emergency' => true,
            'cmd_critical' => true,
            'cmd_error' => true,
            'cmd_warning' => true,
            'cmd_info' => true,
            'cmd_debug' => true,
        ],
        'enabledTraceLevels' => [
            'cmd_emergency' => 10,
            'cmd_critical' => 10,
            'cmd_error' => 10,
            'cmd_warning' => 10,
            'cmd_info' => 0,
            'cmd_debug' => 0,
        ],
        'isTrace' => true,
        'isExtra' => false,
    ];

    /**
     *
     * @var array Config vars for logging to file per day
     */
    public $logFilePerDay = [
        'enabled' => true,
        'enabledLevels' => [
            'web_emergency' => true,
            'web_critical' => true,
            'web_error' => true,
            'web_warning' => true,
            'web_info' => false,
            'web_debug' => false,

            'cmd_emergency' => true,
            'cmd_critical' => true,
            'cmd_error' => true,
            'cmd_warning' => true,
            'cmd_info' => false,
            'cmd_debug' => false,
        ],
        'enabledTraceLevels' => [
            'web_emergency' => 10,
            'web_critical' => 10,
            'web_error' => 10,
            'web_warning' => 3,
            'web_info' => 0,
            'web_debug' => 0,

            'cmd_emergency' => 10,
            'cmd_critical' => 10,
            'cmd_error' => 10,
            'cmd_warning' => 3,
            'cmd_info' => 0,
            'cmd_debug' => 0,
        ],
        'isTrace' => true,
        'isExtra' => false,
        'file' => Root . '/storing/loggers/%prefix%/monolog/logs/monolog-log',
        'max_files' => 0,
    ];

    /**
     *
     * @var array Config vars for syslog
     */
    public $logSyslog = [
        'enabled' => false,
        'enabledLevels' => [
            'web_emergency' => true,
            'web_critical' => true,
            'web_error' => true,
            'web_warning' => true,
            'web_info' => true,
            'web_debug' => true,

            'cmd_emergency' => true,
            'cmd_critical' => true,
            'cmd_error' => true,
            'cmd_warning' => true,
            'cmd_info' => true,
            'cmd_debug' => true,
        ],
        'facility' => LOG_USER,
        'host' => '127.0.0.1',
        'port' => 514
    ];

    /**
     *
     * @var array Config vars for Telegram
     */
    public $logTelegram = [
        'enabled' => false,
        'enabledLevels' => [
            'web_emergency' => false,
            'web_critical' => false,
            'web_error' => false,
            'web_warning' => false,
            'web_info' => false,
            'web_debug' => false,

            'cmd_emergency' => false,
            'cmd_critical' => false,
            'cmd_error' => false,
            'cmd_warning' => false,
            'cmd_info' => false,
            'cmd_debug' => false,
        ],
        'enabledTraceLevels' => [
            'web_emergency' => 10,
            'web_critical' => 10,
            'web_error' => 10,
            'web_warning' => 3,
            'web_info' => 0,
            'web_debug' => 0,

            'cmd_emergency' => 10,
            'cmd_critical' => 10,
            'cmd_error' => 10,
            'cmd_warning' => 3,
            'cmd_info' => 0,
            'cmd_debug' => 0,
        ],
        'isTrace' => true,
        'isExtra' => false,
        'bot_api' => '280253273:AAG5oiNEFPvTpy8LdnX4RPL1reeZCVx4uKM',
        'chat_id' => '-1001176048898',    // Target Chat ID
        'use_curl' => true,    // Use cURL or not? (default: use when available)
        'timeout' => 10,   // Timeout for API requests
        'verify_peer' => true,   // Verify SSL certificate or not? (development/debugging)
    ];

    #endregion

    #region Core


    public function init()
    {
        parent::init();
        global $boot;

        $this->coreAll['prefix'] = $boot->isCLI() ? 'cmd' : 'web';
        $this->logConsole['enabled'] = $boot->isCLI();
    }


    /**
     *
     * Main function
     *
     * @param string $message
     * @param int $level
     * @throws \yii\base\Exception
     */
    public function log($message, $level = null)
    {
        if ($this->logConsole['enabled'])
            $this->cliLog($message, $level);


        if ($this->logFilePerDay['enabled'])
            $this->filePerDayLog($message, $level);
        /*
                 if ($this->logFile['enabled'])
                    $this->fileLog($message, $level);


                if ($this->logSyslog['enabled'])
                    $this->syslogLog($message, $level);

                if ($this->logTelegram['enabled'])
                    $this->telegramLog($message, $level);*/

    }

    #endregion

    #region Telegram

    /**
     * Sends log message to Telegram
     *
     * @param string $message
     * @param int $level
     *
     * @return void
     * @throws \yii\base\Exception
     */
    public function telegramLog($message, $level = null)
    {
        if ($this->isExcept())
            return true;

        $level = $this->setLevel($level);
        if (!$this->logTelegram['enabledLevels'][$this->coreAll['prefix'] . '_' . $this->levels[$level]])
            return null;

        $trace = self::getTrace($this->logTelegram['enabledTraceLevels'][$this->coreAll['prefix'] . '_' . $this->levels[$level]]);
        $logger = new Logger($this->coreAll['name']);

        $handler = new ZTelegramHandler(
            $this->logTelegram['bot_api'],
            $this->logTelegram['chat_id'],
            $level,
            $this->coreAll['bubble'],
            $this->logTelegram['use_curl'],
            $this->logTelegram['timeout'],
            $this->logTelegram['verify_peer']
        );
        $handler->setFormatter(
            new ZLineFormatter(
                $this->logFormats['telegram'],
                $this->coreAll['dateTimeFormat'],
                $this->logTelegram['isExtra'],
                $this->logTelegram['isTrace'],
                $trace
            )
        );

        $logger->pushHandler($handler);
        $this->pushProcessors($logger, $level, true);
        $logger->addRecord($level, $message, $this->coreAll['context']);
    }

    #endregion

    #region File

    /**
     * Writes logs in one file.
     *
     * @param string $message
     * @param int $level
     *
     * @return void
     * @throws \yii\base\Exception
     */
    public function fileLog($message, $level = null)
    {
        global $boot;

        if ($this->isExcept())
            return null;

        $level = $this->setLevel($level);
        if (!$this->logFile['enabledLevels'][$this->coreAll['prefix'] . '_' . $this->levels[$level]])
            return null;

        $this->logFile['file'] = $boot->folderLoggers . '/monolog/' . $this->levels[$level] . '/data.log';
        $trace = self::getTrace($this->logFile['enabledTraceLevels'][$this->coreAll['prefix'] . '_' . $this->levels[$level]]);
        $logger = new Logger($this->coreAll['name']);

        $handler = new StreamHandler(
            $this->logFile['file'],
            $level,
            $this->coreAll['bubble'],
            $this->coreAll['filePermission'],
            $this->coreAll['useLocking']
        );
        $handler->setFormatter(
            new ZLineFormatter(
                $this->logFormats['file'],
                $this->coreAll['dateTimeFormat'],
                $this->logFile['isExtra'],
                $this->logFile['isTrace'],
                $trace
            )
        );

        $logger->pushHandler($handler);
        $this->pushProcessors($logger, $level);
        $logger->addRecord($level, $message, $this->coreAll['context']);
    }

    /**
     * Writes logs to new file each day
     *
     * @param string $message
     * @param int $level
     *
     * @return void
     * @throws \yii\base\Exception
     */
    public function filePerDayLog($message, $level = null)
    {
        global $boot;

        if ($this->isExcept())
            return null;

        $level = $this->setLevel($level);
        if (!$this->logFilePerDay['enabledLevels'][$this->coreAll['prefix'] . '_' . $this->levels[$level]])
            return null;

        $this->logFilePerDay['file'] = $boot->folderLoggers . '/monolog/' . $this->levels[$level] . '/logs/data.log';
        $trace = self::getTrace($this->logFilePerDay['enabledTraceLevels'][$this->coreAll['prefix'] . '_' . $this->levels[$level]]);
        $logger = new Logger($this->coreAll['name']);

        $handler = new RotatingFileHandler(
            $this->logFilePerDay['file'],
            $this->logFilePerDay['max_files'],
            $level,
            $this->coreAll['bubble'],
            $this->coreAll['filePermission'],
            $this->coreAll['useLocking']
        );
        $handler->setFormatter(
            new ZLineFormatter(
                $this->logFormats['file'],
                $this->coreAll['dateTimeFormat'],
                $this->logFilePerDay['isExtra'],
                $this->logFilePerDay['isTrace'],
                $trace
            )
        );

        $logger->pushHandler($handler);
        $this->pushProcessors($logger, $level);
        $logger->addRecord($level, $message, $this->coreAll['context']);
    }

    /**
     * Writes logs with php function error_log
     *
     * @param string $message
     * @param int $level
     *
     * @return void
     * @throws \yii\base\Exception
     */
    public function filePHPErrorLog($message, $level = null)
    {

        $level = $this->setLevel($level);

        $logger = new Logger($this->coreAll['name']);

        $handler = new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, $level, $this->coreAll['bubble']);
        $handler->setFormatter(new ZLineFormatter(null, $this->coreAll['dateTimeFormat']));

        $logger->pushHandler($handler);

        $this->pushProcessors($logger, $level);

        $logger->addRecord($level, $message, $this->coreAll['context']);
    }

    #endregion

    #region STDIN

    /**
     * Records logs to the CLI.
     *
     * @param string $message
     * @param int $level
     *
     * @return void
     * @throws \yii\base\Exception
     */
    public function cliLog($message, $level = null)
    {
        if ($this->isExcept())
            return null;

        $level = $this->setLevel($level);
        if (!$this->logConsole['enabledLevels'][$this->coreAll['prefix'] . '_' . $this->levels[$level]])
            return null;

        $trace = self::getTrace($this->logConsole['enabledTraceLevels']['cmd_' . $this->levels[$level]]);
        $logger = new Logger($this->coreAll['name']);

        $handler = new StreamHandler(
            STDOUT,
            $level,
            $this->coreAll['bubble'],
            $this->coreAll['filePermission'],
            $this->coreAll['useLocking']
        );
        $handler->setFormatter(
            new ZConsoleFormatter(
                $this->logFormats['cli'],
                $this->coreAll['dateTimeFormat'],
                $this->logConsole['isExtra'],
                $this->logConsole['isTrace'],
                $trace
            )
        );

        $logger->pushHandler($handler);
        $this->pushProcessors($logger, $level);
        $logger->addRecord($level, $message, $this->coreAll['context']);
    }

    #endregion

    #region Syslog

    /**
     * Logs records to the syslog
     *
     * @param string $message
     * @param int $level
     *
     * @return void
     * @throws \yii\base\Exception
     */
    public function syslogLog($message, $level = null)
    {

        $level = $this->setLevel($level);

        $logger = new Logger($this->coreAll['name']);

        $syslogHandler = new SyslogUdpHandler($this->logSyslog['host'], $this->logSyslog['port'], $this->logSyslog['facility'], $level, $this->coreAll['bubble'], 'zettest', SyslogUdpHandler::RFC3164);

        $logger->pushHandler($syslogHandler);
        $logger->addRecord($level, $message, $this->coreAll['context']);
    }

    #endregion

    #region BrowserConsole

    /**
     * PHP Console, providing inline console and notification popup messages within Chrome.
     *
     * @param string $message
     * @param int $level
     *
     * @return void
     * @throws \yii\base\Exception
     */
    public function browserPhpConsoleLog($message, $level = null)
    {

        $level = $this->setLevel($level);

        $logger = new Logger($this->coreAll['name']);

        $phpConsoleHandler = new PHPConsoleHandler([], null, $level);

        $logger->pushHandler($phpConsoleHandler);
        $logger->addRecord($level, $message, $this->coreAll['context']);
    }

    /**
     * PHP Console, providing inline console and notification popup messages within Chrome.
     * For debugging with console.log() in php.
     *
     * @return void
     */
    /*public function browserPhpConsoleDebug($item) {
        $logger = new Logger($this->$this->coreAll['name']);
        $phpConsoleHandler = new PHPConsoleHandler();
        $logger->pushHandler($phpConsoleHandler);

        PC::debug($item);
    }*/

    /**
     * FirePHP, providing inline console messages within FireBug.
     * Doesn't work.
     *
     * @param string $message
     * @param int $level
     *
     * @return void
     * @throws \yii\base\Exception
     */
    public function browserFirePhpLog($message, $level = null)
    {

        $level = $this->setLevel($level);

        $logger = new Logger($this->coreAll['name']);

        $firePHPHandler = new FirePHPHandler();

        $logger->pushHandler($firePHPHandler);
        $logger->addRecord($level, $message, $this->coreAll['context']);
    }

    /**
     * ChromePHP, providing inline console messages within Chrome.
     *
     * @param string $message
     * @param int $level
     *
     * @return void
     * @throws \yii\base\Exception
     */
    public function browserChromePhpLog($message, $level = null)
    {

        $level = $this->setLevel($level);

        $logger = new Logger($this->coreAll['name']);

        $chromePHPHandler = new ChromePHPHandler($level);

        $logger->pushHandler($chromePHPHandler);
        $logger->addRecord($level, $message, $this->coreAll['context']);
    }

    #endregion

    #region Debug

    public function chromeLoggerDebug()
    {
        $args = func_get_args();
        if (count($args) === 1) $args = $args[0];
        ChromePhp::table($args);
    }

    /*public function phpConsoleDebug($var) {
        $handler = Handler::getInstance();
        $handler->start();
        Helper::register();
        PC::debug($var);
    }*/

    public function tracyToolbarDebug()
    {
        Debugger::enable(Debugger::DETECT, Root . '/control/cmd/ALL/tester');

        /*Debugger::getBar()->addPanel($panel = new Panel());
        $panel->setPanelTitle('For deployment');
        $panel->setIconColor('red');*/

        Debugger::$strictMode = true;
        Debugger::log('Unexpected error');

        $arr = [10, 20.2, true, null, 'hello'];
        //vd($arr);
        Debugger::$showLocation = Dumper::LOCATION_LINK;
        //Debugger::dump($arr);
        bdump($arr);

        Debugger::fireLog('Hello World'); // send string into FireLogger console
        //Debugger::fireLog($_SERVER); // or even arrays and objects
        Debugger::fireLog(new Exception('Test Exception')); // or exceptions

    }

    #endregion

    #region Utils

    public static function getTrace($traceLevel = null)
    {

        if ($traceLevel === null)
            $traceLevel = self::$traceLevel;

        $traces = [];
        if ($traceLevel > 0) {
            $count = 0;
            $ts = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            foreach ($ts as $trace) {
                if (
                    isset($trace['file'], $trace['line']) &&
                    strpos($trace['file'], YII2_PATH) !== 0 &&
                    strpos($trace['file'], Monolog::class) === false &&
                    strpos($trace['file'], 'vendor\monolog\monolog\src') === false &&
                    strpos($trace['file'], 'system\Az') === false &&
                    strpos($trace['file'], 'system\monolog\\') === false
                ) {
                    unset($trace['object'], $trace['args']);
                    $trace['file'] = str_replace('\\\\', '\\', $trace['file']);
                    $traces[] = $trace;
                    if (++$count >= $traceLevel) {
                        break;
                    }
                }
            }
        }

        return $traces;
    }

    /*private function isTraceExcept($trace, $traceExcepts) {
        $return = true;
        foreach ($traceExcepts as $traceExceptKey => $traceExceptVal) {
            if (!strpos($trace, $traceExceptVal) !== 0) {
                $return = true;
            }
        }
    }*/

    private function pushProcessors(Logger $logger, $level, $telegram = false)
    {
        foreach ($this->processors as $key => $value) {
            if (is_array($value) && $value['enabled']) {
                if (isset($value['tags']) && $value['tags']) {
                    $logger->pushProcessor(new $value['item']($this->coreAll['tags']));
                } else {
                    $logger->pushProcessor(new $value['item']());
                }
            }
            if ($value === true) {
                $logger->pushProcessor(new $key());
            }
        }
        if ($this->coreAll['customProcessorKey'] !== null) {
            $logger->pushProcessor(function ($record) {
                $record['extra'][$this->coreAll['customProcessorKey']] = $this->coreAll['customProcessorVar'];

                return $record;
            });
        }
    }

    private function setLevel($level)
    {
        if ($level === null)
            return $this->coreAll['level'];
        elseif (!is_int($level) || $level < 0)
            throw new \yii\base\Exception('Level must have int value and must not be negative');
        else
            return $level;
    }

    private function isExcept()
    {
        global $boot;
        $trace = self::getTrace(10);

        $modelClassName = null;
        foreach ($trace as $itemKey => $itemValue) {
                if (isset($itemValue['function']) && $itemValue['function'] === 'log')
                $modelClassName = ZArrayHelper::getValue($itemValue,'file');
        }

        foreach ($this->excepts as $exceptKey => $exceptValue) {
            if (strpos($modelClassName, $exceptValue) !== false) {
                return true;
            }
        }

        return false;
    }
    #endregion
}
