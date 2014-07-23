<?php

return array(

    // Для отдельного проекта можно переопределить способ экспорта
    'export'     => array(
        'class' => 'Csv',
        array(
            'csvDir'    => 'export',
            'fname'     => '',
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
    'attributes' => array(/*
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
    'url'        => 'http://quto.ru/catalog/search/result/?price_max=10000000&order=price_asc',
    'exclude'    => array(),
    // Селектор URL для перехода на уровень ниже
    'deep'       => array(

        'selectors' => array(

            array(
                'selectors' => 'td.search_result_model_name a',
                'select'    => 'href',
            ),

        ),

    ),
);