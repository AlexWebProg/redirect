<?php

class urlProcessing
{
    // Замена букв на одной и той же кнопке
    function sameKey($str){
        $tr = array(
            "й"=>"q","ц"=>"w","у"=>"e","к"=>"r","е"=>"t","н"=>"y","г"=>"u","ш"=>"i","щ"=>"o","з"=>"p","ф"=>"a",
            "ы"=>"s","в"=>"d","а"=>"f","п"=>"g","р"=>"h","о"=>"j","л"=>"k","д"=>"l","я"=>"z","ч"=>"x","с"=>"c",
            "м"=>"v","и"=>"b","т"=>"n","ь"=>"m",

            "Й"=>"Q","Ц"=>"W","У"=>"E","К"=>"R","Е"=>"T","Н"=>"Y","Г"=>"U","Ш"=>"I","Щ"=>"O","З"=>"P","Ф"=>"A",
            "Ы"=>"S","В"=>"D","А"=>"F","П"=>"G","Р"=>"H","О"=>"J","Л"=>"K","Д"=>"L","Я"=>"Z","Ч"=>"X","С"=>"C",
            "М"=>"V","И"=>"B","Т"=>"N","Ь"=>"M",
        );
        return strtr($str,$tr);
    }

    // Замена букв на одной и той же кнопке
    function sameLetter($str){
        $tr = array(
            "у"=>"y","к"=>"k","е"=>"e","н"=>"h","х"=>"x","в"=>"b","а"=>"a","р"=>"p","о"=>"o","с"=>"c","м"=>"m",
            "т"=>"t",

            "У"=>"Y","К"=>"K","Е"=>"E","Н"=>"H","Х"=>"X","В"=>"B","А"=>"A","Р"=>"P","О"=>"O","С"=>"C","М"=>"M",
            "Т"=>"T"
        );
        return strtr($str,$tr);
    }

    /*
     * Функция получает $url сайта, пингует его и выдаёт массив полученных заголовков
     */
    function checkHeaders($url){
        $url = idn_to_ascii($url);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        $arrAnswer = curl_getinfo($ch);
        return $arrAnswer;
    }

    /*
     * Функция получает $url сайта, пингует его и выдаёт ответ: $arrResult['result','redirect_url']
     * result:
     * 1 - сайт доступен,
     * 2 - сайт находится по адресу redirect_url
     * 0 - сайт недоступен
     */
    function pingUrl($url){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        $arrAnswer = curl_getinfo($ch);
        curl_close($ch);
        switch($arrAnswer['http_code']){
            case 200:
            case 403:
                $arrResult = array('result' => 1);
                break;
            case 301:
            case 302:
                $arrResult = array('result' => 2, 'href' => $arrAnswer['redirect_url']);
                break;
            default:
                $arrResult = array('result' => 0);
                break;
        }
        return $arrResult;
    }

    /*
     * Функция получает $url сайта в виде, например:
     * - нету сайта
     * - мой.сайт
     * - www.мой.сайт
     * , проверяет, существует ли такой сайт, по списку:
     * - http://мой.сайт
     * - https://мой.сайт
     * - http://www.мой.сайт
     * - https://www.мой.сайт
     * И возвращает массив ('result' => 1 или 0, 'href' => ссылка на сайт)
     */
    function makeLink($url){
        $arrResult = array('result' => 0);
        $url = mb_strtolower($url, 'UTF-8');
        if ((mb_strpos($url, '.', 0, 'UTF-8') !== false) and (mb_strpos($url, '@', 0, 'UTF-8') === false)){
            $arrCheckURLs = array();
            $url = str_replace(array('http://','https://','www.'),array('','',''),$url);
            $strSameKey = $this->sameKey($url);
            $strSameLetter = $this->sameLetter($url);
            $strIdnToAscii = idn_to_ascii($url);
            $arrVars = array('http://','http://www.','https://','https://www.');
            foreach($arrVars as $strVar){
                $arrCheckURLs[] = $strVar.$url;
                if($strIdnToAscii != $url){
                    $arrCheckURLs[] = $strVar.$strIdnToAscii;
                }
                if($strSameKey != $url){
                    $arrCheckURLs[] = $strVar.$strSameKey;
                }
                if($strSameLetter != $url){
                    $arrCheckURLs[] = $strVar.$strSameLetter;
                }
            }
            foreach($arrCheckURLs as $strUrl){
                $arrPing = $this->pingUrl($strUrl);
                if ($arrPing['result'] == 1){
                    $arrResult['result'] = 1;
                    $arrResult['href'] = $strUrl;
                    break;
                }elseif($arrPing['result'] == 2){
                    $arrResult['result'] = 1;
                    $arrResult['href'] = $arrPing['href'];
                    break;
                }
            }
        }
        return $arrResult;
    }

}
