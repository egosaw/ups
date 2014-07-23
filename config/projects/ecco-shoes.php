<?php 

return array(
			
	// Для отдельного проекта можно переопределить способ экспорта
	'export' => array(
		'class'  => 'Csv',
		array(
			'csvDir'    => 'export',
			'fname'     => '',
			'delimiter' => ';',
			'charset'   => 'UTF-8',
			'chunk'     => 500,
		)
	),

	// Для отдельного проекта можно переопределить способ загрузки данных из сети
	'import' => array(
		'class'  => 'Fgc',
		array(
			'cacheEnabled' => true,
			'cacheDir'     => 'cache',
			'cacheTime'    => 3600,
		)
	),

	// Селекторы данных которые необходимо найти
	'attributes' => array(
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
	),

	// Страница входа для поиска товаров
	'url' => 'http://www.ecco-shoes.ru/',

	'exclude' => array(
		'http://www.ecco-shoes.ru/',
	),

	// Селектор URL для перехода на уровень ниже
	'deep' => array(

		'selectors' => array(

			// Женская обувь
			array(
				'selectors' => 'li[class=women l1 exp] ul[class=submenu]',
				'offset'   => 0,
				// Ниже
				'deep'     => array(
					'selectors' => 'li[class=exp] ul',
					'offset'    => 0,
					// Еще ниже
					'deep'     => array(
						'selectors' => 'li a',
						'select'    => 'href',
					),
					
				),
			),

			// Мужская обувь
			array(
				'selectors' => 'li[class=men l1 exp] ul[class=submenu]',
				'offset'    => 0,
				// Ниже
				'deep'     => array(
					'selectors' => 'li[class=exp] ul',
					'offset'    => 0,
					// Еще ниже
					'deep'     => array(
						'selectors' => 'li a',
						'select'    => 'href',
					),
					
				),
			),
			

		),

		// Селектор URL для перехода глубже относительное селекторов выше; в данном случае непосредственно к товарам
		'deep' => array(

			'selectors' => array(
				array(
					'selectors' => 'ul[class=models] li a', 
					'select'    => 'href'
				), 
			),

		),

	),
);