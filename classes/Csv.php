<?php 

class Csv extends Base implements Output
{

	protected $dir       = '';
	protected $csv_head  = null;
	protected $fname     = '';
	protected $count_str = 0;
	protected $csvfp     = false;
	protected $delim     = ";";
	protected $cfile     = 0;
	protected $limit     = 200;
	protected $charset   = 'UTF-8';
	protected $csvDir    = '';
	
	function __construct($config){            

		$this->dir      = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;

		foreach($config AS $key => $value)
			$this->$key = $value;

		if(!file_exists($this->dir.$this->csvDir) OR !is_dir($this->dir.$this->csvDir))
			if(!mkdir($this->dir.$this->csvDir))
				die('Failed to create directory: '.$this->dir.$this->csvDir);
	}

	function __destruct(){
	
		if($this->csvfp!=false)
			fclose($this->csvfp);
	}
	
	public function set($data){

		if($this->csv_head==null)
			$this->csv_head = array_keys($data);

		$data = $this->csvRowfilter($data);

		fputcsv($this->getFp(), $data, $this->delim);
		$this->count_str++;
		
		return;
	}

	protected function csvRowfilter($str){
		
		if(is_array($str)){
			foreach($str AS $k=>$v)
				$str[$k] = $this->csvRowfilter($v);
			
			return $str;
		}
		
		return str_replace(
			array(";", "\n", "\t", "\r", "\"", "'", PHP_EOL),
			array("",   "",   "",   "",   "",   "", ""     ),
			$str
		);
	}

	protected function getCsvName($path){
		
		$i = 1;
			
		// Сгенерировать имя нового файла
		while(file_exists($path.$this->fname."_".$i."_".$this->limit.".csv"))
			$i++;
		
		return $filename = $this->fname."_".$i."_".$this->limit.".csv";
	}
	
	protected function getFp(){
		
		$path = $this->dir.$this->csvDir . DIRECTORY_SEPARATOR;
		
		// Если нет указателя или достигнут лимит
		if(!$this->csvfp OR $this->count_str >= $this->limit){
			
			if($this->csvfp!=false)
				fclose($this->csvfp);
			
			$this->count_str = 0;
			$filename        = $this->getCsvName($path);
			$this->csvfp     = fopen($path.$filename, "w");
			
			// Записать заголовки в первую строку
			fputcsv($this->csvfp, $this->csv_head, $this->delim);
			
			$this->log("Create new file: ".$filename);
		}
		
		return $this->csvfp;
	}

}