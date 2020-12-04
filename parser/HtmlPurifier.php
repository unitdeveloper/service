<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\parser;


require Root . '/vendori/parser/html/vendor/autoload.php';


use HTMLPurifier_HTML5Config;
use zetsoft\system\kernels\ZFrame;

/**
 * Class    Htmlpurifier
 * @package zetsoft\service\parser
 *
 * @author SuxrobNuraliev
 * https://packagist.org/packages/xemlock/htmlpurifier-html5
 */
class HtmlPurifier extends ZFrame
{
    public ?string $fileSource = null;
    public ?string $fileResult = null;

    public function example()
    {

        $this->fileSource = __DIR__ . '/sample/source.html';
        $this->fileResult = __DIR__ . '/results/source_HtmlPurifier.html';

        $dirty_html = file_get_contents($this->fileSource);

        $config = HTMLPurifier_HTML5Config::createDefault();
        $purifier = new HTMLPurifier($config);
        $dirty_html5 = $this->fileSource;
        $clean_html5 = $purifier->purify($dirty_html5);

        file_put_contents($this->fileResult, $clean_html5);

    }

#endregion

}
