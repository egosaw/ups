<?php 

return array(
	
	// Способ сохранения данных по умолчанию
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

	// Способ загрузки данных из сети по умолчанию
	'import' => array(
		'class'  => 'Fgc',
		array(
			'cacheEnabled' => true,
			'cacheDir'     => 'cache',
			'cacheTime'    => 3600,
		)
	),

	// Проекты
	'projects' => array(

		'carlopazolini.com' => require('projects/carlopazolini.php'),
		'ecco-shoes.ru'     => require('projects/ecco-shoes.php'),
		'zvuchitkruto.ru'   => require('projects/zvuchitkruto.php'),
		'qato.ru'           => require('projects/qato.ru.php'),
		'major-expert'      => require('projects/major-expert.php'),
		'ascgroup'      	=> require('projects/ascgroup.php'),
		'autonissan'      	=> require('projects/autonissan.php'),
		'infiniti-asc'      => require('projects/infiniti-asc.php'),
		'kia-asc'      		=> require('projects/kia-asc.php'),
		'audi-taganka'     	=> require('projects/audi-taganka.php'),
	),

);