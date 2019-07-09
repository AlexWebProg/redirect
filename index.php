<?php
$strLink = filter_input(INPUT_GET,'url',FILTER_SANITIZE_STRING);
if (!empty($strLink)){
    include 'urlProcessing.php';
    $objURLProcessing = new urlProcessing();
    $arrLink = $objURLProcessing->makeLink($strLink);
    if (!empty($arrLink['href'])){
        header('Location: '.$arrLink['href']);
    }else{
        echo('Не удалось выполнить переадресацию на сайт '.$strLink);
    }
}