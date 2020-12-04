<?php



const URL = 'https://agr.uz/sandbox';
const SECRET_KEY = 'PrEe1zL-IY1E4YvdCkawZ0rt61dBPayD';


$params = array(
    'VENDOR_ID' => '101443',
    'MERCHANT_TRANS_ID' => 'AB772059',
    'MERCHANT_TRANS_AMOUNT' => '10001',
    'MERCHANT_CURRENCY' => 'sum',
    'MERCHANT_TRANS_NOTE' => 'transaction_note_example',
    'SIGN_TIME' => '1480056082732',
);
$params['SIGN_STRING'] = md5(SECRET_KEY . $params['VENDOR_ID'] . $params['MERCHANT_TRANS_ID'] . $params['MERCHANT_TRANS_AMOUNT'] . $params['MERCHANT_CURRENCY'] . $params['SIGN_TIME']);





$url = URL . '?' . http_build_query($params);
//echo "<button class='btn-success p-2 rounded ' onclick=\"location.href='{$url}';\">Отправить</button>";


?>



<form method="post" action="<?=URL;?>">
    <!-- <input type="hidden" name="VENDOR_ID">
     <input type="hidden" name="MERCHANT_TRANS_NOTE">
     <input type="hidden" name="SIGN_TIME">-->
    <?php foreach ($params as $name => $value): ?>
        <input type="hidden" name="<?=$name; ?>" value="<?=$value;?>">
    <?php endforeach;?>
    <!--<label for="trans_amount">Сумма:</label>
    <input type="text" id="trans_amount" name="MERCHANT_TRANS_AMOUNT">-->
    <!-- <label for="trans_id">Паспорт серия:</label>
        <input type="text" id="trans_id" name="MERCHANT_TRANS_ID"> <br>

        <input type="text" value="<?/*=$params['MERCHANT_CURRENCY']*/?>" disabled name="MERCHANT_CURRENCY"> <br>-->
    <button type="submit" class='btn-primary p-2 rounded '>Оплатить</button>
</form>
