<?php
/**
 * Author:  Maxamadjonov Jaxongir
 **/

namespace zetsoft\service\auth;

use Swift_Plugins_LoggerPlugin;
use Swift_Plugins_Loggers_ArrayLogger;
use Swift_Plugins_Loggers_EchoLogger;
use Swift_Plugins_ThrottlerPlugin;
use zetsoft\system\helpers\ZTest;
use zetsoft\system\kernels\ZFrame;


class  Swift extends ZFrame
{
#region Vars
    public $messageid;
    public $from = 'confirmationverifyemail@yandex.ru';
    public $to;
    public $sender;
    public $subject;
    public $date;
    public $bcc;
    public $cc;
    public $body;
    public $body_extension = 'text/plain';
    public $body_charset;
    public $attachfile;
    public $filename;
    public $embed;
    public $embedfile;
    public $result;
    public $max;
    public $priority;
    public $reconnect;
    public $pause;
    public $log;
    public $logger = false;
    public $limit = 100;
    public $limit_traffic = 1024 * 1024 * 10;
    const highest = 1;
    const high = 2;
    const normal = 3;
    const low = 4;
    const lowest = 5;

    private $mailer;

#region All
    public function assertTest()
    {
        ZTest::assertEquals(1, 2);
    }

    public function justTest()
    {
        ZTest::assertEquals(1, 1);
    }

    public function init()
    {
        parent::init();
        $this->settings();

        $this->messageid = new \Swift_Message();

    }

    public function run()
    {

        $this->simplemessage();
        $this->send();


    }

    public function simplemessage()
    {

        $this->messageid->setFrom($this->from);
        $this->messageid->setSubject($this->subject);
        if (!empty($this->to)) $this->messageid->setTo($this->to);
        if (!empty($this->cc)) $this->messageid->setCc($this->cc);
        if (!empty($this->bcc)) $this->messageid->setBcc($this->bcc);
        $this->messageid->setBody($this->body, $this->body_extension, $this->body_charset);
    }

    public function addheader()
    {

        if (!empty($this->to)) $this->messageid->addTo($this->to);
        if (!empty($this->cc)) $this->messageid->addCc($this->cc);
        if (!empty($this->bcc)) $this->messageid->addBcc($this->bcc);

    }

    public function sendfile()
    {

        $attachment = \Swift_Attachment::fromPath($this->attachfile, mime_content_type($this->attachfile));
        if (!empty($this->filename)) $attachment->setFilename($this->filename);
        $this->messageid->attach($attachment);
    }

    public function embedimage()
    {
        if (!empty($this->embedfile))
            $this->embed = $this->messageid->embed(\Swift_Image::fromPath($this->embedfile));
    }

    public function embedfile()
    {
        if (!empty($this->embedfile))
            $this->embed = $this->messageid->embed(\Swift_EmbeddedFile::fromPath($this->embedfile));
    }

    public function send()
    {
        $logger = new Swift_Plugins_Loggers_EchoLogger();
        $this->mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));
        $this->result = $this->mailer->send($this->messageid);
        echo $logger->dump();
    }

    public function setlength()
    {
        $this->messageid->setMaxLineLength($this->max);
    }


    public function setpriority()
    {

        $this->messageid->setPriority($this->priority);

    }

    private function settings()
    {
            global $boot;

    /*
     *   'transport' => [
                'class' => Swift_SmtpTransport::class,
                'host' => $boot->env('mailHost'),
                'username' => $boot->env('mailUsername'),
                'password' => $boot->env('mailPassword'),
                'port' => $boot->env('mailPort'),
                'encryption' => $boot->env('mailEncryption'),
            ],
     * */


        //$transport = (new \Swift_SmtpTransport('/usr/sbin/sendmail -bs'));
        $transport = (new \Swift_SmtpTransport());
        $transport->setUsername($boot->env('mailUsername'));
        $transport->setPassword($boot->env('mailPassword'));
        $transport->setHost($boot->env('mailHost'));
        $transport->setPort($boot->env('mailPort'));
        $transport->setEncryption($boot->env('mailEncryption'));




        $this->mailer = new \Swift_Mailer($transport);

    }

#region plugins
    public function antiflood()
    {
        $this->mailer->registerPlugin(new \Swift_Plugins_AntiFloodPlugin($this->reconnect, $this->pause));
    }

    public function throttlerMails()
    {
        $this->mailer->registerPlugin(new Swift_Plugins_ThrottlerPlugin(
            $this->limit, \Swift_Plugins_ThrottlerPlugin::MESSAGES_PER_MINUTE
        ));


    }

    public function throttler_traffic()
    {
        $this->mailer->registerPlugin(new Swift_Plugins_ThrottlerPlugin(
            $this->limit_traffic, \Swift_Plugins_ThrottlerPlugin::BYTES_PER_MINUTE
        ));


    }

    public function logger_echo()
    {
        if ($this->logger) {
            $logger = new Swift_Plugins_Loggers_EchoLogger();
            $this->mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));
            $this->log = $logger->dump();
        }


    }

    public function logger_array()
    {
        if ($this->logger) {
            $logger = new Swift_Plugins_Loggers_ArrayLogger();
            $this->mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));
            $this->log = $logger->dump();
        }
    }

}
