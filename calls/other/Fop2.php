<?php
namespace zetsoft\service\calls;


use zetsoft\system\kernels\ZFrame;
use zetsoft\service\calls\MarceAMI;
use zetsoft\service\calls\Fop;

class Fop2 extends ZFrame
{
    private $props;
    private $callAction;
    private $control;
    private $data;


    public function init()
    {   parent::init();
        $this->control = new Fop();
    }


    public function check()
    {
        $json = $_GET['json'];
        $this->data = json_decode($json);
        $this->callAction = $this->data->actionType;
        $this->act();

    }


    public function act()
    {
        $this->control->ext = $this->data->extension;
        $this->control->callerId = $this->data->callerId;
        if($this->callAction === 'dial'){
            $this->control->originate();
            $this->control->close();
            echo 'Dialing';
        }else if($this->callAction === 'transfer') {
            $this->control->coreShowChannels();
            $this->control->channel = $this->control->liveChannels[$this->data->extension];
            $this->control->redirect();
            $this->control->close();
            echo 'Transfered Successfully';
        }else if($this->callAction === 'hangup'){
            $this->control->coreShowChannels();
            $this->control->channel = $this->control->liveChannels[$this->data->extension];
            $this->control->hangUp();
            $this->control->close();
            echo 'Hanged Up';
        }else if($this->callAction === 'listen'){
            $this->control->listen();
            $this->control->close();
            echo 'Listening';
        }else if($this->callAction === 'whisper'){
            $this->control->whisper();
            $this->control->close();
            echo 'Listening';
        }else if($this->callAction === 'barge'){
            $this->control->bargeIn();
            $this->control->close();
            echo 'Listening';
        }else if($this->callAction === 'callhistory'){
            $usernumber = $this->control->ext;
            include __DIR__ . '\callhistory.php';
        }else if($this->callAction === 'missedcalls') {
            $src = $this->control->ext;
            include __DIR__ . '\missed.php';
        }else
        {
            echo $this->callAction;
        }
    }



    


}
