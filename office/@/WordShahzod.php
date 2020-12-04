<?php


namespace zetsoft\service\office;


use ConvertApi\ConvertApi;
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
use zetsoft\models\ware\WareReturn;
use zetsoft\service\market\GeneratorBarCode;
use zetsoft\system\Az;
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
class WordShahzod extends ZFrame
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

    #region test

    public function test()
    {
        //$this->contract(9);
        /*  $this->generateAct();*/
        /*$this->routeListTest();*/
        /*$this->banderolTest();*/
        //$this->generateRouteSheetTest();
        $this->multiGenerateAct([90, 89, 88]);
        //$this->returnProduct(43);
        //$this->generateAct(31);
    }

    #endregion

    ##region returnProductTest
    public function returnProductTest()
    {
        $id = 68;
        $this->returnProduct($id);
    }
    #endregion
    ##region returnProduct
    public function returnProduct($id)
    {
        $date = new \DateTime();

        $staticWord = [
            'sellerName' => Az::l(''),
            'totalPrice' => Az::l('Нет имени покупателя'),
            'date' => Az::l('Номер заказа отсутствует'),
        ];
        $file_name = "productReturnSh{$id}{$date->format('Y-m-d-H-i-s')}.docx";
        $file_close = Root . '/upload' . $this->file_path . "/" . $file_name;
        $nameFile = '/binary/words/1C/Заявление_на_возврат_товаров/';
        if (file_exists($nameFile . Az::$app->language . '.docx'))
            $file = Root . $nameFile . Az::$app->language . '.docx';
        else $file = Root . $nameFile . 'ru.docx';
        $ware_return = WareReturn::findOne($id);
        $shopInfo = ShopOrder::findOne($ware_return->shop_order_id);
        $shopItem = ShopOrderItem::find()->where(
            ['shop_order_id' => $id]
        )->all();
        if ($shopInfo->user_company_ids !== null && !empty($shopInfo->user_company_ids)) {
            $companyInfo = UserCompany::findOne($shopInfo->
            user_company_ids);
            $staticWord['sellerName'] = $companyInfo->name;
        }
        if ($shopInfo !== null && !empty($shopInfo)) {
            $staticWord['totalPrice'] = $shopInfo->total_price;
            $staticWord['date'] = $shopInfo->created_at;
        }


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
        $templateProcessor->setValues($staticWord);
        $templateProcessor->cloneRowAndSetValues('productName', $dynamic);
        $templateProcessor->saveAs($file_close);

        if ($this->execute)
            shell_exec($file_close);
        if (!$this->isCLI()) {
            $url = $this->file_path . '/' . $file_name;
            /* $this->urlRedirect($url, false);*/
            //   shell_exec($file_close);
        }
        return $file_close;

    }
    #endregion

    #region selectedReturnProduct

    public function selectedReturnProduct($ids)
    {

        $urls = [];

        if (is_array($ids))
            foreach ($ids as $id) {
                $urls[] = str_replace('/', '\\', $this->returnProduct($id));
            }
        else
            $urls[] = str_replace('/', '\\', $this->banderol($ids));
        $merged_file = Az::$app->office->mergeFiles->mergeDocsFromArray($urls);

        $mf = new MergeFiles();

        /*if ($mf->is_pdf)

        else
            $to_pdf = $merged_file;*/

        $to_pdf = $this->docxConvertToPdf($merged_file . ".docx");
         vdd($to_pdf);
        $to_pdf_arr = explode('\\', $to_pdf);

        $size = count($to_pdf_arr);

        $url_pdf = $to_pdf_arr[$size - 3] . '/' . $to_pdf_arr[$size - 2] . '/' . $to_pdf_arr[$size - 1];
        $this->urlRedirect('/' . $url_pdf, false);
    }

    #endregion selectedReturnProduct

    ##region contractTest
    public function contractTest()
    {
        $id = 68;
        $this->execute = true;
        $this->contract($id);

        /*        $numberToWords = new NumberToWords();

        // build a new number transformer using the RFC 3066 language identifier
                $numberTransformer = $numberToWords->getNumberTransformer('en');*/
    }
    #endregion
    #region contract
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
        $file_name = "contractSh{$id}.{$date->format('Y-m-d-H-i-s')}.docx";
        $nameFile = '/binary/words/1C/Договор_заказ/';
        if (file_exists($nameFile . Az::$app->language . '.docx'))
            $file_open = Root . $nameFile . Az::$app->language . '.docx';
        else $file_open = Root . $nameFile . 'ru.docx';

        $file_close = Root . '/upload' . $this->file_path . "/" . $file_name;
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
        vdd($shopOrder->id);
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
        return $file_close;

    }

    #endregion
    #region multiContract

    public function multiContract($ids)
    {

        $urls = [];

        if (is_array($ids))
            foreach ($ids as $id) {
                $urls[] = str_replace('/', '\\', $this->banderol($id));
            }
        else
            $urls[] = str_replace('/', '\\', $this->banderol($ids));

        $merged_file = Az::$app->office->mergeFiles->mergeDocsFromArray($urls);

        $mf = new MergeFiles();

        if ($mf->is_pdf)
            $to_pdf = Az::$app->office->docto->docPdf($merged_file);
        else
            $to_pdf = $merged_file;


        $to_pdf_arr = explode('\\', $to_pdf);

        $size = count($to_pdf_arr);

        $url_pdf = $to_pdf_arr[$size - 3] . '/' . $to_pdf_arr[$size - 2] . '/' . $to_pdf_arr[$size - 1];

        $this->urlRedirect('/' . $url_pdf, false);

    }

    #endregion multiContract

    ##region cashTemplateTest
    public function cashTemplateTest()
    {
        $id = 68;
        $this->cashTemplate($id);
    }
    #endregion

    ##region cashTemplate
    public function cashTemplate($id)
    {

        $date = new \DateTime();
        $ware_return = null;
        $staticWord = [
            'untilDate' => Az::l(''),
            'orderName' => Az::l('Нет имени покупателя'),
            'orderNumber' => Az::l('Номер заказа отсутствует'),
            'date' => Az::l('Дата заказа не указана'),
            'sellerName' => Az::l('Нет имени продавца'),
            'sellerAddress' => Az::l('Нет адреса продавца'),
            'priceAll' => Az::l('sa'),
            'ids' => 1,
            'numstr' => 0,
            'bankName' => Az::l('Адресс покупателя отсутствует'),
            'bankAccount' => Az::l('Адресс покупателя отсутствует'),
            'bankAddress' => Az::l('Адресс покупателя отсутствует'),
            'orderID' => Az::l('Нет'),
            'orderAddress' => Az::l('Адресс покупателя отсутствует'),
        ];
        $userInfo = null;
        $nameFile = '/binary/words/1C/Счет_на_оплату/';
        if (file_exists($nameFile . Az::$app->language . '.docx'))
            $file_open = Root . $nameFile . Az::$app->language . '.docx';
        else $file_open = Root . $nameFile . 'ru.docx';
        $file_name = "/cashDocumentSh{$id}{$date->format('Y-m-d-H-i-s')}.docx";
        $file_close = Root . '/upload' . $this->file_path . "/" . $file_name;
        $ware_return = WareReturn::findOne($id);
        $shopOrder = ShopOrder::findOne($ware_return->shop_order_id);
        $userCompanyInfo = null;
        $shopOrderItem = ShopOrderItem::find()->where(
            ['shop_order_id' => $id]
        )->all();
        $companyInfo = '';
        $sellerAdr = null;
        //$shopOrder = null;
        if ($shopOrder->user_company_ids !== null && !empty($shopOrder->user_company_ids)) {

            $companyInfo = UserCompany::findOne($shopOrder->user_company_ids);
            $staticWord['bankAccount'] = $companyInfo->bank_account;
            $staticWord['bankAddress'] = $companyInfo->bank_address;
            $staticWord['orderAddress'] = $companyInfo->bank_address;
            $staticWord['bankName'] = $companyInfo->bank;
            $staticWord['sellerPhone'] = $companyInfo->phone;
            $staticWord['sellerName'] = $companyInfo->name;


            $sellerAdr = PlaceAdress::findOne($companyInfo->place_adress_id);
        }

        $userInfo = User::findOne($shopOrder->user_id);
        /* $userCompanyInfo = User::findOne($userInfo->user_company_id);*/


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
        vdd($userInfo);
        if ($userInfo->user_company_id !== null || !empty($userInfo->user_company_id)) {
            $userCompanyInfo = UserCompany::findOne($userInfo->user_company_id);
            $staticWord['orderName'] = $userCompanyInfo->name;
        }


        if ($sellerAdr !== null)
            $staticWord['sellerAddress'] = $sellerAdr->name;

        if ($shopOrder !== null) {
            $staticWord['ids'] = $i - 1;
            $staticWord['numstr'] = $this->num2str($shopOrder->total_price);
            $staticWord['priceAll'] = $shopOrder->total_price;

            $staticWord['orderID'] = $shopOrder->id;
            $staticWord['date'] = $shopOrder->created_at;
            $staticWord['untilDate'] = $shopOrder->date_deliver;
        }


        /*$staticWord = [
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
        ];*/
        $templateProcessor = new TemplateProcessor($file_open);
        $templateProcessor->setValues($staticWord);
        $templateProcessor->cloneRowAndSetValues('orderTitle', $dynamic);

        if ($file_close) {
            $templateProcessor->saveAs($file_close);

        } else {
            unlink($file_close);
            $templateProcessor->saveAs($file_close);
        }

        /*  $url = $this->file_path . '/' . $file_name;*/
        /*$this->urlRedirect($url, false);*/
        return $file_close;
    }

    #endregion

    #region selectedCashTemplate

    public function selectedCashTemplate($ids)
    {

        $urls = [];

        if (is_array($ids))
            foreach ($ids as $id) {
                $urls[] = str_replace('/', '\\', $this->banderol($id));
            }
        else
            $urls[] = str_replace('/', '\\', $this->banderol($ids));
        $merged_file = Az::$app->office->mergeFiles->mergeDocsFromArray($urls);

        $mf = new MergeFiles();

        if ($mf->is_pdf)
            $to_pdf = Az::$app->office->docto->docPdf($merged_file);
        else
            $to_pdf = $merged_file;


        $to_pdf_arr = explode('\\', $to_pdf);
        array_filter($to_pdf_arr);

        $size = count($to_pdf_arr);

        $url_pdf = $to_pdf_arr[$size - 3] . '/' . $to_pdf_arr[$size - 2] . '/' . $to_pdf_arr[$size - 1];

        $this->urlRedirect('/' . $url_pdf, false);
    }

    #endregion selectedCashTemplate

    ##region generateActTest
    public function generateActTest()
    {
        $id = 21;
        $this->generateAct($id);
    }
    #endregionTest
    ##region generateAct
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

        $nameFile = '/binary/words/1C/Акт_передачи/';

        if (file_exists($nameFile . Az::$app->language . '.docx'))
            $file_open = Root . $nameFile . Az::$app->language . '.docx';
        else $file_open = Root . $nameFile . 'ru.docx';

        $file_name = "ActSalohiddin{$id}{$date->format('Y-m-d-H-i-s')}.docx";
        $file_close = Root . '/upload' . $this->file_path . "/" . $file_name;
        $ware = WareAccept::findOne(['shop_shipment_id' => $id]);

        $dinamic = [];
        if ($ware !== null) {
            $shopShippment = ShopShipment::findOne(['id' => $id]);

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
                            )->all();


                            if ($catalog !== null) {
                                foreach ($catalog as $n) {
                                    //    vd($n);
                                    $element = ShopElement::findOne(['id' => $n->shop_element_id]);
                                    if ($element !== null) {
                                        $item['productName'] = $element->name ?? 'Не указан';
                                    }


                                }
                            }
                        }

                    }

                    if (empty($order->user_company_ids)) {


                        $userCompany = new UserCompany();
                        $userCompany->name = 'Тестовая компания';
                        $userCompany->phone = '99 999 99 99';

                        $userCompanies[] = $userCompany;
                    } else {
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
        //$url = $this->file_path . '/' . $file_name;

        return $file_close;
    }
    #endregion

    ##region convertToPdf
    public function docxConvertToPdf($file_name)
    {
        ConvertApi:: setApiSecret('XxPgGZYF7cTilGI5');

        $result = ConvertApi::convert('pdf', ['File' => $file_name]);
        $result->getFile()->save($file_name);

        $pdf = explode('.', $file_name)[0] . '.pdf';

        return $pdf;

    }

    #endregion

    #region multiGenerateAct

    public function multiGenerateAct($ids)
    {
        $urls = [];

        if (is_array($ids))
            foreach ($ids as $id) {
                $urls[] = str_replace('/', '\\', $this->generateAct($id));
            }
        else
            $urls[] = str_replace('/', '\\', $this->generateAct($ids));

        $merged_file = Az::$app->office->mergeFiles->mergeDocsFromArray($urls) . '.docx';
      //  $mf = new MergeFiles();

        #tegilmasin************

            //    $to_pdf = $merged_file;
        #tegilmasin************

        //  $this->doc_pdf($to_pdf);

          //  $to_pdf=$merged_file;

            $pdf = explode('.',$merged_file)[0].'.pdf';
            $old_path = getcwd();
            chdir('../../scripts/convert/');
            $out =exec('docto -WD -f ' . $merged_file . ' -O ' . $pdf. ' -T wdFormatPDF',$output,$status);

           // $converted = explode('.', $to_pdf)[0] . '.pdf';

            //$convert='D:/Develop/Projects/ALL/asrorz/zetsoft/upload/excelz/eyuf/';
            //$old_path = getcwd();
            //chdir('C:\Program Files\LibreOffice\program');
            //$output = exec('soffice --headless --convert-to pdf ' . $to_pdf . ' --outdir '  . $convert, $outing,$status);
         //   vd($converted);
            if ($status===0 && $out==="") {
                $to_pdf_arr = explode('\\', $pdf);
                $to_pdf_arr = array_filter($to_pdf_arr);
                $size = count($to_pdf_arr);

                $url_pdf = $to_pdf_arr[$size - 3] . '/' . $to_pdf_arr[$size - 2] . '/' . $to_pdf_arr[$size - 1];


        }
        return $url_pdf;





    }


 public function doc_pdf($file_path)
    {
        $convert = explode('.', $file_path)[0] . '.pdf';
      //  $convert='D:/Develop/Projects/ALL/asrorz/zetsoft/upload/excelz/eyuf/';
        $old_path = getcwd();
        chdir('C:\Program Files\LibreOffice\program');
        $output = exec('soffice --headless --convert-to pdf ' . $file_path . ' --outdir '  . $convert, $outing,$status);
        if ($status === 0){

            $to_pdf_arr = explode('\\', $convert);
            $to_pdf_arr = array_filter($to_pdf_arr);
            $size = count($to_pdf_arr);

            $url_pdf = $to_pdf_arr[$size - 3] . '/' . $to_pdf_arr[$size - 2] . '/' . $to_pdf_arr[$size - 1];
       //       return   $url_pdf;
   //   $this->urlRedirect($url_pdf, false);

        }
        // else vdd($status);


    }

    #endregion multiGenerateAct

    #region multiRouteList

    public function multiRouteList($ids)
    {

        $urls = [];

        if (is_array($ids))
            foreach ($ids as $id) {
                $urls[] = str_replace('/', '\\', $this->routeList($id));
            }
        else
            $urls[] = str_replace('/', '\\', $this->routeList($ids));

        $merged_file = Az::$app->office->mergeFiles->mergeDocsFromArray($urls);

        $mf = new MergeFiles();

        if ($mf->is_pdf)
            $to_pdf = Az::$app->office->docto->docPdf($merged_file);
        else
            $to_pdf = $merged_file;


        $to_pdf_arr = explode('\\', $to_pdf);

        $size = count($to_pdf_arr);

        $url_pdf = $to_pdf_arr[$size - 3] . '/' . $to_pdf_arr[$size - 2] . '/' . $to_pdf_arr[$size - 1];

      //  $this->urlRedirect('/' . $url_pdf, false);
    }

    #endregion multiRouteList

    #region routeListTest
    public function routeListTest()
    {
        $this->routeList(21);
    }
    #endregion
    #region routeList
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

        $nameFile = '/binary/words/1C/Маршрутный_лист/';
        if (file_exists($nameFile . Az::$app->language . '.docx'))
            $file_open = Root . $nameFile . Az::$app->language . '.docx';
        else $file_open = Root . $nameFile . 'ru.docx';
        $file_name = "routeListSh{$id}-{$date->format('Y-m-d-H-i-s')}.docx";
        $file_close = Root . '/upload' . $this->file_path . "/" . $file_name;
        $ware = WareAccept::find()->where(['shop_shipment_id' => $id])->one();
        $subTotal = 0;
        $dinamic = [];
        $companies = [];
        if ($ware !== null) {
            $shopShippment = ShopShipment::findOne(['id' => $ware->shop_shipment_id]);
            if ($shopShippment !== null) {
                $static['docNumber'] = $shopShippment->id ?? 'Номер  заказа не указан';
                $static['deliveryDate'] = $shopShippment->date_deliver ?? 'Дата не указана';
                $static['shipmentDate'] = $shopShippment->date_deliver ?? 'Дата не указана';
                $static['shipmentDate2'] = $shopShippment->date_deliver ?? 'Дата не указана';
            }

            $courier = ShopCourier::findOne(['id' => $shopShippment->shop_courier_id]);
            if ($courier !== null) {
                $static['courier'] = $courier->name ?? 'Курьер не указан';

            }
            $shopOrder = ShopOrder::find()->where([
                'shop_shipment_id' => $shopShippment->id
            ])->all();

            if ($shopOrder !== null) {


                $i = 1;
                foreach ($shopOrder as $order) {

                    $item = [];
                    $userAdd = PlaceAdress::find()->where(
                        ['id' => $order->place_adress_id]
                    )->all();
                    if ($userAdd !== null) {
                        foreach ($userAdd as $ad) {
                            $item['address'] = $ad->place;
                        }
                    }

                    $orderItem = ShopOrderItem::find()->where(
                        ['shop_order_id' => $order->id]
                    )->all();
                    if ($orderItem !== null) {
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

                    if (empty($order->user_company_ids)) {
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
                    } else {
                        $userCompany = UserCompany::find()->where(['id' => $order->user_company_ids])->all();
                        foreach ($userCompany as $uc) {
                            array_push($companies, $uc['name']);
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


        return $file_close;
    }
    #endregion


    #region banderolTets
    public function banderolTest()
    {
        $id = 68;
        $this->banderol($id);
    }
    #endregion
    #region banderol
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

        Az::$app->market->generatorBarCodes->generateBarcodeJpg($id);

        $date = new \DateTime();
        $file_name = "banderol{$id}{$date->format('Y-m-d-H-i-s')}.docx";
        $fileJpg = Root . '/upload/imagez/image' . $id . '.jpg';

        $nameFile = '/binary/words/1C/Бандероль/';
        if (file_exists($nameFile . Az::$app->language . '.docx'))
            $file_open = Root . $nameFile . Az::$app->language . '.docx';
        else $file_open = Root . $nameFile . 'ru.docx';
        $file_close = Root . '/upload' . $this->file_path . "/" . $file_name;
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

        if ($info->user_id !== null && empty($info->user_id) && (int)$info->user_id > 0) {
            $userInfo = User::findOne($info->user_id);
            if ($userInfo !== null)
                $template['orderName'] = $userInfo->name;
        }

        $id = (string)$info->id;
        $bn = '000000000000';
        $bn = substr_replace($bn, $id, strlen($bn) - strlen($id), strlen($id));
        $template['regnum'] = $bn;


        $templateProcessor = new TemplateProcessor($file_open);
        $templateProcessor->setValues($template);
        $templateProcessor->setImageValue("barcode", $fileJpg);
        $templateProcessor->saveAs($file_close);
        if ($this->execute)
            shell_exec($file_close);
        if (!$this->isCLI()) {
            $url = $this->file_path . '/' . $file_name;
            $this->urlRedirect($url, false);
        }
        return $file_close;
    }

    #endregion
    #region multiBanderol

    public function multiBanderol($ids)
    {

        $urls = [];

        if (is_array($ids))
            foreach ($ids as $id) {
                $urls[] = str_replace('/', '\\', $this->banderol($id));
            }
        else
            $urls[] = str_replace('/', '\\', $this->banderol($ids));


        $merged_file = Az::$app->office->mergeFiles->mergeDocsFromArray($urls);

        $mf = new MergeFiles();

        if ($mf->is_pdf)
            $to_pdf = Az::$app->office->docto->docPdf($merged_file);
        else
            $to_pdf = $merged_file;


        $to_pdf_arr = explode('\\', $to_pdf);

        $size = count($to_pdf_arr);

        $url_pdf = $to_pdf_arr[$size - 3] . '/' . $to_pdf_arr[$size - 2] . '/' . $to_pdf_arr[$size - 1];
        vdd($url_pdf);
        $this->urlRedirect('/' . $url_pdf, false);
    }

    #endregion multiBanderol

    #region generateRouteSheetTest
    public function generateRouteSheetTest()
    {
        $id = 68;
        $this->generateRouteSheet($id);
    }
    #endregion
    #region generateRouteSheet
    public function generateRouteSheet($id)
    {

        $date = new \DateTime();

        $file_name = "/cashDocument{$id}{$date->format('Y-m-d-H-i-s')}.docx";
        $nameFile = '/binary/words/1C/';

        if (file_exists($nameFile . Az::$app->language . '.docx'))
            $file_open = Root . $nameFile . Az::$app->language . '.docx';
        else $file_open = Root . $nameFile . 'ru.docx';
        $file_close = Root . '/upload' . $this->file_path . "/" . $file_name;
        $shopInfo = ShopOrder::findOne($id);

        $shopItem = ShopOrderItem::find()->where(
            ['shop_order_id' => $id]
        )->all();

        $shopShippment = ShopShipment::findOne($shopInfo->shop_shipment_id);
        vdd($shopShippment);
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

    #endregion

    #region returnCashTest
    public function returnCashTest()
    {
        $id = 68;
        $this->returnCash($id);
    }
    #endregion
    #region returnCash
    public function returnCash($id)
    {
        $date = new \DateTime();

        $placeInfo = null;
        $userInfo = null;

        $staticWord = [

            'name' => Az::l('Не указан'),
            'address' => Az::l('Не указан'),
            'phone' => Az::l('Не указан'),
            'amount' => Az::l('Не указан'),
            'date' => Az::l('Не указан'),
        ];


        $file_name = "cashReturnSh{$id}{$date->format('Y-m-d-H-i-s')}.docx";
        $file_close = Root . '/upload
        *********' . $this->file_path . "/" . $file_name;
        //////////////////////////////////////////////////////////////////////
        $nameFile = '/binary/words/1C/Заявление_на_возврат_ДС/';
        if (file_exists($nameFile . Az::$app->language . '.docx'))
            $file_open = Root . $nameFile . Az::$app->language . '.docx';
        else $file_open = Root . $nameFile . 'ru.docx';
        $shopOrder = ShopOrder::findOne($id);
        $shopOrderItem = ShopOrderItem::find()->where(
            ['shop_order_id' => $id]
        )->all();
        if ($shopOrder !== null && !empty($shopOrder)) {
            $userInfo = User::findOne($shopOrder->user_id);
            $placeInfo = PlaceAdress::findOne($shopOrder->place_adress_id);
        }


        $y = 0;

        foreach ($shopOrderItem as $k) {

            $y += (int)$k['amount_return'];
        }


        $templateProcessor = new TemplateProcessor($file_open);
        $templateProcessor->setValues($staticWord);
        $templateProcessor->saveAs($file_close);

        /* if ($this->execute)
             shell_exec($file_close);*/
        if (!$this->isCLI()) {
            $url = $this->file_path . '/' . $file_name;
            /*$this->urlRedirect($url, false);*/
        }

        if ($placeInfo !== null && !empty($placeInfo)) {
            $staticWord['address'] = $placeInfo->name;
        }
        if ($userInfo !== null && !empty($userInfo)) {
            $staticWord['name'] = $userInfo->name;
            $staticWord['phone'] = $userInfo->phone;
        }

        if ($shopOrder !== null && !empty($shopOrder)) {
            $staticWord['date'] = $shopOrder->created_at;
        }
        $staticWord['amount'] = $y;

        return $file_close;
    }

    ##endregion
    #region selectedReturnCash

    public function selectedReturnCash($ids)
    {

        $urls = [];

        if (is_array($ids))
            foreach ($ids as $id) {
                $urls[] = str_replace('/', '\\', $this->returnCash($id));
            }
        else
            $urls[] = str_replace('/', '\\', $this->returnCash($ids));
        $merged_file = Az::$app->office->mergeFiles->mergeDocsFromArray($urls);

        $mf = new MergeFiles();

        if ($mf->is_pdf)
            $to_pdf = Az::$app->office->docto->docPdf($merged_file);
        else
            $to_pdf = $merged_file;


        $to_pdf_arr = explode('\\', $to_pdf);

        $size = count($to_pdf_arr);

        $url_pdf = $to_pdf_arr[$size - 3] . '/' . $to_pdf_arr[$size - 2] . '/' . $to_pdf_arr[$size - 1];

        $this->urlRedirect('/' . $url_pdf, false);
    }

    #endregion selectedReturnCash

    function morph($n, $f1, $f2, $f5)
    {
        $n = abs(intval($n)) % 100;
        if ($n > 10 && $n < 20) return $f5;
        $n = $n % 10;
        if ($n > 1 && $n < 5) return $f2;
        if ($n == 1) return $f1;
        return $f5;
    }

    public function num2str($num)
    {
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
                if ($uk > 1) $out[] = $this->morph($v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2]);
            } //foreach
        } else $out[] = $nul;
        $out[] = $this->morph(intval($rub), $unit[1][0], $unit[1][1], $unit[1][2]); // rub
        $out[] = $kop . ' ' . $this->morph($kop, $unit[0][0], $unit[0][1], $unit[0][2]); // kop
        return trim(preg_replace('/ {2,}/', ' ', join(' ', $out)));
    }

}

