<?php

/**
 * @author NurbekMakhmudov
 */

namespace zetsoft\service\utility;

require Root . '/vendors/utility/ALL/vendor/autoload.php';

use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;

use Swift_Plugins_ThrottlerPlugin;
use Swift_Plugins_LoggerPlugin;
use Swift_Plugins_AntiFloodPlugin;
use Swift_Attachment;
use Swift_Plugins_Loggers_EchoLogger;
use Swift_Plugins_DecoratorPlugin;


/**
 * Class SwiftMailer
 * Swift Mailer is a component based library for sending e-mails from PHP applications.
 * @package zetsoft\service\utility
 * @author NurbekMakhmudov
 * https://github.com/swiftmailer/swiftmailer
 * https://packagist.org/packages/swiftmailer/swiftmailer
 * https://swiftmailer.symfony.com/docs/introduction.html
 */
class SwiftMailerNurbek extends ZFrame
{

    private function test()
    {

    }

    //start|NurbekMakhmudov|2020-10-18

    #region Vars

    public $appName;

    private $mailHost;
    private $mailUsername;
    private $mailPassword;
    private $mailPort;
    private $mailTitle;
    private $mailEncryption;

    /**
     * @author NurbekMakhmudov
     * @var string
     * Content type for styling email
     */
    public $contentType = self::contentType['html'];
    public const contentType = [
        'html' => 'text/html',
        'plain' => 'text/plain',
        'jpeg' => 'image/jpeg',
        'pdf' => 'application/pdf',
    ];


    /**
     * @var bool
     * Enable Plugins
     */
    public $isAntiFlood = false;
    public $isThrottler = false;
    public $isLogger = false;
    public $isDecorator = false;

    /**
     * @var int
     * AntiFlood plugin | re-connect after sending 100 emails, if you need change count
     */
    public $emailCount = 100;

    /**
     * @var int
     * AntiFlood plugin | And specify a time in seconds to pause for (30 secs), if you need change time
     */
    public $pausingTime = 30;

    /**
     * @var int
     * Throttler Plugin |  Rate limit to 100 emails per-minute, if you need change count
     */
    public $emailLimitOnPerMinute = 100;

    /**
     * @var float|int
     * Throttler Plugin |  Rate limit to 10MB per-minute, if you need change MB
     */
    public $mbLimitOnPerMinute = 1024 * 1024 * 10;

    /**
     * @var string
     * Logger Plugin | creating log filename
     */
    public $swiftMailerLogFileName = 'swiftMailerLog.html';

    /**
     * @var string
     * Logger Plugin | creating log file path
     */
    public $swiftMailerLogFilePath = Root . '/webhtm/test/log/';


    #endregion

    /**
     * @param bool $isGoogle
     * Configuration Mail Host
     * @author NurbekMakhmudov
     */
    private function config($isGoogle = false)
    {
        $this->appName = $this->bootEnv('appName');

        $server = 'Yandex';
        if ($isGoogle)
            $server = 'Google';

        $this->mailHost = $this->bootEnv('mail' . $server . 'Host');
        $this->mailUsername = $this->bootEnv('mail' . $server . 'Username');
        $this->mailPassword = $this->bootEnv('mail' . $server . 'Password');
        $this->mailPort = $this->bootEnv('mail' . $server . 'Port');
        $this->mailTitle = $this->bootEnv('mail' . $server . 'Title');
        $this->mailEncryption = $this->bootEnv('mail' . $server . 'Encryption');
    }

    /**
     * @return Swift_SmtpTransport
     * @author NurbekMakhmudov
     * Create Smtp Transport
     */
    private function getTransport()
    {
        $transport = (new Swift_SmtpTransport($this->mailHost, $this->mailPort, $this->mailEncryption))
            ->setUsername($this->mailUsername)
            ->setPassword($this->mailPassword);
        return $transport;
    }

    /**
     * @param Swift_SmtpTransport $transport
     * @return Swift_Mailer
     * @author NurbekMakhmudov
     * Get new Mailer object
     */
    private function getMailer(Swift_SmtpTransport $transport)
    {
        return new Swift_Mailer($transport);
    }

    /**
     * @param $subject
     * @param $mailTo
     * @param $body
     * @return Swift_Message
     * @author NurbekMakhmudov
     * Create Swift Message
     */
    private function createMessage($subject, $mailTo, $body)
    {
        return (new Swift_Message())
            ->setSubject($subject)
            ->setFrom($this->mailUsername)
            ->setTo($mailTo)
            ->setBody($body, $this->contentType);
    }

    /**
     * @author NurbekMakhmudov
     * Registration Swift_Mailer plugins
     */
    private function registerPlugins(Swift_Mailer $mailer)
    {
        if ($this->isAntiFlood !== false)
            $this->antiFloodPlugin($mailer);

        if ($this->isDecorator !== false)
            $this->decoratorPlugin($mailer);

        if ($this->isLogger !== false)
            $this->loggerPlugin($mailer);

        if ($this->isThrottler !== false)
            $this->throttlerPlugin($mailer);
    }

    /**
     * @author NurbekMakhmudov
     * Use AntiFlood to re-connect after email limit finished
     * https://swiftmailer.symfony.com/docs/plugins.html#antiflood-plugin
     */
    private function antiFloodPlugin(Swift_Mailer $mailer)
    {
        $mailer->registerPlugin(new Swift_Plugins_AntiFloodPlugin($this->emailCount, $this->pausingTime));
    }

    /**
     * @author NurbekMakhmudov
     * The Logger plugins helps with debugging during the process of sending.
     * It can help to identify why an SMTP server is rejecting addresses, or any other hard-to-find problems that may arise.
     * https://swiftmailer.symfony.com/docs/plugins.html#logger-plugin
     */
    private function loggerPlugin(Swift_Mailer $mailer)
    {
        $logger = new Swift_Plugins_Loggers_EchoLogger(); // Prints output to the screen in realtime. Handy for very rudimentary debug output.

        $mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));

//        $filePathName = $this->swiftMailerLogFilePath . $this->swiftMailerLogFileName;

        echo $logger->dump();
    }


    /**
     * @author NurbekMakhmudov
     * Often there’s a need to send the same message to multiple recipients, but with tiny variations such
     * as the recipient’s name being used inside the message body. The Decorator plugin aims to provide a
     * solution for allowing these small differences.
     * https://swiftmailer.symfony.com/docs/plugins.html#using-the-decorator-plugin
     */
    private function decoratorPlugin(Swift_Mailer $mailer)
    {
//        $mailer->registerPlugin(new Swift_Plugins_DecoratorPlugin($this->replacements));
    }

    /**
     * @author NurbekMakhmudov
     * If your SMTP server has restrictions in place to limit the rate at which you send emails,
     * then your code will need to be aware of this rate-limiting. The Throttler plugin makes
     * Swift Mailer run at a rate-limited speed.
     * https://swiftmailer.symfony.com/docs/plugins.html#throttler-plugin
     */
    private function throttlerPlugin(Swift_Mailer $mailer)
    {
        $mailer->registerPlugin(new Swift_Plugins_ThrottlerPlugin(
            $this->emailLimitOnPerMinute, Swift_Plugins_ThrottlerPlugin::MESSAGES_PER_MINUTE
        ));

        $mailer->registerPlugin(new Swift_Plugins_ThrottlerPlugin(
            $this->mbLimitOnPerMinute, Swift_Plugins_ThrottlerPlugin::BYTES_PER_MINUTE
        ));
    }

    /**
     * @param $mailTo
     * @param $body
     * @param bool $isGoogle
     * @return int
     * @author NurbekMakhmudov
     * Sending mail message
     */
    private function send($mailTo, $body, $isGoogle = false)
    {
        $this->config($isGoogle);
        $mailer = $this->getMailer($this->getTransport());
        $this->registerPlugins($mailer);
        return $mailer->send($this->createMessage($this->appName, $mailTo, $body));
    }


    /**
     * @param $mailTo
     * @param $verify_code
     * @return int|null
     * @author NurbekMakhmudov
     *  Send email and verify code for authorization
     */
    public function verifyWithEmail($mailTo, $verify_code)
    {
        try {
            $baseUrl = $this->urlGetBase();
            $registerUserEmail = $this->sessionGet('registerUserEmail');
            $link = '<a href = "' . $baseUrl . $this->bootEnv('verifyUserUrl') . "?id=$registerUserEmail&code=$verify_code" . '">' . Az::l('ссылку') . '</a>';

            $body = Az::l('Мы рады, что вы с нами!') . '<br><br>'
                . Az::l('Пожалуйста нажмите на эту {link} чтобы активировать аккаунт.', [
                    'link' => $link
                ]) . '<br><br>'
                . Az::l('Если вы не регистрировались на сайте {baseUrl} просто проигнорируйте данное письмо.', [
                    'baseUrl' => $baseUrl
                ]);

//            $resYandex = $this->send($mailTo, $body);
            $resGoogle = $this->send($mailTo, $body, true);

//            return 'Yandex = ' . $resYandex . ' && Google = ' . $resGoogle;
            return 'Google = ' . $resGoogle;
        } catch (\Exception $e) {
            vd($e);
            return null;
        }
    }


    #endregion


    #region Examples

    public function authExample()
    {
        $res = $this->verifyWithEmail('nurmax1993@mail.ru',
            '62ffca3230ee975f568a4ed284b9a6115c6c279530ea18c2fb1664f071713dc4');

//        $res = $this->verifyWithEmail('nurbekmakhmudov@yandex.ru',
//            '62ffca3230ee975f568a4ed284b9a6115c6c279530ea18c2fb1664f071713dc4');

//        $res = $this->verifyWithEmail('nurmax1993@gmail.com',
//            '62ffca3230ee975f568a4ed284b9a6115c6c279530ea18c2fb1664f071713dc4');

//        $res = $this->verifyWithEmail('nurmax93@inbox.ru',
//            '62ffca3230ee975f568a4ed284b9a6115c6c279530ea18c2fb1664f071713dc4');

        vd($res);
    }


    public function decoratorPluginExample()
    {
        $users = array([
            array([
                'email' => 'mark@gmail.com',
                'username' => 'Mark Zukerberg',
                'resetcode' => 123456,
            ]),
            array([
                'email' => 'bill@gmail . com',
                'username' => 'Bill Gates',
                'resetcode' => 456123,
            ]),
            array([
                'email' => 'jeef@gmail . com',
                'username' => 'Jeef Bezos',
                'resetcode' => 789456,
            ]),
        ]);

        $replacements = [];
        foreach ($users as $user => $key) {
            $replacements[$user['email']] = [
                '{
    username}' => $user['username'],
                '{
    resetcode}' => $user['resetcode']
            ];
        }


    }


    #endregion


    //end|NurbekMakhmudov|2020-10-18

}
