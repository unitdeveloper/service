<?php


namespace zetsoft\service\parser;

require Root . '/vendori/parser/vendor/autoload.php';


use zetsoft\system\kernels\ZFrame;
use QL\QueryList;

/**
 * @package xemlock/htmlpurifier-html5
 *
 * @author SuxrobNuraliev
 * https://packagist.org/packages/xemlock/htmlpurifier-html5
 */


class JaegerQuerylist extends ZFrame
{
    public function example(){
          return QueryList::get('https://github.com')->find('img')->attrs('src');
    }
}
