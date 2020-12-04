<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * Date:    11.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\App\eyuf;


use Fusonic\Linq\Linq;
use
    kartik\form\ActiveForm;
use PhpOffice\PhpWord\PhpWord;
use Yii;
use zetsoft\dbitem\App\eyuf\CurrencyItem;
use zetsoft\former\eyuf\EyufCompletedForm;
use zetsoft\former\eyuf\EyufProgramForm;
use zetsoft\models\place\PlaceCountry;
use zetsoft\models\pays\PaysCurrency;
use zetsoft\models\user\User;

use zetsoft\models\App\eyuf\EyufDocument;
use zetsoft\models\App\eyuf\EyufDocumentType;
use zetsoft\models\App\eyuf\EyufScholar;
use zetsoft\system\actives\ZArrayQuery;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\helpers\ZJsonHelper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\widgets\former\ZTableWrapWidget;
use zetsoft\widgets\inputes\ZFileInputWidget;
use kartik\widgets\Growl;
use yii\web\Response;

class Excel2 extends ZFrame
{
    public $phpword;
    public $project_name;
    public $section;


    /**
     *
     * Function  curr
     * @param EyufScholar $scholar
     * @throws \Exception
     */

    public function curr($scholar)
    {
        $currency = PaysCurrency::find()
            ->orderBy([
                'created_at' => SORT_DESC,
            ])
            ->limit(1)
            ->one();

        $item = new CurrencyItem();

        $item->bank = ZArrayHelper::getValue($currency->bank, $scholar->currency);
        $item->cbu = ZArrayHelper::getValue($currency->cbu, $scholar->currency);
        return $item;
    }
}
