<?php

return array(

    'imgDomain' => 'http://www.infiniti-asc.ru',
    'siteDomain' => 'http://www.infiniti-asc.ru',
    //'pathToFiles' => '/var/www/gruber/export/img',
    'pathToFiles' => '/var/www/moscow_export/infiniti-asc/export/img',

    // Для отдельного проекта можно переопределить способ экспорта
    'export'     => array(
        'class' => 'Csv',
        array(
            'csvDir'    => 'export',
            'fname'     => 'infiniti-asc',
            'delimiter' => ';',
            'charset'   => 'UTF-8',
            'chunk'     => 500,
            'limit'    => 1000,
        )
    ),
    // Для отдельного проекта можно переопределить способ загрузки данных из сети
    'import'     => array(
        'class' => 'Network',
        array(
            'cacheEnabled' => true,
            'cacheDir'     => 'cache',
            'cacheTime'    => 3600,
        ),
    ),

    'exclude'    => array(),

    'method' => array(
        'name' => 'get'
    ),

    //Селектор для перехода на страницу из пагинации
    /*'pagination' => array(
      'selectors' => array(
          array(
              'selectors' => 'div[id=comp_3c295307a2eb3930054ccba3a4d6bddd] font[class=text] b',
              'select' => 'href',
              'sibling' => 'next'
          ),
      ),
        //'ajax'=> true,
        //'ajaxUrl'=> 'http://www.major-expert.ru/ajax/get_cars.php',
    //Количество переходов по пагинации
        //'ttl' => 17,
    ),*/

    // Страница входа для поиска товаров
    'url'        => 'http://www.infiniti-asc.ru/old_cars/?cl=n&SHOWALL_1=1',//страница с общим списком товаров


    //$params['uploadFile'] = false;// загружать файлы или нет
    //$params['childPage'] = false;// дочерние страницы
    //$params['required'] = false;// обязательность поля
    //$params['costul'] = false;// используем для индивидуальных костылей
    //$params['replacer'] = array('search', 'replace');// заменяем данные, например пути у фоток подменяем
    //$params['modifier'] = array('md5');// модифицируем данные
    //$params['type'] = 'str';//указываем, чтобы на выход в csv была строка, а не массив
    //$params['select'] = 'src';//указывает, что берем не текст а src картинки

    //---------------->параметры для товаров

    // Селекторы данных которые необходимо найти
    'attributes' => array(
        'id'                            => array('selectors'=>array('div[class=breadcrumb_block] a[class=sel]'), 'required'=>true, 'modifier'=>array('md5'), 'type'=>'str'),
        'Наименование автомобиля'       => array('selectors'=>array('div[class=breadcrumb_block] a[class=sel]'), 'required'=>true, 'filters'=>array('strip_tags','trim'), 'type'=>'str'),
        'Цена'                          => array('selectors'=>array('span[class=price_value]'), 'required'=>true, 'filters'=>array('strip_tags','trim'), 'type'=>'str'), 
        'Комплектация'                  => array('selectors'=>array('div[class=tabs_detail] table[class=equipment] ul li'), 'filters'=>array('trim'), 'type'=>'str'),
        'Технические характеристики'    => array('selectors'=>array('td[class=detail_text_elem] div[class=item_model] table tr'), 'filters'=>array('strip_tags', 'trim'), 'type'=>'str', 'select'=>'innertext', 'replacer'=>array('<td class="value">', '^')),
        'Основное изображение'          => array('selectors'=>array('div[class=pic_detail_car] div[class=pic_block_n] div[class=zoom_a_pic] a[class=cll1]'), 'select'=>'href', 'type'=>'str', 'uploadFile'=>true),
        'Дополнительные изображения'    => array('selectors'=>array('div[class=pic_detail_car] a[class=cll1]'), 'select'=>'href', 'type'=>'str', 'uploadFile'=>true)
    ),
    // Селектор URL для перехода на уровень ниже
    'deep'       => array(
        'selectors' => array(
            array(
                'selectors' => 'table.tbl_auto_list2 tr.item_model2 td.pic_td a', //30 ссылок на странице
                'select'    => 'href',
            ),
        ),
    ),

//---------------->параметры для модификаций


    // Селекторы данных которые необходимо найти
/*    'attributes' => array(
        'id'                            => array('selectors'=>array('div[id=container_h1] h1'), 'required'=>true, 'modifier'=>array('md5'), 'type'=>'str'),
        'Наименование автомобиля'       => array('selectors'=>array('div[id=container_h1] h1'), 'required'=>true, 'filters'=>array('strip_tags','trim'), 'type'=>'str'), //type указываем, чтобы на выход в csv была строка, а не массив
        'Комплектация'                  => array('selectors'=>array('h2[id=complectation_chooser]'), 'required'=>true, 'filters'=>array('strip_tags','trim'), 'type'=>'str'), //type указываем, чтобы на выход в csv была строка, а не массив
        'Цена'                          => array('selectors'=>array('span[id=modification-price-overall-2]'), 'required'=>true, 'filters'=>array('strip_tags','trim'), 'type'=>'str'), //type указываем, чтобы на выход в csv была строка, а не массив
        'Опции'                         => array('selectors'=>array('div[id=complectation] ul[class=complectation_list]'), 'select'=>'innertext', 'sibling'=>'next', 'filters'=>array('trim'), 'type'=>'str', 'json'=>true),
        'Технические характеристики'    => array('selectors'=>array('div[id=complectation] div[class=tabs] ul li a'), 'select'=>'href', 'type'=>'str', 'childPage'=>array('selector'=>'div[id=complectation] table[class=spec_table] tr', 'select'=>'innertext', 'filters'=>array('trim'), 'costul'=>'parameters')),
        
    ),
    // Селектор URL для перехода на уровень ниже
    'deep'       => array(
        'selectors' => array(
            array(
                'selectors' => 'table[id=modifications_list] tr.search_result_model td[style=white-space:nowrap;] a',
                'select'    => 'href',
            ),
            array(
                'selectors' => 'table[id=modifications_list] tr.more_modification td a',
                'select'    => 'href',
            ),
        ),
    ),*/


);