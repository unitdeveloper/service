<?php

/**
 * Author: Jaxongir
 */

namespace zetsoft\service\market;

use FontLib\Table\Type\post;
use Google\ApiCore\OperationResponse;
use Illuminate\Support\Collection;
use yii\caching\TagDependency;
use zetsoft\webs\core\ZReturnAction;
use zetsoft\dbitem\shop\AdminItem;
use zetsoft\dbitem\shop\OrderElementItem;
use zetsoft\dbitem\shop\CompanyCardItem;
use zetsoft\dbitem\shop\SellerItem;
use zetsoft\dbitem\wdg\MenuItem;
use zetsoft\dbitem\shop\OrderItem;
use zetsoft\dbitem\shop\ProductItem;
use zetsoft\dbitem\shop\PropertyItem;
use zetsoft\dbitem\data\ALLApp;
use zetsoft\dbitem\data\Config;
use zetsoft\dbitem\data\ConfigDB;
use zetsoft\dbitem\data\Form;
use zetsoft\former\chart\ChartForm;
use zetsoft\models\core\CoreSession;
use zetsoft\models\page\PageAction;
use zetsoft\models\place\PlaceAdress;
use zetsoft\models\shop\ShopBrand;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopCatalogWare;
use zetsoft\models\shop\ShopCategory;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\shop\ShopShipment;
use zetsoft\models\user\UserCompany;

use zetsoft\models\place\PlaceCountry;
use zetsoft\models\shop\ShopElement;
use zetsoft\models\menu\Menu;
use zetsoft\models\menu\MenuImage;
use zetsoft\models\core\CoreMenuNew;
use zetsoft\models\shop\ShopOption;
use zetsoft\models\shop\ShopOptionType;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopProduct;
use zetsoft\models\place\PlaceRegion;
use zetsoft\models\user\User;
use zetsoft\models\App\eyuf\Cupon;
use zetsoft\system\assets\ZColor;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\helpers\ZUrl;
use zetsoft\system\kernels\ZFrame;
use zetsoft\widgets\former\ZDynaWidget;
use zetsoft\widgets\former\ZFormWidget;
use zetsoft\widgets\incores\ZMCheckboxGroupWidget;
use zetsoft\widgets\incores\ZMRadioWidget;
use zetsoft\widgets\inputes\ZHCheckboxButtonGroupWidget;
use zetsoft\widgets\inputes\ZSelect2Widget;
use zetsoft\widgets\navigat\ZGAccordionWidget;
use zetsoft\widgets\values\ZFormViewWidget;
use function PHPUnit\Framework\isInstanceOf;
use function Spatie\array_keys_exist;


class Admin extends ZFrame
{

    public $users;

    #region init

    public function init()
    {
        parent::init();
        $this->users = User::find()->select('role')->asArray()->all();
    }

    #endregion

    public function adminInfo($role)
    {
        $result = new AdminItem();
        $result->user = 0;
        foreach ($this->users as $user)
        {
            if ($user['role'] === $role) {
                $result->user += 1;
            }
        }

        vdd($result->user);

        vd(array_count_values($result->user,'role'));
        /*$result->seller = User::find()->where([
            'role' => 'seller'
        ])->count();*/

        $form = new ChartForm();
        $form->name = 'Monday';
        $form->in = 1000;
        $form->out = 853.8;
        $form->out2 = 853.8;
        $forms[] = $form;


    }


}
