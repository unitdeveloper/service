<?php

/**
 *
 *
 * Author:  Asror Zakirov
 *
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\App\ALL;

use zetsoft\service\App\eyuf\Chat;
use zetsoft\service\App\eyuf\Davlat;
use zetsoft\service\App\eyuf\Docs;
use zetsoft\service\App\eyuf\Excel;
use zetsoft\service\App\eyuf\Grape;
use zetsoft\service\App\eyuf\Grapes;
use zetsoft\service\App\eyuf\Grapes_;
use zetsoft\service\App\eyuf\Main;
use zetsoft\service\App\eyuf\Margin;
use zetsoft\service\App\eyuf\Pdf;
use zetsoft\service\App\eyuf\Scholar;
use zetsoft\service\App\eyuf\User;
use zetsoft\service\App\eyuf\Word;
use yii\base\Component;



/**
 *
* @property Chat $chat
* @property Davlat $davlat
* @property Docs $docs
* @property Excel $excel
* @property Grape $grape
* @property Grapes $grapes
* @property Grapes_ $grapes_
* @property Main $main
* @property Margin $margin
* @property Pdf $pdf
* @property Scholar $scholar
* @property User $user
* @property Word $word

 */

class Eyuf extends Component
{

    
    private $_chat;
    private $_davlat;
    private $_docs;
    private $_excel;
    private $_grape;
    private $_grapes;
    private $_grapes_;
    private $_main;
    private $_margin;
    private $_pdf;
    private $_scholar;
    private $_user;
    private $_word;

    
    public function getChat()
    {
        if ($this->_chat === null)
            $this->_chat = new Chat();

        return $this->_chat;
    }
    

    public function getDavlat()
    {
        if ($this->_davlat === null)
            $this->_davlat = new Davlat();

        return $this->_davlat;
    }
    

    public function getDocs()
    {
        if ($this->_docs === null)
            $this->_docs = new Docs();

        return $this->_docs;
    }
    

    public function getExcel()
    {
        if ($this->_excel === null)
            $this->_excel = new Excel();

        return $this->_excel;
    }
    

    public function getGrape()
    {
        if ($this->_grape === null)
            $this->_grape = new Grape();

        return $this->_grape;
    }
    

    public function getGrapes()
    {
        if ($this->_grapes === null)
            $this->_grapes = new Grapes();

        return $this->_grapes;
    }
    

    public function getGrapes_()
    {
        if ($this->_grapes_ === null)
            $this->_grapes_ = new Grapes_();

        return $this->_grapes_;
    }
    

    public function getMain()
    {
        if ($this->_main === null)
            $this->_main = new Main();

        return $this->_main;
    }
    

    public function getMargin()
    {
        if ($this->_margin === null)
            $this->_margin = new Margin();

        return $this->_margin;
    }
    

    public function getPdf()
    {
        if ($this->_pdf === null)
            $this->_pdf = new Pdf();

        return $this->_pdf;
    }
    

    public function getScholar()
    {
        if ($this->_scholar === null)
            $this->_scholar = new Scholar();

        return $this->_scholar;
    }
    

    public function getUser()
    {
        if ($this->_user === null)
            $this->_user = new User();

        return $this->_user;
    }
    

    public function getWord()
    {
        if ($this->_word === null)
            $this->_word = new Word();

        return $this->_word;
    }
    


}
