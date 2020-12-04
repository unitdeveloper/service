<?php

/**
 *
 *
 * Author:  Asror Zakirov
 *
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\media;


use zetsoft\dbitem\ALL\ZServiceAppItem;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;


/**
 * Class    Execs
 * @package zetsoft\service\utility
 *
 * https://symfony.com/doc/current/components/process.html
 */
class Execs extends ZFrame
{

    public $cmd;

    public $continous;
    public $background;

  

    public function sox(string $fileA, string $fileB, string $fileResult): bool
    {

        if (!file_exists($fileA))
            return Az::warning($fileA, 'FileA Not Exists');

        if (!file_exists($fileB))
            return Az::warning($fileB, 'FileB Not Exists');

        $sample = '{sSoxExe} -v 4.0 -m "{
        fileA}" "{
        fileB}" -C 48.99 "{
        fileResult}" -V4 highpass 10';
        $sSoxExe = Az::getAlias('@app/service/merge/sox.exe');

        $cmd = strtr(
            $sample,
            [
                '{sSoxExe}' => $sSoxExe,
                '{fileA}' => $fileA,
                '{fileB}' => $fileB,
                '{fileResult}' => $fileResult,
            ]
        );

        $this->exec($cmd, false);

        if (file_exists($fileResult))
            return true;

        return false;
    }

  

}
