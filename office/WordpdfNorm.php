<?php


namespace zetsoft\service\office;
require Root . '/vendors/fileapp/office/vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\Serializer\Encoder\JsonDecode;
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
use zetsoft\system\actives\ZModel;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;
use function Dash\Curry\all;
use function Spatie\SslCertificate\length;

/**
 * Class    Wordpdf
 * @package zetsoft\service\office
 *
 * @author Rowa
 *
 * @license OtabekNosirov
 * @license JaloliddinovSalohiddin
 * @license AkromovAzizjon
 *
 */
class WordpdfNorm extends ZFrame
{

    /**
     * @var
     *
     * PDF file yaratilgandan keyin, uni Adobe Reader da ochib beradi, local kompyuterda
     */
    public $execute;

    /**
     * @var string
     * Yaratilgan PDF file lar shu path ga saqlanadi
     */
    public $file_path = '/excelz/' . App;

    /**
     * @var array
     *
     * Parametr boyicha kevotgan URL lar
     * http://mplace.zoft.uz/shop/user/filter-common/main.aspx?id=944
     * `id=944`
     *
     */
    public $urls = [];

    private $userCompanies = [];

    public $layout = [
        'companies' => "{company}, {phone}\n",
        'adress' => '{adress}, ',
    ];


    #region init

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
        $this->selectedCashTemplateTest();
        $this->multiContractTest();
        $this->multiGenerateActTest();
        $this->multiRouteListTest();
        $this->selectedReturnCashTest();
        $this->selectedReturnProductTest();
        $this->multiBanderolTest();
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

    /**
     *
     * Function  returnProduct -> word generatsiya qilib beradi qaytgan productla ni
     * @param $id -> ShopShipment yoki ShopOrder table ga tegishli id
     * @return  string
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     * @license OtabekNosirov
     * @license JaloliddinovSalohiddin
     * @license AkromovAzizjon
     *
     */
    public function returnProduct($id)
    {
        $date = new \DateTime();

        // todo: start

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

        // todo: end

    }
    #endregion

    #region selectedReturnProductTest


    public function selectedReturnProductTest()
    {

        $this->selectedReturnProduct();
    }


    #endregion selectedReturnProduct
    #region selectedReturnProduct

    /**
     *
     * Function  selectedReturnProduct
     * @param $ids -> ShopShipment yoki ShopOrder tabledagi idga tegishli n-ta wordlarni  bita wordga yegadi(merge)
     * @return  string
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     *
     * @author OtabekNosirov
     * @author JaloliddinovSalohiddin
     * @author AkromovAzizjon
     *
     */
    public function selectedReturnProduct($ids)
    {

        $urls = [];

        if (is_array($ids))
            foreach ($ids as $id) {
                $urls[] = str_replace('/', '\\', $this->returnProduct($id));
            }
        else
            $urls[] = str_replace('/', '\\', $this->returnProduct($ids));

        $merged_file = Az::$app->office->mergeFiles->mergeDocsFromArray($urls) . '.docx';

        $mf = new MergeFiles();

        #tegilmasin************

        if ($mf->is_pdf)
            $to_pdf = Az::$app->office->docto->docPdf($merged_file);
        else
            $to_pdf = $merged_file;
        #tegilmasin************
        $to_pdf_arr = explode('\\', $to_pdf);

        $to_pdf_arr = array_filter($to_pdf_arr);

        $size = count($to_pdf_arr);


        $url_pdf = '/' . $to_pdf_arr[$size - 3] . '/' . $to_pdf_arr[$size - 2] . '/' . $to_pdf_arr[$size - 1];

        return $url_pdf;
    }


    #endregion selectedReturnProduct

    #region changeStatusTest
    /**
     *
     * Function  selectedReturnProduct
     * @param $ids -> ShopShipment yoki ShopOrder tabledagi idga tegishli n-ta wordlarni  bita wordga yegadi(merge)
     * @return  string
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     *
     * @license OtabekNosirov
     * @license JaloliddinovSalohiddin
     * @license AkromovAzizjon
     *
     */
    public function changeStatusTest()
    {
        $this->changeStatus();
    }

    #endregion changeStatus
    #region changeStatus
    /**
     *
     * Function  changeStatus
     * @param $ids
     * @param $modelClass
     * @param $attr
     * @param $val
     * @param null $requiredClass
     * @return  mixed
     * @license OtabekNosirov
     * @license JaloliddinovSalohiddin
     * @license AkromovAzizjon
     */
    public function changeStatus($ids, $modelClass, $attr, $val, $requiredClass = null)
    {

        if (!empty($ids)) {
            foreach ($ids as $id) {
                /** @var ZModel $model */

                Az::$app->market->wares->createWareExit($id);
                $model = $modelClass::findOne($id);


                $fromCamelCase = ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $modelClass)), '_') . '_id';

                $fromCamelCase_arr = explode('\\', $fromCamelCase);

                $fromCamelCase = $fromCamelCase_arr[count($fromCamelCase_arr) - 1];

                $fromCamelCase = substr($fromCamelCase, 1);

                if ($requiredClass != null && $requiredClass != '') {
                    $model = $requiredClass::find()->where([$fromCamelCase => $id])->all();
                    foreach ($model as $item) {
                        if (ZArrayHelper::keyExists($attr, $item->columns)) {
                            $item->$attr = $val;
                        }

                        $item->configs->rules = [
                            [validatorSafe]
                        ];
                        $this->paramSet(paramNoEvent, true);
                        if (!$item->save())
                            return $item->errors();
                    }

                } else {
                    if (ZArrayHelper::keyExists($attr, $model->columns))
                        $model->$attr = $val;
                    if (Az::$app->market->wares->childs($id)) {
                        $childs = Az::$app->market->wares->childs($id);

                        foreach ($childs as $child) {
                            if (ZArrayHelper::keyExists($attr, $child->columns)) {
                                $child->$attr = $val;
                            }

                            $child->configs->rules = [
                                [validatorSafe]
                            ];
                            $this->paramSet(paramNoEvent, true);
                            $child->save();
                        }
                    }

                    $model->configs->rules = [
                        [validatorSafe]
                    ];
                    $this->paramSet(paramNoEvent, true);
                    if (!$model->save())
                        return $model->errors();
                }


            }
        }

    }

    #endregion changeStatus

    ##region contractTest
    public function contractTest()
    {
        $id = 68;
        $this->execute = true;
        $this->contract($id);
    }
    #endregion
    #region contract
    /**
     *
     * Function  contract
     * @param $id
     * @return  string
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     * @license OtabeNosirov
     * @license JaloliddinovSalohiddin
     * @license AkromovAzizjon
     */
    public function contract_old($id)
    {
        $shopOrder = ShopOrder::findOne($id);

        $placeInfo = PlaceAdress::findOne($shopOrder->place_adress_id);

        $id = (string)$shopOrder->id;
        $bn = '000000000000';
        $bn = substr_replace($bn, $id, strlen($bn) - strlen($id), strlen($id));

        $staticWord = [
            'orderName' => $shopOrder->contact_name ?? '{Не указан!}',
            'orderNumber' => $shopOrder->id ?? '{Номер товара не указан!}',
            'date' => $shopOrder->created_at ?? '{Дата не указана!}',
            'sellerName' => Az::l('Нет имени продавца'),
            'sellerAddress' => Az::l('Нет адреса продавца'),
            'registerNumber' => $bn ?? '{Пусто}',
            'bn' => '{Пусто}',
            'orderAddress' => $placeInfo->name ?? Az::l('Адресс покупателя отсутствует'),
            'serviceCharge' => $shopOrder->price ?? '{Пусто}',
            'totalMoney' => $shopOrder->total_price ?? 0,
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

        $dynamic = [];

        Az::$app->market->generatorBarCodes->generateBarcodeJpg($id);

        foreach ($shopOrderItem as $item) {
            $items = [];
            $items['productName'] = $item['name'] ?? 'Не указан!';
            $items['quanty'] = $item['amount'] ?? 0;
            $items['money'] = $item['price'] ?? 0;
            $items['productMoney'] = $item['price_all'] ?? 0;
            $staticWord['totalMoney'] += $item['price_all'] ?? 0;
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
        /*if (!$this->isCLI()) {
            $url = $this->file_path . '/' . $file_name;
            $this->urlRedirect($url, false);
        }*/
        return $file_close;

    }

    public function contract($id)
    {
        $shopOrder = ShopOrder::findOne($id);
        $childShopOrder = ShopOrder::findAll(['parent' => $shopOrder->id]);
        $childNumbers = count($childShopOrder);

        $placeInfo = PlaceAdress::findOne($shopOrder->place_adress_id);

        $id = (string)$shopOrder->id;
        $bn = '000000000000';
        $bn = substr_replace($bn, $id, strlen($bn) - strlen($id), strlen($id));

        $staticWord = [
            'orderNumber' => $shopOrder->id ?? '{Номер товара не указан!}',
            'bn' => '{Пусто}',
            'sellerName' => $shopOrder->contact_name ?? 'Не указан!',
            'sellerAddress' => $placeInfo->name ?? 'Не указан!',
            'registerNumber' => $bn,
            'orderName' => $shopOrder->contact_name ?? 'Не указан!',
            'orderAddress' => $placeInfo->name ?? 'Не указан!',
        ];
        $date = new \DateTime();
        $file_name = "contractSh{$id}.{$date->format('Y-m-d-H-i-s')}.docx";
        $nameFile = '/binary/words/1C/Договор_заказ/';
        if (file_exists($nameFile . Az::$app->language . '.docx'))
            $file_open = Root . $nameFile . Az::$app->language . '.docx';
        else $file_open = Root . $nameFile . 'ru.docx';

        $file_close = Root . '/upload' . $this->file_path . "/" . $file_name;
        $fileJpg = Root . '/upload/imagez/image' . $id . '.jpg';

        $shopOrderItem = ShopOrderItem::find()->where(
            ['shop_order_id' => $id]
        )->all();

        $idi = (string)$shopOrder->id;
        $bn = '000000000000';
        $bn = substr_replace($bn, $idi, strlen($bn) - strlen($idi), strlen($idi));
        $staticWord ['bn'] = $bn;
        if ((int)$shopOrder->user_company_ids > 0) {

            $companyInfo = UserCompany::findOne($shopOrder->user_company_ids);
            if ($companyInfo !== null) {
                $companyAddress = PlaceAdress::findOne($companyInfo->place_adress_id);
                if ($companyInfo !== null) {
                    $staticWord['sellerAddress'] = $companyAddress->name ?? 'Не указан!';
                }
                $sellerAdr = PlaceAdress::findOne($companyInfo->place_adress_id ?? 3);

                if ($sellerAdr !== null)
                    $staticWord['sellerAddress'] = $sellerAdr->place;
            }

        }

        Az::$app->market->generatorBarCodes->generateBarcodeJpg($id);
        $templateProcessor = new TemplateProcessor($file_open);
        $templateProcessor->setValues($staticWord);
        $templateProcessor->setImageValue("barcode", $fileJpg);
        $templateProcessor->cloneBlock('mainBlock', $childNumbers + 1, true, true);
        $templateProcessor->cloneBlock('titleBlock', $childNumbers + 1, true, true);

        $allquantity = 0;

        // parent
        $dynamic = [];
        $dynamic['orderNumberDyna#1'] = $shopOrder->number ?? 'Не указан!';

        $dynamic['dateDyna#1'] = date('d.m.Y', strtotime($shopOrder->date)) ?? '{Дата не указана!}';
        $dynamic['sellerNameDyna#1'] = Az::l('Нет имени продавца');
        $dynamic['sellerAddressDyna#1'] = Az::l('Нет адреса продавца');
        $dynamic['registerNumberDyna#1'] = $bn ?? '{Пусто}';
        $dynamic['orderNameDyna#1'] = $bn ?? '{Пусто}';
        $dynamic['orderAddressDyna#1'] = $placeInfo->name ?? Az::l('Адресс покупателя отсутствует');
        $dynamic['numsrtrDyna#1'] = '';

        $repeat = [];
        $totalmoney = 0;
        foreach ($shopOrderItem as $item) {
            $items = [];
            $items['n1#1'] = $item['amount'] ?? 0;
            $allquantity += $items['n1#1'];
            $items['moneyDyna#1'] = $item['price'] ?? 0;
            $items['productMoneyDyna#1'] = $item['price_all'];
            $totalmoney += $items['productMoneyDyna#1'];

            $shopcatalog = ShopCatalog::findOne($item['shop_catalog_id']);
            if ($shopcatalog !== null) {
                $shopelement = ShopElement::findOne($shopcatalog->shop_element_id);
                $items['productNameDyna#1'] = $shopelement->name ?? 'Не указан!';
            } else
                $items['productNameDyna#1'] = 'Не указан!';

            $repeat[] = $items;

        }
        $dynamic['totalDyna#1'] = $totalmoney;
        $templateProcessor->setValues($dynamic);
        $templateProcessor->cloneRowAndSetValues('productNameDyna#1', $repeat);

        // Children
        foreach ($childShopOrder as $key => $childitems) {
            $placeInfo = PlaceAdress::findOne($childitems->place_adress_id);
            $shopOrderItem = ShopOrderItem::find()->where(
                ['shop_order_id' => $childitems->id]
            )->all();

            $dynamic['dateDyna'] = $childitems->created_at ?? '{Дата не указана!}';

            $index = $key + 2;
            $dynamic = [];
            $dynamic['orderNumberDyna#' . $index] = $childitems->number ?? 'Не указан!';
            $dynamic['dateDyna#' . $index] = date('d.m.Y', strtotime($childitems->date)) ?? '{Дата не указана!}';
            $dynamic['sellerNameDyna#' . $index] = Az::l('Нет имени продавца');
            $dynamic['sellerAddressDyna#' . $index] = Az::l('Нет адреса продавца');
            $dynamic['registerNumberDyna#' . $index] = $bn ?? '{Пусто}';
            $dynamic['orderNameDyna#' . $index] = $bn ?? '{Пусто}';
            $dynamic['orderAddressDyna#' . $index] = $placeInfo->name ?? Az::l('Адресс покупателя отсутствует');
            $dynamic['numsrtrDyna#' . $index] = '';

            $repeat = [];
            $totalmoney = 0;
            foreach ($shopOrderItem as $item) {
                $items = [];
                $items['n1#' . $index] = $item['amount'] ?? 0;
                $allquantity += $items['n1#' . $index];
                $items['moneyDyna#' . $index] = $item['price'] ?? 0;
                $items['productMoneyDyna#' . $index] = $item['price_all'];
                $totalmoney += $items['productMoneyDyna#' . $index];

                $shopcatalog = ShopCatalog::findOne($item['shop_catalog_id']);
                if ($shopcatalog !== null) {
                    $shopelement = ShopElement::findOne($shopcatalog->shop_element_id);
                    $shopelement->name;
                    $items['productNameDyna#' . $index] = $shopelement->name ?? 'Не указан!';
                } else
                    $items['productNameDyna#' . $index] = 'Не указан!';

                $repeat[] = $items;
            }
            $dynamic['totalDyna#' . $index] = $totalmoney;
            $templateProcessor->setValues($dynamic);
            $templateProcessor->cloneRowAndSetValues('productNameDyna#' . $index, $repeat);
        }

        // All quantity to OCN
        $static = [
            'ocn' => $allquantity,
        ];
        $templateProcessor->setValues($static);


        if ($file_close) {
            $templateProcessor->saveAs($file_close,);

        } else {
            unlink($file_close);
            $templateProcessor->saveAs($file_close,);
        }


        if ($this->execute)
            shell_exec($file_close);
        /*if (!$this->isCLI()) {
            $url = $this->file_path . '/' . $file_name;
            $this->urlRedirect($url, false);
        }*/
        return $file_close;

    }

    public function contractDoub($id)
    {


    }

    #endregion

    #region multiContractTest

    public function multiContractTest()
    {

        $this->multiContract();
    }


    #endregion
    #region multiContract


    /**
     *
     * Function  multiContract
     *
     * Kontrakt generatsiya qiladi
     * http://mplace.zoft.uz/shop/user/filter-common/main.aspx?id=1234
     *
     * @param $ids
     * @return  string
     *
     * @license OtabeNosirov
     * @license JaloliddinovSalohiddin
     * @license AkromovAzizjon
     */
    public function multiContract($ids)
    {

        $urls = [];

        if (is_array($ids))
            foreach ($ids as $id) {
                $urls[] = str_replace('/', '\\', $this->contract($id));
            }
        else
            $urls[] = str_replace('/', '\\', $this->contract($ids));
        $merged_file = Az::$app->office->mergeFiles->mergeDocsFromArray($urls) . '.docx';

        if (Az::$app->office->mergeFiles->is_pdf)
            $to_pdf = Az::$app->office->docto->docPdf($merged_file);
        else
            $to_pdf = $merged_file;


        $to_pdf_arr = explode('\\', $to_pdf);

        $to_pdf_arr = array_filter($to_pdf_arr);

        $size = count($to_pdf_arr);

        $url_pdf = '/' . $to_pdf_arr[$size - 3] . '/' . $to_pdf_arr[$size - 2] . '/' . $to_pdf_arr[$size - 1];


        return $url_pdf;
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
    /**
     *
     * Function  cashTemplate
     * @param $id
     * @return  string
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     * @license OtabeNosirov
     * @license JaloliddinovSalohiddin
     * @license AkromovAzizjon
     */
    public function cashTemplate($id)
    {
        $ware_return = WareReturn::findOne($id);

        $date = new \DateTime();

        $i = 1;

        $shopOrder = ShopOrder::findOne($ware_return->shop_order_id);

        $staticWord = [
            'untilDate' => $shopOrder->date_deliver ?? Az::l(''),
            'orderName' => Az::l('Нет имени покупателя'),
            'orderNumber' => Az::l('Номер заказа отсутствует'),
            'date' => $shopOrder->created_at ?? Az::l('Дата заказа не указана'),
            'sellerName' => Az::l('Нет имени продавца'),
            'sellerAddress' => Az::l('Нет адреса продавца'),
            'priceAll' => $shopOrder->total_price ?? 0,
            'ids' => $i - 1,
            'numstr' => $this->num2str($shopOrder->total_price ?? 0),
            'bankName' => Az::l('Адресс покупателя отсутствует'),
            'bankAccount' => Az::l('Адресс покупателя отсутствует'),
            'bankAddress' => Az::l('Адресс покупателя отсутствует'),
            'orderID' => $shopOrder->id ?? Az::l('Нет'),
            'orderAddress' => Az::l('Адресс покупателя отсутствует'),
        ];

        $nameFile = '/binary/words/1C/Счет_на_оплату/';
        if (file_exists($nameFile . Az::$app->language . '.docx'))
            $file_open = Root . $nameFile . Az::$app->language . '.docx';
        else $file_open = Root . $nameFile . 'ru.docx';
        $file_name = "/cashDocumentSh{$id}{$date->format('Y-m-d-H-i-s')}.docx";
        $file_close = Root . '/upload' . $this->file_path . "/" . $file_name;


        $shopOrderItem = ShopOrderItem::find()->where(
            ['shop_order_id' => $id]
        )->all();
        $companyInfo = '';
        $sellerAdr = null;

        if ($shopOrder->user_company_ids !== null && !empty($shopOrder->user_company_ids)) {

            $companyInfo = UserCompany::findOne($shopOrder->user_company_ids);
            $staticWord['bankAccount'] = $companyInfo->bank_account ?? "No Bank account";
            $staticWord['bankAddress'] = $companyInfo->bank_address ?? "No bank address";
            $staticWord['orderAddress'] = $companyInfo->bank_address ?? "No order address";
            $staticWord['bankName'] = $companyInfo->bank ?? "No bank name";
            $staticWord['sellerPhone'] = $companyInfo->phone ?? "No seller phone";
            $staticWord['sellerName'] = $companyInfo->name ?? "No seller name";


            $sellerAdr = PlaceAdress::findOne($companyInfo->place_adress_id);
        }

        $userInfo = User::findOne($shopOrder->user_id);
        //$userCompanyInfo = User::findOne($userInfo->user_company_id);


        $dynamic = [];

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

        //if ($userInfo->user_company_id !== null || !empty($userInfo->user_company_id)) {
        $userCompanyInfo = $userInfo ? UserCompany::findOne($userInfo->user_company_id) : '';
        $staticWord['orderName'] = $userCompanyInfo->name ?? 'Не задано';
        //}


        if ($sellerAdr !== null)
            $staticWord['sellerAddress'] = $sellerAdr->name;


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

    #region selectedCashTemplateTest

    public function selectedCashTemplateTest()
    {
        $this->selectedCashTemplate();

    }

    #endregion selectedCashTemplate
    #region selectedCashTemplate
    /**
     *
     * Function  selectedCashTemplate
     * @param $ids
     * @return  string
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     *
     * @author OtabeNosirov
     * @author JaloliddinovSalohiddin
     * @author AkromovAzizjon
     */

    public function selectedCashTemplate($ids)
    {

        $urls = [];

        if (is_array($ids))
            foreach ($ids as $id) {
                $urls[] = str_replace('/', '\\', $this->cashTemplate($id));
            }
        else
            $urls[] = str_replace('/', '\\', $this->cashTemplate($ids));

        $merged_file = Az::$app->office->mergeFiles->mergeDocsFromArray($urls) . '.docx';

        $mf = new MergeFiles();

        #tegilmasin************
        if ($mf->is_pdf)
            $to_pdf = Az::$app->office->docto->docPdf($merged_file);
        else
            $to_pdf = $merged_file;
        #tegilmasin************

        $to_pdf_arr = explode('\\', $to_pdf);
        $to_pdf_arr = array_filter($to_pdf_arr);
        $size = count($to_pdf_arr);

        $url_pdf = '/' . $to_pdf_arr[$size - 3] . '/' . $to_pdf_arr[$size - 2] . '/' . $to_pdf_arr[$size - 1];


        return $url_pdf;

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
    /**
     *
     * Function  generateAct
     * @param $id
     * @return  string
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     *
     * @license OtabeNosirov
     * @license JaloliddinovSalohiddin
     * @license AkromovAzizjon
     */
    public function generateAct($id)
    {
        $shopShippment = ShopShipment::findOne(['id' => $id]);
        $ware = WareAccept::findOne(['shop_shipment_id' => $id]);

        $static = [
            'docNumber' => $shopShippment->id ?? Az::l('Не указан'),
            'courier' => Az::l('Нет имени курьера'),
            'createDate' => $shopShippment->created_at ?? Az::l('Дата не указана'),
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
        //$ware = WareAccept::findOne($id);

        $dinamic = [];
        if ($ware !== null) {

            $courier = ShopCourier::findOne(['id' => $shopShippment->shop_courier_id]);

            if ($courier !== null) {
                $static['courier'] = $courier->name ?? 'Не указан';
            }

            $orders = ShopOrder::find()->where([
                'shop_shipment_id' => $shopShippment->id
            ])->all();

            $dinamic = [];
            if ($orders !== null) {
                $subTotal = 0;
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


                    $item['productName'] = $order->name ?? 'Не указан';
                    $item['amount'] = $order->amount ?? 'Не указан';
                    $item['fullName'] = $order->contact_name ?? 'Не указан';
                    $item['price'] = $order->total_price ?? 0;
                    $item['orderNumber'] = $order->id ?? 'Не указан';
                    $item['order'] = $order->id ?? 'Не указан';
                    $item['id'] = $i;
                    $subTotal += $order->total_price;
                    $i++;
                    $dinamic[] = $item;
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

    #region multiGenerateActTest

    public function multiGenerateActTest()
    {
        Az::$app->office->wordpdf->execute = true;
        $id = 142;
        $this->multiGenerateAct($id);

    }
    #endregion multiGenerateAct
    #region multiGenerateAct


    /**
     *
     * Function  multiGenerateAct
     *
     * Funkciya nima qiladi
     *
     * @param $ids ShopOrder yoki ShopShipment larni id lari , shu id lar bo'yicha doc.pdf generatsiya qiladi
     * @return  string
     *
     * @author OtabeNosirov
     * @author JaloliddinovSalohiddin
     * @author AkromovAzizjon
     * @license DilshodKhudoyarov
     *
     */
    public function multiGenerateAct($ids)
    {
        $urls = $this->urls;
        if (is_array($ids))

            foreach ($ids as $id) {
                $urls[] = str_replace('/', '\\', $this->generateAct($id));
            }
        else
            $urls[] = str_replace('/', '\\', $this->generateAct($ids));
        $merged_file = Az::$app->office->mergeFiles->mergeDocsFromArray($urls) . '.docx';
        $mf = new MergeFiles();

        if ($mf->is_pdf)
            $to_pdf = Az::$app->office->docto->doc_pdfTest($merged_file);
        else
            $to_pdf = $merged_file;

        return $this->url_pdf($to_pdf);
    }

    public function url_pdf($to_pdf)
    {
        $to_pdf_arr = explode('\\', $to_pdf);
        $to_pdf_arr = array_filter($to_pdf_arr);
        $size = count($to_pdf_arr);

        $url_pdf = '/' . $to_pdf_arr[$size - 3] . '/' . $to_pdf_arr[$size - 2] . '/' . $to_pdf_arr[$size - 1];

        return $url_pdf;
    }

    #endregion multiGenerateAct

    #region multiRouteListTest

    public function multiRouteListTest()
    {
        $this->multiRouteList();
    }

    #endregion multiRouteList
    #region multiRouteList
    /**
     *
     * Function  multiRouteList
     * @param $ids
     * @return  string
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     *
     * @author  OtabekNosirov
     * @author  JaloliddinovSalohiddin
     * @author  AkromovAzizjon
     */
    public function multiRouteList($ids)
    {
        $urls = [];

        if (is_array($ids))
            foreach ($ids as $id) {
                $urls[] = str_replace('/', '\\', $this->routeList($id));
            }
        else
            $urls[] = str_replace('/', '\\', $this->routeList($ids));

        $merged_file = Az::$app->office->mergeFiles->mergeDocsFromArray($urls) . '.docx';
        $mf = new MergeFiles();

        #tegilmasin************
        if ($mf->is_pdf)
            $to_pdf = Az::$app->office->docto->docPdf($merged_file);
        else
            $to_pdf = $merged_file;
        #tegilmasin************

        $to_pdf_arr = explode('\\', $to_pdf);
        $to_pdf_arr = array_filter($to_pdf_arr);
        $size = count($to_pdf_arr);

        $url_pdf = '/' . $to_pdf_arr[$size - 3] . '/' . $to_pdf_arr[$size - 2] . '/' . $to_pdf_arr[$size - 1];

        return $url_pdf;
    }

    #endregion multiRouteList

    #region routeListTest
    public function routeListTest()
    {
        $this->routeList(21);
    }

    #endregion
    #region routeList

    /**
     *
     * Function  routeList
     * @param $id
     * @return  string
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     *
     * @license OtabeNosirov
     * @license JaloliddinovSalohiddin
     * @license AkromovAzizjon
     */
    public function routeList($id)
    {
        $ware = WareAccept::find()->where(['shop_shipment_id' => $id])->one();

        $shopShippment = ShopShipment::findOne($id);

        $courier = ShopCourier::findOne(['id' => $shopShippment->shop_courier_id]);

        $static = [
            'docNumber' => $shopShippment->id ?? Az::l('Номер заказа не указан'),
            'courier' => $courier->name ?? Az::l('Курьер не указан'),
            'deliveryDate' => $shopShippment->date_deliver ?? Az::l('Дата не указана'),
            'shipmentDate' => $shopShippment->date_deliver ?? Az::l('Дата не указана'),
            'shipmentDate2' => $shopShippment->date_deliver ?? Az::l('Дата не указана'),
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
        $subTotal = 0;
        $dinamic = [];
        $companies = [];
        if ($ware !== null) {

            $shopOrder = ShopOrder::findAll([
                'shop_shipment_id' => $shopShippment->id,
            ]);

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

                    $shopelements_ids = $order->shop_element_ids;
                    $string = '';
                    foreach ($shopelements_ids as $key => $shopelement_id) {
                        $shopelements = ShopElement::findOne($shopelement_id);

                        if ($key < 1)
                            $string = $shopelements->name;
                        else
                            $string .= ', ' . $shopelements->name;

                    }
                    $item['attachmentList'] = $string;

//                    if ($orderItem !== null) {
//                        foreach ($orderItem as $e) {
//                            $catalog = ShopCatalog::find()->where(
//                                ['id' => $e->shop_catalog_id]
//                            )->all();
//                            $catalog->
//                            foreach ($catalog as $n) {
//
//                                $item['attachmentList'] = $n->name;
//
//                            }
//
//                        }
//                    }

                    if (empty($order->user_company_ids)) {
                        $uCompany = new UserCompany();
                        $uCompany->name = "Компания не указана!";

                    } else {
                        $userCompany = UserCompany::find()->where(['id' => $order->user_company_ids])->all();
                        foreach ($userCompany as $uc) {
                            $companies[] = $uc['name'];
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
        /*if (!$this->isCLI())
            $this->urlRedirect($url, false);*/


        return $file_close;
    }

    #endregion

    #region banderolTestBanderol
    public function banderolTest()
    {
        $id = 68;
        $this->banderol($id);
    }

    #endregion

    #region banderol
    /**
     *
     * Function  banderol
     * @param $id
     * @return  string
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     *
     * @license OtabeNosirov
     * @license JaloliddinovSalohiddin
     * @license AkromovAzizjon
     */
    public function banderol2($id)
    {
        $info = ShopOrder::findOne($id);
        $childShopOrder = ShopOrder::findAll(['parent' => $info->id]);
        $total_price = $info->total_price;
        foreach ($childShopOrder as $item) {
            $total_price += $item->total_price;
        }

        $price = $info->price;
        $deliver_price = $info->deliver_price;

        $name = $info->contact_name;

        if ($info->place_adress_id) {
            $placeaddress = PlaceAdress:: findOne($info->place_adress_id);
            if (!$placeaddress || empty($placeaddress->place))
                $address = "Нет адреса";
            else
                $address = "$placeaddress->place";
        } else
            $address = "Нет адреса";


        $template = [

            'banderolNumber' => $id,
            'sellerName' => Az::l('Нет имени продавца'),
            'phoneNumber' => Az::l('Нет номера телефона'),
            'sellerAddress' => Az::l('Нет адреса'),
            'deliveryCash' => $deliver_price,
            'price' => $price,
            'totalCash' => $total_price,
            'weight' => $info->weight,
            'index' => Az::l('Нет индекса компании'),
            'deliveryDate' => date('d.m.Y', strtotime($info->date_deliver)),
            'orderName' => $name,

            'orderAddress' => $address,
            'orderCity' => Az::l('Нет информации о регионе'),
            'orderStreet' => Az::l('Нет информации об улице'),
            'regnum' => '____',

        ];

        Az::$app->market->generatorBarCodes->generateBarcodeJpg($id);

        #region template and file
        $date = new \DateTime();
        $file_name = "banderol{$id}{$date->format('Y-m-d-H-i-s')}.docx";
        $fileJpg = Root . '/upload/imagez/image' . $id . '.jpg';

        $nameFile = '/binary/words/1C/Бандероль/';
        if (file_exists($nameFile . Az::$app->language . '.docx'))
            $file_open = Root . $nameFile . Az::$app->language . '.docx';
        else $file_open = Root . $nameFile . 'ru.docx';
        $file_close = Root . '/upload' . $this->file_path . "/" . $file_name;
        ##endregion


        if ((int)$info->user_company_ids > 0) {
            $companyInfo = UserCompany::findOne($info->user_company_ids);

            if ($companyInfo !== null) {
                $template['sellerName'] = $companyInfo->name;
                $template['phoneNumber'] = $companyInfo->phone;
                $template['index'] = $companyInfo->index;


                if (!empty($companyInfo->place_adress_id)) {

                    $sellerAdr = PlaceAdress::findOne($companyInfo->place_adress_id);

                    if ($sellerAdr !== null)
                        $template['sellerAddress'] = $sellerAdr->place;
                }
            }
        }

        if ($info->place_adress_id)
            $placeInfo = PlaceAdress::findOne($info->place_adress_id);
        else
            $placeInfo = null;

        if ($placeInfo !== null) {
            $template['orderStreet'] = $placeInfo->street;

            $regionInfo = PlaceRegion::findOne($placeInfo->place_region_id);
            if ($regionInfo !== null)
                $template['orderCity'] = $regionInfo->name;
        }

        if ($info->user_id !== null && empty($info->user_id) && (int)$info->user_id > 0) {
            $userInfo = User::findOne($info->user_id);
            if ($userInfo !== null)
                $template['orderName'] = $userInfo->name . ' hello';
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

        return $file_close;
    }

    public function banderol($id)
    {

        $order = ShopOrder::findOne($id);
        $childs = ShopOrder::findAll(['parent' => $order->id]);

        if (!$order)
            return null;

        $total_price = $order->total_price;
        foreach ($childs as $child) {
            $total_price += $child->total_price;
        }

        $place_adress = PlaceAdress::findOne($order->place_adress_id);
        $place_region = PlaceRegion::findOne($place_adress->place_region_id);
        
        $address = 'Адрес не указан';
        $street = 'Улица не указана';
        if ($place_adress) {
            $address = $place_adress->place;
            $street = $place_adress->street;
        }

        $city = 'Город не указан';
        if ($place_region) {
            $city = $place_region->name;
        }

        $companies = UserCompany::findAll($order->user_company_ids);

        $company = '';
        $sellerAdress = '';
        if (!empty($companies)) {

            /** @var UserCompany $company */
            foreach ($companies as $company) {
            
                $company .= strtr($this->layout['companies'], [
                    '{company}' => $company->name,
                    '{phone}' => $company->phone,
                ]);

                $adress = PlaceAdress::findOne($company->place_adress_id);

                if ($address)
                    $sellerAdress .= "$adress->place, ";
                    
            }


        }

        $id = (string)$order->id;
        $bn = '000000000000';
        $bn = substr_replace($bn, $id, strlen($bn) - strlen($id), strlen($id));

        if (empty($bn))
            $bn  = '_____';

        $template = [
            'banderolNumber' => $id,
            'company' => $company,
            'sellerAddress' => $sellerAdress,
            'deliveryCash' => $order->deliver_price,
            'price' => $order->price,
            'totalCash' => $total_price,
            'weight' => $order->weight,
            'index' => '',
            'deliveryDate' => date('d.m.Y', strtotime($order->date_deliver)),
            'orderName' => $order->contact_name,
            'orderAddress' => $address,
            'orderCity' => $city,
            'orderStreet' => $street,
            'regnum' => $bn,
        ];

        Az::$app->market->generatorBarCodes->generateBarcodeJpg($id);

        $date = new \DateTime();
        $file_name = "banderol{$id}{$date->format('Y-m-d-H-i-s')}.docx";
        $fileJpg = Root . '/upload/imagez/image' . $id . '.jpg';

        $nameFile = '/binary/words/1C/Бандероль/';
        if (file_exists($nameFile . Az::$app->language . '.docx'))
            $file_open = Root . $nameFile . Az::$app->language . '.docx';
        else $file_open = Root . $nameFile . 'ru.docx';
        $file_close = Root . '/upload' . $this->file_path . '/' . $file_name;
        ##endregion

        if ($order->place_adress_id)
            $placeInfo = PlaceAdress::findOne($order->place_adress_id);
        else
            $placeInfo = null;

        if ($placeInfo !== null) {
            $template['orderStreet'] = $placeInfo->street;

            $regionInfo = PlaceRegion::findOne($placeInfo->place_region_id);
            if ($regionInfo !== null)
                $template['orderCity'] = $regionInfo->name;
        }

        if ($order->user_id !== null && empty($order->user_id) && (int)$order->user_id > 0) {
            $userInfo = User::findOne($order->user_id);
            if ($userInfo !== null)
                $template['orderName'] = $userInfo->name . ' hello';
        }

        $id = (string)$order->id;
        $bn = '000000000000';
        $bn = substr_replace($bn, $id, strlen($bn) - strlen($id), strlen($id));
        $template['regnum'] = $bn;

        $templateProcessor = new TemplateProcessor($file_open);
        $templateProcessor->setValues($template);
        $templateProcessor->setImageValue("barcode", $fileJpg);
        $templateProcessor->saveAs($file_close);
        if ($this->execute)
            shell_exec($file_close);

        return $file_close;
    }

    #endregion

    #region multiBanderolTest

    public function multiBanderolTest()
    {
        $this->multiBanderol();
    }

    #endregion multiBanderol
    #region multiBanderol
    /**
     *
     * Function  multiBanderol
     * @param $ids
     * @return  string
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     *
     * @author  OtabeNosirov
     * @author  JaloliddinovSalohiddin
     * @author  AkromovAzizjon
     */
    public function multiBanderol($ids)
    {

        $urls = [];
        if (is_array($ids))
            foreach ($ids as $id) {
                $urls[] = str_replace('/', '\\', $this->banderol($id));
            }
        else
            $urls[] = str_replace('/', '\\', $this->banderol($ids));

        $merged_file = Az::$app->office->mergeFiles->mergeDocsFromArray($urls) . '.docx';

        $mf = new MergeFiles();

        if ($mf->is_pdf)
            $to_pdf = Az::$app->office->docto->docPdf($merged_file);
        else
            $to_pdf = $merged_file;

        $to_pdf_arr = explode('\\', $to_pdf);
        $to_pdf_arr = array_filter($to_pdf_arr);
        $size = count($to_pdf_arr);
        $url_pdf = '/' . $to_pdf_arr[$size - 3] . '/' . $to_pdf_arr[$size - 2] . '/' . $to_pdf_arr[$size - 1];

        return $url_pdf;
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

    /**
     *
     * Function  generateRouteSheet
     * @param $id
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     *
     * @license OtabeNosirov
     * @license JaloliddinovSalohiddin
     * @license AkromovAzizjon
     */
    public function generateRouteSheet($id)
    {

        $date = new \DateTime();

        $file_name = "/cashDocument{$id}{$date->format('Y-m-d-H-i-s')}.docx";
        $nameFile = '/binary/words/1C/';

        if (file_exists($nameFile . Az::$app->language . '.docx'))
            $file_open = Root . $nameFile . Az::$app->language . '.docx';
        else $file_open = Root . $nameFile . 'ru.docx';
        $file_close = Root . '/upload ' . $this->file_path . "/" . $file_name;
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

    /**
     *
     * Function  returnCash
     * @param $id
     * @return  string
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     *
     * @license OtabeNosirov
     * @license JaloliddinovSalohiddin
     * @license AkromovAzizjon
     */
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
        $file_close = Root . '/upload' . $this->file_path . "/" . $file_name;

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

        if ($placeInfo !== null) {
            $staticWord['address'] = $placeInfo->name;
        }
        if ($userInfo !== null) {

            $staticWord['name'] = $userInfo->name;
            $staticWord['phone'] = $userInfo->phone;
        }

        if ($shopOrder !== null) {
            $staticWord['date'] = $shopOrder->created_at;
        }
        $staticWord['amount'] = $y;

        $templateProcessor = new TemplateProcessor($file_open);
        $templateProcessor->setValues($staticWord);
        $templateProcessor->saveAs($file_close);

        /* if ($this->execute)
             shell_exec($file_close);*/
        if (!$this->isCLI()) {
            $url = $this->file_path . '/' . $file_name;
            /*$this->urlRedirect($url, false);*/
        }


        return $file_close;
    }

    ##endregion

    #region selectedReturnCashTest

    public function selectedReturnCashTest()
    {

        $this->selectedReturnCash();
    }

    #endregion selectedReturnCash
    #region selectedReturnCash
    /**
     *
     * Function  selectedReturnCash
     * @param $ids
     * @return  string
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     *
     * @author OtabeNosirov
     * @author JaloliddinovSalohiddin
     * @author AkromovAzizjon
     */

    public function selectedReturnCash($ids)
    {

        $urls = [];

        if (is_array($ids))
            foreach ($ids as $id) {
                $urls[] = str_replace('/', '\\', $this->returnCash($id));
            }
        else
            $urls[] = str_replace('/', '\\', $this->returnCash($ids));
        $merged_file = Az::$app->office->mergeFiles->mergeDocsFromArray($urls) . '.docx';

        $mf = new MergeFiles();

        #tegilmasin************
        if ($mf->is_pdf)
            $to_pdf = Az::$app->office->docto->docPdf($merged_file);
        else
            $to_pdf = $merged_file;
        #tegilmasin************

        $to_pdf_arr = explode('\\', $to_pdf);
        $to_pdf_arr = array_filter($to_pdf_arr);
        $size = count($to_pdf_arr);

        $url_pdf = '/' . $to_pdf_arr[$size - 3] . '/' . $to_pdf_arr[$size - 2] . '/' . $to_pdf_arr[$size - 1];

        return $url_pdf;
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
        $ten = [
            ['', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'],
            ['', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'],
        ];
        $a20 = ['десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать'];
        $tens = [2 => 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто'];
        $hundred = ['', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот'];

        $unit = [
            ['сум', 'сум', 'тийин', 0],
            ['тысяча', 'тысячи', 'тысяч', 1],
            ['миллион', 'миллиона', 'миллионов', 0],
            ['миллиард', 'милиарда', 'миллиардов', 0],
        ];
        //
        [$rub, $kop] = explode('.', sprintf("%015.2f", floatval($num)));

        $out = [];
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

