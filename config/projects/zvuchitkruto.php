<?php 

return array(

	// Для отдельного проекта можно переопределить способ экспорта
	'export' => array(
		'class'  => 'Csv',
		array(
			'csvDir'    => 'export',
			'fname'     => 'zvuchitkruto',
			'delimiter' => ';',
			'charset'   => 'UTF-8',
			'chunk'     => 500,
		)
	),

	// Селекторы данных которые необходимо найти
	'attributes' => array(
		'Цена'     => array('selectors'=>array('div[class=price]'), 'filters'=>array('Ego::formatPrice'), 'type'=>'single'),
		'Артикул'  => array('selectors'=>array('h1[id=skuValue]'), 'type'=>'single'),
		'Название' => array('selectors'=>array('h1[class=product-name]'), 'type'=>'single'),
		'Описание' => array('selectors'=>array('div[class=tab-content tabs-content selected]'), 'type'=>'single'),
		'Крошки'   => array('selectors'=>array('div[class=crumbs]'), 'type'=>'multi'),
		'Картинки' => array('selectors'=>array('figure[class=flexslider-carousel-item]'), 'select'=>'data-imagepicker-img', 'type'=>'multi'),
	),

	// Страница входа для поиска товаров
	'url'      => 'http://zvuchitkruto.ru/',

	'basehref' => 'http://zvuchitkruto.ru/',

	// todo: не всегда удаляются URL'ы
	'exclude' => array(

	),

	// Селектор URL для перехода на уровень ниже
	'deep' => array(

		'selectors' => array(
			array(
				'selectors' => 'div[class=tree-submenu-column] a', 
				'select'    => 'href',
			), 
		),

		// Селектор URL для перехода непосредственно к товарам
		'deep' => array(

			'selectors' => array(
				array(
					'selectors' => 'class[class=page-all]', 
					'select'    => 'href',
				),
				array(
					'selectors' => 'a[class=link-pv-name]',
					'select'    => 'href'
				),
			),
			'deep' => array(

				'selectors' => array(
					array(
						'selectors' => 'a[class=link-pv-name]',
						'select'    => 'href'
					),
				),

			),

		),
	),
);