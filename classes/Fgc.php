<?php 

class Fgc extends Base implements Input
{

	protected $dir;
	protected $cacheDir;
	protected $cacheEnabled = false;
	protected $cacheTime    = 0;

	function __construct($config){

		$this->dir      = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;

		foreach($config AS $key => $value)
			$this->$key = $value;

		if(!file_exists($this->dir.$this->cacheDir) OR !is_dir($this->dir.$this->cacheDir))
			if(!mkdir($this->dir.$this->cacheDir))
				die('Failed to create directory: '.$this->dir.$this->cacheDir);
	}

	//function __destruct(){}

	public function get($url){
		
		if($this->cacheEnabled){
			
			// ищем в кеше
			$file = $this->dir.$this->cacheDir.DIRECTORY_SEPARATOR.md5($url);

			// проверяем время файла
			if(file_exists($file) AND filectime($file) < $this->cacheTime )
				$result = file_get_contents($file);

			$this->log("Loaded from cache: ".md5($url));
		}

		return isset($result) ? $result : $this->loadData($url);
	}

	protected function loadData($url){

		$data = @file_get_contents($url);

		if($this->cacheEnabled){

			$file = $this->dir.$this->cacheDir.DIRECTORY_SEPARATOR.md5($url);

			file_put_contents($file, $data);
		}

		return $data;
	}
}