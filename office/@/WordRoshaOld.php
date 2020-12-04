<?php


namespace zetsoft\service\office;


use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;
use zetsoft\models\App\eyuf\EyufScholar;
use zetsoft\models\place\PlaceAdress;
use zetsoft\models\place\PlaceRegion;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopCourier;
use zetsoft\models\shop\ShopElement;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\shop\ShopShipment;
use zetsoft\models\user\User;
use zetsoft\models\user\UserCompany;
use zetsoft\models\ware\WareAccept;
use zetsoft\service\market\GeneratorBarCode;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;


/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */
class WordRosha extends ZFrame
{
    public $phpword;
    public $project_name;
    public $section;
    public $execute;
    public $file_path = '/excelz/' . App;

    public $userCompanies = [];

    #region Init

    public function init()
    {
        parent::init();

        $this->userCompanies = collect(UserCompany::find()
            ->asArray()
            ->all());

    }

    /**
     * Склоняем словоформу
     * @ author runcore
     */

    public function testy()
    {
        $test = $this->test(213);
    }


    #endregion


    #region Utils

    public function test()
    {
        //$this->contract(9);
        $this->routelist(62);
         //$this->banderol(9);
        //$this->returnCash(43);
        //$this->returnProduct(43);
        //$this->generateAct(31);
    }

    #endregion

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
        $staticWord = [
            'orderName' => Az::l('Нет имени покупателя'),
            'orderNumber' => Az::l('Номер заказа отсутствует'),
            'date' => Az::l('Дата заказа не указана'),
            'sellerName' => Az::l('Нет имени продавца'),
            'sellerAddress' => Az::l('Нет адреса продавца'),
            'registerNumber' => '{Пусто}',
            'bn' => '{Пусто}',
            'orderAddress' => Az::l('Адресс покупателя отсутствует'),
            'serviceCharge' => Az::l('Не указан'),
            'totalMoney' => 0,
            'numsrtr' => '',
        ];
        $date = new \DateTime();
        $file_name = "contractTest{$id}.{$date->format('Y-m-d-H-i-s')}.docx";
        $file_open = Root . '/binary/words/1C/Договор_заказ/template.docx';
        $file_close = Root . '/upload ' . $this->file_path . "/" . $file_name;
        $fileJpg = Root . '/upload/imagez/image' . $id . '.jpg';
        $shopOrder = ShopOrder::findOne($id);

        if ($shopOrder !== null) {
            $staticWord['serviceCharge'] = $shopOrder->price ?? '{Пусто}';
            $staticWord['orderNumber'] = $shopOrder->id ?? '{Номер товара не указан!}';
            $staticWord['totalMoney'] = $shopOrder->total_price ?? '{Не указан}';
            $staticWord['date'] = $shopOrder->created_at ?? '{Не указан!}';
            $staticWord['orderName'] = $shopOrder->contact_name ?? '{Не указан!}';
        }

        $shopOrderItem = ShopOrderItem::find()->where(
            ['shop_order_id' => $id]
        )->all();
        $idi = (string)$shopOrder->id;
        $bn = '000000000000';
        $bn = substr_replace($bn, $idi, strlen($bn) - strlen($idi), strlen($idi));
        $staticWord['registerNumber'] = $bn;
        $staticWord ['bn'] = $bn;
        if ((int)$shopOrder->user_company_ids > 0) {

            $companyInfo = UserCompany::findOne($shopOrder->user_company_ids);

            if ($companyInfo !== null) {
                $staticWord['sellerName'] = $companyInfo->name;
                $staticWord['phoneNumber'] = $companyInfo->phone;
                $staticWord['index'] = $companyInfo->index;
                $staticWord['numsrtr'] = $this->num2str($shopOrder->total_price);
            }
            $sellerAdr = PlaceAdress::findOne($companyInfo->place_adress_id ?? 3);

            if ($sellerAdr !== null)
                $staticWord['sellerAddress'] = $sellerAdr->place;
        }


        $placeInfo = PlaceAdress::findOne($shopOrder->place_adress_id);
        if ($placeInfo !== null) {
            $staticWord['orderAddress'] = $placeInfo->name;
        }

        $dynamic = [];
        $id = (string)$shopOrder->id;
        $bn = '000000000000';
        $bn = substr_replace($bn, $id, strlen($bn) - strlen($id), strlen($id));
        $staticWord['registerNumber'] = $bn;

        Az::$app->market->generatorBarCodes->generateBarcodeJpg($id);
        /*  $staticWord = [
              'orderName' => $companyInfo->name??'{Пусто}',+
              'orderNumber' => $shopOrder->id??'{Пусто}',+
              'date' => $shopOrder->created_at??'{Пусто}',+
              'sellerName' => $companyInfo->name??'{Пусто}',
              'sellerAddress' => $sellerAdr->place??'{Пусто}',
              'registerNumber' => $bn??'{Пусто}',+
              'bn' => $bn??'{Пусто}',+
              'orderAddress' => $placeInfo->place??'',
              'serviceCharge' => $shopOrder->price??'{Пусто}',+
              'totalMoney' => $shopOrder->total_price??'{Пусто}',+
              'numsrtr' => $this->num2str($shopOrder->total_price),+
          ];
  */
        foreach ($shopOrderItem as $item) {
            $items = [];
            $items['productName'] = $item['name'] ?? 'Не указан!';
            $items['quanty'] = $item['amount'] ?? 'Не указан!';
            $items['money'] = $item['price'] ?? 'Не указан!';
            $items['productMoney'] = $item['price_all'] ?? 'Не указан!';
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
            $this->urlRedirect($url, false);
        }


    }

    #endregion

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
        $static = [
            'docNumber' => Az::l('Не указан'),
            'courier' => Az::l('Нет имени курьера'),
            'createDate' => Az::l('Дата не указана'),
            'shipmentDate' => Az::l('Дата не указана'),
            'shipmentDate2' => Az::l('Дата не указана'),
            'numProducts' => 0,
            'totalPrice' => 0,
            'company' => Az::l('Не указан'),
            'companyPhone' => Az::l('Не указан')
        ];
        $date = new \DateTime();
        $file_open = Root . '/binary/words/1C/Акт_передачи/template.docx';
        $file_name = "Act{$id}{$date->format('Y-m-d-H-i-s')}.docx";
        $file_close = Root . '/upload ' . $this->file_path . "/" . $file_name;
        $ware = WareAccept::findOne(['shop_shipment_id'=>$id]);

        $dinamic = [];


        if ($ware !== null) {
            $shopShippment = ShopShipment::findOne(['id' => $ware->shop_shipment_id]);

            if ($shopShippment !== null) {
                $static['docNumber'] = $shopShippment->id ?? 'Не указан';
                $static['createDate'] = $shopShippment->date_deliver ?? 'Не указан';
                $static['shipmentDate'] = $shopShippment->date_deliver ?? 'Не указан';
                $static['shipmentDate2'] = $shopShippment->date_deliver ?? 'Не указан';

            }

            $courier = ShopCourier::findOne(['id' => $shopShippment->shop_courier_id]);

            if ($courier !== null) {
                $static['courier'] = $courier->name ?? 'Не указан';

            }
            $orders = ShopOrder::find()->where([
                'shop_shipment_id' => $shopShippment->id
            ])
                ->all();

            if ($orders !== null) {
                $subTotal = 0;
                $dinamic = [];
                $companies = [];
                $comPhones = [];
                $i = 1;
                foreach ($orders as $order) {


                    $item = [];

                    $userAdd = PlaceAdress::find()->where(
                        ['id' => $order->place_adress_id]
                    )->all();

                    if ($userAdd !== null) {
                        foreach ($userAdd as $ad) {
                            $item['address'] = $ad->place ?? 'Не указан';
                        }
                    }


                    $orderItem = ShopOrderItem::find()->where(
                        ['shop_order_id' => $order->id]
                    )->all();

                    if ($orderItem !== null) {

                        foreach ($orderItem as $e) {
                            $item['amount'] = $e['amount'] ?? 'Не указан';

                            $catalog = ShopCatalog::find()->where(
                                ['id' => $e->shop_catalog_id]
                            )
                                ->all();


                            if ($catalog !== null) {


                                foreach ($catalog as $n) {
                                    $element = ShopElement::findOne(['id' => $n->shop_element_id]);
                                    if ($element !== null) {
                                        $item['productName'] = $element->name ?? 'Не указан';
                                    }


                                }
                            }
                        }

                    }

                    if (empty($order->user_company_ids))
                    {


                        $userCompany = new UserCompany();
                        $userCompany->name = 'Тестовая компания';
                        $userCompany->phone = '99 999 99 99';

                        $userCompanies[] = $userCompany;
                    }
                    else {
                        /** @var UserCompany[] $userCompanies */
                        $userCompanies = UserCompany::find()
                            ->where(['id' => $order->user_company_ids])
                            ->all();
                    }


                    if ($userCompanies !== null) {
                        foreach ($userCompanies as $userCompany) {
                            $com = [];
                            $cP = [];
                            $com = $userCompany->name ?? 'Не указан';
                            $cP = $userCompany->phone ?? 'Не указан';

                            $companies[] = $com;
                            $comPhones[] = $cP;
                        }
                    }


                    $item['fullName'] = $order->contact_name ?? 'Не указан';
                    $item['price'] = $order->total_price ?? 0;
                    $item['orderNumber'] = $order->id ?? 'Не указан';
                    $item['order'] = $order->id ?? 'Не указан';
                    $item['id'] = $i;
                    $subTotal += $order->total_price;
                    $i++;
                    array_push($dinamic, $item);
                }
                $cc = implode(", ", $companies);
                $static['company'] = $cc ?? 'Не указан';
                $static['companyPhone'] = implode(", ", $comPhones) ?? 'Не указан';
                $static['numProducts'] = $i - 1;
                $static['totalPrice'] = $subTotal;
            }


            /* $static = [
                 //'docNumber' => $shopShippment->id,+
                // 'courier' => $courier->name,
                 //'createDate' => $shopShippment->date_deliver,+
                 //'shipmentDate' => $shopShippment->date_deliver,+
                // 'shipmentDate2' => $shopShippment->date_deliver,+
                 //'numProducts' => $i - 1,+
                 //'totalPrice' => $subTotal,+
                 //'company' => $cc,+
                 //'companyPhone' => implode(", ", $comPhones)+
             ];
             */
        }


        /*  $orders = ShopOrder::find()->where(['shop_shipment_id' => $ware->shop_shipment_id])->one();
          if ($orders !== null)
              $shopOrderItem = ShopOrderItem::find()->where(
                  ['shop_order_id' => $orders->id]
              )->all();

          if ($orders === null) return false;

          $companyInfo = UserCompany::find()->where(['id' => $orders->user_company_ids])->one();
          $userInfo = User::find()->where(['id' => $orders->user_id])->one();
          $userCompanyInfo = User::find()->where(['user_company_id' => $userInfo->user_company_id])->one();
          $sellerAdr = PlaceAdress::findOne($companyInfo->place_adress_id);
          $dynamic = [];
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
              'untilDate' => $orders->date_deliver,
              'orderName' => $userCompanyInfo->name,
              'date' => $orders->created_at,
              'bankName' => $companyInfo->bank,
              'bankAccount' => $companyInfo->bank_account,
              'bankAddress' => $companyInfo->bank_address,
              'orderID' => $orders->id,
              'orderAddress' => $companyInfo->bank_address,
              'sellerName' => $companyInfo->name,
              'sellerPhone' => $companyInfo->phone,
              'sellerAddress' => $sellerAdr->name,
              'priceAll' => $orders->total_price,
              'ids' => $i - 1,
              'numstr' => $this->num2str($orders->total_price)
          ];*/
        $templateProcessor = new TemplateProcessor($file_open);
        $templateProcessor->setValues($static);
        $templateProcessor->cloneRowAndSetValues('order', $dinamic);
        if ($file_close) {
            $templateProcessor->saveAs($file_close);

        } else {
            unlink($file_close);
            $templateProcessor->saveAs($file_close);
        }
        $url = $this->file_path . '/' . $file_name;
        if (!$this->isCLI())
            $this->urlRedirect($url, false);
        else {
            vdd($url);
        }
    }

    public function banderol($id)
    {
        $template = [
            'banderolNumber' => $id,
            'sellerName' => Az::l('Нет имени продавца'),
            'phoneNumber' => Az::l('Нет номера телефона'),
            'sellerAddress' => Az::l('Нет адреса'),
            'deliveryCash' => 0,
            'price' => 0,
            'totalCash' => 0,
            'index' => Az::l('Нет индекса компании'),
            'weight' => 0,
            'deliveryDate' => Az::l('Нет даты доставки'),
            'orderName' => Az::l('Нет названия заказаНет названия заказа'),
            'orderAddress' => Az::l('Нет адреса'),
            'orderCity' => Az::l('Нет информации о регионе'),
            'orderStreet' => Az::l('Нет информации об улице'),
            'regnum' => '____',

        ];

        $date = new \DateTime();
        $file_name = "banderol{$id}{$date->format('Y-m-d-H-i-s')}.docx";
        $fileJpg = Root . '/upload/imagez/image' . $id . '.jpg';
        $file_open = Root . '/binary/words/1C/Бандероль/template.docx';
        $file_close = Root . '/upload ' . $this->file_path . "/" . $file_name;
        $info = ShopOrder::findOne($id);
        if ((int)$info->user_company_ids > 0) {
            $companyInfo = UserCompany::findOne($info->user_company_ids);
            if ($companyInfo !== null) {
                $template['sellerName'] = $companyInfo->name;
                $template['phoneNumber'] = $companyInfo->phone;
                $template['index'] = $companyInfo->index;
            }

            $sellerAdr = PlaceAdress::findOne($companyInfo->place_adress_id ?? 3);

            if ($sellerAdr !== null)
                $template['sellerAddress'] = $sellerAdr->place;
        }
        $placeInfo = PlaceAdress::findOne($info->place_adress_id);
        if ($placeInfo !== null) {
            $template['orderStreet'] = $placeInfo->street;
            $template['orderAddress'] = $placeInfo->place;

            $regionInfo = PlaceRegion::findOne($placeInfo->place_region_id);
            if ($regionInfo !== null)
                $template['orderCity'] = $regionInfo->name;
        }


        $userInfo = User::findOne($info->user_id);
        if ($userInfo !== null)
            $template['orderName'] = $userInfo->name;

        $id = (string)$info->id;
        $bn = '000000000000';
        $bn = substr_replace($bn, $id, strlen($bn) - strlen($id), strlen($id));
        $template['regnum'] = $bn;


        $templateProcessor = new TemplateProcessor($file_open);
        $templateProcessor->setValues($template);
        if (file_exists($fileJpg))
            $templateProcessor->setImageValue("barcode", $fileJpg);
        $templateProcessor->saveAs($file_close);
        if ($this->execute)
            shell_exec($file_close);
        if (!$this->isCLI()) {
            $url = $this->file_path . '/' . $file_name;
            $this->urlRedirect($url, false);
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
        $static = [
            'docNumber' => Az::l('Номер заказа не указан'),
            'courier' => Az::l('Курьер не указан'),
            'deliveryDate' => Az::l('Дата не указана'),
            'shipmentDate' => Az::l('Дата не указана'),
            'shipmentDate2' => Az::l('Дата не указана'),
            'numProducts' => 0,
            'subTotal' => 0,
            'company' => Az::l('Не указан'),
        ];
        $date = new \DateTime();

        $file_open = Root . '/binary/words/1C/Маршрутный_лист/template.docx';
        $file_name = "routeList{$id}-{$date->format('Y-m-d-H-i-s')}.docx";
        $file_close = Root . '/upload' . $this->file_path . "/" . $file_name;
        $ware = WareAccept::find()->where(['shop_shipment_id' => $id])->one();
        $subTotal = 0;
        $dinamic = [];
        $companies = [];
        if ($ware !== null){
            $shopShippment = ShopShipment::findOne(['id' => $ware->shop_shipment_id]);
            if ($shopShippment !== null){
                $static['docNumber'] = $shopShippment->id ?? 'Номер  заказа не указан';
                $static['deliveryDate'] = $shopShippment->date_deliver ??  'Дата не указана';
                $static['shipmentDate'] = $shopShippment->date_deliver ??  'Дата не указана';
                $static['shipmentDate2'] = $shopShippment->date_deliver ??  'Дата не указана';


            }
            $courier = ShopCourier::findOne(['id' => $shopShippment->shop_courier_id]);
            if($courier !== null){
                $static['courier'] = $courier->name ?? 'Курьер не указан' ;

            }
            $shopOrder = ShopOrder::find()->where([
                'shop_shipment_id' => $shopShippment->id
            ])->all();

            if ($shopOrder !== null){


                $i = 1;
                foreach ($shopOrder as $order) {

                    $item = [];
                    $userAdd = PlaceAdress::find()->where(
                        ['id' => $order->place_adress_id]
                    )->all();
                    if ($userAdd !== null){
                        foreach ($userAdd as $ad) {
                            $item['address'] = $ad->place;
                        }
                    }

                    $orderItem = ShopOrderItem::find()->where(
                        ['shop_order_id' => $order->id]
                    )->all();
                    if ($orderItem !== null){
                        foreach ($orderItem as $e) {
                            $catalog = ShopCatalog::find()->where(
                                ['id' => $e->shop_catalog_id]
                            )->all();
                            foreach ($catalog as $n) {
                                $element = ShopElement::findOne(['id' => $n->shop_element_id]);
                                $item['attachmentList'] = $element->name;

                            }

                        }
                    }

                  if (empty($order->user_company_ids)){
                      $uCompany = new UserCompany();
                      $uCompany->name = "Компания не указана!";

                      /*$userCompany = UserCompany::find()->where(['id' => $order->user_company_ids])->all();
                      if ($userCompany !== null){
                          foreach ($userCompany as $uc) {
                              $com = [];
                              $com = $uc['name'];
                              array_push($companies, $com);
                          }
                      }*/
                  }
                  else{
                      $userCompany = UserCompany::find()->where(['id' => $order->user_company_ids])->all();
                          foreach ($userCompany as $uc) {
                              $com = [];
                              $com = $uc['name'];
                              array_push($companies, $com);
                          }
                  }

                    $item['fullName'] = $order->contact_name;
                    $item['phone'] = $order->contact_phone;
                    $item['totalAmount'] = $order->total_price;
                    $item['orderNumber'] = $order->id;
                    $item['deliveryDate'] = $order->date_deliver;
                    $item['additionalPhone'] = $order->add_contact_phone;
                    $item['prePayment'] = $order->prepayment;
                    $item['id'] = $i;
                    $subTotal += $order->total_price;
                    $i++;
                    array_push($dinamic, $item);
                }
            }
            $cc = implode(", ", $companies);
        }
        $static['numProducts'] = $i ?? 0;
        $static['subTotal'] = $subTotal ?? 0;
        $static['company'] = $cc ?? 'Компания не указана!';
       // $static = [
           // 'docNumber' => $shopShippment->id,+
           // 'courier' => $courier->name,+
           // 'deliveryDate' => $shopShippment->date_deliver,+
           // 'shipmentDate' => $shopShippment->date_deliver,+
           // 'shipmentDate2' => $shopShippment->date_deliver,+
           // 'numProducts' => $i - 1,
           // 'subTotal' => $subTotal,
           // 'company' => $cc,

        $templateProcessor = new TemplateProcessor($file_open);
        $templateProcessor->setValues($static);
        $templateProcessor->cloneRowAndSetValues('fullName', $dinamic);
        if ($file_close) {
            $templateProcessor->saveAs($file_close);
        } else {
            unlink($file_close);
            $templateProcessor->saveAs($file_close);
        }
        $url = $this->file_path . '/' . $file_name;
        if (!$this->isCLI())
            $this->urlRedirect($url, false);
        else {
            vdd($url);
        }
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
        }
    }
}

