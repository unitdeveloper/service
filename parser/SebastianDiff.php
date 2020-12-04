<?php

namespace zetsoft\service\parser;

require Root . '/vendori/parser/vendor/autoload.php';

/*
 * @package sebastian/diff
 * @author Sukhrob Nuraliev
 * https://packagist.org/packages/sebastian/diff
 */

use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use zetsoft\system\kernels\ZFrame;
use SebastianBergmann\Diff\Differ;

class SebastianDiff extends ZFrame
{
    /*
     * PhpExamples
     */

    // The Differ class can be used to generate a textual representation of the difference between two strings:
    public function generatingDiff()
    {
        $differ = new Differ();
        echo $differ->diff('foo', 'bar');
    }

    // This is default builder, which generates the output close to udiff and is used by PHPUnit.
    public function UnifiedDiffOutputBuilder()
    {
        $builder = new UnifiedDiffOutputBuilder(
            "--- Oriignal\n++ New\n",       // custom header
            false                           // do not add line numbers to the diff
        );

        $differ = new Differ($builder);
        echo $differ->diff('foo', 'bar');
    }
}
