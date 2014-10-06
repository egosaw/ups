<?php

return array(

    'imgDomain' => 'http://www.ascgroup.ru',
    'siteDomain' => 'http://www.ascgroup.ru',
    //'pathToFiles' => '/var/www/gruber/export/img',
    'pathToFiles' => '/var/www/moscow_export/kia-asc/export/img',

    // Для отдельного проекта можно переопределить способ экспорта
    'export'     => array(
        'class' => 'Csv',
        array(
            'csvDir'    => 'export',
            'fname'     => 'kia-asc',
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
    'pagination' => array(
      'selectors' => array(
          array(
              'selectors' => 'div[class=nav_string_news] span[class=sel]',
              'select' => 'href',
              'sibling' => 'next'
          ),
      ),
        //'ajax'=> true,
        //'ajaxUrl'=> 'http://www.major-expert.ru/ajax/get_cars.php',
    //Количество переходов по пагинации
        //'ttl' => 17,
    ),

    // Страница входа для поиска товаров
    'url'        => 'http://www.ascgroup.ru/dealers/',//страница с общим списком товаров


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
        'id'                            => array('selectors'=>array('div[class=hproduct] h1'), 'required'=>true, 'modifier'=>array('md5'), 'type'=>'str'),
        'Наименование автомобиля'       => array('selectors'=>array('div[class=hproduct] h1'), 'required'=>true, 'filters'=>array('strip_tags','trim'), 'type'=>'str'),
        'Цена'                          => array('selectors'=>array('span[class=price_value] span[class=price]'), 'required'=>true, 'filters'=>array('strip_tags','trim'), 'type'=>'str'), 
        'Комплектация'                  => array('selectors'=>array('div[id=cont_tabs] div[class=identifier] ul li'), 'filters'=>array('trim'), 'type'=>'str'),        
        'Технические характеристики'    => array('selectors'=>array('td[class=detail_text_elem] div[class=item_model] table tr'), 'filters'=>array('strip_tags', 'trim'), 'type'=>'str', 'select'=>'innertext', 'replacer'=>array('<td class="value">', '^')),
        'Основное изображение'          => array('selectors'=>array('div[class=pic_detail_car] div[class=pic_block_n] a[class=cll1]'), 'select'=>'href', 'type'=>'str', 'uploadFile'=>true),
        'Дополнительные изображения'    => array('selectors'=>array('div[class=pic_detail_car] a[class=cll1]'), 'select'=>'href', 'type'=>'str', 'uploadFile'=>true)
    ),
    // Селектор URL для перехода на уровень ниже
    'deep'       => array(
        'selectors' => array(
            array(
                'selectors' => 'table.tbl_auto_list2 tr.item_model2 td.pic_td a', //10 ссылок на странице
                'select'    => 'href',
            ),
        ),
    ),

);