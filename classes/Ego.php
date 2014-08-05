<?php

class Ego extends Base
{

    protected $config;
    protected $projects;

    protected $input;
    protected $output;

    protected $current_url;
    protected $variables;
    protected $urlMap = array();

    public static function createApplication($config)
    {
        return new Ego($config);
    }

    function __construct($config)
    {
        if (!file_exists($config))
            die("No such file {$config}" . PHP_EOL);

        spl_autoload_register(function ($class) {
            include 'classes/' . $class . '.php';
        });

        $this->config = require $config;
        $this->projects = $this->config['projects'];

        $this->output = new $this->config['export']['class']($this->config['export'][0]);
		$this->input = new $this->config['import']['class']($this->config['import'][0]);
	}

    public function run($name = null)
    {

        if ($name == null)
            foreach ($this->projects AS $name => $params)
                $this->runProject($name);
        else
            $this->runProject($name);

    }

    protected function runProject($name, $url = false, $ttl = 1)
    {

        if (!isset($this->projects[$name]))
            die("Project '{$name}' not found in config file" . PHP_EOL);

        $this->config = $this->projects[$name];
        //если повторный вызов
        if ($url) {
            $this->config["url"] = $url;
            $this->config['pagination']['ttl'] = $ttl;
        }

        if (isset($this->config['export']))
            $this->output = new $this->config['export']['class']($this->config['export'][0]);
		
		if (isset($this->config['import']))
            $this->input = new $this->config['import']['class']($this->config['import'][0]);

		$this->log("Run project " . $name, true);

		//$this-> test();die();

		// Получить все ссылки на товары
		$this->urlMap = $this->deep($this->config['url'], $this->config['deep']);

        // Получить все ссылки пагинации
        $pagination = $this->deep($this->config['url'], $this->config['pagination']);

        //var_dump(file_get_contents($this->config["url"]));

		// Пройтись по каждой ссылке
		foreach ($this->urlMap as $url) {

            $this->log("Parse: " . $url);

            // Распарсить данные по URL
            $this->current_url = $url;
            $data = $this->input->get($this->current_url);
            $data = $this->parse($data);

            print_r($data);

            if (!$data)
                continue;

            $data['url'] = $this->current_url;

            //Записать в базу данные
            $this->output->set($data);

            //die("\nFinish\n");
        }
        //Если есть пагинаторы на странице и количество переходов не исчерпано,
        //то заново запускаем проект, с $url и уменьшеным на единицу ttl
        if ((count($pagination) > 1) && ($this->config['pagination']['ttl'] != 0)) {
            $this->config['pagination']['ttl']--;
            foreach ($pagination as $key => $value) {
                if ($key != count($pagination) - 1) {
                    $this->runProject($name, $value, $this->config['pagination']['ttl']);
                }
            }
        }
	}

    protected function test2()
    {
        $this->current_url = 'http://www.carlopazolini.com/ru/collection/women/shoes/pumps/fl-glf3-20';
        //$this->current_url = 'http://www.carlopazolini.com/ru/collection/women/shoes/lace-ups-and-loafers/fg-sma1-4r1';
        $data = $this->input->get($this->current_url);
        $data = $this->parse($data);
        $data['url'] = $this->current_url;
        print_r($data);
        //$this->output->set($data);
    }

    protected function test()
    {

        // Получем все URL адреса по которым необходимо собрать товары
        $data = $this->deep($this->config['url'], $this->config['deep']);
        print_r($data);
    }

    protected function deep($url, $params, $result = array())
    {

        if (array_search($url, $this->config['exclude'])) {
            $this->log("Exclude URL: {$url}", true);
            return $result;
        } else
            $this->log("Deep URL: {$url}", true);

        $data = $this->input->get($url);

        if ($data) {

            $basehref = isset($this->config['basehref']) ? $this->config['basehref'] : $url;

            $deep = $this->parse($data, array('urls' => array('selectors' => $params['selectors'], 'filters' => array('Ego::uri2absolute' => $basehref))));

            $result = $this->merge($result, array_merge($deep['urls'], array($url)), $this->config['exclude']);
        }
        if (isset($params['deep'])) {
            foreach ($deep['urls'] as $durl)
                $result = $this->deep($durl, $params['deep'], $result);
        }


        return $this->merge($result, array(), $this->config['exclude']);
    }

    protected function merge($arr1, $arr2, $exclude)
    {
        $result = array_merge($arr1, $arr2);

        foreach ($result as $key => $url) {

            if (array_search($url, $exclude)) {
                unset($result[$key]);
                $this->log("Exclude URL: {$url}", true);
            }
        }

        return array_unique($result);
    }

    protected function applyFilters($data, $filters)
    {

        if (!is_array($filters))
            return $data;

        if (!isset($filters['filters']) OR !is_array($filters['filters']))
            $filters['filters'] = array();

        if (!is_array($data))
            $data = array($data);

        // Replace
        if (isset($filters['replace']) AND is_array($filters['replace']))
            foreach ($data as $key => $value)
                $data[$key] = str_replace(array_keys($filters['replace']), array_values($filters['replace']), $value);

        // Filters
        foreach ($filters['filters'] as $filter => $args) {
            $arguments = is_array($args) ? $args : array_fill_keys($data, $args);
            $data = is_int($filter) ? array_map($args, $data) : array_map($filter, $data, $arguments);
        }

        // Default
        if (isset($filters['default']) AND !empty($filters['default'])) {

            if (count($data) == 0) {

                $data = $filters['default'];

            } else {

                foreach ($data as $key => $value)
                    $data[$key] = empty($value) ? $filters['default'] : $value;
            }
        }

        return $data;
    }

    protected function parseRecursive($html, $selector)
    {

        if ($html == null)
            return $html;

        // Выдернуть данные по селектору с учетом offset
        if (isset($selector['offset']))
            $html = $html->find($selector['selectors'], $selector['offset']);
        else
            $html = $html->find($selector['selectors']);

        // проверить наличие вложенного селектора
        if (isset($selector['deep'])) {
            $html = $this->parseRecursive($html, $selector['deep']);

        } elseif (is_array($html)) {

            $result = array();

            foreach ($html AS $item)
                $result[] = $item->{$selector['select']};

            $html = $result;

        } elseif ($html instanceof simple_html_dom)
            $html = $html->{$selector['select']};


        return $html;
    }

    protected function parse($data, $attributes = null)
    {


        $attributes = $attributes == null ? $this->config['attributes'] : $attributes;

        $result = array();
        $html = str_get_html($data);

        if ($html instanceof simple_html_dom) {

            //$params['uploadFile'] = false;// загружать файлы или нет
            //$params['manyFiles'] = false;// указывается, если файлов много по одном селектору
            //$params['required'] = false;// обязательность поля

            // пройтись по атрибутам
            foreach ($attributes AS $name => $params) {

                $result[$name] = array();

                if (isset($params['selectors'])) {

                    $params['select'] = isset($params['select']) ? $params['select'] : 'plaintext';

                    foreach ($params['selectors'] as $selector) {

                        //Проверка на вложенный селектор
                        if (is_array($selector)) {

                            $temp = $this->parseRecursive($html, $selector);

                            if (is_array($temp))
                                $result[$name] = array_merge($result[$name], $temp);
                            else
                                $result[$name][] = $temp;

                        } else {

                            $i = 0;
                            foreach ($html->find($selector) AS $element) {
                                if ($this->checkParam($params, 'uploadFile')) {
                                    $this->uploadFile($element->{$params['select']});
                                }

                                $result[$name][] = $element->{$params['select']};
                            }

                        }

                        $result[$name] = array_unique($result[$name]);

                        $result[$name] = $this->applyFilters($result[$name], $params);

                    }

                } elseif (isset($params['fromUrl'])) {

                    foreach ($params['fromUrl'] AS $pattern => $value) {
                        if (stristr($this->current_url, $pattern))
                            $result[$name] = $value;
                    }

                }


                if (isset($params['type']) AND $params['type'] != 'multi' AND is_array($result[$name])) {
                    $result[$name] = implode(",", $result[$name]); //todo: Вынести в конфиг разделитель для implode
                }
                // Проверить, если атрибут обязательный и пустой, пропускаем всю партию
                if ($this->checkParam($params, 'required') AND empty($result[$name])) {
                    return false;
                }

            }

            $html->clear();
        }

        return $result;
    }


    public function delDomainFromPath($path = false)
    {

        if ($path !== false) {
            return str_replace($this->config['imgDomain'], '', $path);
        }

        return false;
    }


    public function uploadFile($file = false, $parentDir = false)
    {
        if ($file !== false) {

            if ($parentDir == false) {
                $parentDir = $this->config['pathToFiles'];
            }

            $newFile = $this->delDomainFromPath($file); //делаем относительные пути к файлам

            preg_match('%(.*)/%', $newFile, $arPath); //забираем путь до файла

            if ($arPath[1] !== '') { //если необходимо, то создаем директории для хранения файлов
                $this->createPath($arPath[1]);
            }

            $fullPath = $parentDir . $newFile;

            $content = file_get_contents($file);

            if ($content !== false) {
                $answer = file_put_contents($fullPath, $content);

                if ($answer == false) {
                    $this->log("Ошибка загрузки файла: " . $fullPath . "\n");
                } else {
                    $this->log("Файл успешно загружен: " . $fullPath . "\n");
                }
            } else {
                $this->log("Ошибка загрузки файла: " . $fullPath . "\n");
            }


        }

        return false;
    }

    public function createPath($path = false)
    {
        if ($path !== false) {

            $parentDir = $this->config['pathToFiles'];

            $fullDirPath = $parentDir . $path;

            if (!file_exists($fullDirPath)) {
                if (mkdir($fullDirPath, 0755, true)) {
                    //return $this->log("Создана новая директория: ".$fullDirPath."\n");
                }
            } else {
                //return $this->log("Директория существует: ".$fullDirPath."\n");
            }
        }
        //return $this->log("Ошибка: необходимо передать путь к директории\n");
    }


    public function checkParam($arr, $key)
    {

        if (array_key_exists($key, $arr)) {
            if ($arr[$key] == true) {
                return true;
            }
        }
        return false;
    }


    /*
    // Извлечение переменных для хлебных крошек. Временно отказываемся от этого
    // Взамен используем извлечение данных fromUrl
    protected function extractVariables($html, $selectors){

        $result = array();

        foreach($selectors AS $name => $params){

            if(is_array($params)){
            // Если переменная указана как селектор, распарсить и сохранить

                $result = array_merge($result, $this->parse($html, array($name => $params)) );

            }else{
            // Если переменная задана, сохранить ее

                $result[$name] = $params;
            }

        }

        return $result;

    }
    */

}