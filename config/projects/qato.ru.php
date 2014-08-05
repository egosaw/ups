<?php

return array(

    'imgDomain' => 'http://i.quto.ru',
    'pathToFiles' => '/var/www/gruber/export/img',

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
        'class' => 'Fgc',
        array(
            'cacheEnabled' => true,
            'cacheDir'     => 'cache',
            'cacheTime'    => 3600,
        )
    ),
    // Селекторы данных которые необходимо найти
    'attributes' => array(
        'Наименование автомобиля' => array('selectors' => array('div[id=container_h1] h1'), 'required' => true, 'filters' => array('strip_tags', 'trim'), 'type' => 'str'), //type указываем, чтобы на выход в csv была строка, а не массив
        'Описание модели' => array('selectors' => array('div[class=watch_for_updates_fix seo_text]'), 'filters' => array('trim'), 'type' => 'str'),
        'Основное изображение' => array('selectors' => array('img[id=current_photo_img]'), 'select' => 'src', 'type' => 'str', 'uploadFile' => true), //select=>src указывает, что берем не текст а src картинки
        'Дополнительные изображения' => array('selectors' => array('div[class=photo_previews] a[class=photo]'), 'select' => 'href', 'type' => 'str', 'uploadFile' => true),
        //'Дополнительные изображения'    => array('selectors'=>array('div[class=photo_previews] li[class=more] a'), 'select'=>'href', 'type'=>'str'),
        /*
            'Сезон коллекции'       => array('selectors'=>array(''), 'filters'=>array('strip_tags','trim') ),
            'Название коллекции'    => array('selectors'=>array('')),
            'Тип обуви'             => array('selectors'=>array('') ),
            'Название модели'       => array('selectors'=>array('div[id=description] h4') ),
            'Уникальный код модели' => array('selectors'=>array('div[id=description] h4') ),
            'Пол'                   => array('selectors'=>array('') ),
            'Возможные цвета'       => array('selectors'=>array('ul[class=colors] a'), 'replace'=>array('background-color: '=>'', ';'=>''), 'select'=>'style' ),
            'Линейка размеров'      => array('selectors'=>array('duv[id=productSize]'), 'select'=>'plaintext' ),
            'Вид сезона'            => array('selectors'=>array('') ),
            'Материал изготовления' => array('selectors'=>array(''), 'default'=>'Кожа' ),
            'Страна изготовления'   => array('selectors'=>array(''), 'default'=>'Россия' ),
            'Текущая цена'          => array('selectors'=>array('p[class=price]'), 'filters'=>array('Ego::formatPrice') ),
            'Первая цена'           => array('selectors'=>array('p[class=price]') ),
    */
    ),
    // Страница входа для поиска товаров
    'url' => 'http://quto.ru/catalog/search/result/?order=price_asc',
    'exclude'    => array(),
    // Селектор URL для перехода на уровень ниже
    'deep'       => array(

        'selectors' => array(
            /*array(
                'selectors' => 'td.search_result_model_name a',
                'select'    => 'href',
            ),*/
            array(
                'selectors' => 'td.search_result_model_name a',
                'select'    => 'href',
            ),
        ),

        'deep' => array(

            'selectors' => array(
                array(
                    'selectors' => 'div[class=photo_previews] li[class=more] a',
                    'select' => 'href'
                ),
            ),

        ),

    ),
    //Селектор для перехода на страницу из пагинации
    'pagination' => array(
        'selectors' => array(
            array(
                'selectors' => 'p.pages_navigation a.left',
                'select' => 'href',
            ),
        ),
        //Количество переходов по пагинации
        'ttl' => 3,
    ),
);



