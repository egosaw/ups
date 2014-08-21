<?php

/**
 * Created by PhpStorm.
 * User: bug
 * Date: 22.07.14
 * Time: 13:06
 */
class Network extends Base implements Input
{
    const UA_CHROME = 'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36';
    const UA_OPERA = 'Opera/9.80 (Windows NT 6.0) Presto/2.12.388 Version/12.14';
    const UA_IE = 'Mozilla/5.0 (compatible; MSIE 10.6; Windows NT 6.1; Trident/5.0; InfoPath.2; SLCC1; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET CLR 2.0.50727) 3gpp-gba UNTRUSTED/1.0';
    const UA_FF = 'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0';
    const UA_SAFARI = 'Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5355d Safari/8536.25';
    const UA_OPERA_MINI = 'Opera/9.80 (J2ME/MIDP; Opera Mini/9.80 (S60; SymbOS; Opera Mobi/23.348; U; en) Presto/2.5.25 Version/10.54';
    const UA_AWB = 'Mozilla/5.0 (Linux; U; Android 4.0.3; ko-kr; LG-L160L Build/IML74K) AppleWebkit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30';
    const HTTP_CODES = '../assets/http_codes.ini';
    const UA_RANDOM = true;
    const FILE_UPLOAD = false;
    private $ua_array = array(self::UA_CHROME, self::UA_OPERA, self::UA_IE, self::UA_FF, self::UA_SAFARI, self::UA_OPERA_MINI);
    private $domain = false;
    private $imgName = false;
    private $savePath = '/home/snowmedia/public_html/monitor.snow-media.ru/base/gruber/runtime/captcha/';
    private $apikey = '7400ba4b48b966b063d8541a45e001be';
    private $antidomain = 'antigate.com';
    protected $cacheDir = 'cache';
    protected $cacheEnabled = false;
    protected $cacheTime = 0;
    protected $dir;
    public $path;
    public $debug = false;
    public $logging = true;
    public $log_file = '../runtime/log/logfile.txt';
    public $max_size_log_file = 4096;
    public $driver;
    public $user_agent = self::UA_CHROME;
    public $tor = true;
    public $proxy = '192.168.3.6:9050';
    public $proxy_timeout = 500;
    public $change_ip = '127.0.0.1:9051';
    public $pass = '';
    public $cookies = 'cookies.txt';
    public $headers = array();
    public $html;
    public $antigate = false;
    public $antigate_login;
    public $antigate_passw;
    public $antigate_limit = 1000;

    function __construct($config)
    {
        if (!function_exists('curl_init')) {
            if ($this->logging) {
                $this->log('You must have CURL enabled in order to use this extension.', true);
            }
        }

        $this->dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;

        foreach ($config AS $key => $value) {
            $this->$key = $value; // устанавливаем параметры кеширования $cacheEnabled, $cacheDir, $cacheTime
        }

        if (!file_exists($this->dir . $this->cacheDir) OR !is_dir($this->dir . $this->cacheDir)) {
            if (!mkdir($this->dir . $this->cacheDir)) {
                die('Failed to create directory: ' . $this->dir . $this->cacheDir);
            }
        }

        $this->cookies = $this->dir . 'runtime/cookies/' . $this->cookies;
    }

    private function magic_curl($url, $method, $content = true, $options = array())
    {
        if ($ch = curl_init()) {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            if (!$content) {
                curl_setopt($ch, CURLOPT_NOBODY, true);
            }
            if ($method == "POST") {
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->proxy_timeout);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $options);
            }
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookies);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookies);

            if ($this->tor) {
                curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
                curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
            }
            $ss = curl_exec($ch);
            if (!$ss === false) {
                if (!$content) {
                    $this->headers = self::http_parse_headers($ss);
                } else {
                    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                    $headers = substr($ss, 0, $header_size);
                    $arheaders = explode("\r\n\r\n", $headers);
                    $this->html = substr($ss, $header_size, strlen($ss) - $header_size);
                    foreach ($arheaders as $key => $value) {
                        if ($key != (count($arheaders) - 1)) {
                            $this->headers[] = self::http_parse_headers($value);
                        }
                    }
                }
                curl_close($ch);
                return $this;
            } else {
                if ($this->logging) {
                    $this->log('Ошибка curl: ' . curl_error($ch) . 'URL: ' . $url, true);
                }
                curl_close($ch);
                return false;
            }
        }
    }

    private static function http_parse_headers($raw_headers)
    {
        $headers = array();
        $key = '';

        foreach (explode("\n", $raw_headers) as $i => $h) {
            $h = explode(':', $h, 2);

            if (isset($h[1])) {
                if (!isset($headers[$h[0]])) {
                    $headers[$h[0]] = trim($h[1]);
                } elseif (is_array($headers[$h[0]])) {
                    $headers[$h[0]] = array_merge($headers[$h[0]], array(trim($h[1])));
                } else {
                    $headers[$h[0]] = array_merge(array($headers[$h[0]]), array(trim($h[1])));
                }

                $key = $h[0];
            } else {
                if (substr($h[0], 0, 1) == "\t") {
                    $headers[$key] .= "\r\n\t" . trim($h[0]);
                } elseif (!$key) {
                    $headers[0] = trim($h[0]);
                }
                trim($h[0]);
            }
        }
        preg_match('|HTTP/\d\.\d\s+(\d+)\s+.*|', $headers[0], $match);
        $headers['Code'] = $match[1];
        $http_codes = parse_ini_file(dirname(__FILE__) . DIRECTORY_SEPARATOR . self::HTTP_CODES);
        $headers['Text_code'] = $http_codes[$headers['Code']];
        return $headers;
    }

    public function get($url, $content = true, $dis_cache = false)
    {
        if ($this->cacheEnabled) {

            // ищем в кеше
            $file = $this->dir . $this->cacheDir . DIRECTORY_SEPARATOR . md5($url);

            // проверяем время файла
            if (file_exists($file) AND filectime($file) < $this->cacheTime) {
                $result = file_get_contents($file);
            }

            $this->log("Loaded from cache: " . md5($url));
        }

        if (isset($result)) {
            return $result;
        } else {
            return $this->loadData($url, "GET");
        }
    }

    public function post($url, $options = array(), $content = true)
    {
        if (!$url) {
            if ($this->logging) {
                if ($this->logging) {
                    $this->log('Неверный аргумент передан в POST в качестве ссылки URL: ' . $url, true);
                }
            }
        }
        if ($this->logging) {
            $this->log('POST Запрос. URL: ' . $url, true);
        }

        if ($this->cacheEnabled) {

            // ищем в кеше
            $file = $this->dir . $this->cacheDir . DIRECTORY_SEPARATOR . md5($url);

            // проверяем время файла
            if (file_exists($file) AND filectime($file) < $this->cacheTime) {
                $result = file_get_contents($file);
            }

            $this->log("Loaded from cache: " . md5($url));
        }

        if (isset($result)) {
            return $result;
        } else {
            return $this->loadData($url, "POST", true, $options);
        }

    }

    public function getAllInfo($url, $content = true)
    { //вывод полной инфы по запросу
        $data = $this->magic_curl($url, "GET", $content);
        return $data;
    }

    protected function loadData($url, $method = "GET", $content = true, $options = array())
    {

        if ($method == 'POST') {
            $data = $this->magic_curl($url, $method, $content, $options)->html; //получаем html-страницу
        }

        if ($method == 'GET') {
            $data = $this->magic_curl($url, $method, $content)->html; //получаем html-страницу
        }

        if ($this->cacheEnabled) { //если кеш вкл, то сохраняем страницу
            $file = $this->dir . $this->cacheDir . DIRECTORY_SEPARATOR . md5($url);
            file_put_contents($file, $data);
        }

        return $data;
    }


    public function changeUA($UA = self::UA_RANDOM)
    {
        if ($UA === self::UA_RANDOM) {
            $this->user_agent = $this->ua_array[array_rand($this->ua_array)];
        } else {
            $this->user_agent = $UA;
        }
        return true;
    }

    public function changeIp()
    {
        if ($this->tor) {
            list($tor_ip, $control_port) = explode(":", $this->change_ip, 2);

            $fp = fsockopen($tor_ip, $control_port, $errno, $errstr, 30);
            if (!$fp) { //обработчик ошибки соединения с тором
                if ($this->logging) {
                    $this->log('Невозможно открыть сокет для TOR', true);
                }
                return false;
            }

            fputs($fp, "AUTHENTICATE \"$this->pass\" \r\n");
            $response = fread($fp, 1024);
            list($code, $text) = explode(' ', $response, 2);
            if ($code != '250') {
                if ($this->logging) {
                    $this->log('Proxy. Ошибка аутентификации.' . $code . $text, true);
                }
                return false;
            }
            // шлём запрос на смену звена
            fputs($fp, "signal NEWNYM\r\n");
            $response = fread($fp, 1024);
            list($code, $text) = explode(' ', $response, 2);
            if ($code != '250') {
                if ($this->logging) {
                    $this->log('Proxy. Невозможно сменить IP' . $code . $text, true);
                }
                return false;
            }
            if ($this->logging) {
                $this->log('Proxy. IP изменен', true);
            }
            fclose($fp);
            return true;
        } else {
            if ($this->logging) {
                $this->log('Proxy. Включите свойство tor, пример $this->tor = true;', true);
            }
            return false;
        }
    }

    private function curl_get_content($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public function find($url)
    {
        if (!is_dir($this->savePath)) {
            if (!mkdir($this->savePath, 0777)) {
                if ($this->logging) {
                    $this->log('Captcha. Невозможно создать папку для хранений изображений капчи', true);
                }
            }
        }
        if (!$url) {
            if ($this->logging) {
                $this->log('Captcha. В качестве URL для поиска капчи передан неправильный параметр' . var_export($url, true), true);
            }
            return false;
        }
        $this->domain = parse_url($url)['host'];
        $this->imgName = array_pop(explode('/', $url));
        $content = $this->curl_get_content($url);
        preg_match_all('/<img[^>]+\>/i', $content, $match);
        $srcs = false;
        foreach ($match[0] as $img) {
            if (preg_match('%(?:sorry)|(?:captch)|(?:защитный код)|(?:код безопасности)%', $img)) {
                preg_match('%(?<=src\=\")(?:[^\s]+)(?=\")%', $img, $src);
                if (preg_match('%^http%', $src[0])) {
                    $srcs[] .= $src[0];
                } else {
                    if (!preg_match('%^\/%', $src[0])) {
                        $src[0] = '/' . $src[0];
                    }
                    $srcs[] .= $this->domain . $src[0];
                }
            }
        }
        if (!$srcs) {
            if ($this->logging) {
                $this->log('Captcha. По указанному URL картинок с капчей не найдено' . $url, true);
            }
        } else {
            foreach ($srcs as $image) {
                $this->imgName = array_pop(explode('/', $image));
                $this->path = $this->savePath . $this->imgName;
                $ch = curl_init($image);
                $fp = fopen($this->path, 'wb');
                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_exec($ch);
                curl_close($ch);
                fclose($fp);
            }
        }
        if ($this->logging) {
            $this->log('Captcha. Обнаружена капча по ссылке: ' . $url, true);
        }
        return $this->path;
    }

    public function recognize(
        $filename,
        $rtimeout = 5,
        $mtimeout = 120,
        $is_phrase = 0,
        $is_regsense = 0,
        $is_numeric = 0,
        $min_len = 0,
        $max_len = 0,
        $is_russian = 0
    )
    {
        if (!file_exists($filename)) {
            if ($this->logging) {
                $this->log('Antigate. Указанный файл не найден: ' . $filename, true);
            }
            return false;
        }
        $postdata = array(
            'method' => 'post',
            'key' => $this->apikey,
            'file' => '@' . $filename,
            'phrase' => $is_phrase,
            'regsense' => $is_regsense,
            'numeric' => $is_numeric,
            'min_len' => $min_len,
            'max_len' => $max_len,
            'is_russian' => $is_russian

        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://$this->antidomain/in.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            if ($this->logging) {
                $this->log('Antigate. CURL вернула ошибку: ' . curl_error($ch), true);
            }
            return false;
        }
        curl_close($ch);
        if (strpos($result, "ERROR") !== false) {
            if ($this->logging) {
                $this->log('Antigate. Сервер вернул ошибку: ' . $result, true);
            }
            return false;
        } else {
            $ex = explode("|", $result);
            $captcha_id = $ex[1];
            if ($this->logging) {
                $this->log(
                    'Antigate. Капча принята, ей присвоен идентификатор: ' . $captcha_id . 'Имя файла: ' . $filename, true);
            }
            $waittime = 0;
            if ($this->logging) {
                $this->log('Antigate. Капча в очереди. Ждем ' . $rtimeout . ' секунд', true);
            }
            sleep($rtimeout);
            while (true) {
                $result = file_get_contents("http://$this->antidomain/res.php?key=" . $this->apikey . '&action=get&id=' . $captcha_id);
                if (strpos($result, 'ERROR') !== false) {
                    if ($this->logging) {
                        $this->log('Antigate. Сервер вернул ошибку: ' . $result, true);
                    }
                    return false;
                }
                if ($result == "CAPCHA_NOT_READY") {
                    if ($this->logging) {
                        $this->log('Antigate. Капча еще не распознана', true);
                    }
                    $waittime += $rtimeout;
                    if ($waittime > $mtimeout) {
                        if ($this->logging) {
                            $this->log('Лимит времени' . $mtimeout . 'превышен', true);
                        }
                        break;
                    }
                    if ($this->logging) {
                        $this->log('Ждем ' . $rtimeout . ' секунд', true);
                    }
                    sleep($rtimeout);
                } else {
                    $ex = explode('|', $result);
                    if (trim($ex[0]) == 'OK') {
                        if ($this->logging) {
                            $this->log('Captcha. Картинка распознана. Ответ: ' . trim($ex[1]), true);
                        }
                        return trim($ex[1]);
                    }
                }
            }

            return false;
        }
    }
}