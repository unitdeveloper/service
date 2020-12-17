<?php


namespace zetsoft\service\office;
require Root . '/vendors/fileapp/office/vendor/autoload.php';

use Google\Cloud\Core\Retry;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use zetsoft\models\App\eyuf\Scholar;
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
use zetsoft\system\except\ZException;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;
use function Dash\Curry\all;
use function Safe\substr;
use function Spatie\SslCertificate\length;

/**
 * Class    Wordpdf
 * @package zetsoft\service\office
 *
 * @author Rowa
 * @author Bahodir
 *
 * @license OtabekNosirov
 * @license JaloliddinovSalohiddin
 * @license AkromovAzizjon
 *
 */
class Wordpdf extends ZFrame
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

    private ?ShopOrder $testOrder = null;

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

    #region Order


    public function create(): int
    {

        $this->createOrder();
        $this->createOrderItem();

        return $this->testOrder->id;
    }

    public function createOrder(): int
    {

        $order = ShopOrder::findOne([
            'contact_name' => 'Иван Иванович Иванов'
        ]);
        if ($order === null)
            $order = new ShopOrder();

        $order->contact_name = 'Иван Иванович Иванов';

        $order->save();
        $this->testOrder = $order;
    }


    public function createOrderItem()
    {
        if ($this->testOrder->getShopOrderItemsWithShopOrderId()->count() > 0)
            return null;

        $orderItem = new ShopOrderItem();
        $orderItem->shop_catalog_id = 44;


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

        // todo: start

        $staticWord = [
            'sellerName' => Az::l(''),
            'totalPrice' => Az::l('Нет имени покупателя'),
            'date' => Az::l('Номер заказа отсутствует'),
        ];
        $date = new \DateTime();
        $ext = '.docx';
        $file_name = "productReturnSh{$id}{$date->format('Y-m-d-H-i-s')}" . $ext;
        $nameFile = '/binary/words/1C/Заявление_на_возврат_товаров/';

        if (file_exists($nameFile . Az::$app->language . $ext)) {
            $file = Root . $nameFile . Az::$app->language . '.docx';
        } else {
            $file = Root . $nameFile . 'ru.docx';
        }

        $directory = Root . '/upload' . $this->file_path . '/';
        $file_close = $directory . $file_name . $ext;
        $ware_return = WareReturn::findOne($id);

        $shopInfo = ShopOrder::findOne([
            'id' => $ware_return->shop_order_ids
        ]);

        if ($shopInfo === null)
            throw new ZException('Shop Order с данным ID нету');

        $shopItem = ShopOrderItem::find()->where(
            ['shop_order_id' => $id]
        )->all();


        if ($shopInfo->user_company_ids !== null && !empty($shopInfo->user_company_ids)) {
            $companyInfo = UserCompany::findOne($shopInfo->user_company_ids);
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

        return [
            'directory' => $directory,
            'file_name' => $file_name,
            'full_path' => $directory . $file_name . $ext
        ];

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

    #region contract
    public function contractTest()
    {
        $id = $this->create();

        $this->execute = true;
        $this->contract($id);
    }

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

    public function contract2($id)
    {
        $shopOrder = ShopOrder::findOne($id);

        if (!$shopOrder)
            return null;

        $childShopOrder = ShopOrder::findAll([
            'parent' => $shopOrder->id
        ]);

        $childNumbers = count($childShopOrder);

        $placeInfo = PlaceAdress::findOne($shopOrder->place_adress_id);

        $id = (string)$shopOrder->id;
        $bn = $shopOrder->code;
        //$bn = '000000000000';
        //$bn = substr_replace($bn, $id, strlen($bn) - strlen($id), strlen($id));

        $staticWord = [
            'orderNumber' => $shopOrder->number ?? '{Номер товара не указан!}',
            'bn' => '{Пусто}',
            'sellerName' => 'Не указан!',
            'sellerAddress' => 'Не указан!',
            'registerNumber' => $bn,
            'orderName' => $shopOrder->contact_name ?? 'Не указан!',
            'orderAddress' => $placeInfo->name ?? 'Не указан!',
        ];

        $date = new \DateTime();
        $ext = '.docx';
        $file_name = "contractSh{$id}.{$date->format('Y-m-d-H-i-s')}";
        $nameFile = '/binary/words/1C/Договор_заказ/';
        if (file_exists($nameFile . Az::$app->language . $ext))
            $file_open = Root . $nameFile . Az::$app->language . $ext;
        else $file_open = Root . $nameFile . 'ru' . $ext;

        $directory = Root . '/upload' . $this->file_path . '/';
        $file_close = $directory . $file_name . $ext;
        $fileJpg = Root . '/upload/imagez/image' . $bn . '.jpg';

        $return = [
            'directory' => $directory,
            'file_name' => $file_name,
            'full_path' => $directory . $file_name . $ext
        ];

        $shopOrderItem = ShopOrderItem::find()
            ->where([
                'shop_order_id' => $id
            ])
            ->all();

        $idi = (string)$shopOrder->id;

        $staticWord['bn'] = $bn;
        if ((int)$shopOrder->user_company_ids > 0) {
            $companyInfo = UserCompany::findOne($shopOrder->user_company_ids);
            if ($companyInfo !== null) {
                $staticWord['sellerName'] = $companyInfo->name ?? 'Не указан!';

                $companyAddress = PlaceAdress::findOne($companyInfo->place_adress_id);
                if ($companyInfo !== null) {
                    $staticWord['sellerAddress'] = $companyAddress->name ?? 'Не указан!';
                }
                $sellerAdr = PlaceAdress::findOne($companyInfo->place_adress_id ?? 3);

                if ($sellerAdr !== null)
                    $staticWord['sellerAddress'] = $sellerAdr->name;
            }

        }

        Az::$app->market->generatorBarCodes->generateBarcodeJpg($bn);
        $templateProcessor = new TemplateProcessor($file_open);
        $templateProcessor->setValues($staticWord);
        $templateProcessor->setImageValue('barcode', $fileJpg);
        $templateProcessor->cloneBlock('mainBlock', $childNumbers + 1, true, true);
        $templateProcessor->cloneBlock('titleBlock', $childNumbers + 1, true, true);

        $allquantity = 0;

        // parent
        $dynamic = [];
        $dynamic['orderNumberDyna#1'] = $shopOrder->number ?? 'Не указан!';

        if ($shopOrder->created_at === null)
            $dynamic['dateDyna#1'] = '{Дата не указана!}';
        else
            $dynamic['dateDyna#1'] = date('d.m.Y', strtotime($shopOrder->date_deliver));

        $dynamic['sellerNameDyna#1'] = Az::l('Нет имени продавца');
        $dynamic['sellerAddressDyna#1'] = Az::l('Нет адреса продавца');
        $dynamic['registerNumberDyna#1'] = $bn ?? '{Пусто}';
        $dynamic['orderNameDyna#1'] = $bn ?? '{Пусто}';
        $dynamic['orderAddressDyna#1'] = $placeInfo->name ?? Az::l('Адресс покупателя отсутствует');

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
//                $shopelement = ShopElement::findOne($shopcatalog->shop_element_id);
                $items['productNameDyna#1'] = $shopcatalog->title;
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

            $index = $key + 2;
            $dynamic = [];
            $dynamic['orderNumberDyna#' . $index] = $childitems->number ?? 'Не указан!';

            if ($shopOrder->created_at === null)
                $dynamic['dateDyna#' . $index] = '{Дата не указана!}';
            else
                $dynamic['dateDyna#' . $index] = date('d.m.Y', strtotime($childitems->date_deliver));

            $repeat = [];
            $totalmoney = 0;
            foreach ($shopOrderItem as $item) {
                $items = [];
                $items['n1#' . $index] = $item['amount'] ?? 0;
                $allquantity += $items['n1#' . $index];
                $items['moneyDyna#' . $index] = $item['price'] ?? 0;
                $items['productMoneyDyna#' . $index] = $item['price_all'];
                $totalmoney += $items['productMoneyDyna#' . $index];

                /** @var ShopOrderItem $item */
                $shopcatalog = $item->getShopCatalogOne();
//                if ($shopcatalog)
//                    $shopelement = $shopcatalog->getShopElementOne();

                $items['productNameDyna#' . $index] = $shopcatalog->title ?? 'Не указан!';

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
        return [
            'directory' => $directory,
            'file_name' => $file_name,
            'full_path' => $directory . $file_name . $ext
        ];


    }

    public function contract3($id)
    {
        $shopOrder = ShopOrder::findOne($id);

        if (!$shopOrder)
            return null;

        $childShopOrder = ShopOrder::findAll([
            'parent' => $shopOrder->id
        ]);

        $childNumbers = count($childShopOrder);

        $placeInfo = PlaceAdress::findOne($shopOrder->place_adress_id);

        $id = (string)$shopOrder->id;
        $bn = $shopOrder->code;
        //$bn = '000000000000';
        //$bn = substr_replace($bn, $id, strlen($bn) - strlen($id), strlen($id));

        $staticWord = [
            'orderNumber' => $shopOrder->number ?? '{Номер товара не указан!}',
            'bn' => '{Пусто}',
            'sellerName' => 'Не указан!',
            'sellerAddress' => 'Не указан!',
            'registerNumber' => $bn,
            'orderName' => $shopOrder->contact_name ?? 'Не указан!',
            'orderAddress' => $placeInfo->name ?? 'Не указан!',
        ];

        $date = new \DateTime();
        $ext = '.docx';
        $file_name = "contractSh{$id}.{$date->format('Y-m-d-H-i-s')}";
        $nameFile = '/binary/words/1C/Договор_заказ/';
        if (file_exists($nameFile . Az::$app->language . $ext))
            $file_open = Root . $nameFile . Az::$app->language . $ext;
        else $file_open = Root . $nameFile . 'ru' . $ext;

        $directory = Root . '/upload' . $this->file_path . "/";
        $file_close = $directory . $file_name . $ext;
        $fileJpg = Root . '/upload/imagez/image' . $bn . '.jpg';

        $return = [
            'directory' => $directory,
            'file_name' => $file_name,
            'full_path' => $directory . $file_name . $ext
        ];

        $shopOrderItem = ShopOrderItem::find()
            ->where([
                'shop_order_id' => $id
            ])
            ->all();

        $idi = (string)$shopOrder->id;

        $staticWord['bn'] = $bn;
        if ((int)$shopOrder->user_company_ids > 0) {
            $companyInfo = UserCompany::findOne($shopOrder->user_company_ids);
            if ($companyInfo !== null) {
                $staticWord['sellerName'] = $companyInfo->name ?? 'Не указан!';

                $companyAddress = PlaceAdress::findOne($companyInfo->place_adress_id);
                if ($companyInfo !== null) {
                    $staticWord['sellerAddress'] = $companyAddress->name ?? 'Не указан!';
                }
                $sellerAdr = PlaceAdress::findOne($companyInfo->place_adress_id ?? 3);

                if ($sellerAdr !== null)
                    $staticWord['sellerAddress'] = $sellerAdr->name;
            }

        }

        Az::$app->market->generatorBarCodes->generateBarcodeJpg($bn);
        $templateProcessor = new TemplateProcessor($file_open);
        $templateProcessor->setValues($staticWord);
        $templateProcessor->setImageValue('barcode', $fileJpg);
        $templateProcessor->cloneBlock('mainBlock', $childNumbers + 1, true, true);
        $templateProcessor->cloneBlock('titleBlock', $childNumbers + 1, true, true);

        $allquantity = 0;

        // parent
        $dynamic = [];
        $dynamic['orderNumberDyna#1'] = $shopOrder->number ?? 'Не указан!';

        if ($shopOrder->created_at === null)
            $dynamic['dateDyna#1'] = '{Дата не указана!}';
        else
            $dynamic['dateDyna#1'] = date('d.m.Y', strtotime($shopOrder->date_deliver));

        $dynamic['sellerNameDyna#1'] = Az::l('Нет имени продавца');
        $dynamic['sellerAddressDyna#1'] = Az::l('Нет адреса продавца');
        $dynamic['registerNumberDyna#1'] = $bn ?? '{Пусто}';
        $dynamic['orderNameDyna#1'] = $bn ?? '{Пусто}';
        $dynamic['orderAddressDyna#1'] = $placeInfo->name ?? Az::l('Адресс покупателя отсутствует');

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
//                $shopelement = ShopElement::findOne($shopcatalog->shop_element_id);
                $items['productNameDyna#1'] = $shopcatalog->title;
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

            $index = $key + 2;
            $dynamic = [];
            $dynamic['orderNumberDyna#' . $index] = $childitems->number ?? 'Не указан!';

            if ($shopOrder->created_at === null)
                $dynamic['dateDyna#' . $index] = '{Дата не указана!}';
            else
                $dynamic['dateDyna#' . $index] = date('d.m.Y', strtotime($childitems->date_deliver));

            $repeat = [];
            $totalmoney = 0;
            foreach ($shopOrderItem as $item) {
                $items = [];
                $items['n1#' . $index] = $item['amount'] ?? 0;
                $allquantity += $items['n1#' . $index];
                $items['moneyDyna#' . $index] = $item['price'] ?? 0;
                $items['productMoneyDyna#' . $index] = $item['price_all'];
                $totalmoney += $items['productMoneyDyna#' . $index];

                /** @var ShopOrderItem $item */
                $shopcatalog = $item->getShopCatalogOne();
//                if ($shopcatalog)
//                    $shopelement = $shopcatalog->getShopElementOne();

                $items['productNameDyna#' . $index] = $shopcatalog->title ?? 'Не указан!';

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
        return [
            'directory' => $directory,
            'file_name' => $file_name,
            'full_path' => $directory . $file_name . $ext
        ];


    }

    public function contract($id)
    {

        //start|DavlatovRavshan|2020.10.10

        $shopOrder = ShopOrder::findOne($id);

        if (!$shopOrder)
            return null;

        $id = (string)$shopOrder->id;
        $bn = $shopOrder->code;

        $date = new \DateTime();
        $ext = '.docx';
        $file_name = "contractSh{$id}.{$date->format('Y-m-d-H-i-s')}";

        $nameFile = '/binary/words/1C/Договор_заказ/';

        if (file_exists($nameFile . Az::$app->language . $ext))
            $file_open = Root . $nameFile . Az::$app->language . $ext;
        else
            $file_open = Root . $nameFile . 'ru' . $ext;

        $directory = Root . '/upload' . $this->file_path . '/';
        $file_close = $directory . $file_name . $ext;

        $fileJpg = Root . '/upload/imagez/image' . $bn . '.jpg';

        $templateProcessor = new TemplateProcessor($file_open);

        Az::$app->market->generatorBarCodes->generateBarcodeJpg($bn);
        $templateProcessor->setImageValue('barcode', $fileJpg);

        $childShopOrder = ShopOrder::findAll([
            'parent' => $shopOrder->id
        ]);

        $allOrders = ZArrayHelper::merge([$shopOrder], (array)$childShopOrder);

        $count = count(ZArrayHelper::getColumn($allOrders, 'id'));

        $templateProcessor->cloneBlock('titleBlock', $count, true, true);
        $templateProcessor->cloneBlock('mainBlock', $count, true, true);

        $static = [];
        $sellerAddress = [];
        $sellerName = [];
        $orderAddress = [];
        $orderName = [];
        $totalAmount = 0;

        /** @var ShopOrder $order */
        $key = 1;
        foreach ($allOrders as $order) {

            $userCompany = null;
            if (!empty($order->user_company_ids))
                $userCompany = UserCompany::findAll($order->user_company_ids);

            $placeAdress = null;
            if (!empty($order->place_adress_id))
                $placeAdress = PlaceAdress::findOne($order->place_adress_id);

            if ($placeAdress)
                $orderAddress[$placeAdress->name] = $placeAdress->name;

            $orderName[$order->contact_name] = $order->contact_name;

            /** @var UserCompany $company */
            if (!empty($userCompany)) {
                foreach ($userCompany as $company) {

                    $adress = PlaceAdress::findOne($company->place_adress_id);
                    if ($adress)
                        $sellerAddress[$adress->place] = $adress->place;

                    $sellerName[$company->name] = $company->name;

                }
            }

            $orderItems = ShopOrderItem::findAll([
                'shop_order_id' => $order->id,
                'check_return' => [
                    false,
                    null
                ]
            ]);


            $itemsCount = count(ZArrayHelper::getColumn($orderItems, 'id'));

            $templateProcessor->cloneRow('productNameDyna#' . $key, $itemsCount);

            $totalPrice = 0;
            $itemKey = 1;
            foreach ($orderItems as $orderItem) {

                $catalog = ShopCatalog::findOne($orderItem->shop_catalog_id);

                $totalPrice += (int)$orderItem->price_all;
                $totalAmount += (int)$orderItem->amount;

                $catalogName = 'Описание товара не указано';
                if ($catalog && !empty($catalog->title))
                    $catalogName = $catalog->title;

                $number = "$key#$itemKey";
                $static['productNameDyna#' . $number] = $catalogName;
                $static['n1#' . $number] = (int)$orderItem->amount;
                $static['moneyDyna#' . $number] = (int)$orderItem->price;
                $static['productMoneyDyna#' . $number] = (int)$orderItem->price_all;

                $itemKey++;

            }
            $static['orderNumberDyna#' . $key] = $order->number;
            $static['dateDyna#' . $key] = date('d-m-Y', strtotime($order->date));
            $static['totalDyna#' . $key] = $totalPrice;

            $key++;

        }

        $static['orderNumber'] = $shopOrder->number;
        $static['orderName'] = implode(' | ', $orderName);
        $static['orderAddress'] = implode(' | ', $orderAddress);
        $static['sellerName'] = implode(' | ', $sellerName);
        $static['sellerAddress'] = implode(' | ', $sellerAddress);

        $static['ocn'] = $totalAmount;
        $static['bn'] = $bn;
        $static['registerNumber'] = $bn;

        $templateProcessor->setValues($static);

        //end | DavlatovRavshan | 10.10.2020

        if ($file_close) {
            $templateProcessor->saveAs($file_close);
        } else {
            unlink($file_close);
            $templateProcessor->saveAs($file_close);
        }

        if ($this->execute)
            shell_exec($file_close);
        /*if (!$this->isCLI()) {
            $url = $this->file_path . '/' . $file_name;
            $this->urlRedirect($url, false);
        }*/
        return [
            'directory' => $directory,
            'file_name' => $file_name,
            'full_path' => $directory . $file_name . $ext
        ];


    }

    #endregion

    #region multiContractTest

    public function multiContractTest()
    {
        Az::$app->office->wordpdf->execute = false;
        $url = $this->multiContract([1490, 1491]);
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
        return $this->universalDocument($ids, 'multiContract');
        /*     $pdfs = [];
             if (is_array($ids)){
                 foreach ($ids as $id) {
                     $docx_file = str_replace('/', '\\', $this->contract($id));
                     $to_pdf = Az::$app->office->libreOffice->docPdf($docx_file['full_path'], $docx_file['directory']);
                     if ( strpos($to_pdf,'convert') )  // if string consists 'convert', conversion is successful
                         vdd($to_pdf);
                     $pdfs[] = [
                         'directory' => $docx_file['directory'],
                         'pdf_full_path' => $docx_file['directory'].$docx_file['file_name'].'.pdf',
                         'id' => $id
                     ];
                 }
             } else {
                 $docx_file = str_replace('/', '\\', $this->contract($ids));
                 $to_pdf = Az::$app->office->libreOffice->docPdf($docx_file['full_path']);
                 if ( strpos($to_pdf,'convert') )  // if string consists 'convert', conversion is successful
                     vdd($to_pdf);
                 $pdfs[] = [
                     'directory' => $docx_file['file_name'],
                     'pdf_full_path' => $docx_file['directory'].$docx_file['file_name'].'.pdf',
                     'id' => $ids
                 ];
             }


     //            $file_name = Az::$app->office->jupiternDocs->mergeDocsFromArray($urls);

     //            $merged_file =  $file_name['filepath']. '.docx';




     //           $to_pdf = Az::$app->office->docto->docPdf($merged_file);



             $merged_pdf_url = Az::$app->office->juroshPdfMerge->mergePdfsFromArray($pdfs);

              //vdd($merged_file);
              //$to_pdf = Az::$app->office->openOffice->docPdf($merged_file);
     //        $to_pdf = Az::$app->office->officetopdf->docPdf($merged_file);
     //        $to_pdf = Az::$app->office->pandoc->docPdf($merged_file);
     //        if (strpos($to_pdf, 'Access is denied, ProgID: "Word.Application"'))

     //        $to_pdf_arr = explode('\\', $to_pdf);
     //
     //        $to_pdf_arr = array_filter($to_pdf_arr);
     //
     //        $size = count($to_pdf_arr);
     //
     //        $url_pdf = '/'. $to_pdf_arr[$size - 3] . '/' . $to_pdf_arr[$size - 2] . '/' . $file_name['random_name'].'.pdf';
     //

             return $merged_pdf_url;*/
    }
    //endregion

    //region universalDocument
    public function universalDocumentTest()
    {
        $ids = ['1490'];
        $type = 'multiBanderol';
        vdd($this->universalDocument($ids, $type));
    }

    public function universalDocument($ids, $type)
    {
        // $type =
        //multiGenerateAct
        //multiRouteList
        //multiContract
        //selectedCashTemplate
        //multiBanderol
        //selectedReturnCash
        //selectedReturnProduct
        //returnFromClient

        $pdfs = [];

        foreach ($ids as $id) {
            switch ($type) {
                case 'multiContract':
                    $docx = $this->contract($id);
                    break;
                case 'multiBanderol':
                    $docx = $this->banderol3($id);
                    break;
                case 'multiGenerateAct':
                    $docx = $this->generateAct($id);
                    break;
                case 'multiRouteList':
                    $docx = $this->routeList($id);
                    break;
                case 'selectedCashTemplate':
                    $docx = $this->cashTemplate($id);
                    break;
                case 'selectedReturnCash':
                    $docx = $this->returnCash($id);
                    break;
                case 'selectedReturnProduct':
                    $docx = $this->returnProduct($id);
                    break;
                case 'selectedReturnFromClient':
                    $docx = $this->returnFromClient($id);
                    break;

                default:
                    vdd('type is wrong');
            }


            $docx_file = str_replace('/', '\\', $docx);
            $to_pdf = Az::$app->office->libreOffice->docPdf($docx_file['full_path'], $docx_file['directory']);
            if (strpos($to_pdf, 'convert'))  // if string consists 'convert', conversion is successful
                vdd($to_pdf);

            $pdfs[] = [
                'directory' => $docx_file['directory'],
                'pdf_full_path' => $docx_file['directory'] . $docx_file['file_name'] . '.pdf',
                'id' => $id
            ];

        }


        $merged_pdf_url = Az::$app->office->juroshPdfMerge->mergePdfsFromArray($pdfs, $type);

        return $merged_pdf_url;
    }
    #endregion universalDocument


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


        $i = 1;

        $shopOrder = ShopOrder::findOne($ware_return->shop_order_ids);

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
        $date = new \DateTime();
        $ext = '.docx';
        $file_name = "/cashDocumentSh{$id}{$date->format('Y-m-d-H-i-s')}" . $ext;
        $nameFile = '/binary/words/1C/Счет_на_оплату/';

        if (file_exists($nameFile . Az::$app->language . $ext))
            $file_open = Root . $nameFile . Az::$app->language . $ext;
        else $file_open = Root . $nameFile . 'ru' . $ext;

        $directory = Root . '/upload' . $this->file_path . "/";
        $file_close = $directory . $file_name . $ext;

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

        $templateProcessor = new TemplateProcessor($file_open);
        $templateProcessor->setValues($staticWord);
        $templateProcessor->cloneRowAndSetValues('orderTitle', $dynamic);

        if ($file_close) {
            $templateProcessor->saveAs($file_close);

        } else {
            unlink($file_close);
            $templateProcessor->saveAs($file_close);
        }

        return [
            'directory' => $directory,
            'file_name' => $file_name,
            'full_path' => $directory . $file_name . $ext
        ];
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

    public function generateAct2($id)
    {
        $shopShippment = ShopShipment::findOne($id);

        $date_create = Az::l('Дата не указана');
        if ($shopShippment->created_at)
            $date_create = date('d.m.Y', strtotime($shopShippment->created_at));

        $date = new \DateTime();
        $ext = '.docx';
        $file_name = "ActSalohiddin{$id}{$date->format('Y-m-d-H-i-s')}.docx";
        $nameFile = '/binary/words/1C/Акт_передачи/';

        if (file_exists($nameFile . Az::$app->language . $ext))
            $file_open = Root . $nameFile . Az::$app->language . $ext;
        else
            $file_open = Root . $nameFile . 'ru' . $ext;

        $directory = Root . '/upload' . $this->file_path . '/';
        $file_close = $directory . $file_name . $ext;

        $courier = ShopCourier::findOne(['id' => $shopShippment->shop_courier_id]);

        if ($courier !== null) {
            $static['courier'] = $courier->name ?? 'Не указан';
        }

        $static = [
            'docNumber' => $shopShippment->id ?? Az::l('Не указан'),
            'courier' => Az::l('Нет имени курьера'),
            'createDate' => $date_create,
            'totalPrice' => 0,
            'company' => Az::l('Не указан'),
            'companyPhone' => Az::l('Не указан')
        ];

        $orders = ShopOrder::find()->where([
            'shop_shipment_id' => $shopShippment->id,
            'status_logistics' => ShopOrder::status_logistics['courier_appointment']
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

                $orderItem = ShopOrderItem::find()
                    ->where([
                        'shop_order_id' => $order->id
                    ])
                    ->all();

                if ($orderItem !== null) {

                    foreach ($orderItem as $e) {
                        $item['amount'] = $e['amount'] ?? 'Не указан';

                        $catalog = ShopCatalog::find()
                            ->where([
                                'id' => $e->shop_catalog_id
                            ])->all();


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
                        $com = $userCompany->name ?? 'Не указан';
                        $cP = $userCompany->phone ?? 'Не указан';

                        $companies[] = $com;
                        $comPhones[] = $cP;
                    }
                }

                $item['productName'] = $order->name ?? 'Не указан';

                $shop_order_items = ShopOrderItem::find()->where([
                    'shop_order_id' => $order->id,
                ])->all();
                $item['amount'] = count($shop_order_items);
                $item['fullName'] = $order->contact_name ?? 'Не указан';
                $item['price'] = $order->price ?? 0;
                $item['orderNumber'] = $order->id ?? 'Не указан';
                $item['order'] = $order->number ?? 'Не указан';
                $item['id'] = $i;
                $subTotal += $order->price;
                $i++;
                $dinamic[] = $item;
            }


            $static['company'] = implode(', ', array_unique($companies)) ?? 'Не указан';
            $static['companyPhone'] = implode(', ', array_unique($comPhones)) ?? 'Не указан';
            $static['numProducts'] = $i - 1;
            $static['totalPrice'] = $subTotal;

        }

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

        return [
            'directory' => $directory,
            'file_name' => $file_name,
            'full_path' => $directory . $file_name . $ext
        ];

    }

    public function generateAct($id)
    {
        $shopShippment = ShopShipment::findOne($id);

        $date_create = Az::l('Дата не указана');
        if ($shopShippment->created_at)
            $date_create = date('d.m.Y', strtotime($shopShippment->created_at));

        $static = [
            'docNumber' => $shopShippment->id ?? Az::l('Не указан'),
            'courier' => Az::l('Нет имени курьера'),
            'createDate' => $date_create,
            'totalPrice' => 0,
            'company' => Az::l('Не указан'),
            'companyPhone' => Az::l('Не указан')
        ];

        $date = new \DateTime();
        $ext = '.docx';
        $file_name = "ActSalohiddin{$id}{$date->format('Y-m-d-H-i-s')}.docx";
        $nameFile = '/binary/words/1C/Акт_передачи/';

        if (file_exists($nameFile . Az::$app->language . $ext))
            $file_open = Root . $nameFile . Az::$app->language . $ext;
        else $file_open = Root . $nameFile . 'ru' . $ext;

        $directory = Root . '/upload' . $this->file_path . "/";
        $file_close = $directory . $file_name . $ext;

        $dinamic = [];
//        if ($ware !== null) {

        $courier = ShopCourier::findOne(['id' => $shopShippment->shop_courier_id]);

        if ($courier !== null) {
            $static['courier'] = $courier->name ?? 'Не указан';
        }

        $orders = ShopOrder::find()
            ->where([
                'shop_shipment_id' => $shopShippment->id,
                /*'status_logistics' => [
                    ShopOrder::status_logistics['courier_appointment'],
                    ShopOrder::status_logistics['reported'],
                ]*/
            ])
            ->all();

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
                        $com = $userCompany->name ?? 'Не указан';
                        $cP = $userCompany->phone ?? 'Не указан';

                        $companies[] = $com;
                        $comPhones[] = $cP;
                    }
                }

                $item['productName'] = $order->name ?? 'Не указан';

                $shop_order_items = ShopOrderItem::find()->where([
                    'shop_order_id' => $order->id,
                ])->all();
                $item['amount'] = count($shop_order_items);
                $item['fullName'] = $order->contact_name ?? 'Не указан';
                $item['price'] = $order->price ?? 0;
                $item['orderNumber'] = $order->id ?? 'Не указан';
                $item['order'] = $order->number ?? 'Не указан';
                $item['id'] = $i;
                $subTotal += $order->price;
                $i++;
                $dinamic[] = $item;
            }


            $static['company'] = implode(', ', array_unique($companies)) ?? 'Не указан';
            $static['companyPhone'] = implode(', ', array_unique($comPhones)) ?? 'Не указан';
            $static['numProducts'] = $i - 1;
            $static['totalPrice'] = $subTotal;

        }

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

        return [
            'directory' => $directory,
            'file_name' => $file_name,
            'full_path' => $directory . $file_name . $ext
        ];

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
     * @license UzakbaevAxmet
     *
     */
    public function multiGenerateAct($ids)
    {
        $urls = $this->urls;
        if (is_array($ids)) {

            foreach ($ids as $id) {
                $urls[] = str_replace('/', '\\', $this->generateAct($id));
            }
        } else {
            $urls[] = str_replace('/', '\\', $this->generateAct($ids));
        }


        $merged_file = Az::$app->office->mergeFiles->mergeDocsFromArray($urls) . '.docx';

        $mf = new MergeFiles();

        if ($mf->is_pdf)
            $to_pdf = Az::$app->office->docto->docPdf($merged_file);
        else
            $to_pdf = $merged_file;

        return $this->url_pdf($to_pdf);
    }

    public function url_pdf($to_pdf)
    {
        $to_pdf_arr = explode('\\', $to_pdf);
        $to_pdf_arr = array_filter($to_pdf_arr);
        $size = count($to_pdf_arr);

        return '/' . $to_pdf_arr[$size - 3] . '/' . $to_pdf_arr[$size - 2] . '/' . $to_pdf_arr[$size - 1];
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
     * @return string[]
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     *
     * @license OtabeNosirov
     * @license JaloliddinovSalohiddin
     * @license AkromovAzizjon
     */
    public function routeList3($id)
    {

        $shopShippment = ShopShipment::findOne($id);

        $courier = ShopCourier::findOne(['id' => $shopShippment->shop_courier_id]);

        if ($shopShippment->date_deliver)
            $date_deliver = date('d.m.Y', strtotime($shopShippment->date_deliver));
        else
            $date_deliver = Az::l('Дата не указана');
        $static = [
            'docNumber' => $shopShippment->id ?? Az::l('Номер заказа не указан'),
            'courier' => $courier->name ?? Az::l('Курьер не указан'),
            'deliveryDate' => $date_deliver,
            'numProducts' => 0,
            'subTotal' => 0,
            'company' => Az::l('Не указан'),
        ];
        $date = new \DateTime();
        $ext = '.docx';
        $file_name = "routeListSh{$id}-{$date->format('Y-m-d-H-i-s')}.docx";
        $nameFile = '/binary/words/1C/Маршрутный_лист/';

        if (file_exists($nameFile . Az::$app->language . $ext))
            $file_open = Root . $nameFile . Az::$app->language . $ext;
        else $file_open = Root . $nameFile . 'ru' . $ext;

        $directory = Root . '/upload' . $this->file_path . "/";
        $file_close = $directory . $file_name . $ext;

        $subTotal = 0;
        $dinamic = [];
        $companies = [];

        $shopOrder = ShopOrder::find()
            ->where([
                'shop_shipment_id' => $shopShippment->id,
                /* 'status_logistics' => [
                     ShopOrder::status_logistics['courier_appointment'],
                     ShopOrder::status_logistics['reported'],
                 ],*/
                'parent' => null, // Child items are ignored
            ])
            ->orderBy('place_region_id')
            ->all();


        $i = count($shopOrder);
        $counter = 1;
        if ($shopOrder !== null) {

            $place_region_ids = [];
            foreach ($shopOrder as $index => $order) {
                $placeregion = $order->getPlaceRegionOne();
                if ($placeregion)
                    $place_region_ids[] = $placeregion->id;
            }

            $region_counter = count(array_unique($place_region_ids));
            $templateProcessor = new TemplateProcessor($file_open);

            $templateProcessor->setValue('temp', $region_counter);
            $templateProcessor->cloneBlock('bodyBlock', $region_counter, true, true);

            foreach ($shopOrder as $index => $order) {

                Az::$app->market->wares->changeStatusLogisticsTo($order->id, ShopOrder::status_logistics['reported']);

                $item = [];

                $userCompany = $order->getUserCompaniesFromUserCompanyIdsMulti();
                if ($userCompany)
                    foreach ($userCompany as $uc)
                        $companies[] = $uc['name'];

                $fullName = [$order->contact_name];
                $phone = [$order->contact_phone];

                $placeregion = $order->getPlaceRegionOne();
                $previos_placeregion = $placeregion->title;
                $placeaddress = $order->getPlaceAdressOne();
                if ($placeaddress->street && $placeaddress->home && $placeaddress->orientation)
                    $address = [$placeaddress->street . ', ' . $placeaddress->home . ', ' . $placeaddress->orientation];
                else
                    $address = ['Нет адреса'];

                $attachment = [];
                $shopelements_ids = $order->shop_element_ids;
                if ($shopelements_ids)
                    foreach ($shopelements_ids as $shopelement_id) {
                        $shopelements = ShopElement::findOne($shopelement_id);
                        if ($shopelements)
                            $attachment[] = $shopelements->name;
                    }

                $orderNumber = [$order->number];

                if ($order->date_deliver)
                    $date_order_deliver = [date('d.m.Y', strtotime($order->date_deliver))];
                else
                    $date_order_deliver = [Az::l('Дата не указана')];

                $time_delivery = [$order->time_deliver];
                $additionalPhone = [$order->add_contact_phone];
                $prepayment = [$order->prepayment];
                $totalamount = $order->price;

                $shoporderchildren = ShopOrder::findAll(['parent' => $order->id]);
                if ($shoporderchildren)
                    foreach ($shoporderchildren as $shoporderchild) {
                        $fullName[] = $shoporderchild->contact_name;
                        $phone[] = $shoporderchild->contact_phone;

                        $shopelements_ids = $shoporderchild->shop_element_ids;
                        if ($shopelements_ids)
                            foreach ($shopelements_ids as $shopelement_id) {
                                $shopelements = ShopElement::findOne($shopelement_id);
                                if ($shopelements)
                                    $attachment[] = $shopelements->name;
                            }

                        $placeaddress = $shoporderchild->getPlaceAdressOne();
                        if ($placeaddress->street && $placeaddress->home && $placeaddress->orientation)
                            $address[] = $placeaddress->street . ', ' . $placeaddress->home . ', ' . $placeaddress->orientation;
                        else
                            $address[] = "Нет адреса";

//                        $orderNumber[] = $shoporderchild->number;

                        if ($shoporderchild->date_deliver)
                            $date_order_deliver[] = date('d.m.Y', strtotime($shoporderchild->date_deliver));

                        $time_delivery[] = $shoporderchild->time_deliver;
                        $additionalPhone[] = $shoporderchild->add_contact_phone;
                        $prepayment[] = $shoporderchild->prepayment;
                        $totalamount += $shoporderchild->price;
                    }

                $fullName = implode(', ', array_unique($fullName));
                $phone = implode(', ', array_unique($phone));
                $address = implode(', ', array_unique($address));
                $attachment = implode(', ', array_unique($attachment));
                $orderNumber = implode(', ', array_unique($orderNumber));
                $date_order_deliver = implode(', ', array_unique($date_order_deliver));
                $time_delivery = implode(', ', array_unique($time_delivery));
                $additionalPhone = implode(', ', array_unique($additionalPhone));
                $prepayment = implode(', ', array_unique($prepayment));

                $item['fullName#' . $counter] = $fullName;
                $item['phone#' . $counter] = $phone;
                $item['address#' . $counter] = $address;
                $item['attachmentList#' . $counter] = $attachment;
                $item['orderNumber#' . $counter] = $orderNumber;
                $item['deliveryDate#' . $counter] = $date_order_deliver;
                $item['timeDelivery#' . $counter] = $time_delivery;
                $item['additionalPhone#' . $counter] = $additionalPhone;
                $item['prepayment#' . $counter] = $prepayment;
                $item['totalAmount#' . $counter] = $totalamount;
                $item['id#' . $counter] = $index + 1;
                $subTotal += $totalamount;
                $dinamic[] = $item;

                if ($placeregion->title !== $previos_placeregion) {

                    $region['placeRegion#' . $counter] = $placeregion->title ?? 'Компания не указана!';

                    $templateProcessor->setValues($region);
                    $templateProcessor->cloneRowAndSetValues('fullName#' . $counter, $dinamic);
                    $counter++;
                    $dinamic = [];
                }

            }
        }

        $cc = implode(", ", array_unique($companies));
        $static['numProducts'] = $i ?? 0;
        $static['subTotal'] = $subTotal ?? 0;
        $static['company'] = $cc ?? 'Компания не указана!';

        $templateProcessor->setValues($static);
//        $templateProcessor->cloneRowAndSetValues('fullName', $dinamic);

        if ($file_close) {
            $templateProcessor->saveAs($file_close);
        } else {
            unlink($file_close);
            $templateProcessor->saveAs($file_close);
        }
        $url = $this->file_path . '/' . $file_name;
        /*if (!$this->isCLI())
            $this->urlRedirect($url, false);*/

        return [
            'directory' => $directory,
            'file_name' => $file_name,
            'full_path' => $directory . $file_name . $ext
        ];

    }

    public function routeList($id)
    {

        //start|DavlatovRavshan|2020.10.10
        $shop_shipment = ShopShipment::findOne($id);

        if (!$shop_shipment)
            return false;

        $date = new \DateTime();
        $ext = '.docx';
        $file_name = "routeListSh{$id}-{$date->format('Y-m-d-H-i-s')}.docx";
        $nameFile = '/binary/words/1C/Маршрутный_лист/';

        $file_open = Root . $nameFile . 'ru' . $ext;
        if (file_exists($nameFile . Az::$app->language . $ext))
            $file_open = Root . $nameFile . Az::$app->language . $ext;

        $directory = Root . '/upload' . $this->file_path . '/';

        $file_close = $directory . $file_name . $ext;

        $shopOrders = ShopOrder::find()
            ->where([
                'shop_shipment_id' => $shop_shipment->id,
                'parent' => null,
            ])
            ->orderBy('place_region_id')
            ->all();

        $numProducts = count(ZArrayHelper::getColumn($shopOrders, 'id'));

        $indexes = [];
        $place_regions = [];
        foreach ($shopOrders as $index => $shopOrder) {

            $index++;

            $placeregion = $shopOrder->getPlaceRegionOne();
            if ($placeregion && !ZArrayHelper::isIn($placeregion->id, $place_regions))
                $place_regions[] = $placeregion->id;

            $indexes[$shopOrder->id] = $index;

        }

        $templateProcessor = new TemplateProcessor($file_open);
        $templateProcessor->cloneBlock('bodyBlock', count($place_regions), true, true);

        $static = [];
        $companies = [];
        $subTotal = 0;
        /** @var PlaceRegion $place_region */
        foreach ($place_regions as $key => $place_region) {

            $key++;

            $orders = ShopOrder::find()
                ->where([
                    'shop_shipment_id' => $shop_shipment->id,
                    'parent' => null,
                    'place_region_id' => $place_region
                ])
                ->orderBy('place_region_id')
                ->all();

            $count = count(ZArrayHelper::getColumn($orders, 'id'));

            $templateProcessor->cloneRow('fullName#' . $key, $count);
            /** @var ShopOrder $order */
            $indexkey = 1;
            foreach ($orders as $order) {

                $orderItems = $this->getOrderItems($order);

                /** @var ShopOrderItem $orderItem */

                $elements = [];
                $totalAmount = 0;
                foreach ($orderItems as $orderItem) {

                    $totalAmount += (int)$orderItem->price_all_partial;

                    if ($orderItem->check_return === true) {
                        continue;
                    }

                    $shop_catalog = ShopCatalog::findOne($orderItem->shop_catalog_id);

                    if ($shop_catalog) {
                        $elements[$shop_catalog->id] = $shop_catalog->title;
                    }

                    $company = UserCompany::findOne($orderItem->user_company_id);

                    if ($company) {
                        $companies[$orderItem->user_company_id] = $company->name;
                    }
                }
                $subTotal += $totalAmount;


                $place_adress = PlaceAdress::findOne($order->place_adress_id);
                $adress = 'Адрес не указан';
                if ($place_adress)
                    $adress = $place_adress->street . ', ' . $place_adress->home . ', ' . $place_adress->orientation;

                $ordersKey = "$key#$indexkey";

                $static["id#$ordersKey"] = $indexes[$order->id];
                $static["fullName#$ordersKey"] = $order->contact_name;
                $static["address#$ordersKey"] = $adress;
                $static["phone#$ordersKey"] = $order->contact_phone;
                $static["orderNumber#$ordersKey"] = $order->number;
                $static["attachmentList#$ordersKey"] = implode(',', $elements);
                $static["deliveryDate#$ordersKey"] = date('d-m-Y', strtotime($order->date_deliver));
                $static["timeDelivery#$ordersKey"] = $order->time_deliver;
                $static["prepayment#$ordersKey"] = $order->prepayment;
                $static["additionalPhone#$ordersKey"] = $order->add_contact_phone;
                $static["totalAmount#$ordersKey"] = $totalAmount;

                $indexkey++;
            }

            $region = PlaceRegion::findOne($place_region);
            $static['placeRegion#' . $key] = $region ? $region->title : 'Регион не указан';

        }

        $courier = ShopCourier::findOne($shop_shipment->shop_courier_id);

        $static['docNumber'] = $shop_shipment->id;
        $static['courier'] = $courier ? $courier->name : 'Курьер не указан';
        $static['company'] = implode(',', $companies);
        $static['deliveryDate'] = date('d-m-Y', strtotime($shop_shipment->date_deliver));
        $static['numProducts'] = $numProducts;
        $static['subTotal'] = $subTotal;

        $templateProcessor->setValues($static);

        if ($file_close) {
            $templateProcessor->saveAs($file_close);
        } else {
            unlink($file_close);
            $templateProcessor->saveAs($file_close);
        }

        //end | DavlatovRavshan | 10.10.2020
        return [
            'directory' => $directory,
            'file_name' => $file_name,
            'full_path' => $directory . $file_name . $ext
        ];

    }


    public function getOrderItems($order)
    {

        $orderItems = ShopOrderItem::find()
            ->where([
                'shop_order_id' => $order->id,
            ])
            ->all();

        if (!empty($order->children))
            foreach ($order->children as $child) {

                $orderItemsChild = ShopOrderItem::find()
                    ->where([
                        'shop_order_id' => $child,
                    ])
                    ->all();

                $orderItems = ZArrayHelper::merge($orderItems, (array)$orderItemsChild);

            }

        return $orderItems;

    }

    public function routeList5($id)
    {

        $shop_shipment = ShopShipment::findOne($id);

        if (!$shop_shipment)
            return false;

        $date = new \DateTime();
        $ext = '.docx';
        $file_name = "routeListSh{$id}-{$date->format('Y-m-d-H-i-s')}.docx";
        $nameFile = '/binary/words/1C/Маршрутный_лист/';

        $file_open = Root . $nameFile . 'ru' . $ext;
        if (file_exists($nameFile . Az::$app->language . $ext))
            $file_open = Root . $nameFile . Az::$app->language . $ext;

        $directory = Root . '/upload' . $this->file_path . '/';
        $file_close = $directory . $file_name . $ext;

        $shopOrders = ShopOrder::find()
            ->where([
                'shop_shipment_id' => $shop_shipment->id,
                'parent' => null,
            ])
            ->orderBy('place_region_id')
            ->all();

        $numProducts = count(ZArrayHelper::getColumn($shopOrders, 'id'));

        $indexes = [];
        $place_regions = [];
        foreach ($shopOrders as $index => $shopOrder) {

            $index++;

            $placeregion = $shopOrder->getPlaceRegionOne();
            if ($placeregion && !ZArrayHelper::isIn($placeregion->id, $place_regions))
                $place_regions[] = $placeregion->id;

            $indexes[$shopOrder->id] = $index;

        }

        $templateProcessor = new TemplateProcessor($file_open);
        $templateProcessor->cloneBlock('bodyBlock', count($place_regions), true, true);

        $static = [];
        $companies = [];
        $subTotal = 0;
        /** @var PlaceRegion $place_region */
        foreach ($place_regions as $key => $place_region) {

            $key++;

            $orders = ShopOrder::find()
                ->where([
                    'shop_shipment_id' => $shop_shipment->id,
                    'parent' => null,
                    'place_region_id' => $place_region
                ])
                ->orderBy('place_region_id')
                ->all();

            $count = count(ZArrayHelper::getColumn($orders, 'id'));

            $templateProcessor->cloneRow('fullName#' . $key, $count);
            /** @var ShopOrder $order */
            foreach ($orders as $indexkey => $order) {

                $indexkey++;

                $childs = ShopOrder::findAll([
                    'parent' => $order->id
                ]);

                $allOrders = ZArrayHelper::merge([$order], (array)$childs);


                foreach ($allOrders as $allOrder) {

                    $orderItems = ShopOrderItem::find()
                        ->where([
                            'shop_order_id' => $allOrder->id
                        ])
                        ->all();

                }


                if (!empty($order->children))
                    foreach ($order->children as $child) {

                        $orderItemsChild = ShopOrderItem::find()
                            ->where([
                                'shop_order_id' => $child
                            ])
                            ->all();

                        $orderItems = ZArrayHelper::merge($orderItems, (array)$orderItemsChild);
                    }

                /** @var ShopOrderItem $orderItem */

                $elements = [];
                $totalAmount = 0;
                foreach ($orderItems as $orderItem) {
                    /*
                                        if ($orderItem->check_return === true)
                                            continue;*/

                    $totalAmount += (int)$orderItem->price_all_partial;

                    $shop_catalog = ShopCatalog::findOne($orderItem->shop_catalog_id);
                    if ($shop_catalog)
                        $elements[$shop_catalog->title] = $shop_catalog->title;

                    $company = UserCompany::findOne($orderItem->user_company_id);
                    if ($company)
                        $companies[$orderItem->user_company_id] = $company->name;

                }

                $subTotal += $totalAmount;

                $place_adress = PlaceAdress::findOne($order->place_adress_id);
                $adress = 'Адрес не указан';
                if ($place_adress)
                    $adress = $place_adress->street . ', ' . $place_adress->home . ', ' . $place_adress->orientation;

                $ordersKey = "$key#$indexkey";

                $static["id#$ordersKey"] = $indexes[$order->id];
                $static["fullName#$ordersKey"] = $order->contact_name;
                $static["address#$ordersKey"] = $adress;
                $static["phone#$ordersKey"] = $order->contact_phone;
                $static["orderNumber#$ordersKey"] = $order->number;
                $static["attachmentList#$ordersKey"] = implode(',', $elements);
                $static["deliveryDate#$ordersKey"] = date('d-m-Y', strtotime($order->date_deliver));
                $static["timeDelivery#$ordersKey"] = $order->time_deliver;
                $static["prepayment#$ordersKey"] = $order->prepayment;
                $static["additionalPhone#$ordersKey"] = $order->add_contact_phone;
                $static["totalAmount#$ordersKey"] = $totalAmount;

            }

            $region = PlaceRegion::findOne($place_region);
            $static['placeRegion#' . $key] = $region ? $region->title : 'Регион не указан';

        }

        $courier = ShopCourier::findOne($shop_shipment->shop_courier_id);

        $static['docNumber'] = $shop_shipment->id;
        $static['courier'] = $courier ? $courier->name : 'Курьер не указан';
        $static['company'] = implode(',', $companies);
        $static['deliveryDate'] = date('d-m-Y', strtotime($shop_shipment->date_deliver));
        $static['numProducts'] = $numProducts;
        $static['subTotal'] = $subTotal;

        $templateProcessor->setValues($static);

        if ($file_close) {
            $templateProcessor->saveAs($file_close);
        } else {
            unlink($file_close);
            $templateProcessor->saveAs($file_close);
        }

        return [
            'directory' => $directory,
            'file_name' => $file_name,
            'full_path' => $directory . $file_name . $ext
        ];

    }

    public function routeList2($id)
    {

        $shopShippment = ShopShipment::findOne($id);

        $courier = ShopCourier::findOne([
            'id' => $shopShippment->shop_courier_id
        ]);

        $date_deliver = 'Дата не указана';
        if ($shopShippment->date_deliver)
            $date_deliver = date('d.m.Y', strtotime($shopShippment->date_deliver));

        $date = new \DateTime();
        $ext = '.docx';
        $file_name = "routeListSh{$id}-{$date->format('Y-m-d-H-i-s')}.docx";
        $nameFile = '/binary/words/1C/Маршрутный_лист/';

        if (file_exists($nameFile . Az::$app->language . $ext))
            $file_open = Root . $nameFile . Az::$app->language . $ext;
        else $file_open = Root . $nameFile . 'ru' . $ext;

        $directory = Root . '/upload' . $this->file_path . "/";
        $file_close = $directory . $file_name . $ext;

        $subTotal = 0;
        $dinamic = [];
        $companies = [];

        $shopOrder = ShopOrder::findAll([
            'shop_shipment_id' => $shopShippment->id,
            /*'status_logistics' => [
                ShopOrder::status_logistics['courier_appointment'],
                ShopOrder::status_logistics['reported'],
            ],*/
        ]);

        $i = count($shopOrder);
        if ($shopOrder !== null) {
            foreach ($shopOrder as $index => $order) {

                Az::$app->market->wares->changeStatusLogisticsTo($order->id, ShopOrder::status_logistics['reported']);

                $item = [];

                $placeaddress = $order->getPlaceAdressOne();

                $item['address'] = $placeaddress->name;

                $shopelements_ids = $order->shop_element_ids;
                if ($shopelements_ids) {
                    $string = 'Нет элементов';
                    foreach ($shopelements_ids as $key => $shopelement_id) {
                        $shopelements = ShopElement::findOne($shopelement_id);
                        if ($shopelements) {
                            if ($key < 1)
                                $string = $shopelements->name;
                            else
                                $string .= ', ' . $shopelements->name;
                        }
                    }
                } else
                    $string = 'Нет элементов';

                $item['attachmentList'] = $string;

                $userCompany = $order->getUserCompaniesFromUserCompanyIdsMulti();
                if ($userCompany)
                    foreach ($userCompany as $uc)
                        $companies[] = $uc['name'];

                if ($order->date_deliver)
                    $date_order_deliver = date('d.m.Y', strtotime($order->date_deliver));
                else
                    $date_order_deliver = Az::l('Дата не указана');

                $item['fullName'] = $order->contact_name;
                $item['phone'] = $order->contact_phone;
                $item['totalAmount'] = $order->price;
                $item['orderNumber'] = $order->number;
                $item['commentAgent'] = $order->comment_agent;
                $item['deliveryDate'] = $date_order_deliver;
                $item['additionalPhone'] = $order->add_contact_phone;
                $item['prePayment'] = $order->prepayment;
                $item['id'] = $index + 1;
                $subTotal += $order->price;
                $dinamic[] = $item;

            }
        }

        $cc = implode(', ', $companies);

        $static = [
            'docNumber' => $shopShippment->id,
            'courier' => $courier->name ?? Az::l('Курьер не указан'),
            'deliveryDate' => $date_deliver,
            'company' => Az::l('Не указан'),
            'numProducts' => $i ?? 0,
            'subTotal' => $subTotal ?? 0,
            //'company' => $cc ?? 'Компания не указана!',
            //'company' => Az::l('Не указан'),
        ];

//        }
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

        return [
            'directory' => $directory,
            'file_name' => $file_name,
            'full_path' => $directory . $file_name . $ext
        ];

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
    public function banderol($id)
    {
        //start|DavlatovRavshan|2020.10.10
        $info = ShopOrder::findOne($id);
        $childShopOrder = ShopOrder::findAll(['parent' => $info->id]);

        $total_price = $info->price;
        $name = $info->contact_name;

        $address = "Нет адреса";
        if ($info->place_adress_id) {
            $placeaddress = PlaceAdress:: findOne($info->place_adress_id);
            if ($placeaddress && !empty($placeaddress->place))
                $address = $placeaddress->place;
        }

        foreach ($childShopOrder as $key => $item) {
            $total_price += $item->price;
            $name .= ', ' . $item->contact_name;

            $address_child = "Нет адреса";
            if ($item->place_adress_id) {
                $placeaddress = PlaceAdress:: findOne($item->place_adress_id);
                if ($placeaddress && !empty($placeaddress->place))
                    $address_child = $placeaddress->place;
            }

            $address .= ', ' . $address_child;
        }


        $template = [
            'banderolNumber' => $info->number,
            'sellerName' => $info->contact_name ?? Az::l('Нет имени продавца'),
            'sellerAddress' => $info->place_adress_id ?? Az::l('Нет адреса'),
            'totalCash' => $total_price,
            'index' => Az::l('Нет индекса компании'),
            'deliveryDate' => date('d.m.Y', strtotime($info->date_deliver)),
            'orderName' => $name,
            'orderAddress' => $address,
            'orderCity' => Az::l('Нет информации о регионе'),
            'orderStreet' => Az::l('Нет информации об улице'),
            'regnum' => '____',

        ];

        Az::$app->market->generatorBarCodes->generateBarcodeJpg($id);

        $date = new \DateTime();

        $ext = '.docx';
        $file_name = "banderol{$id}.{$date->format('Y-m-d-H-i-s')}";
        $fileJpg = Root . '/upload/imagez/image' . $id . '.jpg';

        $nameFile = '/binary/words/1C/Бандероль/';
        if (file_exists($nameFile . Az::$app->language . $ext))
            $file_open = Root . $nameFile . Az::$app->language . $ext;
        else $file_open = Root . $nameFile . 'ru' . $ext;

        $directory = Root . '/upload' . $this->file_path . "/";
        $file_close = $directory . $file_name . $ext;

        if ((int)$info->user_company_ids > 0) {
            $companyInfo = UserCompany::findOne($info->user_company_ids);

            if ($companyInfo !== null) {
                $template['sellerName'] = $companyInfo->name;
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

        //end | DavlatovRavshan | 10.10.2020
        return [
            'directory' => $directory,
            'file_name' => $file_name,
            'full_path' => $directory . $file_name . $ext
        ];
    }

    public function getOrderPrice($order_id)
    {

        $orderItems = ShopOrderItem::findAll([
            'shop_order_id' => $order_id
        ]);

        $total = 0;
        foreach ($orderItems as $orderItem) {
            $total += (int)$orderItem->price_all;
        }

        return $total;

    }

    public function banderol2($id)
    {

        $order = ShopOrder::findOne($id);
        $childs = ShopOrder::find()
            ->where(['parent' => $order->id])
            ->all();

        if (!$order)
            return null;

        $total_price = $this->getOrderPrice($id);
        foreach ($childs as $child) {
            $total_price += (int)$child->price;
        }

        $place_adress = PlaceAdress::findOne($order->place_adress_id);
        if (!$place_adress)
            $place_region = null;
        else
            $place_region = PlaceRegion::findOne($place_adress->place_region_id);

        $adress = 'Адрес не указан';
        $street = 'Улица не указана';
        if ($place_adress) {
            $adress = $place_adress->street . ', ' . $place_adress->home . ', ' . $place_adress->orientation;
            $street = $place_adress->street;
        }

        $city = 'Город не указан';
        if ($place_region) {
            $city = $place_region->name;
        }

        $companies = [];
        if (!empty($order->user_company_ids)) {
            $companies = UserCompany::findAll([
                'id' => $order->user_company_ids,
            ]);
        }
        if ($order->created_at === null)
            $dynamic['dateDyna#1'] = '{Дата не указана!}';
        else
            $dynamic['dateDyna#1'] = date('d.m.Y', strtotime($order->date_deliver));

        $company = '';
        $sellerAdress = '';
        $index = '';
        if (!empty($companies)) {

            /** @var UserCompany $company */
            foreach ($companies as $comp) {

                $company .= strtr($this->layout['companies'], [
                    '{company}' => $comp->name,
                    '{phone}' => $comp->phone,
                ]);

                $place = PlaceAdress::findOne($comp->place_adress_id);

                if (!empty($comp->index))
                    $index .= $comp->index;

                if ($place)
                    $sellerAdress .= "$place->place, ";
            }
        }

        if (empty($company))
            $company = 'Не указано';

        if (empty($index))
            $index = 'Не указано';

        if (empty($sellerAdress))
            $sellerAdress = 'Не указано';

        $id = (string)$order->number;
        $bn = $order->code;

        if (empty($bn))
            $bn = '_____';
        if ($order->created_at === null)
            $dynamic['dateDyna#' . $index] = '{Дата не указана!}';
        else
            $dynamic['dateDyna#' . $index] = date('d.m.Y', strtotime($order->date_deliver));

        $template = [
            'banderolNumber' => $id,
            'sellerName' => $company,
            'sellerAddress' => $sellerAdress,
            'deliveryCash' => $order->deliver_price,
            //'price' => $order->price,
            'totalCash' => $total_price,
            'weight' => $order->weight,
            'index' => $index,
            'deliveryDate' => date('d.m.Y', strtotime($order->date_deliver)),
            'orderName' => $order->contact_name,
            'orderAddress' => $adress,
            'orderCity' => $city,
            'orderStreet' => $street,
            'regnum' => $bn,
        ];

        Az::$app->market->generatorBarCodes->generateBarcodeJpg($bn);

        $date = new \DateTime();

        $ext = '.docx';
        $file_name = "banderol{$bn}.{$date->format('Y-m-d-H-i-s')}";
        $fileJpg = Root . '/upload/imagez/image' . $bn . '.jpg';

        $nameFile = '/binary/words/1C/Бандероль/';
        if (file_exists($nameFile . Az::$app->language . $ext))
            $file_open = Root . $nameFile . Az::$app->language . $ext;
        else $file_open = Root . $nameFile . 'ru' . $ext;

        $directory = Root . '/upload' . $this->file_path . '/';
        $file_close = $directory . $file_name . $ext;

        $bn = $order->code;
        $template['regnum'] = $bn;

        $templateProcessor = new TemplateProcessor($file_open);
        $templateProcessor->setValues($template);
        $templateProcessor->setImageValue('barcode', $fileJpg);
        $templateProcessor->saveAs($file_close);
        if ($this->execute)

            shell_exec($file_close);

        return [
            'directory' => $directory,
            'file_name' => $file_name,
            'full_path' => $directory . $file_name . $ext
        ];
    }

    public function banderol3($id)
    {
        //start|DavlatovRavshan|2020.10.10
        $shopOrder = ShopOrder::findOne($id);

        if (!$shopOrder)
            return null;

        $childs = ShopOrder::find()
            ->where([
                'parent' => $shopOrder->id
            ])
            ->all();

        $date = new \DateTime();

        $bn = $shopOrder->code;

        $ext = '.docx';
        $file_name = "banderol{$bn}.{$date->format('Y-m-d-H-i-s')}";
        $fileJpg = Root . '/upload/imagez/image' . $bn . '.jpg';

        $nameFile = '/binary/words/1C/Бандероль/';
        if (file_exists($nameFile . Az::$app->language . $ext))
            $file_open = Root . $nameFile . Az::$app->language . $ext;
        else
            $file_open = Root . $nameFile . 'ru' . $ext;

        $directory = Root . '/upload' . $this->file_path . '/';
        $file_close = $directory . $file_name . $ext;

        $allOrders = ZArrayHelper::merge([$shopOrder], $childs);

        Az::$app->market->generatorBarCodes->generateBarcodeJpg($bn);

        $templateProcessor = new TemplateProcessor($file_open);

        $templateProcessor->setImageValue('barcode', $fileJpg);

        $static = [];
        $sellerAddress = [];
        $indexes = [];
        $sellerName = [];
        $orderAddress = [];
        $orderCity = [];
        $orderName = [];
        $totalCash = 0;

        /** @var ShopOrder $order */
        foreach ($allOrders as $order) {

            $userCompany = null;
            if (!empty($order->user_company_ids))
                $userCompany = UserCompany::findAll($order->user_company_ids);

            $placeAdress = null;
            if (!empty($order->place_adress_id))
                $placeAdress = PlaceAdress::findOne($order->place_adress_id);

            $placeRegion = null;
            if ($placeAdress) {
                $placeRegion = PlaceRegion::findOne($placeAdress->place_region_id);
                $place = $placeAdress->street . ', ' . $placeAdress->home . ', ' . $placeAdress->orientation;
                $orderAddress[$place] = $place;
            }

            if ($placeRegion)
                $orderCity[$placeRegion->name] = $placeRegion->name;

            $orderName[$order->contact_name] = $order->contact_name;

            /** @var UserCompany $company */
            if (!empty($userCompany)) {
                foreach ($userCompany as $company) {

                    $phoneName = $company->name . ' ' . $company->phone;
                    $sellerName[$phoneName] = $phoneName;
                    $indexes[$company->index] = $company->index;

                    $adress = PlaceAdress::findOne($company->place_adress_id);
                    if ($adress) {
                        $sellerAddress[$adress->place] = $adress->place;
                    }

                }
            }

            $orderItems = ShopOrderItem::findAll([
                'shop_order_id' => $order->id,
                'check_return' => [
                    false,
                    null
                ]
            ]);

            $totalSum = 0;
            foreach ($orderItems as $orderItem) {
                $totalSum += (int)$orderItem->price_all;
            }

            $totalCash += $totalSum;

        }

        $static['regnum'] = $bn;
        $static['banderolNumber'] = $shopOrder->number;
        $static['sellerName'] = implode(' | ', $sellerName);
        $static['sellerAddress'] = implode(' | ', $sellerAddress);
        $static['orderName'] = implode(' | ', $orderName);
        $static['orderAddress'] = implode(' | ', $orderAddress);
        $static['orderCity'] = implode(' | ', $orderCity);
        $static['index'] = implode(' | ', $indexes);
        $static['deliveryDate'] = date('d.m.Y', strtotime($shopOrder->date_deliver));
        $static['totalCash'] = $totalCash;

        $templateProcessor->setValues($static);

        $templateProcessor->saveAs($file_close);
        if ($this->execute)
            shell_exec($file_close);
        //end | DavlatovRavshan | 10.10.2020
        return [
            'directory' => $directory,
            'file_name' => $file_name,
            'full_path' => $directory . $file_name . $ext
        ];
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
        return $this->universalDocument($ids, 'multiBanderol');
//        $urls = [];
//        if (is_array($ids))
//            foreach ($ids as $id) {
//                $urls[] = str_replace('/', '\\', $this->banderol($id));
//            }
//        else
//            $urls[] = str_replace('/', '\\', $this->banderol($ids));
//
//        $merged_file = Az::$app->office->mergeFiles->mergeDocsFromArray($urls) . '.docx';
//
//        $mf = new MergeFiles();
//
//        #tegilmasin************
//        if ($mf->is_pdf)
//            $to_pdf = Az::$app->office->docto->docPdf($merged_file);
//        else
//            $to_pdf = $merged_file;
//        #tegilmasin************
//        /* vdd($to_pdf);*/
//        $to_pdf_arr = explode('\\', $to_pdf);
//        $to_pdf_arr = array_filter($to_pdf_arr);
//        $size = count($to_pdf_arr);
//        $url_pdf = '/' . $to_pdf_arr[$size - 3] . '/' . $to_pdf_arr[$size - 2] . '/' . $to_pdf_arr[$size - 1];
//
//        return $url_pdf;
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

        $placeInfo = null;
        $userInfo = null;

        $staticWord = [

            'name' => Az::l('Не указан'),
            'address' => Az::l('Не указан'),
            'phone' => Az::l('Не указан'),
            'amount' => Az::l('Не указан'),
            'date' => Az::l('Не указан'),
        ];

        $date = new \DateTime();
        $ext = '.docx';
        $file_name = "cashReturnSh{$id}{$date->format('Y-m-d-H-i-s')}" . $ext;
        $nameFile = '/binary/words/1C/Заявление_на_возврат_ДС/';

        if (file_exists($nameFile . Az::$app->language . '.docx'))
            $file_open = Root . $nameFile . Az::$app->language . '.docx';
        else $file_open = Root . $nameFile . 'ru.docx';

        $directory = Root . '/upload' . $this->file_path . "/";
        $file_close = $directory . $file_name . $ext;

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

        return [
            'directory' => $directory,
            'file_name' => $file_name,
            'full_path' => $directory . $file_name . $ext
        ];
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


    /**
     * @param $id
     * @return string[]|null
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     * @author Umid Muminov
     */

    public function returnFromClient($id)
    {

        //start|DavlatovRavshan|2020.10.10
        $ware_return = WareReturn::findOne($id);

        $shop_orders = ShopOrder::findAll($ware_return->shop_order_ids);
        $count = ShopOrder::find()
            ->where([
                'id' => $ware_return->shop_order_ids
            ])
            ->count();

        $date = new \DateTime();
        $ext = '.docx';
        $file_name = "productReturnSh{$id}{$date->format('Y-m-d-H-i-s')}" . $ext;
        $nameFile = '/binary/words/1C/Возврат_от_клиента/';

        if (file_exists($nameFile . Az::$app->language . $ext)) {
            $file = Root . $nameFile . Az::$app->language . '.docx';
        } else {
            $file = Root . $nameFile . 'ru.docx';
        }

        $directory = Root . '/upload' . $this->file_path . '/';
        $file_close = $directory . $file_name . $ext;

        $templateProcessor = new TemplateProcessor($file);
        $templateProcessor->cloneRow('shopOrderName', $count);

        $staticWord = [];

        $total_price = 0;
        $all_amount = 0;

        $courierName = 'Курьер не указан';

        foreach ($shop_orders as $key => $shop_order) {

            $key++;

            $shop_order_items = ShopOrderItem::findAll([
                'shop_order_id' => $shop_order->id
            ]);

            $price = 0;
            $amount = 0;
            foreach ($shop_order_items as $shop_order_item) {
                $amount += (int)$shop_order_item->amount_return;
                $price += (int)$shop_order_item->price_all_return;
            }

            $courier = ShopCourier::findOne($ware_return->shop_courier_id);

            if ($courier)
                $courierName = $courier->name;

            $staticWord['i#' . $key] = $key;
            $staticWord['shopOrderName#' . $key] = $shop_order->name;
            $staticWord['amount#' . $key] = $amount;
            $staticWord['price#' . $key] = $price;

            $total_price += $price;
            $all_amount += $amount;

        }

        $staticWord['courier'] = $courierName;
        $staticWord['wareReturnName'] = $ware_return->name;
        $staticWord['goodsQuantity'] = $all_amount;
        $staticWord['totalPrice'] = $total_price;

        $templateProcessor->setValues($staticWord);
        $templateProcessor->saveAs($file_close);

        if ($this->execute)
            shell_exec($file_close);
        if (!$this->isCLI()) {
            $url = $this->file_path . '/' . $file_name;
        }

        //end | DavlatovRavshan | 10.10.2020
        return [
            'directory' => $directory,
            'file_name' => $file_name,
            'full_path' => $directory . $file_name . $ext
        ];

        // todo: end

    }

}
