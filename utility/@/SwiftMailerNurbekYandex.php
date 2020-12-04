<?php

namespace zetsoft\service\utility;

use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

require Root . '/vendori/utility/ALL/vendor/autoload.php';

/**
 * Class SwiftMailer
 * Swift Mailer is a component based library for sending e-mails from PHP applications.
 * @package zetsoft\service\utility
 * @author NurbekMakhmudov
 * https://github.com/swiftmailer/swiftmailer
 * https://packagist.org/packages/swiftmailer/swiftmailer
 * https://swiftmailer.symfony.com/docs/introduction.html
 */
class SwiftMailerNurbekYandex extends ZFrame
{

    //start|NurbekMakhmudov|2020-10-18

    #region Vars

    private $appName;

    // Yandex
    private \Swift_SmtpTransport $swiftSmtpTransportYandex;
    private \Swift_Mailer $swiftMailerYandex;
    private \Swift_Message $swiftMessageYandex;

    private $mailYandexHost;
    private $mailYandexUsername;
    private $mailYandexPassword;
    private $mailYandexPort;
    private $mailYandexTitle;
    private $mailYandexEncryption;

    // Plugins
    public $antiFlood = false;
    public $throttler = false;
    public $logger = false;
    public $decorator = false;

    // re-connect after 100 emails, if you need change count
    public $emailCount = 100;
    // And specify a time in seconds to pause for (30 secs), if you need change time
    public $pausingTime = 30;


    #endregion

    /**
     * initialization
     * @author NurbekMakhmudov
     */
    public function init()
    {
        parent::init();

        $this->appName = $this->bootEnv('appName');

        $this->mailYandexHost = $this->bootEnv('mailYandexHost');
        $this->mailYandexUsername = $this->bootEnv('mailYandexUsername');
        $this->mailYandexPassword = $this->bootEnv('mailYandexPassword');
        $this->mailYandexPort = $this->bootEnv('mailYandexPort');
        $this->mailYandexTitle = $this->bootEnv('mailYandexTitle');
        $this->mailYandexEncryption = $this->bootEnv('mailYandexEncryption');

        $this->swiftSmtpTransportYandex = new \Swift_SmtpTransport(
            $this->mailYandexHost, $this->mailYandexPort, $this->mailYandexEncryption
        );

        $this->swiftSmtpTransportYandex->setUsername($this->mailYandexUsername);
        $this->swiftSmtpTransportYandex->setPassword($this->mailYandexPassword);

        $this->swiftMailerYandex = new \Swift_Mailer($this->swiftSmtpTransportYandex);

        $this->registerPlugins();

        $this->swiftMessageYandex = new \Swift_Message();
    }


    /**
     * @author NurbekMakhmudov
     * Registration Swift_Mailer plugins
     */
    public function registerPlugins()
    {
        if ($this->antiFlood !== false)
            $this->antiFloodPlugin();

        if ($this->logger !== false)
            $this->loggerPlugin();

        if ($this->decorator !== false)
            $this->decoratorPlugin();

        if ($this->throttler !== false)
            $this->throttlerPlugin();
    }

    /**
     * @author NurbekMakhmudov
     * Use AntiFlood to re-connect after email limit finished
     * https://swiftmailer.symfony.com/docs/plugins.html#antiflood-plugin
     */
    public function antiFloodPlugin()
    {
        $this->swiftMailerYandex->registerPlugin(new \Swift_Plugins_AntiFloodPlugin(100, 30));
    }

    /**
     * @author NurbekMakhmudov
     * The Logger plugins helps with debugging during the process of sending.
     * It can help to identify why an SMTP server is rejecting addresses, or any other hard-to-find problems that may arise.
     * https://swiftmailer.symfony.com/docs/plugins.html#logger-plugin
     */
    public function loggerPlugin()
    {

    }


    public function decoratorPlugin()
    {
    }

    public function throttlerPlugin()
    {
    }

    /**
     * @param $mail
     * @param $password
     * @param string $name
     * @author NurbekMakhmudov
     */
    public function auth($mail, $password, $name = null)
    {
        try {
            $baseUrl = 'http://eyuf.zetsoft.uz/';
            $verificationCode = Az::$app->guid->sGuid->create();

            $body = "Мы рады, что вы с нами!"
                . "\n\nЛогин: " . $mail . "\nПароль: " . $password
                . "\n\nПодтвердить email: " . $baseUrl . "?verificationCode=" . $verificationCode . "\n\n"
                . "Если вы не регистрировались на сайте " . $baseUrl . ", просто проигнорируйте данное письмо.";

            $message = $this->swiftMessageYandex
                ->setSubject($this->appName)
                ->setFrom([$this->mailYandexUsername => $this->appName])
                ->setTo([$mail => $name])
                ->setBody($body);

            return $this->swiftMailerYandex->send($message);

        } catch (\Exception $e) {
            vd($e);
            return null;
        }
    }

    #endregion

    #region Examples

    public function authExample()
    {
        $res = $this->auth('nurbekmakhmudov@yandex.ru', 'Pass12345');
//        $res = $this->auth('eyufconfirmationemail@gmail.com', '123Pass', 'Nurbek');
        vd($res);
    }

    #endregion





    //end|NurbekMakhmudov|2020-10-18


    public function authT($mail, $password, $name = null)
    {
        try {
            $baseUrl = 'http://eyuf.zetsoft.uz/';
            $verificationCode = Az::$app->guid->sGuid->create();

            $body = "Мы рады, что вы с нами!"
                . "\n\nЛогин: " . $mail . "\nПароль: " . $password
                . "\n\nПодтвердить email: " . $baseUrl . "?verificationCode=" . $verificationCode . "\n\n"
                . "Если вы не регистрировались на сайте " . $baseUrl . ", просто проигнорируйте данное письмо.";

            $message = $this->swiftMessage
                ->setSubject($this->appName)
                ->setFrom([$this->mailUsername => $this->appName])
                ->setTo([$mail => $name])
                ->setBody($body);

            return $this->swiftMailer->send($message);

        } catch (\Exception $e) {
            return null;
        }
    }

}