<?php

namespace zetsoft\service\markup;

use zetsoft\system\kernels\ZFrame;


include Root . '\vendor\cerdic\css-tidy\class.csstidy.php';

class CssTidy extends ZFrame
{
    #region Vars

    public array $def_cfg = [
        'remove_bslash' => true,
        'compress_colors' => true,
        'compress_font-weight' => true,
        'lowercase_s' => false,
        /*
            1 common shorthands optimization
            2 + font property optimization
            3 + background property optimization
         */
        'optimise_shorthands' => 1, // 1 2 3
        'remove_last_;' => true,
        'space_before_important' => false,
        /* rewrite all properties with low case, better for later gzip OK, safe*/
        'case_properties' => 1,
        /* sort properties in alpabetic order, better for later gzip
         * but can cause trouble in case of overiding same propertie or using hack
         */
        'sort_properties' => false,
        /*
            1, 3, 5, etc -- enable sorting selectors inside @media: a{}b{}c{}
            2, 5, 8, etc -- enable sorting selectors inside one CSS declaration: a,b,c{}
            preserve order by default cause it can break functionnality
         */
        'sort_selectors' => 0,
        /* is dangeroues to be used: CSS is broken sometimes */
        'merge_selectors' => 0,
        /* preserve or not browser hacks */
        /* Useful to produce a rtl css from a ltr one (or the opposite) */
        'reverse_left_and_right' => 0,
        'discard_invalid_selectors' => false,
        'discard_invalid_properties' => false,
        'css_level' => 'CSS3.0',
        'preserve_css' => false,
        'timestamp' => false,
        'template' => 'highest', // say that propertie exist highest high low default
    ];

    #endregion

    #region Test

    public function test($cssCode, $arr = null)
    {
        echo $this->getCode($cssCode, $arr);

    }

    #endregion

    #region Event

    public function getCode($csscode, array $array = null)
    {
        $csstidy = new \csstidy();

        if(substr($csscode, -4) === '.css'){
            $file = file_get_contents($csscode);
            $csscode = $file;

        }
        if ($array === null) {
           $array = $this->def_cfg;
        }

        $csstidy->set_cfg($array);

        // Parse the CSS
        $csstidy->parse($csscode);
        $code = $csstidy->print->plain();
        return $code;


    }

    #endregion
}
