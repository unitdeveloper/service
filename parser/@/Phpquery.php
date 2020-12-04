<?php
/**
 * Author:  Xolmat Ravshanov
 */
namespace zetsoft\service\parser;

use zetsoft\system\kernels\ZFrame;

class Phpquery extends ZFrame
{

    #region  Vars

    #endregion
    public function test()
    {

    }

    public function init()
    {
        parent::init();
    }

    public function loadFile()
    {
        phpQuery::newDocumentFileXHTML();
    }




}
