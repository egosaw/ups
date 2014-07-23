<?php 

return array(

	// Для отдельного проекта можно переопределить способ экспорта
	'export' => array(
		'class'  => 'Csv',
		array(
			'csvDir'    => 'export',
			'fname'     => 'carlopazolini',
			'delimiter' => ';',
			'charset'   => 'UTF-8',
			'chunk'     => 500,
		)
	),

	// Селекторы данных которые необходимо найти
	'attributes' => array(
		'Сезон коллекции'       => array('type'=>'single'),
		'Вид сезона'            => array('type'=>'single'),
		'Название коллекции'    => array('type'=>'single'),
		'Первая цена'           => array('selectors'=>array('p[class=price size15] del'), 'filters'=>array('Ego::formatPrice'), 'type'=>'single'),
		'Текущая цена'          => array('selectors'=>array('p[class=price size25]'), 'filters'=>array('Ego::formatPrice'), 'type'=>'single', 'requireied'=>true),
		'Название модели'       => array('selectors'=>array('div[id=description] h4'), 'type'=>'single'),
		'Уникальный код модели' => array('selectors'=>array('div[id=description] h4'), 'type'=>'single'),
		'Линейка размеров'      => array('selectors'=>array('div[id=productSize] a'), 'type'=>'single'),
		
		'Пол'                   => array(

			'fromUrl'=>array(

				'/men/'   => 'Мужской',
				'/women/' => 'Женский'
			), 'type'=>'single'
		),

		'Тип обуви'             => array(

			'fromUrl'=>array(

				'/pumps'        => 'ТУФЛИ',
				'/heel-sandals' => 'БОСОНОЖКИ',
				'/wedges'       => 'НА ТАНКЕТКЕ',
				'/ballet-flats' => 'БАЛЕТКИ',
				'/moccasins'    => 'МОКАСИНЫ',
				'/sneakers'     => 'СНИКЕРЫ',
				'/sandals'      => 'САНДАЛИИ',
				'/lace-ups'     => 'НА ШНУРКАХ',
				'/moccasins'    => 'МОКАСИНЫ',
				'/sneakers'     => 'СНИКЕРЫ',
				'/sandals'      => 'САНДАЛИИ',
				'/lace-ups-and-loafers'  => 'НА ШНУРКАХ И ЛОФЕРЫ',
				'/loafers-and-moccasins' => 'ПОЛУБОТИНКИ',
			), 'type'=>'single'
		),
		
		'Возможные цвета'       => array(

			'selectors'=>array(
				
				array(
					'selectors'=>'div[id=description]', 
					'offset' => 0, 
					'deep'   => array(
						'selectors'=>'div[class=description]', 
						'offset' => 0, 
						'deep'   => array(
							'selectors'=>'p', 
							'offset' => 0,
							'select' => 'plaintext',
						),
					),
				),


			),
			'replace' => array('Цвет:'=>'', '.'=>''),
			'filters' => array('strip_tags', 'trim'),
			'type'=>'single'
		),
		
		'Материал изготовления' => array(

			'selectors'=>array(
				
				array(
					'selectors'=>'div[id=description]', 
					'offset' => 0, 
					'deep'   => array(
						'selectors'=>'div[class=description]', 
						'offset' => 0, 
						'deep'   => array(
							'selectors'=>'p', 
							'offset' => 1,
							'select' => 'plaintext',
						),
					),
				),


			),
			'type'=>'single',
			'filters' => array('strip_tags', 'trim'),
		),
		
		'Страна изготовления'   => array(

			'selectors'=>array(
				
				array(
					'selectors'=>'div[id=description]', 
					'offset' => 0, 
					'deep'   => array(
						'selectors'=>'div[class=description]', 
						'offset' => 0, 
						'deep'   => array(
							'selectors'=>'p', 
							'offset' => -1,
							'select' => 'plaintext',
						),
					),
				),


			),
			'replace' => array('Страна производства:'=>''),
			'filters' => array('strip_tags', 'trim'),
			'type'=>'single'
		),

	),

	// Страница входа для поиска товаров
	'url' => 'http://www.carlopazolini.com/ru/',

	// todo: не всегда удаляются URL'ы
	'exclude' => array(
		'http://www.carlopazolini.com/ru/',
		'http://www.carlopazolini.com/ru/cpworld/events',
		'http://www.carlopazolini.com/ru/cpworld/press',
		'http://www.carlopazolini.com/ru/cpworld/store-design',
		'http://www.carlopazolini.com/ru/collection/women/handbags',
		'http://www.carlopazolini.com/ru/collection/women/belts',
		'http://www.carlopazolini.com/ru/collection/men/bags',
		'http://www.carlopazolini.com/ru/collection/men/belts',
	),

	// Селектор URL для перехода на уровень ниже
	'deep' => array(

		// todo: сделать переменные из plaintext для подстановки в attributes
		'selectors' => array(
			array(
				'selectors' => 'div[id=submenu] li[class=woman] a', 
				'offset'    => 1,
				'select'    => 'href',
			), 
			array(
				'selectors' => 'div[id=submenu] li[class=men] a',
				'offset'    => 1,
				'select'    => 'href',
			),
		),

		// Селектор URL для перехода непосредственно к товарам
		'deep' => array(

			'selectors' => array(

				array(
					'selectors' => 'ul[id=collection-cat] li a', 
					'select'    => 'href',
					
					/*'variables' => array(
						'Тип обуви'  => array(
							'selectors'=>array(
								array(
									'selectors'=>'span[class=current]',
									'offset' => 0,
									'select' => 'plaintext',
								),
							),
							'type'    => 'single',
							'filters' => array('trim')
						),
						'Переменная' => 'Значение по умолчанию'
					),*/
					
				), 
			),

			'deep' => array(
				
				'selectors' => array(
					array(
						'selectors' => 'div[id=collection_content] a[class=xs-link]', 
						'select'    => 'href'
					), 
				),

			),
		),
	),
);