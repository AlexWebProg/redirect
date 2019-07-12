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
}else{
?>
<form method="get">
    <label for="url">
        Enter URL to redirect:
    </label>
    &nbsp;&nbsp;
    <input id="url" type="text" name="url" placeholder="any URL"/>
    &nbsp;&nbsp;
    <button type="submit">Find site and redirect</button>
</form>
<?
}