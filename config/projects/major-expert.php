<?php

return array(

    'imgDomain' => 'http://www.major-expert.ru',
    'siteDomain' => 'http://www.major-expert.ru',
    //'pathToFiles' => '/var/www/gruber/export/img',
    'pathToFiles' => '/var/www/moscow_export/major/export/img',

    // Для отдельного проекта можно переопределить способ экспорта
    'export'     => array(
        'class' => 'Csv',
        array(
            'csvDir'    => 'export',
            'fname'     => 'major',
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
        'name' => 'post',
        'options' => array(
            "json_bm" => '{"json":[{"b":50,"m":0},{"b":52,"m":0},{"b":47,"m":0},{"b":6,"m":0},{"b":34,"m":0},{"b":2,"m":0},{"b":29,"m":0},{"b":16,"m":0},{"b":22,"m":0},{"b":30,"m":0},{"b":43,"m":0},{"b":39,"m":0},{"b":12,"m":0},{"b":25,"m":0},{"b":53,"m":0},{"b":46,"m":0},{"b":14,"m":0},{"b":1,"m":0},{"b":18,"m":0},{"b":51,"m":0},{"b":37,"m":0},{"b":32,"m":0},{"b":20,"m":0},{"b":15,"m":0},{"b":26,"m":0},{"b":38,"m":0},{"b":8,"m":0},{"b":9,"m":0},{"b":35,"m":0},{"b":4,"m":0},{"b":7,"m":0},{"b":10,"m":0},{"b":3,"m":0},{"b":19,"m":0},{"b":33,"m":0},{"b":31,"m":0},{"b":60,"m":0},{"b":28,"m":0},{"b":24,"m":0},{"b":27,"m":0},{"b":23,"m":0},{"b":5,"m":0},{"b":21,"m":0},{"b":13,"m":0},{"b":36,"m":0},{"b":11,"m":0},{"b":62,"m":0},{"b":61,"m":0}]}',
            "json_full" => '{"price":[{"from":0,"to":0}],"gear":[{"akpp":0,"mkpp":0}],"volume":[{"from":0,"to":0}],"power":[{"from":0,"to":0}],"special":[{"special":"none"}],"salon":[{"salon":"all"}],"body":[{"body":"all"}],"drive":"all","engine":[{"petrol":0,"diesel":0}],"run":0,"year":[{"from":0,"to":0}]}',
            "json_other" => '{"sort_type":3,"sort_direct":0,"wrapper":2,"spb":0,"moscow":0,"currency":"rub","panel_status":"simple"}',
            "page" => 1
        )
    ),

    //Селектор для перехода на страницу из пагинации
    'pagination' => array(
      /*'selectors' => array(
          array(
              'selectors' => 'p[class=pages_navigation] a[class=right]',
              'select' => 'href',
          ),
      ),*/
        'ajax'=> true,
        'ajaxUrl'=> 'http://www.major-expert.ru/ajax/get_cars.php',
    //Количество переходов по пагинации
        'ttl' => 257,
    ),

    // Страница входа для поиска товаров
    //'url'        => 'http://www.major-expert.ru/#:{brands:50,52,47,6,34,2,29,16,22,30,43,39,12,25,53,46,14,1,18,51,37,32,20,15,26,38,8,9,35,4,7,10,3,19,33,31,60,28,24,27,23,5,21,13,36,11,62,61;page:2;}',
    'url'        => 'http://www.major-expert.ru/ajax/get_cars.php',


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
        'id'                            => array('selectors'=>array('div[class=card_header] h1'), 'required'=>true, 'modifier'=>array('md5'), 'type'=>'str'),
        'Наименование автомобиля'       => array('selectors'=>array('div[class=card_header] h1'), 'required'=>true, 'filters'=>array('strip_tags','trim'), 'type'=>'str'),
        'Цена'                          => array('selectors'=>array('div[class=global_card_price_container]'), 'required'=>true, 'filters'=>array('strip_tags','trim'), 'type'=>'str'), 
        'Описание модели'               => array('selectors'=>array('div[class=text] div[id=div3] p[!style]'), 'filters'=>array('trim'), 'type'=>'str'),
        'Комплектация'                  => array('selectors'=>array('div[class=text] div[id=div2] li'), 'filters'=>array('trim'), 'type'=>'str'),
        'Технические характеристики'    => array('selectors'=>array('div[class=text] div[id=div1] table tr'), 'filters'=>array('strip_tags', 'trim'), 'type'=>'str', 'select'=>'innertext', 'replacer'=>array('<td class="bold">', '^')),
        'Основное изображение'          => array('selectors'=>array('img[id=CarIDF]'), 'select'=>'src', 'type'=>'str', 'uploadFile'=>true),
        'Дополнительные изображения'    => array('selectors'=>array('div[class=smallphoto] img'), 'select'=>'src', 'type'=>'str', 'uploadFile'=>true, 'replacer'=>array('small', 'big'))
    ),
    // Селектор URL для перехода на уровень ниже
    'deep'       => array(
        'selectors' => array(
            array(
                'selectors' => 'div.list div.floats h1 a',
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