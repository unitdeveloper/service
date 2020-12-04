<?php

/**
 * Author: Xolmat
 * Date:    07.06.2020
 * ModifyedBy: Javohir
 */

namespace zetsoft\service\market;

use Foolz\SphinxQL\Expression;
use Illuminate\Support\Collection;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Url;
use yii\base\ErrorException;
use zetsoft\dbitem\chat\QuestionItem;
use zetsoft\dbitem\wdg\MenuItem;
use zetsoft\models\shop\ShopBrand;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopCategory;
use zetsoft\models\shop\ShopElement;
use zetsoft\models\shop\ShopOffer;
use zetsoft\models\shop\ShopOption;
use zetsoft\models\shop\ShopOptionBranch;
use zetsoft\models\shop\ShopOptionType;
use zetsoft\models\shop\ShopProduct;
use zetsoft\models\shop\ShopQuestion;
use zetsoft\models\user\UserCompany;
use zetsoft\models\user\UserRbac;
use zetsoft\system\Az;
use zetsoft\models\core\CoreMenuNew;
use zetsoft\models\shop\ShopReview;
use zetsoft\models\user\User;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\schema\ArrayExpression;
use function Clue\StreamFilter\fun;


class Offer extends ZFrame
{
    public $shop_products;
    public $is_seperated_by_branch = true;
    public $shop_elements;
    public $shop_catalogs;
    public $shop_categories;
    public $core_option_types;
    public $core_options;
    public $core_companies;
    public $core_option_branches;
    public $core_brands;

    /* @var Collection $shop_offer */
    public $shop_offer;
    public $shop_option_branches;

    #region init
    public function init()
    {



        $this->core_brands = collect(ShopBrand::find()->asArray()->all());
        $this->shop_option_branches = collect(ShopOptionBranch::find()->asArray()->all());
        $this->shop_products = collect(ShopProduct::find()->asArray()->all());
        $this->shop_elements = collect(ShopElement::find()->asArray()->all());
        $this->shop_catalogs = collect(ShopCatalog::find()->asArray()->all());
        $this->shop_categories = collect(ShopCategory::find()->asArray()->all());
        $this->core_option_types = collect(ShopOptionType::find()->asArray()->all());
        $this->core_options = collect(ShopOption::find()->asArray()->all());
        $this->core_companies = collect(UserCompany::find()->asArray()->all());
        $this->core_option_branches = collect(ShopOptionBranch::find()->asArray()->all());
        $this->shop_offer = collect(ShopOffer::find()->asArray()->all());
        parent::init();
    }

    public function test()
    {
        // $this->productByStatusTest();
        // $this->getOffersWithStatusTest();
        // $this->offersTest();
        $this->catalogByStatusTest();
    }
    #endregion

    #region GetAllOffer

    #region   getOffersWithStatus
    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    //this function shows offers by $status
    public function getOffersWithStatusTest()
    {
        $status = true;
        $date = true;
        $date_condition = "=";
        $data = $this->getOffersWithStatus($status, $date, $date_condition);
        vd($data);
    }


    public function getOffersWithStatus($status, $date, $date_condition = '=')
    {
        Az::start(__FUNCTION__);

        $catalog_offers = ShopCatalog::findBySql('SELECT  * FROM shop_catalog WHERE offer @> \'[{"offer" : "' . $status . '"}]\' ;')->all();

        $catalog_offers = collect($catalog_offers);
        $list_offer = [];

        $data = $catalog_offers->map(function ($catalog_offer) use ($status, $date, $date_condition) {
            $list_offer['offer'] = collect($catalog_offer->offer)->where('offer', $status)->where('end', $date_condition, $date);
            $list_offer['id'] = $catalog_offer->id;
            $list_offer['code'] = $catalog_offer->code;
            $list_offer['user_company_id'] = $catalog_offer->user_company_id;
            $list_offer['shop_element_id'] = $catalog_offer->shop_element_id;
            $list_offer['amount'] = $catalog_offer->amount;
            $list_offer['price'] = $catalog_offer->price;
            $list_offer['price_old'] = $catalog_offer->price_old;
            $list_offer['currency'] = $catalog_offer->currency;
            $list_offer['available'] = $catalog_offer->available;

            return $list_offer;

        });
        $data = $data->reject(function ($catalog_offers) {
            return (\Dash\count($catalog_offers['offer']) === 0);
        });

        return $data;

    }

    #endregion

    #region   offerCategoriesWithProducts
    public function offerCategoriesWithProductsTest()
    {
        $data = $this->getOffersWithStatus();
        vd($data);
    }

    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    //this function shows offers by $status
    public function offersTest()
    {
        $status = 'free_delivery';
        $data = $this->offers($status);
        vdd($data);
    }

    public function offers($offer)
    {

        $value = ZVarDumper::search($offer);

        $data = ShopOffer::find()
            ->where("type @> $value")
            ->asArray()
            ->all();


        return $data;
        /*$offers = collect($data);

        vd($offers);*/

    }

    public function offerCategoriesWithProducts($category_id = null, $status, $company_id = null, $count = 3)
    {

        $offers = $this->shop_offer
            ->where('type', $status)
            ->all();

        $ids = [];
        foreach ($offers as $offer) {
            $ids[] = $offer['shop_catalog_id'];
        }

        if ($company_id == null)
            $catalogs = collect(ShopCatalog::find()
                ->where([
                    'id' => $ids
                ])
                ->asArray()->all());
        else
            $catalogs = collect(ShopCatalog::find()
                ->where([
                    'id' => $ids,
                    'user_company_id' => $company_id
                ])
                ->asArray()->all());


        $elements = $this->shop_elements->whereIn('id', $catalogs->pluck('shop_element_id'));

        $products = $this->shop_products->whereIn('id', $elements->pluck('shop_product_id'));
        // vdd($category_id);
        if ($category_id !== null)
            $products = $products->where('shop_category_id', $category_id);
        // vdd($products);
        $products = $products->take($count)->map(function ($product, $key) {
            $category = ShopCategory::findOne($product['shop_category_id']);
            $menuItme = new MenuItem();
            $menuItme->name = $category->name;
            $menuItme->url = "asdfasdf";
            $menuItme->id = $category->id;

            $menuItme->items = Az::$app->market->product->product($product['id']);

            return $menuItme;
        });

        return $products;


    }
    #endregion
    #region  productByStatus
    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    //this function shows products by $status
    public function productByStatusTest()
    {
        $category_id = null;
        $status = 'deal_of_day';
        $count = 3;
        $company_id = null;

        $data = $this->productByStatus($category_id, $status, $count, $company_id);
        vd($data);


    }

    public function statusTest() {
        return [];
    }


    public function productByStatus($status, $count = 3)
    {


        Az::start(__FUNCTION__);
        $ids = [];

        foreach ($this->shop_products as $offer) {
            if ($offer['offer'] != null) {

               /// $array = json_decode($offer['offer'], true);

                if (is_array($offer['offer']))
                    if (in_array($status, $offer['offer'])) {
                        $ids[] = $offer['id'];
                    }
            }

        }
        
        $products = $this->shop_products->whereIn('id', $ids);
        $products = $products->take($count)
            ->map(function ($product, $key) {
            
        // todo: AzimjonToirov bitta PRODUCT NULL kevotti ushani filterlab beradi

                $oneProduct = Az::$app->market->product->product($product['id']);

                if ($oneProduct === null)
                    return false;

                return $oneProduct;

            })->reject(function ($oneProduct) {
                return $oneProduct === false;
            });
            
        // todo: AzimjonToirov bitta PRODUCT NULL kevotti ushani filterlab beradi
        
        return $products;

    }

    #endregion
    #region catalogByStatus

    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    //this function shows products by $status
    public function catalogByStatusTest()
    {
        $status = 'new';
        $company_id = 133;
        $count = 10;
        $data = $this->catalogByStatus($status, $company_id, $count);
        vdd($data);
    }

    public function catalogByStatus($status, $company_id = null, $count = 3)
    {
        $ids = [];
        foreach ($this->shop_offer as $offer) {
            if ($offer['type'] != null) {
                $array = json_decode($offer['type'], true);
                if (is_array($array))
                    if (in_array($status, $array))
                        $ids[] = $offer['shop_catalog_id'];
            }
        }
        if ($company_id == null)
            $catalogs = collect(ShopCatalog::find()
                ->where([
                    'id' => $ids
                ])
                ->asArray()->all());
        else
            $catalogs = collect(ShopCatalog::find()
                ->where([
                    'id' => $ids,
                    'user_company_id' => $company_id
                ])
                ->asArray()->all());


        $elements = $this->shop_elements->whereIn('id', $catalogs->pluck('shop_element_id'));

        $products = $this->shop_products->whereIn('id', $elements->pluck('shop_product_id'));
        $cart = $this->sessionGet('cart');
        /* Az::$app->market->product->singleProductItemByOptions(41, 133, [207, 210, 213, 215]);*/
        $products = $products->take($count)->map(function ($product, $key) use ($company_id, $elements) {
            $item = Az::$app->market->product->product($product['id'], $company_id, true);

            if ($item->is_multi) {
                foreach ($elements as $element)
                    if ($element['shop_product_id'] === $product['id']) {

                        if ($element['shop_option_ids'] === null) {
                            return Az::$app->market->product->singleProductItemByOptions($product['id'], $company_id, []);
                        }

                        $opts = json_decode($element['shop_option_ids'], true);

                        if (is_array($opts))

                            return Az::$app->market->product->singleProductItemByOptions($product['id'], $company_id, $opts);
                    }

            } else
                return $item;
        });

        if ($cart)
            foreach ($cart as $value) {
                $catalogId = $value['catalog_id'];
                $amount = $value['amount'];
                foreach ($products as $item) {
                    $item->cart_amount = $catalogId === $item->catalogId ? $amount : $item->cart_amount;
                }
            }
        return $products;

    }

    #endregion


#endregion

#region testJaxongir
    public function offerJaxongir($category_id = null, $status, $company_id = null, $count = 3)
    {

        $offers = $this->shop_offer
            ->where('type', $status)
            ->all();

        $ids = [];
        foreach ($offers as $offer) {
            $ids[] = $offer['shop_catalog_id'];
        }

        if ($company_id == null)
            $catalogs = $this->shop_catalogs->whereIn('id', $ids);
        else
            $catalogs = $this->shop_catalogs->whereIn('id', $ids,
                'user_company_id', $company_id);

        $elements = $this->shop_elements->whereIn('id', $catalogs->pluck('shop_element_id'));
        $products = $this->shop_products->whereIn('id', $elements->pluck('shop_product_id'));

        if ($category_id !== null)
            $products = $products->where('shop_category_id', $category_id);

        $products = $products->take($count)->map(function ($product, $key) {
            $category = $this->shop_categories->where('id', $product['shop_category_id'])->first();
            $menuItme = new MenuItem();
            $menuItme->name = $category['name'];
            $menuItme->url = "asdfasdf";
            $menuItme->id = $category['id'];

            $menuItme->items = Az::$app->market->product->product($product['id']);

            return $menuItme;
        });

        return $products;


    }

    #endregion


    #region

    public function getOffersByMarketId($marketId = null)
    {

        $markets = ShopCatalog::find()
            ->where(['user_company_id' => $marketId])
            ->all();

        /*$types = ShopOffer::find()
            ->where([
                'shop_catalog_id' => $markets->id
            ])->one();*/


    }

    #endregion
}
