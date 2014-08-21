<?php

return array(

    'imgDomain' => 'http://i.quto.ru',
    'siteDomain' => 'http://quto.ru',
    //'pathToFiles' => '/var/www/gruber/export/img',
    'pathToFiles' => '/home/snowmedia/public_html/monitor.snow-media.ru/base/gruber/export/img',

    // Для отдельного проекта можно переопределить способ экспорта
    'export'     => array(
        'class' => 'Csv',
        array(
            'csvDir'    => 'export',
            'fname' => 'quto',
            'delimiter' => ';',
            'charset'   => 'UTF-8',
            'chunk'     => 500,
        )
    ),
    // Для отдельного проекта можно переопределить способ загрузки данных из сети
    'import'     => array(
        'class' => 'Network',
        array(
            'cacheEnabled' => true,
            'cacheDir'     => 'cache',
            'cacheTime'    => 3600,
        )
    ),

    'exclude' => array(),
    //Селектор для перехода на страницу из пагинации
    'pagination' => array(
        'selectors' => array(
            array(
                'selectors' => 'p[class=pages_navigation] a[class=right]',
                'select' => 'href',
            ),
        ),
        //Количество переходов по пагинации
        'ttl' => 3,
    ),

    // Страница входа для поиска товаров
    'url' => 'http://quto.ru/catalog/search/result/?order=price_desc',

    //$params['uploadFile'] = false;// загружать файлы или нет
    //$params['childPage'] = false;// дочерние страницы
    //$params['required'] = false;// обязательность поля
    //$params['costul'] = false;// используем для индивидуальных костылей
    /*
        'Возможные цвета'       => array('selectors'=>array('ul[class=colors] a'), 'replace'=>array('background-color: '=>'', ';'=>''), 'select'=>'style' ),
        'Страна изготовления'   => array('selectors'=>array(''), 'default'=>'Россия' ),
        'Текущая цена'          => array('selectors'=>array('p[class=price]'), 'filters'=>array('Ego::formatPrice') ),
    */


//---------------->параметры для товаров

    // Селекторы данных которые необходимо найти
    'attributes' => array(
        'id'                            => array('selectors'=>array('div[id=container_h1] h1'), 'required'=>true, 'modifier'=>array('md5'), 'type'=>'str'),
        'Наименование автомобиля'       => array('selectors'=>array('div[id=container_h1] h1'), 'required'=>true, 'filters'=>array('strip_tags','trim'), 'type'=>'str'), //type указываем, чтобы на выход в csv была строка, а не массив
        'Описание модели'               => array('selectors'=>array('div[class=watch_for_updates_fix seo_text]'), 'filters'=>array('trim'), 'type'=>'str'),
        'Основное изображение'          => array('selectors'=>array('img[id=current_photo_img]'), 'select'=>'src', 'type'=>'str', 'uploadFile'=>true), //select=>src указывает, что берем не текст а src картинки
        'Дополнительные изображения'    => array('selectors'=>array('div[class=photo_previews] a[class=photo]'), 'select'=>'href', 'type'=>'str', 'uploadFile'=>true),
        'Фотогалерея'                   => array('selectors'=>array('div[class=photo_previews] li[class=more] a'), 'select'=>'href', 'type'=>'str', 'childPage'=>array('uploadFile'=>true, 'selector'=>'ul[class=car_photo_gallery_nav_preview] a img', 'select'=>'src')),
    ),
    // Селектор URL для перехода на уровень ниже
    'deep'       => array(
        'selectors' => array(
            array(
                'selectors' => 'td.search_result_model_name a',
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