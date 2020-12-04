<?php

namespace zetsoft\service\utility;

require Root . '/vendori/utility/ALL/vendor/autoload.php';

use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;


/**
 * Class SwiftMailer
 * Swift Mailer is a component based library for sending e-mails from PHP applications.
 * @package zetsoft\service\utility
 * @author NurbekMakhmudov
 * https://github.com/swiftmailer/swiftmailer
 * https://packagist.org/packages/swiftmailer/swiftmailer
 * https://swiftmailer.symfony.com/docs/introduction.html
 */
class SwiftMailerNurbek_v2 extends ZFrame
{

    //start|NurbekMakhmudov|2020-10-18

    #region Vars

    //Swift Mailer
    private \Swift_SmtpTransport $swiftSmtpTransportYandex;
    private \Swift_Mailer $swiftMailerYandex;
    private \Swift_Message $swiftMessageYandex;

//    private Swift_SmtpTransport $swiftSmtpTransportGoogle;
//    private Swift_Mailer $swiftMailerGoogle;
//    private Swift_Message $swiftMessageGoogle;

    private $appName;

    //Smpt servers
    private $mailYandexHost;
    private $mailYandexUsername;
    private $mailYandexPassword;
    private $mailYandexPort;
    private $mailYandexTitle;
    private $mailYandexEncryption;

//    private $mailGoogleHost;
//    private $mailGoogleUsername;
//    private $mailGooglePassword;
//    private $mailGooglePort;
//    private $mailGoogleTitle;
//    private $mailGoogleEncryption;
//    private $mailGoogleTest;

    // Plugins
    public $antiFlood = false;
    public $throttler = false;
    public $logger = false;
    public $decorator = false;

    // AntiFlood plugin | re-connect after 100 emails, if you need change count
    public $emailCount = 100;
    // AntiFlood plugin | And specify a time in seconds to pause for (30 secs), if you need change time
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

//        $this->mailGoogleHost = $this->bootEnv('mailGoogleHost');

//        $this->mailGoogleUsername = 'nurdars@gmail.com';
//        $this->mailGooglePassword = 'nur123gole321max';

        /*
        $this->mailGoogleUsername = $this->bootEnv('mailGoogleUsername');
        $this->mailGooglePassword = $this->bootEnv('mailGooglePassword');
        */

//        $this->mailGooglePort = $this->bootEnv('mailGooglePort');
//        $this->mailGoogleTitle = $this->bootEnv('mailGoogleTitle');
//        $this->mailGoogleEncryption = $this->bootEnv('mailGoogleEncryption');
//        $this->mailGoogleTest = $this->bootEnv('mailGoogleTest');

        // Yandex
        $this->swiftSmtpTransportYandex = new \Swift_SmtpTransport(
            $this->mailYandexHost,
            $this->mailYandexPort,
            $this->mailYandexEncryption
        );

        $this->swiftSmtpTransportYandex->setUsername($this->mailYandexUsername);
        $this->swiftSmtpTransportYandex->setPassword($this->mailYandexPassword);

        $this->swiftMailerYandex = new \Swift_Mailer($this->swiftSmtpTransportYandex);

        // Google
        /*
         $this->swiftSmtpTransportGoogle = new Swift_SmtpTransport(
            $this->mailGoogleHost,
            $this->mailGooglePort,
            $this->mailGoogleEncryption
        );

        $this->swiftSmtpTransportGoogle->setUsername($this->mailGoogleUsername);
        $this->swiftSmtpTransportGoogle->setPassword($this->mailGooglePassword);

        $this->swiftMailerGoogle = new Swift_Mailer($this->swiftSmtpTransportGoogle);
        */

//        $this->registerPlugins();
        $this->swiftMessageYandex = new \Swift_Message();
//        $this->swiftMessageGoogle = new Swift_Message();
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
        $this->swiftMailerYandex->registerPlugin(new \Swift_Plugins_AntiFloodPlugin($this->emailCount, $this->pausingTime));
//        $this->swiftMailerGoogle->registerPlugin(new Swift_Plugins_AntiFloodPlugin($this->emailCount, $this->pausingTime));
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
    public function auth($mailTo, $verify_code)
    {
        try {
            $baseUrl = $this->urlGetBase();
            $registerUserEmail = $this->sessionGet('registerUserEmail');
            $link = '<a href="' . $baseUrl . $this->bootEnv('verifyUserUrl') . "?id=$registerUserEmail&code=$verify_code" . '">' . Az::l('ссылку') . '</a>';

            $body = Az::l('Мы рады, что вы с нами!') . '<br><br>'
                . Az::l('Пожалуйста нажмите на эту {link} чтобы активировать аккаунт.', [
                    'link' => $link
                ]) . '<br><br>'
                . Az::l('Если вы не регистрировались на сайте {baseUrl} просто проигнорируйте данное письмо.', [
                    'baseUrl' => $baseUrl
                ]);

            // Yandex
            $messageYandex = $this->swiftMessageYandex
                ->setSubject($this->appName)
                ->setFrom([$this->mailYandexUsername => $this->appName])
                ->setTo($mailTo)
                ->setBody($body, 'text/html');

            return $this->swiftMailerYandex->send($messageYandex);

            /*
             $messageGoogle = $this->swiftMessageGoogle
                ->setSubject($this->appName)
                ->setFrom([$this->mailGoogleUsername => $this->appName])
                ->setTo($mailTo)
                ->setBody($body, 'text/html');

            echo 'Google = '.  $this->swiftMailerGoogle->send($messageGoogle);
            */

        } catch (\Exception $e) {
            vd($e);
            return null;
        }
    }

    #endregion

    #region Examples

    public function authExample()
    {
        $res = $this->auth([
            'nurbekmakhmudov@yandex.ru',
            /*'nurmax93@inbox.ru',
            'nurmax1993@gmail.com',
            'nurmax1993@mail.ru'*/
        ],
             '62ffca3230ee975f568a4ed284b9a6115c6c279530ea18c2fb1664f071713dc4');

        vd($res);
    }

    #endregion



    //end|NurbekMakhmudov|2020-10-18

}