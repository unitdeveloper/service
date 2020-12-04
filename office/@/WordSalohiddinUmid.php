<?php


namespace zetsoft\service\office;


use Cake\Core\App;
use Mpdf\Tag\Section;
use NumberToWords\NumberToWords;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;
use zetsoft\models\page\PageAction;
use zetsoft\models\App\eyuf\EyufScholar;
use zetsoft\models\place\PlaceAdress;
use zetsoft\models\place\PlaceRegion;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopCourier;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\shop\ShopShipment;
use zetsoft\models\user\User;
use zetsoft\models\user\UserCompany;
use zetsoft\models\ware\WareAccept;
use zetsoft\service\cores\Date;
use zetsoft\service\market\GeneratorBarCode;
use zetsoft\service\market\GeneratorBarCodes;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZUrl;
use zetsoft\system\kernels\ZFrame;
use function Matrix\add;


/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */
class WordSalohiddinUmid extends ZFrame
{
    public $phpword;
    public $project_name;
    public $section;
    public $execute;
    public $file_path = '/excelz/' . App;
    public $shipment;
    public $order;
    public $orderItem;
    public $userCompanies = [];

    #region Init

    public function init()
    {
        parent::init();
        $this->shipment = collect(ShopShipment::find()->asArray()->all());
        $this->order = collect(ShopOrder::find()->asArray()->all());
        $this->orderItem = collect(ShopOrderItem::find()->asArray()->all());
        $this->userCompanies = collect(UserCompany::find()
            ->asArray()
            ->all());


    }

    public function test()
    {
        $this->purchaseProductTest();
        //$this->contract(213);
        /*$this->routelist(9);*/
        /*$this->generateAct(9);
        $this->banderol(213);
        $this->returnCash(213);
        $this->returnProduct(213);*/
    }


    #endregion


    #region Utils

    public function num2str($num)
    {
        function morph($n, $f1, $f2, $f5)
        {
            $n = abs(intval($n)) % 100;
            if ($n > 10 && $n < 20) return $f5;
            $n = $n % 10;
            if ($n > 1 && $n < 5) return $f2;
            if ($n == 1) return $f1;
            return $f5;
        }

        $nul = 'ноль';
        $ten = array(
            array('', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
            array('', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
        );
        $a20 = array('десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать');
        $tens = array(2 => 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто');
        $hundred = array('', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот');
        $unit = array( // Units
            array('сум', 'сум', 'тийин', 0),
            array('тысяча', 'тысячи', 'тысяч', 1),
            array('миллион', 'миллиона', 'миллионов', 0),
            array('миллиард', 'милиарда', 'миллиардов', 0),
        );
        //
        list($rub, $kop) = explode('.', sprintf("%015.2f", floatval($num)));
        $out = array();
        if (intval($rub) > 0) {
            foreach (str_split($rub, 3) as $uk => $v) { // by 3 symbols
                if (!intval($v)) continue;
                $uk = sizeof($unit) - $uk - 1; // unit key
                $gender = $unit[$uk][3];
                list($i1, $i2, $i3) = array_map('intval', str_split($v, 1));
                // mega-logic
                $out[] = $hundred[$i1]; # 1xx-9xx
                if ($i2 > 1) $out[] = $tens[$i2] . ' ' . $ten[$gender][$i3]; # 20-99
                else $out[] = $i2 > 0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
                // units without rub & kop
                if ($uk > 1) $out[] = morph($v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2]);
            } //foreach
        } else $out[] = $nul;
        $out[] = morph(intval($rub), $unit[1][0], $unit[1][1], $unit[1][2]); // rub
        $out[] = $kop . ' ' . morph($kop, $unit[0][0], $unit[0][1], $unit[0][2]); // kop
        return trim(preg_replace('/ {2,}/', ' ', join(' ', $out)));
    }

    #endregion


    /**
     * Склоняем словоформу
     * @ author runcore
     */

    public function testy()
    {
        $test = $this->test(213);
    }

    #region contract

    public function contractTest()
    {
        $id = 4;
        $this->execute = true;
        $this->contract($id);

        /*        $numberToWords = new NumberToWords();

        // build a new number transformer using the RFC 3066 language identifier
                $numberTransformer = $numberToWords->getNumberTransformer('en');*/
    }

    public function contract($id)
    {
        $date = new \DateTime();
        $file_name = "contractTest{$id}.{$date->format('Y-m-d-H-i-s')}.docx";
        $file_open = Root . '/binary/words/1C/Договор_заказ/template.docx';
        $file_close = Root . '/upload ' . $this->file_path . "/" . $file_name;
        $fileJpg = Root . '/upload/imagez/image' . $id . '.jpg';
        $shopOrder = ShopOrder::findOne($id);
        $shopOrderItem = ShopOrderItem::find()->where(
            ['shop_order_id' => $id]
        )->all();
        $companyInfo = UserCompany::findOne($shopOrder->user_company_ids);


        $placeInfo = PlaceAdress::findOne($shopOrder->place_adress_id);
        $sellerAdr = PlaceAdress::findOne($companyInfo->place_adress_id);
        $dynamic = [];
        $id = (string)$shopOrder->id;
        $bn = '000000000000';
        $bn = substr_replace($bn, $id, strlen($bn) - strlen($id), strlen($id));
        Az::$app->market->generatorBarCodes->generateBarcodeJpg($id);
        $staticWord = [
            'orderName' => $companyInfo->name,
            'orderNumber' => $shopOrder->id,
            'date' => $shopOrder->created_at,
            'sellerName' => $companyInfo->name,
            'sellerAddress' => $sellerAdr->place,
            'registerNumber' => $bn,
            'bn' => $bn,
            'orderAddress' => $placeInfo->place,
            'serviceCharge' => $shopOrder->price,
            'totalMoney' => $shopOrder->total_price,
            'numsrtr' => $this->num2str($shopOrder->total_price),
        ];

        foreach ($shopOrderItem as $item) {
            $items = [];
            $items['productName'] = $item['name'];
            $items['quanty'] = $item['amount'];
            $items['money'] = $item['price'];
            $items['productMoney'] = $item['price_all'];
            $dynamic[] = $items;
        }

        $templateProcessor = new TemplateProcessor($file_open);
        $templateProcessor->setValues($staticWord);
        $templateProcessor->setImageValue("barcode", $fileJpg);
        $templateProcessor->cloneRowAndSetValues('productName', $dynamic);

        if ($file_close) {
            $templateProcessor->saveAs($file_close,);

        } else {
            unlink($file_close);
            $templateProcessor->saveAs($file_close,);
        }


        if ($this->execute)
            shell_exec($file_close);
        if (!$this->isCLI()) {
            $url = $this->file_path . '/' . $file_name;
            return $url;
            //$this->urlRedirect($url, false);
        }


    }

    #endregion


    public function cashTemplate($id)
    {
        $date = new \DateTime();
        $file_open = Root . '/binary/words/1C/Счет_на_оплату/template.docx';
        $file_name = "/cashDocument{$id}{$date->format('Y-m-d-H-i-s')}.docx";
        $file_close = Root . '/upload ' . $this->file_path . "/" . $file_name;
        $shopOrder = ShopOrder::findOne($id);
        $shopOrderItem = ShopOrderItem::find()->where(
            ['shop_order_id' => $id]
        )->all();
        $companyInfo = UserCompany::findOne($shopOrder->user_company_ids);;
        $userInfo = User::findOne($shopOrder->user_id);
        $userCompanyInfo = User::findOne($userInfo->user_company_id);
        $sellerAdr = PlaceAdress::findOne($companyInfo->place_adress_id);
        $dynamic = [];
        $i = 1;
        foreach ($shopOrderItem as $item) {
            $items = [];
            $items ['id'] = $i;
            $items['orderTitle'] = $item['name'] ?? ' ';
            $items['amount'] = $item['amount'] ?? ' ';
            $items['price'] = $item['price'] ?? ' ';
            $items['priceAmount'] = $item['price_all'] ?? ' ';
            $i++;
            array_push($dynamic, $items);
        }
        $staticWord = [
            'untilDate' => $shopOrder->date_deliver,
            'orderName' => $userCompanyInfo->name,
            'date' => $shopOrder->created_at,
            'bankName' => $companyInfo->bank,
            'bankAccount' => $companyInfo->bank_account,
            'bankAddress' => $companyInfo->bank_address,
            'orderID' => $shopOrder->id,
            'orderAddress' => $companyInfo->bank_address,
            'sellerName' => $companyInfo->name,
            'sellerPhone' => $companyInfo->phone,
            'sellerAddress' => $sellerAdr->name,
            'priceAll' => $shopOrder->total_price,
            'ids' => $i - 1,
            'numstr' => $this->num2str($shopOrder->total_price)
        ];
        $templateProcessor = new TemplateProcessor($file_open);
        $templateProcessor->setValues($staticWord);
        $templateProcessor->cloneRowAndSetValues('orderTitle', $dynamic);

        if ($file_close) {
            $templateProcessor->saveAs($file_close);

        } else {
            unlink($file_close);
            $templateProcessor->saveAs($file_close);
        }
        $url = $this->file_path . '/' . $file_name;
        $this->urlRedirect($url, false);
    }


    public function generateAct($id)
    {
        $date = new \DateTime();
        $file_open = Root . '/binary/words/1C/Счет_на_оплату/template.docx';
        $file_name = "/cashDocument{$id}{$date->format('Y-m-d-H-i-s')}.docx";
        $file_close = Root . '/upload ' . $this->file_path . "/" . $file_name;
        $ware = WareAccept::findOne($id);

        $shopOrder = ShopOrder::find()->where(['shop_shipment_id' => $ware->shop_shipment_id])->one();
        if ($shopOrder !== null)
            $shopOrderItem = ShopOrderItem::find()->where(
                ['shop_order_id' => $shopOrder->id]
            )->all();

        if ($shopOrder === null) return false;

        $companyInfo = UserCompany::find()->where(['id' => $shopOrder->user_company_ids])->one();
        $userInfo = User::find()->where(['id' => $shopOrder->user_id])->one();

        $userCompanyInfo = User::find()->where(['user_company_id' => $userInfo->user_company_id])->one();
        $sellerAdr = PlaceAdress::findOne($companyInfo->place_adress_id);
        $dynamic = [];
        //  vdd($sellerAdr,$companyInfo);
        $i = 1;
        foreach ($shopOrderItem as $item) {
            $items = [];
            $items ['id'] = $i;
            $items['orderTitle'] = $item['name'];
            $items['amount'] = $item['amount'];
            $items['price'] = $item['price'];
            $items['priceAmount'] = $item['price_all'];
            $i++;
            array_push($dynamic, $items);
        }
        $staticWord = [
            'untilDate' => $shopOrder->date_deliver,
            'orderName' => $userCompanyInfo->name,
            'date' => $shopOrder->created_at,
            'bankName' => $companyInfo->bank,
            'bankAccount' => $companyInfo->bank_account,
            'bankAddress' => $companyInfo->bank_address,
            'orderID' => $shopOrder->id,
            'orderAddress' => $companyInfo->bank_address,
            'sellerName' => $companyInfo->name,
            'sellerPhone' => $companyInfo->phone,
            'sellerAddress' => $sellerAdr->name,
            'priceAll' => $shopOrder->total_price,
            'ids' => $i - 1,
            'numstr' => $this->num2str($shopOrder->total_price)
        ];
        $templateProcessor = new TemplateProcessor($file_open);
        $templateProcessor->setValues($staticWord);
        $templateProcessor->cloneRowAndSetValues('orderTitle', $dynamic);
        if ($file_close) {
            $templateProcessor->saveAs($file_close);

        } else {
            unlink($file_close);
            $templateProcessor->saveAs($file_close);
        }
        $url = $this->file_path . '/' . $file_name;
        $this->urlRedirect($url, false);
    }


    public function banderol($id)
    {
        $date = new \DateTime();
        $file_name = "banderol{$id}{$date->format('Y-m-d-H-i-s')}.docx";
        $fileJpg = Root . '/upload/imagez/image' . $id . '.jpg';
        $file_open = Root . '/binary/words/1C/Бандероль/template.docx';
        $file_close = Root . '/upload ' . $this->file_path . "/" . $file_name;
        $info = ShopOrder::findOne($id);
        $companyInfo = UserCompany::findOne($info->user_company_ids);
        $placeInfo = PlaceAdress::findOne($info->place_adress_id);
        $sellerAdr = PlaceAdress::findOne($companyInfo->place_adress_id);
        $userInfo = User::findOne($info->user_id);
        $regionInfo = PlaceRegion::findOne($placeInfo->place_region_id);

        $id = (string)$info->id;
        $bn = '000000000000';
        $bn = substr_replace($bn, $id, strlen($bn) - strlen($id), strlen($id));
        $staticWord = [
            'banderolNumber' => $id,
            'sellerName' => $companyInfo->name,
            'phoneNumber' => $companyInfo->phone,
            'sellerAddress' => $sellerAdr->place,
            'deliveryCash' => $info->deliver_price,
            'price' => $info->price,
            'totalCash' => $info->total_price,
            'index' => $companyInfo->index,
            'weight' => $info->weight,
            'deliveryDate' => $info->date_deliver,
            'orderName' => $userInfo->name,
            'index' => '00124',
            'orderAddress' => $placeInfo->place,
            'orderCity' => $regionInfo->name,
            'orderStreet' => $placeInfo->street,
            'regnum' => $bn,

        ];
        $templateProcessor = new TemplateProcessor($file_open);
        $templateProcessor->setValues($staticWord);
        $templateProcessor->setImageValue("barcode", $fileJpg);
        $templateProcessor->saveAs($file_close);

        if ($this->execute)
            shell_exec($file_close);
        if (!$this->isCLI()) {
            $url = $this->file_path . '/' . $file_name;
            return $url;
            //$this->urlRedirect($url, false);
        }


    }


    public function generateRouteSheet($id)
    {
        $date = new \DateTime();
        $file_open = Root . '/binary/words/1C//template.docx';
        $file_name = "/cashDocument{$id}{$date->format('Y-m-d-H-i-s')}.docx";
        $file_close = Root . '/upload ' . $this->file_path . "/" . $file_name;
        $shopInfo = ShopOrder::findOne($id);
        $shopItem = ShopOrderItem::find()->where(
            ['shop_order_id' => $id]
        )->all();
        $shopShippment = ShopShipment::findOne($shopInfo->shop_shipment_id);
        $couriers = ShopCourier::findOne($shopShippment->shop_courier_id);
        $companyInfo = UserCompany::findOne($shopInfo->user_company_id);
        $userInfo = User::findOne($shopInfo->user_id);
        $userAddress = PlaceAdress::findOne($shopInfo->place_adress_id);
        Az::$app->market->generatorBarCodes->generateBarcodeJpg($id);
        $static = [
            'docNumber' => $shopInfo->id,
            'courier' => $couriers->name,
            'deliveryDate' => $shopInfo->date_deliver,
            'company' => $companyInfo->name,
            'subTotal' => $shopInfo->price,
            'numProducts' => count($shopItem),
            'total' => $shopInfo->total_price,
            'courierId' => $couriers->id,
            'shipmentDate' => '26.06.2020',
            'shipmentDate2' => '26.06.2020'
        ];


        $dinamic = [];
        $i = 1;
        foreach ($couriers as $courier) {
            $item = [];
            $item['phone'] = $companyInfo->phone;
            $item['fullName'] = $userInfo->name;
            $item['totalAmount'] = $shopInfo->total_price;
            $item['attachmentList'] = 'Гипертофорт';
            $item['orderNumber'] = $shopInfo->id;
            $item['deliveryDate'] = $shopInfo->date_deliver;
            $item['additionalPhone'] = '';
            $item['prePayment'] = $shopShippment->prepayment;
            $item['address'] = $userAddress->name;
            $item['id'] = $i;
            $i++;
            array_push($dinamic, $item);
        }
        $templateProcessor = new TemplateProcessor($file_open);
        $templateProcessor->setValues($static);
        $templateProcessor->cloneRowAndSetValues('fullName', $dinamic);

        $templateProcessor->saveAs($file_close);
        shell_exec($file_close);
    }

    public function routeList($id)
    {
        $date = new \DateTime();
        //  $file_name = "contractTest{$id}.{$date->format('Y-m-d-H-i-s')}.docx";
        $file_open = Root . '/binary/words/1C/Маршрутный_лист/template.docx';
        $file_name = "routeList{$id}-{$date->format('Y-m-d-H-i-s')}.docx";

        $file_close = Root . '/upload' . $this->file_path . "/" . $file_name;

        $ware = WareAccept::findOne($id);


        /** @var ShopOrder $shopInfo */
        $shopInfo = ShopOrder::find()->where(['shop_shipment_id' => $ware->shop_shipment_id]);


        $shopShippment = ShopShipment::findOne($shopInfo->one()->shop_shipment_id);
        //$shopShippment = $shopInfo->getShopShipment();
        // $shopShippment = $shopInfo->getShopShipmentOne();

        $couriers = ShopCourier::findOne($shopShippment->shop_courier_id);
        $companyInfo = UserCompany::find()->where(['id' => $shopInfo->one()->user_company_ids])->one();

        $numProducts = ShopOrderItem::find()->where(['shop_order_id' => $shopInfo->one()->id])->count();


        $dinamic = [];
        $i = 1;
        $subTotal = 0;
        foreach ($shopInfo->all() as $order) {

            $userInfo = User::find()->where(['id' => $order->user_id])->one();
            $userAddress = PlaceAdress::find()->where(['id' => $order->place_adress_id])->one();

            $order_items = ShopOrderItem::find()->where(['shop_order_id' => $order->id])->all();
            $items = [];

            foreach ($order_items as $item) {
                $catalog = ShopCatalog::find()->where(['id' => $item->shop_catalog_id])->one();
                $items[] = $catalog->name;
            }

            $subTotal += $order->total_price;

            $item = [];
            $item['phone'] = $userInfo->phone;
            $item['fullName'] = $userInfo->name;
            $item['totalAmount'] = $order->total_price;
            $item['attachmentList'] = toString($items);
            $item['orderNumber'] = $order->id;
            $item['deliveryDate'] = $order->date_deliver;
            $item['additionalPhone'] = '';
            $item['prePayment'] = $shopShippment->prepayment;
            $item['address'] = $userAddress->name;
            $item['id'] = $i;
            $i++;
            $dinamic[] = $item;

        }

        $static = [
            'docNumber' => $shopInfo->one()->id,
            'courier' => $couriers->name,
            'deliveryDate' => $shopInfo->one()->date_deliver,
            'company' => $companyInfo->name,
            'subTotal' => $subTotal,
            'numProducts' => $i - 1,
            'total' => $shopInfo->one()->total_price,
            'courierId' => $couriers->id,
            'courierName' => $couriers->name,
            'shipmentDate' => $shopShippment->created_at,
            'shipmentDate2' => $shopShippment->modified_at
        ];
        $templateProcessor = new TemplateProcessor($file_open);
        $templateProcessor->setValues($static);
        $templateProcessor->cloneRowAndSetValues('fullName', $dinamic);

        if ($file_close) {
            $templateProcessor->saveAs($file_close);
        } else {
            unlink($file_close);
            $templateProcessor->saveAs($file_close);
        }

        //$url = $file_close;
        $url = $this->file_path . '/' . $file_name;
        //vdd($url);
        if (!$this->isCLI())
            $this->urlRedirect($url, false);
        else {
            vdd($url);
        }
    }


    public function generateReturnGoods()
    {
        $templateProcessor = new TemplateProcessor(Root . '/binary/words/1C/ReturnGoodsTemplate.docx');

        // Dinamic starts
        $dinamic = [
            [
                'orderId' => 1,
                'orderNumber' => 'CP00-052905',
                'orderedDate' => '19.06.2020',
                'amount' => 3,
                'price' => '297,000'
            ],
            [
                'orderId' => 2,
                'orderNumber' => 'CP00-052905',
                'orderedDate' => '19.06.2020',
                'amount' => 3,
                'price' => '297,000'
            ],
            [
                'orderId' => 3,
                'orderNumber' => 'CP00-052905',
                'orderedDate' => '19.06.2020',
                'amount' => 3,
                'price' => '297,000'
            ],
        ];
        $templateProcessor->cloneRowAndSetValues('orderId', $dinamic);
        // Dinamic ends

        // Static starts
        $static = [
            'courier' => 'Ахунджанов Даврон',
            'totalPrice' => '2,784,000',
            'goodsQuantity' => 3,
        ];
        $templateProcessor->setValues($static);
        // Static ends

        $el = $templateProcessor->save();
        $newFile = IOFactory::load($el);
        $objWriter = IOFactory::createWriter($newFile, 'Word2007');

        $objWriter->save(Root . '/binary/words/1C/returngoods/ReturnGoods.docx');
    }

    public function returnCash($id)
    {
        $date = new \DateTime();
        $file_name = "/cashReturn{$id}{$date->format('Y-m-d-H-i-s')}.docx";
        $file_close = Root . '/upload ' . $this->file_path . "/" . $file_name;
        $file_open = Root . '/binary/words/1C/Заявление_на_возврат_ДС/template.docx';
        $shopOrder = ShopOrder::findOne($id);
        $shopOrderItem = ShopOrderItem::find()->where(
            ['shop_order_id' => $id]
        )->all();

        $userInfo = User::findOne($shopOrder->user_id);
        $placeInfo = PlaceAdress::findOne($shopOrder->place_adress_id);

        $y = 0;
        foreach ($shopOrderItem as $k) {
            $y += $k['amount_return'];
        }
        $staticWord = [
            'name' => $userInfo->name,
            'address' => $placeInfo->name,
            'phone' => $userInfo->phone,
            'amount' => $y,
            'date' => $shopOrder->created_at

        ];
        $templateProcessor = new TemplateProcessor($file_open);
        $templateProcessor->setValues($staticWord);

        $templateProcessor->saveAs($file_close);


        if ($this->execute)
            shell_exec($file_close);
        if (!$this->isCLI()) {
            $url = $this->file_path . '/' . $file_name;
            return $url;
            //   shell_exec($file_close);
        }

    }


    public function returnProduct($id)
    {
        $date = new \DateTime();
        $file_name = "/productReturn{$id}{$date->format('Y-m-d-H-i-s')}.docx";
        $file_close = Root . '/upload ' . $this->file_path . "/" . $file_name;

        $file = Root . '/binary/words/1C/Заявление_на_возврат_товаров/template.docx';
        $shopInfo = ShopOrder::findOne($id);
        $shopItem = ShopOrderItem::find()->where(
            ['shop_order_id' => $id]
        )->all();
        $companyInfo = UserCompany::findOne($shopInfo->user_company_ids);
        $static = [
            'sellerName' => $companyInfo->name,
            'totalPrice' => $shopInfo->total_price,
            'date' => $shopInfo->created_at,
        ];
        $templateProcessor = new TemplateProcessor($file);
        $dynamic = [];
        $i = 1;
        foreach ($shopItem as $items) {
            $item = [];
            $item['productName'] = $items->name;
            $item['id'] = $i;
            $item['amount'] = $items->amount;
            $item['price'] = $items->price;
            $i++;
            array_push($dynamic, $item);
        }
        $templateProcessor->setValues($static);
        $templateProcessor->cloneRowAndSetValues('productName', $dynamic);
        $templateProcessor->saveAs($file_close);

        if ($this->execute)
            shell_exec($file_close);
        if (!$this->isCLI()) {
            $url = $this->file_path . '/' . $file_name;
            return $url;
            //   shell_exec($file_close);
        }

    }

    public function purchaseProductTest()
    {
        $this->purchaseProduct(104);
    }

    public function purchaseProduct($user_id)
    {

        $orders = $this->order->where('status_logistics', 'part_refunded')->where('user_id', $user_id);
        $shipment = $this->shipment->whereIn('id', $orders->pluck('shop_shipment_id'))->first();
        $productId = User::findOne($user_id)->name;
        $courier = User::findOne($shipment['shop_courier_id'])->name;

        $orderItem = $this->orderItem;
        $countUser = 0;
        $totalPrice = 0;
        $dinamic = [];
        foreach ($orders as $val) {

            $orderItem = $orderItem->where('shop_order_id', $val['id']);

            foreach ($orderItem as $value) {
                $item = [];
                $item['orderId'] = $val['id'];
                $item['orderNumber'] = $val['name'];
                $item['orderedDate'] = $val['created_at'];
                $countUser++;
                
                $totalPrice += doubleval($value['price_all']);
                $item['amount'] = $value['amount'];
                $item['price'] = string(doubleval($value['price_all']));
                $dinamic[]=$item;
            }
        }

        $date = new \DateTime();
        $file_name = "contractTestSaloh{$user_id}.{$date->format('Y-m-d-H-i-s')}.docx";
        $file_open = Root . '/binary/words/1C/Возврат_от_клиента/template.docx';
        $file_close = Root . '/upload ' . $this->file_path . "/" . $file_name;


        $static = [
            'productId' => $productId,
            'courier' => $courier,
            'goodsQuantity' => $countUser,
            'totalPrice' => $totalPrice,
        ];

       
        $templateProcessor = new TemplateProcessor($file_open);
        $templateProcessor->setValues($static);

        $templateProcessor->cloneRowAndSetValues('orderId', $dinamic);

        if ($file_close) {
            $templateProcessor->saveAs($file_close);

        } else {

            unlink($file_close);
            $templateProcessor->saveAs($file_close);
        }


        if ($this->execute)
            shell_exec($file_close);
        if (!$this->isCLI()) {
            $url = $this->file_path . '/' . $file_name;

            return $url;

        }
    }
}

