<?php

return array(

    'imgDomain' => 'http://usedcar.audi-taganka.ru',
    'siteDomain' => 'http://usedcar.audi-taganka.ru',
    //'pathToFiles' => '/var/www/gruber/export/img',
    'pathToFiles' => '/var/www/moscow_export/audi-taganka/export/img',

    // Для отдельного проекта можно переопределить способ экспорта
    'export'     => array(
        'class' => 'Csv',
        array(
            'csvDir'    => 'export',
            'fname'     => 'audi-taganka',
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
              'selectors' => 'div[class=pages] ul[class=paginator] li a[class=last]',
              'select' => 'href'
          ),
      ),
        //'ajax'=> true,
        //'ajaxUrl'=> 'http://www.major-expert.ru/ajax/get_cars.php',
    //Количество переходов по пагинации
        //'ttl' => 17,
    ),*/

    // Страница входа для поиска товаров
    //'url'        => 'http://usedcar.audi-taganka.ru/Car/Results',//страница с общим списком товаров
    'url'        => 'http://usedcar.audi-taganka.ru/Car/Results_Page?page=1&sort=0&rows=1000',//страница со всеми товарами


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
        'id'                            => array('selectors'=>array('div[class=car-block] h2'), 'required'=>true, 'modifier'=>array('md5'), 'type'=>'str'),
        'Наименование автомобиля'       => array('selectors'=>array('div[class=car-block] h2'), 'required'=>true, 'filters'=>array('strip_tags','trim'), 'type'=>'str'),
        'Цена'                          => array('selectors'=>array('div[class=car-block] li span[class=cufon]'), 'required'=>true, 'filters'=>array('strip_tags','trim'), 'type'=>'str'), 
        'Технические характеристики'    => array('selectors'=>array('table[class=car-info-table] tr'), 'filters'=>array('strip_tags', 'trim'), 'type'=>'str', 'select'=>'innertext', 'replacer'=>array('<td>', '^')),
        'Изображения'                   => array('selectors'=>array('div[id=CarPhotos] a'), 'select'=>'href', 'type'=>'str', 'uploadFile'=>true)
    ),
    // Селектор URL для перехода на уровень ниже
    'deep'       => array(
        'selectors' => array(
            array(
                'selectors' => 'div[class=car] li[class=name] a', //10 ссылок на странице
                'select'    => 'href',
            ),
        ),
    ),

);