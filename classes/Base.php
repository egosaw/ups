<?php 

class Base
{
	public function log($str, $display=EGO_DEBUG){
		
		if($display)
			echo PHP_EOL.$str;
	}

	public function multi_implode($array, $glue=',') {
	    $ret = '';

	    foreach ($array as $item) {
	        if (is_array($item)) {
	            $ret .= $this->multi_implode($item, $glue) . $glue;
	        } else {
	            $ret .= $item . $glue;
	        }
	    }

	    $ret = substr($ret, 0, 0-strlen($glue));

	    return $ret;
	}

	public static function formatPrice($price){
		$price = preg_replace("/[^0-9\.\,]/", '', $price);
		$price = str_replace(",", ".", $price);
		return trim($price, '.');
	}

	/**
	* Приведение ссылки к абсолютному URI
	*
	* @param string $link ссылка (абсолютный URI, абсолютный путь на сайте, относительный путь)
	* @param string $base базовый URI (можно без "http://")
	* @return string абсолютный URI ссылки
	*/
	public static function uri2absolute(&$link, $base){

		//print_r(array($link, $base));
		
		if (!preg_match('~^(http://[^/?#]+)?([^?#]*)?(\?[^#]*)?(#.*)?$~i', $link.'#', $matchesLink))
			return false;
		
		if (!empty($matchesLink[1]))
			return $link;
		
		if (!preg_match('~^(http://)?([^/?#]+)(/[^?#]*)?(\?[^#]*)?(#.*)?$~i', $base.'#', $matchesBase))
			return false;
		
		if (empty($matchesLink[2])) {

			if (empty($matchesLink[3]))
				return 'http://'.$matchesBase[2].$matchesBase[3].$matchesBase[4];

			return 'http://'.$matchesBase[2].$matchesBase[3].$matchesLink[3];
		}

		$pathLink = explode('/', $matchesLink[2]);

		if ($pathLink[0] == '')
			return 'http://'.$matchesBase[2].$matchesLink[2].$matchesLink[3];
		
		$pathBase = explode('/', preg_replace('~^/~', '', $matchesBase[3]));

		if (sizeOf($pathBase) > 0)
			array_pop($pathBase);
		
		foreach ($pathLink as $p) {

			if ($p == '.') {
				continue;

			}elseif ($p == '..'){
				if (sizeOf($pathBase) > 0)
					array_pop($pathBase);
				
			}else{
				array_push($pathBase, $p);
			}
		}

		return 'http://'.$matchesBase[2].'/'.implode('/', $pathBase).$matchesLink[3];
	}
}