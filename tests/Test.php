<?php


namespace zetsoft\service\tests;


use Facebook\WebDriver\Exception\UnsupportedOperationException;
use zetsoft\models\shop\ShopOrder;
use zetsoft\service\inputs\Fileinput;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\kernels\ZFrame;

class Test extends ZFrame
{
    public function getValue($arg) {
        return '123';
    }

    public function testExcel(){

   /*    Az::$app->office->excel->modelClass = ShopOrder::class;
        $excel = Az::$app->office->excel->run();*/
       //   vd($excel);
        $path = Root . '/upload/excelz/eyuf/Заказы_2020-10-26_01-32-48.xlsx';
        $inline = $this->httpGet('inline', true);

        $app = App;
        $path = Az::getAlias("@root/upload/excelz/bosya/{$path}");
        vd($path);
        if (!file_exists($path))
            return '12312312312';

        if (ZArrayHelper::isIn(ZFileHelper::extension($path), FileInput::blocked))
            throw new UnsupportedOperationException('Blocked File');

        /*
         *   return $this->alertDanger( $path, Az::l('Файл отсутсвует на сервере'));
         *   return $this->alertDanger( $path, Az::l('Файл заблокирован'));
         **/

        $fileName = bname($path);

        $response = Az::$app->response;
        $response->sendFile($path, $fileName,[
            'inline' => $inline
        ])->send();

        return true;
    }
}
