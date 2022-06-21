<?php
include 'simple_html_dom.php';

// Примеры урлов.
$url_90915_YZZE2 = 'https://www.autozap.ru/goods/90915-YZZE2/';
$url_17177 = 'https://www.autozap.ru/goods/17177/';
$url_OC264 = 'https://www.autozap.ru/goods/OC264/';
$url_2825 = 'https://www.autozap.ru/goods/2825/';

function parse($url, $filePath) {
    $html = file_get_html(strtolower($url));

    // Поиск названия изготовителя для первой строки.
    $first_string = $html->find('#tabGoods', 0)->find('tr', 1)->find('td', 1);
    // Удаление лишнего текста для получения имени из html.
    $remains = $html->find('#tabGoods', 0)->find('tr', 1)->find('td', 1)->find('span', 0);
    $remains->outertext = '';
    // Запрос по первой странице.
    $new_url = $url . strtolower($first_string->innertext);
    $html_page = file_get_html($new_url);

    $table_rows = $html_page->find('#tabGoods', 0);

    $subarray = array();
    $array = array();

    // Получаем заранее name, brand, article, потому что в последующих строках эти значения будут пустыми.
    $name = $table_rows->find('tr', 2)->find('.name', 0)->plaintext;
    $remains = $table_rows->find('tr', 2)->find('.producer',0)->find('span', 0);
    $remains->outertext = '';

    $brand = $table_rows->find('tr', 2)->find('.producer', 0)->innertext;
    $article = $table_rows->find('tr', 2)->find('.code', 0)->plaintext;

    // Номер строки, с которой начинается парсинг таблицы.
    $i = 2;

    while($item = $table_rows -> children($i++)) {
        if ($item->class == 'header_tr') {
            break;
        }

        $subarray['name'] = $name;
        $subarray['price'] = $item->find('.price', 0)->find('span', 0)->plaintext;
        $subarray['article'] = $article;
        $subarray['brand'] = $brand;
        $subarray['count'] = $item->find('.storehouse',0)->find('span', 0)->innertext;

        $subarray['time'] = preg_replace('/[^0-9]/', '', $item->find('.article', 0)->plaintext);
        $subarray['id'] = $item->find('.storehouse', 0)->find('input[id^=g]')[0]->value;

        array_push($array, $subarray);
    }

    // Запись в файл.
    file_put_contents($filePath, json_encode($array, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES));
}

// Вызов функции.
parse($url_90915_YZZE2, '90915-YZZE2.json');
//parse($url_17177, '17177.json');
//parse($url_2825, '2825.json');
//parse($url_OC264, 'OC264.json');

?>