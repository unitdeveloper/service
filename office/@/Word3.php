<?php


namespace zetsoft\service\office;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;
use zetsoft\models\place\PlaceAdress;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopCourier;
use zetsoft\models\shop\ShopElement;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\shop\ShopProduct;
use zetsoft\models\shop\ShopShipment;
use zetsoft\models\user\User;
use zetsoft\models\user\UserCompany;
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
class Word3 extends ZFrame
{

    public function testGenerateInvoice()
    {
        $this->generateInvoice(213);
    }

    public function generateInvoice($id)
    {
        // 1) user_company
        // 2) shop_order
        // 3) shop_order_item
        // 4) user_id(name)
        $file_open = Root . '/binary/words/1C/Акт_передачи/template.docx';
        $file_close = Root . "/service/office/other/invoice{$id}.docx";

        $shopOrder = ShopOrder::findOne($id);
        $shopShipment = ShopShipment::findOne($shopOrder->shop_shipment_id);
        $courierInfo = ShopCourier::findOne($shopShipment->shop_courier_id);
        $companyInfo = UserCompany::findOne($shopOrder->user_company_id);
        $orderItem = ShopOrderItem::find()->where(
            ['shop_order_id' => $id]
        )->all();
        $sellerAdr = PlaceAdress::findOne($companyInfo->place_adress_id);
        $staticWord = [
            'company' => $companyInfo->name,
            'companyAddress' => $sellerAdr->name,
            'companyPhone' => $companyInfo->phone,
            'orderid' => $shopShipment->id,
            'createDate' => $shopOrder->created_at,
            'courier' => $courierInfo->name,
            'totalPrice' => $shopOrder->total_price
        ];
        $dynamic = [];
        $i = 1;
        foreach ($orderItem as $item) {
            $items = [];
            $items['productName'] = $item['name'];
            $items['amount'] = $item['amount'];
            $items['price'] = $item['price'];
            $items['order'] = $item['id'];
            $items['productMoney'] = $item['price_all'];
            $items['id'] = $i;
            $i++;
            array_push($dynamic, $items);

        }
        $templateProcessor = new TemplateProcessor($file_open);
        $templateProcessor->setValues($staticWord);
        $templateProcessor->cloneRowAndSetValues('productName', $dynamic);

        $templateProcessor->saveAs($file_close);

        shell_exec($file_close);
    }

    public function testGenerateReturnGoods()
    {
        $data = $this->generateReturnGoods();
        vd($data);
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

    public function testGenerateCashReceipt()
    {
        $data = $this->generateCashReceipt();
        vd($data);
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

    public function testGenerateForm114()
    {
        $data = $this->generateForm114();
        vd($data);
    }

    public function generateForm114()
    {
        $templateProcessor = new TemplateProcessor(Root . '/binary/words/1C/Form114Template.docx');

        $data = [
            'clientFullName' => 'Нормуродов Эркин',
            'clientAddress' => 'ТОШКЕНТ ШАҲРИ ТОШКЕНТ ШАҲРИ ТОШКЕНТ ШАҲРИ г.Ташкент Бектимирский р-н о-р Куйлюк базар',
            'clientPhone' => '912624259',
            'companyName' => 'ЧП Турдиев Б.Э',
            'companyAddress' => 'г.Ташкент Яккасарайский р-н, ул.Фархадская 6а',
            'companyPhone' => '+998911648454',
            'amount' => '495,000',
            'passportSerial' => 'АА',
            'passportId' => '5503581',
            'passportGivenDate' => '23.05.2014',
            'passportIssueDate' => '22.05.2024',
            'passportIssuedBy' => 'Тошкент шах. Миробод тум ИИБ',
            'passportAddress' => 'г.Ташкент Миробадский р-н ул.Инокобод 2- проезд Д 7'
        ];
        $templateProcessor->setValues($data);

        $el = $templateProcessor->save();
        $newFile = IOFactory::load($el);
        $objWriter = IOFactory::createWriter($newFile, 'Word2007');

        $objWriter->save(Root . '/binary/words/1C/form114s/Form114.docx');
    }

    public function testGenerateRefundApplication()
    {
        $data = $this->generateRefundApplication();
        vd($data);
    }

    public function generateRefundApplication()
    {
        $templateProcessor = new TemplateProcessor(Root . '/binary/words/1C/RefundApplicationTemplate.docx');

        $data = [
            'fullName' => 'Нормуродов Эркин',
            'address' => 'г Ургенч, Аль Хорезмий ул.',
            'phone' => '998912759707',
            'returnGoodsQuantity' => 1,
            'returnReason' => '',
            'date' => '10/3/2018 4:02:57 PM',
        ];
        $templateProcessor->setValues($data);

        $el = $templateProcessor->save();
        $newFile = IOFactory::load($el);
        $objWriter = IOFactory::createWriter($newFile, 'Word2007');

        $objWriter->save(Root . '/binary/words/1C/refundapps/RefundApplication.docx');
    }
}
//    public function testGenerateBarcode()
//    {
//        $data = $this->generateBarcode('C128');
//        vd($data);
//    }
//
//    public function generateBarcode($code)
//    {
//        // Generate PNG barcode starts
//
////        $barcodeobj = new \TCPDFBarcode($code,'C128');
////
////        $barcode = $barcodeobj->getBarcodePNG(2, 30, array(0,0,0));
//
//        // Generate PNG barcode ends
//
//        $templateProcessor = new TemplateProcessor(Root . '/binary/words/1C/banderolTemplate.docx');
//
//        $templateProcessor->setImageValue('barcode', $barcode);
//
//        $el = $templateProcessor->save();
//        $newFile = IOFactory::load($el);
//        $objWriter = IOFactory::createWriter($newFile, 'Word2007');
//
//        $objWriter->save(Root . '/binary/words/1C/invoices/BarcodeTest.docx');
//
//    }}                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
