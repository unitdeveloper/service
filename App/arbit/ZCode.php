<?php

/**
 *
 *
 * Author:  Asror Zakirov
 *
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\App\vade;


use zetsoft\enums\vade\CodeStatus;
use zetsoft\enums\vade\OrderStatus;
use zetsoft\models\vade\Code;
use zetsoft\models\vade\Order;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\system\kernels\ZFrame;


class ZCode extends ZFrame
{

    public $iID;

    /* @var Order $model ; */
    public $model;

    /* @var Code $_model ; */
    public $code;

    public const sSerialNum = '100000000';
    public const sPinStart = '100000000000';
    public const sPinEnd = '999999999999';
    public $isTest = false;

    private $_iStart;

    private $_iSerial;
    private $_iPinCode;

    private $_filePath;

    public function test()
    {

        Az::start(__FUNCTION__);

        $this->create();
        $this->run();

//        Az::$app->cores->exec->php('codes/generate/go ' . 2, false, false);
        Az::end();
    }

    public function clean()
    {
        Az::start(__FUNCTION__);

        $aOrder = Order::find()
            ->select('id')
            ->all();

        foreach ($aOrder as $order) {
            $this->_clean_id($order->id);
        }

        Az::end();
    }


    public function filePath(int $iID)
    {

        $filePath = Root . '/storing/zcodes/' . $iID . '.csv';

        Az::debug($filePath, 'File Path');

        return $filePath;
    }

    private function _clean_id(int $iID)
    {
        $this->_init($iID);

        @unlink($this->_filePath);


        $iNum = Az::$app->db->createCommand()->delete('order', [
            'id' => $iID
        ])->execute();

        Az::debug($iNum, 'Remove Order | Affected Rows');


        $iNum = Az::$app->db->createCommand()->delete('code', [
            'order_id' => $iID
        ])->execute();
        Az::debug($iNum, 'Remove Code | Affected Rows');

    }

    public function run(int $iID = null)
    {
        Az::start(__FUNCTION__);

        $iID = $iID ?? $this->iID;
        $this->_init($iID);

        if (!$this->_order())
            return false;

        if ($this->isTest)
            Az::$app->cores->exec->exec('calc.exe');

        $this->_all();

        Az::end();
        return true;
    }


    private function _order()
    {
        $this->model = Order::findOne($this->iID);
        if ($this->model === null)
            return Az::warning($this->iID, 'Model is empty');

        /**
         *
         * Variables
         */
        $iQuantity = (int)$this->model->quantity;
        $iEnd = $this->_iStart + $iQuantity;


        /**
         *
         *
         * Initializing
         */

        $this->_iSerial = $this->_iStart;

        $this->model->start = (string)$this->_iStart;
        $this->model->end = (string)$iEnd;

        $this->model->status = OrderStatus::Vprocesse;

        if ($this->model->save())
            return Az::debug($this->model->id, 'OrderStatus::Vprocesse');

        return true;
    }

    /**
     *
     * Function  first
     */
    private function _init(int $iID)
    {
        $this->iID = $iID;

        Az::info($this->iID, 'This is an ID for Model');
        $this->_filePath = $this->filePath($this->iID);
        /**
         *
         * Set Path
         */
        try {
            unlink($this->_filePath);
        } catch (\Exception $e) {
            Az::error($e->getMessage(), 'Cannot Remove File');
        }

        $sText = "Serial Num,Pin Code\n";
        file_put_contents($this->_filePath, $sText);

        $this->_iStart = $this->_orderEndMax() + 1;
    }


    /**
     *
     * Function  run
     */
    private function _all()
    {

        for ($i = 0; $i < $this->model->quantity; $i++) {
            $this->_code();
        }

        $this->model->status = OrderStatus::Sgenerirovan;
        if ($this->model->save())
            Az::debug($this->model->id, 'OrderStatus::Sgenerirovan');

    }


    public function create()
    {
        $order = new Order();
        $order->name = 'TestApp';
        $order->quantity = (string)10000;
        $order->save();

        $this->iID = $order->id;
    }


    private function _code()
    {

        $this->_generate();

        if (!$this->code->validate()) {
            Az::debug($this->_codeShow(), 'Regenerate Attempt #1');
            $this->_generate();
        }

        $this->_fileWrite();

        if ($this->code->validate()) {
            Az::debug($this->_codeShow(), 'Create New Code');
            $this->code->save();
        } else {
            Az::debug($this->_codeShow(), 'Not Validated');
        }

    }


    private function _codeShow()
    {

        return "Serial: {$this->code->serial_num} | PinCode: {$this->code->pin_code}";
    }


    private function _generate()
    {
        $this->_iPinCode = random_int(self::sPinStart, self::sPinEnd);
        $this->_iSerial++;

        $this->code = new Code();
        $this->code->serial_num = (string)$this->_iSerial;
        $this->code->pin_code = (string)$this->_iPinCode;
        $this->code->status = CodeStatus::Sgenerirovan;
        $this->code->order_id = $this->model->id;
        $this->code->qr_code = Az::$app->App->vade->zQrCode->run($this->code->serial_num);
    }

    private function _fileWrite()
    {

        $sText = $this->code->serial_num . "," . $this->code->pin_code . "\n";

        try {
            file_put_contents($this->_filePath, $sText, FILE_APPEND);
        } catch (\Exception $exception) {
            Az::error($exception->getMessage(), 'Exception');
        }

    }

    public function _orderEndMax()
    {
        // SELECT * FROM code ORDER BY "serial_num"::FLOAT DESC LIMIT 1

        $aOrder = Order::find()
            ->orderBy('"end"::FLOAT')
            ->limit(2)
            ->orderBy([
                'end' => SORT_DESC
            ])
            ->all();

        if (count($aOrder) < 2)
            return self::sSerialNum;

        $order = $aOrder[1];

        if ($order->end === null)
            return self::sSerialNum;

        return (int)$order->end;

    }


}
