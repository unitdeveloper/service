<?php


namespace zetsoft\service\office;


use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;
use zetsoft\models\page\PageAction;
use zetsoft\models\App\eyuf\EyufScholar;
use zetsoft\models\place\PlaceAdress;
use zetsoft\models\place\PlaceRegion;
use zetsoft\models\shop\ShopCourier;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\shop\ShopShipment;
use zetsoft\models\user\User;
use zetsoft\models\user\UserCompany;
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
class WordShop extends ZFrame
{
    public $phpword;
    public $project_name;
    public $section;

    public function testy(){
        $test = $this->test(213);
    }

    public function test($id)
    {

        $shopOrder = ShopOrder::findOne($id);
        $shopOrderItem = ShopOrderItem::find()->where(
            ['shop_order_id'=> $id]
        )->all();
        $companyId = $shopOrder->user_company_id;
        $companyInfo = UserCompany::findOne($shopOrder->user_company_id);
        $placeInfo = PlaceAdress::findOne($shopOrder->place_adress_id);
        $sellerAdr= PlaceAdress::findOne($companyInfo->place_adress_id);
        $dynamic = [];
        $staticWord = [
            'orderName'     => $shopOrder->contact_name,
            'orderNumber'  => $shopOrder->id,
            'date'  => $shopOrder->created_at,
            'sellerName'  => $companyInfo->name,
            'sellerAddress'  => $sellerAdr->place,
            'registerNumber'  => '000009652468',
            'orderAddress'  => $placeInfo->place,
            'serviceCharge' => $shopOrder->price,
            'totalMoney' => $shopOrder->total_price,
        ];

       foreach ($shopOrderItem as $item){
           $items=[];
           $items['productName'] = $item['name'];
           $items['quanty'] = $item['amount'];
           $items['money'] = $item['price'];
           $items['productMoney'] = $item['price_all'];
           array_push($dynamic, $items);

       }


        $templateProcessor = new TemplateProcessor(Root . '/binary/words/1C/contractTemplate.docx');
        $templateProcessor->setValues($staticWord);
        $templateProcessor->cloneRowAndSetValues('productName',$dynamic);

     // $templateProcessor->setValues($word);
        $el = $templateProcessor->save();
        $newFile = IOFactory::load($el);
        $objWriter = IOFactory::createWriter($newFile, 'Word2007');

        $objWriter->save(Root . '/service/office/other/contractTest.docx');

    }
    public function generateCashReceipt()
    {
        $templateProcessor = new TemplateProcessor(Root . '/binary/words/1C/CashReceiptTemplate.docx');

        $templateProcessor->setValue('company', 'ЧП Турдиев Б.Э.');

        $el = $templateProcessor->save();
        $newFile = IOFactory::load($el);
        $objWriter = IOFactory::createWriter($newFile, 'Word2007');

        $objWriter->save(Root . '/binary/words/1C/cashreceipts/CashReceipt.docx');
    }
    public function cashTemplate($id)
    {

        $shopOrder = ShopOrder::findOne($id);
        $shopOrderItem = ShopOrderItem::find()->where(
            ['shop_order_id'=> $id]
        )->all();
        $companyId = $shopOrder->user_company_id;
        $companyInfo = UserCompany::findOne($shopOrder->user_company_id);
        $placeInfo = PlaceAdress::findOne($shopOrder->place_adress_id);
        $userInfo = User::findOne($shopOrder->user_id);
        $userCompanyInfo = User::findOne($userInfo->user_company_id);
        $sellerAdr= PlaceAdress::findOne($companyInfo->place_adress_id);

        $dynamic = [];
        $staticWord = [
            'untilDate'     => $shopOrder->date_deliver,
            'orderName'  => $userCompanyInfo->name,
            'date'  => $shopOrder->created_at,
            'bankName'  => $companyInfo->bank,
            'bankAccount'  => $companyInfo->bank_account,
            'bankAddress'  => $companyInfo->bank_address,
            'orderID'=> $shopOrder->id,
            'registerNumber'  => '000009652468',
            'orderAddress'  => $companyInfo->bank_address,
            'sellerName' => $companyInfo->name,
            'sellerPhone' => $companyInfo->phone,
            'sellerAddress' => $sellerAdr->name,
        ];

        foreach ($shopOrderItem as $item){

            for ($i = 0; $i <= count($shopOrderItem); $i++){
                $items=[];
                $items ['id'] = $i;
                $items['orderTitle'] = $item['name'];
                $items['amount'] = $item['amount'];
                $items['price'] = $item['price'];
                $items['price_amount'] = $item['price_all'];
                array_push($dynamic, $items);
            }


        }


        $templateProcessor = new TemplateProcessor(Root . '/binary/words/1C/cashTemplate.docx');
        $templateProcessor->setValues($staticWord);
        $templateProcessor->cloneRowAndSetValues('orderTitle',$dynamic);

        // $templateProcessor->setValues($word);
        $el = $templateProcessor->save();
        $newFile = IOFactory::load($el);
        $objWriter = IOFactory::createWriter($newFile, 'Word2007');

        $objWriter->save(Root . "/service/office/other/cashTest{$id}.docx");

    }
    public function bandtest(){
        $tn = $this->banderol(213);
    }
    public function banderol($id)
    {
        $info = ShopOrder::findOne($id);

        $companyInfo = UserCompany::findOne($info->user_company_id);
        $placeInfo = PlaceAdress::findOne($info->place_adress_id);
        $sellerAdr= PlaceAdress::findOne($companyInfo->place_adress_id);
        $userInfo = User::findOne($info->user_id);
        $regionInfo = PlaceRegion::findOne($placeInfo->place_region_id);
        $id = (string) $info->id;
        $bn = '000000000000';
        $bn = substr_replace($bn, $id, strlen($bn) - strlen($id), strlen($id));

        $staticWord = [
            'banderolNumber'     => $bn,
            'sellerName'     => $companyInfo->name,
            'sellerPhoneNumber'     => $companyInfo->phone,
            'sellerAddress'     => $sellerAdr->place,
            'deliveryCash'     => $info->deliver_price,
            'price'     => $info->price,
            'totalCash'     => $info->total_price,
            'weight'     => $info->weight,
            'deliveryDate'     => $info->date_deliver,
            'orderName'     => $userInfo->name,
            'index'     => '00124',
            'orderAddress'     => $placeInfo->place,
            'orderCity'     => $regionInfo->name,
            'orderStreet'     => $placeInfo->street,

        ];

        $templateProcessor = new TemplateProcessor(Root . '/binary/words/1C/banderolTemplate.docx');
        $templateProcessor->setValues($staticWord);

        // $templateProcessor->setValues($word);
        $el = $templateProcessor->save();
        $newFile = IOFactory::load($el);
        $objWriter = IOFactory::createWriter($newFile, 'Word2007');

        $objWriter->save(Root . '/service/office/other/banderolTest1.docx');

    }
    public function testGenerateBarcode()
    {
        $data = $this->generateBarcode('02022654213');
        vd($data);
    }
    public function generateRouteSheet($id)
    {

        $shopInfo = ShopOrder::findOne($id);
        $shopItem = ShopOrderItem::find()->where(
            ['shop_order_id'=> $id]
        )->all();
        $shopShippment = ShopShipment::findOne($shopInfo->shop_shipment_id);
        $couriers = ShopCourier::findOne($shopShippment->shop_courier_id);
        $companyInfo = UserCompany::findOne($shopInfo->user_company_id);
        $userInfo = User::findOne($shopInfo->user_id);
        $userAddress = PlaceAdress::findOne($shopInfo->place_adress_id);
        $static = [
            'docNumber' => $shopInfo->id,
            'courier' => $couriers->name,
            'deliveryDate' => $shopInfo->date_deliver,
            'company' => $companyInfo->name,
            'subTotal' => $shopInfo->price,
            'numProducts' => count($shopItem),
            'total' => $shopInfo->total_price,
            'fullName' => $userInfo->name,
            'courierId' => $couriers->id,
            'shipmentDate' => '26.06.2020',
            'shipmentDate2' => '26.06.2020'
        ];

        $templateProcessor = new TemplateProcessor(Root . '/binary/words/1C/shipment/RouteSheetTemplate.docx');

        // Static starts



        $templateProcessor->setValues($static);

        // Static ends

        // Dinamic starts

        $dinamic = [];

        foreach ($couriers as $courier) {
            $item = [];
            $item['phone'] = $companyInfo->phone;
            $item['totalAmount'] = $shopInfo->total_price;
            $item['attachmentList'] = 'Гипертофорт';
            $item['orderNumber'] = $shopInfo->id;
            $item['deliveryDate'] = $shopInfo->date_deliver;
            $item['additionalPhone'] = '';
            $item['prePayment'] = $shopShippment->prepayment;
            $item['address'] = $userAddress->name;
            array_push($dinamic, $item);
        }

        $templateProcessor->cloneRowAndSetValues('courierId', $dinamic);

        // Dinamic ends

        $el = $templateProcessor->save();
        $newFile = IOFactory::load($el);
        $objWriter = IOFactory::createWriter($newFile, 'Word2007');

        $objWriter->save(Root . '/binary/words/1C/routesheets/RouteSheetTest.docx');
    }

    public function generateBarcode($code)
    {
        // Generate PNG barcode starts

        $barcodeobj = new \TCPDFBarcode("$code", 'MSI');

        $barcode = $barcodeobj->getBarcodePNG(300, 200, array(0,0,0));

        // Generate PNG barcode ends

        $templateProcessor = new TemplateProcessor(Root . '/binary/words/1C/banderolTemplate.docx');

        $templateProcessor->setImageValue('barcode', $barcode);

        $el = $templateProcessor->save();
        $newFile = IOFactory::load($el);
        $objWriter = IOFactory::createWriter($newFile, 'Word2007');

        $objWriter->save(Root . '/binary/words/1C/invoices/BarcodeTest.docx');

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
}

