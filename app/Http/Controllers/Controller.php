<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getCurrencies()
    {
        $date = date('d/m/Y');
        $url = "http://www.cbr.ru/scripts/XML_daily.asp?date_req=$date";
        $curs = array(); // массив с данными

        if (!$xml = simplexml_load_file($url)) die('Ошибка загрузки XML'); // загружаем полученный документ в дерево XML
        $curs['date'] = $this->get_timestamp($xml->attributes()->Date); // получаем текущую дату

        foreach ($xml->Valute as $m) { // перебор всех значений
            // для примера будем получать значения курсов лишь для двух валют USD и EUR
            if ($m->CharCode == "USD" || $m->CharCode == "EUR") {
                $curs[(string)$m->CharCode] = (float)str_replace(",", ".", (string)$m->Value); // запись значений в массив
            }
        }

        return $curs;
    }

    function get_timestamp($date)
    {
        list($d, $m, $y) = explode('.', $date);
        return mktime(0, 0, 0, $m, $d, $y);
    }

}
