<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\payer;

use React\Tests\Filesystem\UnknownNodeType;
use zetsoft\dbitem\data\Config;
use zetsoft\dbitem\data\Form;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\pays\PaysCurrency;
use zetsoft\models\shop\ShopProduct;
use zetsoft\service\https\Guzzle;
use zetsoft\system\Az;
use zetsoft\system\actives\ZModel;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\dbitem\data\Event;
use zetsoft\dbitem\data\ConfigDB;
use zetsoft\system\helpers\ZJsonHelper;
use zetsoft\system\kernels\ZFrame;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;


/**
 *
 * Class CoreCurrencyForm
 */
class  Currency2 extends ZFrame
{
    public $core_currency;

    public function init()
    {
        parent::init();

        $this->core_currency = collect(PaysCurrency::find()->asArray()->all());
    }


    public function getCurrencyList()
    {

        return $res = Az::$app->https->guzzle->request(Guzzle::types['GET'], 'https://cbu.uz/oz/arkhiv-kursov-valyut/json/');

    }

    public function getCurrencyListNBU()
    {
        return $res = Az::$app->https->guzzle->request(Guzzle::types['GET'], 'https://nbu.uz/uz/exchange-rates/json/');
    }

    public function listCurrencies()
    {
        $core_catalog = new ShopCatalog();
        $list = $core_catalog->_currency;

        $list_currencies = [];
        $cl = $this->getCurrencyList();
        if ($cl->getStatusCode() === 200) {
            $res = ZJsonHelper::decode($cl->getBody());
            foreach ($res as $currency) {
                foreach ($list as $key => $value) {
                    if (ZArrayHelper::isIn($key, $currency)) {
                        $list_currencies[$currency['Ccy']] = $currency['Rate'];
                    }
                }
            }
            return $list_currencies;
        }

        return Az::error(__FUNCTION__ . ' CBU Currency api not working');

    }

    public function listCurrenciesNBU()
    {
        $core_catalog = new ShopCatalog();
        $list = $core_catalog->_currency;

        $list_currencies = [];
        $cl = $this->getCurrencyListNBU();
        if ($cl->getStatusCode() === 200) {
            $res = ZJsonHelper::decode($cl->getBody());
            foreach ($res as $currency) {
                foreach ($list as $key => $value) {
                    if (ZArrayHelper::isIn($key, $currency)) {
                        $list_currencies[$currency['code']] = $currency['cb_price'];

                    }
                }
            }
            return $list_currencies;
        } else {
            return Az::error(__FUNCTION__ . 'NBU Currency api not working');
        }

    }

    public function listCellCurrenciesNBU()
    {
        $core_catalog = new ShopCatalog();
        $list = $core_catalog->_currency;

        $list_currencies = [];
        $cl = $this->getCurrencyListNBU();
        if ($cl->getStatusCode() === 200) {
            $res = ZJsonHelper::decode($cl->getBody());
            foreach ($res as $currency) {
                foreach ($list as $key => $value) {
                    if (ZArrayHelper::isIn($key, $currency)) {
                        $list_currencies[$currency['code']] = $currency['nbu_cell_price'];

                    }
                }
            }
            return $list_currencies;
        } else {
            return Az::error(__FUNCTION__ . 'NBU Currency api not working');
        }

    }


    public function fullCurrenyTable()
    {
        $currency = new PaysCurrency();

        $currency->cbu = $this->listCurrencies();

        $currency->bank = $this->listCurrenciesNBU();

        $currency->bank_sell = $this->listCellCurrenciesNBU();

        $currency->save();

        return $currency;

    }


    public function convert($from = 'uzs', $to = 'uzs', $amount)
    {
        //sortByDesc
        $latest = $this->core_currency->sortByDesc('created_at')->first();

        if ($latest === null) return 0;
        /*$current_bank = json_decode($latest['cbu'], true);*/
            //todo:start Daho
            $current_bank = $latest['cbu'];
            //todo:end 

        $currency_from = $current_bank[$from] ?? 1;
        $currency_to = $current_bank[$to] ?? 1;

        $sum = $amount * $currency_from;

        return ceil(($sum / $currency_to) * 100) / 100;

    }
}
