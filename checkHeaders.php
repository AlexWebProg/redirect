<?php
$strLink = filter_input(INPUT_GET,'link',FILTER_SANITIZE_STRING);
if (!empty($strLink)){
    include 'urlProcessing.php';
    $objURLProcessing = new urlProcessing();
    $arrHeaders = $objURLProcessing->checkHeaders($strLink);
}
?>
<form method="get">
    <label for="link">
        Введите адрес сайта:
    </label>
    &nbsp;&nbsp;
    <input id="link" type="text" name="link" placeholder="Адрес сайта"/>
    &nbsp;&nbsp;
    <button type="submit">Проверить заголовки</button>
</form>
<?if (!empty($arrHeaders)){?>
    <hr/>
    <h3><?=$strLink?></h3>
    <pre>
    <?print_r($arrHeaders);?>
    </pre>
<?}