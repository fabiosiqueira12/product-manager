<?php

use App\Models\Product;
use App\Core\AuthControl;
use App\Core\CacheControl;
use App\Models\Order;
use App\Services\MenuService;
use App\Services\Users\TypeUserService;

if (!function_exists('json')) {
    function json($data)
    {
        return json_encode($data);
    }
}

if (!function_exists('is_localhost')) {
    function is_localhost()
    {
        $whitelist = array(
            '127.0.0.1',
            '::1',
            'localhost'
        );

        if (in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('create_codigo')) {
    function create_codigo()
    {
        $permitted_chars = '01234567890123456789';
        return substr(str_shuffle($permitted_chars), 0, 12);
    }
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

if (!function_exists('create_token')) {
    function create_token()
    {
        //String com valor possíveis do resultado, os caracteres pode ser adicionado ou retirados conforme sua necessidade
        $basic = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $return = "";
        for ($count = 0; 150 > $count; $count++) {
            //Gera um caracter aleatorio
            $return .= $basic[rand(0, strlen($basic) - 1)];
        }
        return $return;
    }
}


if (!function_exists('price_format')) {
    if (!function_exists('price_format')) {
        function price_format($value,$withStr = true)
        {
            $price = number_format($value, 2, ',', '.');
            return $withStr ? "R$ {$price}" : "{$price}";
        }
    }
}

if (!function_exists('price_format2')) {
    function price_format2($value)
    {
        $price = number_format($value, 3, ',', '.');

        return "R$ {$price}";
    }
}

if (!function_exists('return_months')) {
    function return_months($month = -1)
    {
        $months = [
            1 => 'Janeiro',
            2 => 'Fevereiro',
            3 => 'Março',
            4 => 'Abril',
            5 => 'Maio',
            6 => 'Junho',
            7 => 'Julho',
            8 => 'Agosto',
            9 => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro'
        ];
        if ($month != -1) {
            return $months[intval($month)];
        } else {
            return $months;
        }
    }
}

if (!function_exists(('slugify'))) {
    function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}


if (!function_exists('get_lat_long')) {
    function get_lat_long($body)
    {   
        $query_body = http_build_query($body);
        $baseUrl = 'https://www.latlong.net/';
        // Iniciamos a função do CURL:
        $ch = curl_init($baseUrl);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_RETURNTRANSFER => 1,
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $resposta = curl_exec($ch);
        curl_close($ch);
        return $resposta;
    }
}

function DOMinnerHTML(DOMNode $element) 
{ 
    $innerHTML = ""; 
    $children  = $element->childNodes;
    foreach ($children as $child) 
    { 
        $innerHTML .= $element->ownerDocument->saveHTML($child);
    }
    return $innerHTML; 
}

if (!function_exists('user_logged')) {
    /**
     *
     * Retorna o usuário logado na sessão
     *
     *
     * @return \App\Models\User
     *
     */
    function user_logged()
    {
        $authControl = new AuthControl();
        return $authControl->getUserLogged();
    }
}

if (!function_exists('getTypesUsers')) {
    function getTypesUsers()
    {
        $service = new TypeUserService();
        return $service->getPermissoes();
    }
}

if (!function_exists('is_active')) {
    function is_active($router)
    {
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST] $_SERVER[REQUEST_URI]";
        if (strpos($actual_link, $router) !== false) {
            return true;
        }
        return false;
    }
}

if (!function_exists('mask_cpf')) {
    function mask_cpf($cpf)
    {
        $number = substr_replace($cpf, ".", 3, 0);
        $number = substr_replace($number, ".", 7, 0);
        $number = substr_replace($number, "-", 11, 0);
        return $number;
    }
}

if (!function_exists('mask_cnpj')){
    function mask_cnpj($cnpj)
    {
        $str = preg_replace("/\D/", '', $cnpj);
        return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $str);
    }
}

if (!function_exists('format_phone_send')) {
    function format_phone_send($numero)
    {
        $numero = str_replace(['(', ')', ' ', '-'], ['', '', '', ''], $numero);
        if (strlen($numero) == 11) {
            if ($numero[2] == 9) {
                $start =  substr($numero, 0, 2);
                $end = substr($numero, 3, 10);
                $numero = $start . $end;
            }
        }
        $numero = '55' . $numero;
        return $numero;
    }
}

if (!function_exists('return_pagination')) {
    function return_pagination($actualPage, $numPages, $nameController)
    {
        $pagination = [];
        for ($i = 0; $i < $numPages; $i++) {
            $isActive = false;
            if (($i + 1) == $actualPage) {
                $isActive = true;
            }
            $pagination[] = (object)['link' => '/' . $nameController . '?pag=' . ($i + 1), 'active' => $isActive];
        }
        return $pagination;
    }
}

if (!function_exists('validate_cnpj')) {
    function validate_cnpj($cnpj = null)
    {

        $cnpj = preg_replace('/[^0-9]/', '', (string)$cnpj);
        // Valida tamanho
        if (strlen($cnpj) != 14)
            return false;
        // Valida primeiro dígito verificador
        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
            $soma += $cnpj{
                $i} * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;
        if ($cnpj{
            12} != ($resto < 2 ? 0 : 11 - $resto))
            return false;
        // Valida segundo dígito verificador
        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
            $soma += $cnpj{
                $i} * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;
        return $cnpj{
            13} == ($resto < 2 ? 0 : 11 - $resto);
    }
}

if (!function_exists('validate_cpf')) {
    function validate_cpf($cpf = null)
    {

        // Verifica se um número foi informado
        if (empty($cpf)) {
            return false;
        }

        // Elimina possivel mascara
        $cpf = preg_replace("/[^0-9]/", "", $cpf);
        $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

        // Verifica se o numero de digitos informados é igual a 11 
        if (strlen($cpf) != 11) {
            return false;
        }
        // Verifica se nenhuma das sequências invalidas abaixo 
        // foi digitada. Caso afirmativo, retorna falso
        else if (
            $cpf == '00000000000' ||
            $cpf == '11111111111' ||
            $cpf == '22222222222' ||
            $cpf == '33333333333' ||
            $cpf == '44444444444' ||
            $cpf == '55555555555' ||
            $cpf == '66666666666' ||
            $cpf == '77777777777' ||
            $cpf == '88888888888' ||
            $cpf == '99999999999'
        ) {
            return false;
            // Calcula os digitos verificadores para verificar se o
            // CPF é válido
        } else {

            for ($t = 9; $t < 11; $t++) {

                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf{
                        $c} * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf{
                    $c} != $d) {
                    return false;
                }
            }

            return true;
        }
    }
}


if (!function_exists('get_dia_semana')) {
    function get_dia_semana($timestamp)
    {
        $timestamp = strtotime($timestamp);
        $date = getdate($timestamp);
        $diaSemana = $date['weekday'];
        if (preg_match('/(sunday|domingo)/mi', $diaSemana)) $diaSemana = 'Domingo';
        else if (preg_match('/(monday|segunda)/mi', $diaSemana)) $diaSemana = 'Segunda';
        else if (preg_match('/(tuesday|terça)/mi', $diaSemana)) $diaSemana = 'Terça';
        else if (preg_match('/(wednesday|quarta)/mi', $diaSemana)) $diaSemana = 'Quarta';
        else if (preg_match('/(thursday|quinta)/mi', $diaSemana)) $diaSemana = 'Quinta';
        else if (preg_match('/(friday|sexta)/mi', $diaSemana)) $diaSemana = 'Sexta';
        else if (preg_match('/(saturday|sábado)/mi', $diaSemana)) $diaSemana = 'Sábado';
        return $diaSemana;
    }
}

if (!function_exists('vencimento_boleto')) {
    function vencimento_boleto($data)
    {
        $quant = 3;
        $data = date('Y-m-d', strtotime($data . '+ ' . $quant . ' days'));
        //Data planejada para o vencimento
        if (get_dia_semana($data) == ("Sábado")) {
            $data = date('Y-m-d', strtotime($data . ' + 2 days'));
        } else if (get_dia_semana($data) == ("Segunda")) {
            $data = date('Y-m-d', strtotime($data . ' + 2 days'));
        } else if (get_dia_semana($data) == ("Domingo")) {
            $data = date('Y-m-d', strtotime($data . '+ 1 days'));
        }
        return $data;
    }
}

if (!function_exists('getBrowser')) {
    function getBrowser()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($user_agent, 'MSIE') !== FALSE)
            return 'Internet explorer';
        elseif (strpos($user_agent, 'Edge') !== FALSE) //Microsoft Edge
            return 'Microsoft Edge';
        elseif (strpos($user_agent, 'Trident') !== FALSE) //IE 11
            return 'Internet explorer';
        elseif (strpos($user_agent, 'Opera Mini') !== FALSE)
            return "Opera Mini";
        elseif (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR') !== FALSE)
            return "Opera";
        elseif (strpos($user_agent, 'Firefox') !== FALSE)
            return 'Mozilla Firefox';
        elseif (strpos($user_agent, 'Chrome') !== FALSE)
            return 'Google Chrome';
        elseif (strpos($user_agent, 'Safari') !== FALSE)
            return "Safari";
        else
            return 'Não detectado';
    }
}

if (!function_exists('primeiro_name')) {
    function primeiro_nome($name)
    {
        $nome = Explode(" ", $name);
        $primeiro_nome = $nome[0];
        return $primeiro_nome;
    }
}

if (!function_exists('getOs')) {
    function getOS()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $os_platform    =   "Não detectado";
        $os_array       =   array(
            '/windows nt 10/i'     =>  'Windows', //Windows 10 
            '/windows nt 6.3/i'     =>  'Windows', // Windows 8.1
            '/windows nt 6.2/i'     =>  'Windows', // Windows 8
            '/windows nt 6.1/i'     =>  'Windows', // Windows 7
            '/windows nt 6.0/i'     =>  'Windows', // Windows Vista
            '/windows nt 5.2/i'     =>  'Windows', //Windows Server
            '/windows nt 5.1/i'     =>  'Windows', // Windows XP
            '/windows xp/i'         =>  'Windows', // Windows XP
            '/windows nt 5.0/i'     =>  'Windows', // windows 2000
            '/windows me/i'         =>  'Windows',
            '/win98/i'              =>  'Windows',
            '/win95/i'              =>  'Windows',
            '/win16/i'              =>  'Windows',
            '/macintosh|mac os x/i' =>  'Macintosh',
            '/mac_powerpc/i'        =>  'Macintosh',
            '/linux/i'              =>  'Linux',
            '/ubuntu/i'             =>  'Linux',
            '/iphone/i'             =>  'iPhone',
            '/ipod/i'               =>  'iPod',
            '/ipad/i'               =>  'iPad',
            '/android/i'            =>  'Android',
            '/blackberry/i'         =>  'BlackBerry',
            '/webos/i'              =>  'Mobile'
        );

        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $os_platform    =   $value;
            }
        }
        return $os_platform;
    }
}

if (!function_exists('get_type_dispositivo')) {
    function get_type_dispositivo()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $os_platform    =   "Não detectado";
        $os_array       =   array(
            '/windows nt 10/i'     =>  'Área de trabalho', //Windows 10 
            '/windows nt 6.3/i'     =>  'Área de trabalho', // Windows 8.1
            '/windows nt 6.2/i'     =>  'Área de trabalho', // Windows 8
            '/windows nt 6.1/i'     =>  'Área de trabalho', // Windows 7
            '/windows nt 6.0/i'     =>  'Área de trabalho', // Windows Vista
            '/windows nt 5.2/i'     =>  'Área de trabalho', //Windows Server
            '/windows nt 5.1/i'     =>  'Área de trabalho', // Windows XP
            '/windows xp/i'         =>  'Área de trabalho', // Windows XP
            '/windows nt 5.0/i'     =>  'Área de trabalho', // windows 2000
            '/windows me/i'         =>  'Área de trabalho',
            '/win98/i'              =>  'Área de trabalho',
            '/win95/i'              =>  'Área de trabalho',
            '/win16/i'              =>  'Área de trabalho',
            '/macintosh|mac os x/i' =>  'Área de trabalho',
            '/mac_powerpc/i'        =>  'Área de trabalho',
            '/linux/i'              =>  'Área de trabalho',
            '/ubuntu/i'             =>  'Área de trabalho',
            '/iphone/i'             =>  'Mobile',
            '/ipod/i'               =>  'Mobile',
            '/ipad/i'               =>  'Mobile',
            '/android/i'            =>  'Mobile',
            '/blackberry/i'         =>  'Mobile',
            '/webos/i'              =>  'Mobile'
        );

        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $os_platform    =   $value;
            }
        }
        return $os_platform;
    }
}

if (!function_exists('get_client_ip')) {
    function get_client_ip()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
            {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
            {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}

if(!function_exists('validate_cep'))
{
    function validate_cep($cep)
    {
        if (preg_match('/[0-9]{5,5}([-]?[0-9]{3})?$/', $cep)){
            return true;
        }else{
            return false;
        }
    }
}

if (!function_exists('image')) {
    /**
     * Retorna imagem com crop
     *
     * @param string $url
     * @param array $parans
     * @param boolean $withPath
     * @return string
     */
    function image($url,$parans,$withPath = false)
    {
        if (strpos($url, 'default.jpg') !== false) {
            return $url;
        }
        if (strpos($url, '.svg') !== false) {
            return base_url_new() . "assets/uploads/" . $url;
        }
        if (strpos($url, '.ico') !== false) {
            return base_url_new() . "assets/uploads/" . $url;
        }
        $s = explode(DIRECTORY_SEPARATOR,$url);
        $url_image = isset($s[count($s) - 1]) ? $s[count($s) - 1] : $url;
       /*  return base_url_new() . 'assets/uploads/' . $url_image; */
        return base_url_new() . 'img/crop?img='. $url_image . "&" . http_build_query($parans);
    }
}

if (!function_exists('get_location_by_ip')) {
    function get_location_by_ip($ip)
    {
        $ch = curl_init('http://ipinfo.io/' . $ip . '/geo');
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_RETURNTRANSFER => 1,
        ]);
        $resposta = curl_exec($ch);
        curl_close($ch);
        return json_decode($resposta);
    }
}

/**
 * Retorna o peso formatado com abreviação
 *
 * @param float $peso
 * @param boolean $withAbreviacao
 * @return string
 */
function format_peso($peso,$withAbreviacao = true)
{  
    if ($peso == 0){
        return "";
    }
    $abreviacaoHelper = "";
    if ($peso >= 1000){
        $peso = $peso/1000;
        $peso = number_format($peso,2,",",".");
        $abreviacaoHelper = "KG";
    }else{
        $abreviacaoHelper = "G";
    }
    return $withAbreviacao ? $peso . $abreviacaoHelper : $peso;  
}


if (!function_exists('base_url_new')) {
    function base_url_new($atRoot = false, $atCore = false)
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            $http = is_localhost() ?  'http' : 'https';
            $hostname = $_SERVER['HTTP_HOST'];
            $dir =  str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

            $core = preg_split('@/@', str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath(dirname(__FILE__))), NULL, PREG_SPLIT_NO_EMPTY);
            $core = $core[0];

            $tmplt = $atRoot ? ($atCore ? "%s://%s/%s/" : "%s://%s/") : ($atCore ? "%s://%s/%s/" : "%s://%s%s");
            $end = $atRoot ? ($atCore ? $core : $hostname) : ($atCore ? $core : $dir);
            $base_url = sprintf($tmplt, $http, $hostname, $end);
        } else $base_url = 'http://localhost/';
        return $base_url;
    }
}

if (!function_exists('add_months')) {
    function add_months($months, DateTime $dateObject)
    {
        $next = new DateTime($dateObject->format('Y-m-d'));
        $next->modify('last day of +' . $months . ' month');
        if ($dateObject->format('d') > $next->format('d')) {
            return $dateObject->diff($next);
        } else {
            return new DateInterval('P' . $months . 'M');
        }
    }
}

if (!function_exists('is_image')) {
    function is_image($type)
    {
        if (preg_match("/^image\/(pjpeg|jpeg|png|gif|bmp)$/", $type)) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('return_dia_str')) {
    function return_dia_str($key)
    {
        $array = [1 => 'Segunda',2 => 'Terça',3 => 'Quarta',4 => 'Quinta',5 => 'Sexta',6 => 'Sábado',7 => 'Domingo'];
        return $array[$key];
    }
}

if (!function_exists(('normaliza_slug'))) {
    function normaliza_slug($string)
    {
        $table = array(
            'Š' => 'S', 'š' => 's', 'Đ' => 'Dj', 'đ' => 'dj', 'Ž' => 'Z', 'ž' => 'z', 'Č' => 'C', 'č' => 'c', 'Ć' => 'C', 'ć' => 'c',
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
            'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O',
            'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e',
            'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o',
            'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b',
            'ÿ' => 'y', 'Ŕ' => 'R', 'ŕ' => 'r',
        );
        $translate = strtr($string, $table);
        $final = slugify($translate);
        return mb_strtolower($final);
    }
}

if(!function_exists('limitar_texto'))
{
    function limitar_texto($texto, $limite)
    {
        $contador = strlen($texto);
        if ($contador == 0){
            return $texto;
        }
        if ($contador >= $limite) {
            $texto = substr($texto, 0, strrpos(substr($texto, 0, $limite), ' ')) . '...';
            return $texto;
        } else {
            return $texto;
        }
    }
}

if (!function_exists('verify_permission')) {
        function verify_permission($route)
        {

            $is_permited = false;
            $menuService = new MenuService();
            $menu = $menuService->getByController($route);

            if ($menu == false || $menu == null){
                return true;
            }
        
            if ($menu->permissions != '' && $menu->permissions != null){
                $permissions = explode(',', $menu->permissions);
            }else{
                $permissions = [];
            }

            $userType = user_logged()->getType();
            if (count($permissions) > 0) {
                if (( \in_array($userType, $permissions) || \user_logged()->isAdmin() ) && $menu->status_active == 1) {
                    $is_permited = true;
                }
                return $is_permited;
            } else {
                return true;
            }
            
        }
    }

if (!function_exists('format_bytes'))
{
    function format_bytes($bytes, $precision = 2) 
    {
        if ($bytes > pow(1024,3)) return round($bytes / pow(1024,3), $precision)."GB";
        else if ($bytes > pow(1024,2)) return round($bytes / pow(1024,2), $precision)."MB";
        else if ($bytes > 1024) return round($bytes / 1024, $precision)."KB";
        else return ($bytes)."B";
    }
}

if (!function_exists('throwJsonException')) {
    function throwJsonException($msg)
    {
        echo json_encode(array('result' => 0, 'message' => $msg));
    }
}

function throwJsonExceptionReturn($msg){
    return json_encode(array('result' => 0, 'message' => $msg));
}


if (!function_exists('return_dados')) {
        /**
         * Retorna os dados do sistema
         *
         * @return object
         */
        function return_dados()
        {
            
            $key = cache_key("system/dados",null);
            $cacheControl = CacheControl::getInstance();
            $cacheData = $cacheControl->get($key);

            //Ainda tem CACHE
            if ($cacheData != null){
                return json_decode($cacheData);
            }

            //Verifica se está expirado e deleta o CACHE
            if ($cacheControl->checkExpired($key)){
                $cacheControl->delete($key);
            }

            $dadosService = new \App\Services\DadosService();
            $dados = $dadosService->getDados();

            //Salva o novo cache
            $cacheControl->save(json_encode($dados),$key);

            return $dados;
        }
}

function convertToHoursMins($time) {
    if ($time < 1) {
       return "00:00";
    }
    $hours = floor($time / 60);
    $minutes = ($time % 60);
    return sprintf('%02d:%02d', $hours, $minutes);
}

if (!function_exists('link_whatsapp')) 
{
    function link_whatsapp($phone)
    {   
        return "https://api.whatsapp.com/send?phone=55" . str_replace(['(',')','-',' '],['','','',''],$phone);
    }
}

/**
 * Preenche com zeros a esquerda
 * @param int $int
 * @param int $length
 * @return string
 */
function zerofill($int, $length = 11)
{
    return str_pad(strval($int), $length, '0', 0);
}

/**
 * Calculates the great-circle distance between two points, with
 * the Vincenty formula.
 * @param float $latitudeFrom Latitude of start point in [deg decimal]
 * @param float $longitudeFrom Longitude of start point in [deg decimal]
 * @param float $latitudeTo Latitude of target point in [deg decimal]
 * @param float $longitudeTo Longitude of target point in [deg decimal]
 * @param float $earthRadius Mean earth radius in [m]
 * @return float Distance between points in [m] (same as earthRadius)
 */
function vincentyGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
{
    // convert from degrees to radians
    $latFrom = deg2rad($latitudeFrom);
    $lonFrom = deg2rad($longitudeFrom);
    $latTo = deg2rad($latitudeTo);
    $lonTo = deg2rad($longitudeTo);
  
    $lonDelta = $lonTo - $lonFrom;
    $a = pow(cos($latTo) * sin($lonDelta), 2) +
      pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
    $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);
  
    $angle = atan2(sqrt($a), $b);
    $meters = $angle * $earthRadius;
    $km = $meters / 100000; // amount of "full" kilometers
    //return number_format($km, 2, '.', '');
    return $meters;
}

/**
 * Retorna a distância entre dois pontos
 *
 * @param float $lat1
 * @param float $lon1
 * @param float $lat2
 * @param float $lon2
 * @param string $unit
 * @return float
 */
function distance($lat1, $lon1, $lat2, $lon2, $unit) {

    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos(min(max($dist,-1.0),1.0)); 
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);
  
    if ($unit == "K") {
        return number_format(($miles * 1.609344),2,'.','');
    } else if ($unit == "N") {
        return ($miles * 0.8684);
    } else {
        return $miles;
    }
}

/**
 * Retorna os dias da semana abreviados
 *
 * @return array
 */
function get_dias_semana_abrev()
{
    return [
        0 => 'Dom',
        1 => 'Seg',
        2 => 'Ter',
        3 => 'Qua',
        4 => 'Qui',
        5 => 'Sex',
        6 => 'Sáb'
    ];
}

function get_dias_semana()
{
    return [
        0 => 'Domingo',
        1 => 'Segunda',
        2 => 'Terça',
        3 => 'Quarta',
        4 => 'Quinta',
        5 => 'Sexta',
        6 => 'Sábado',
        100 => 'Segunda à Sexta',
        150 => 'Segunda à Sabado'
    ];
}

function return_dia_semana($dia)
{
    if (array_key_exists($dia,\get_dias_semana())){
        return \get_dias_semana()[$dia];
    }else{
        return 0;
    }
}

function get_address_via_cep($cep)
{
    error_reporting(E_ERROR | E_PARSE);
    $baseUrl = 'https://viacep.com.br/ws/' .  $cep . '/json/';
    // Iniciamos a função do CURL:
    $ch = curl_init($baseUrl);
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_RETURNTRANSFER => 1,
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $resposta = curl_exec($ch);
    curl_close($ch);
    $address = null;
    if ($resposta != null && $resposta != ""){
        $address = json_decode($resposta,JSON_HEX_APOS);
        if (isset($address->erro) && $address->erro == true){
            return null;
        }
    }
    return $address;
}

/**
 * Remove objetos repetidos de um array
 */
function my_array_unique($array, $keep_key_assoc = false){
    $duplicate_keys = array();
    $tmp = array();       

    foreach ($array as $key => $val){
        // convert objects to arrays, in_array() does not support objects
        if (is_object($val))
            $val = (array)$val;

        if (!in_array($val, $tmp))
            $tmp[] = $val;
        else
            $duplicate_keys[] = $key;
    }

    foreach ($duplicate_keys as $key)
        unset($array[$key]);

    return $keep_key_assoc ? $array : array_values($array);
}

/**
 * Retorna um novo código
 *
 * @return string
 */
function create_code_voucher()
{
    $letters=range('a','z'); // ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z']
    shuffle($letters);
    $numbers=range(0,9);  // [0,1,2,3,4,5,6,7,8,9]
    shuffle($numbers);
    $string='';
    for($x=0; $x<4; ++$x){
        $string.=$letters[$x].$numbers[$x];
    }
    return $string;
}

if (!function_exists('company_logged')) {
    /**
     *
     * Retorna a empresa logada na essão
     *
     * @return \App\Models\Company
     *
     */
    function company_logged()
    {
        $authControl = new AuthControl();
        return $authControl->getCompanyLogged();
    }
}

/**
 * Verifica se os campos do usuários informados são válidos
 *
 * @param array $body
 * @return object
 */
function validation_user($body)
{
    if (\strlen($body['cpf']) != 14){
        return \throwJsonException('Digite o CPF completo');
    }
    if (!\validate_cpf($body['cpf'])){
        return \throwJsonException('Digite um CPF válido');
    }

    $body['login'] = \normaliza_slug($body['first_name']);

    $valid_keys = ['email','first_name','last_name','cpf','date_birth','celphone','login'];
    $keys = array_keys($body);
    if (!is_array($keys) || count($keys) == 0){
        return throwJsonException('Você precisa preencher os campos');
    }

    foreach($valid_keys as $v){
        if (!in_array($v,$keys) || $body[$v] == ""){
            switch ($v) {
                case 'celphone':
                    $message = "o telefone";
                    break;
                case 'first_name':
                    $message = "o primeiro nome";
                    break;
                case 'last_name':
                    $message = 'o sobrenome';
                    break;
                case 'cpf':
                    $message = 'o CPF';
                    break;
                case 'login':
                    $message = 'o usuário';
                    break;
                case 'date_birth':
                    $message = 'a data de aniversário';
                    break;
                default:
                    $message = 'o campo: ' . $v;
                    break;
            }
            return throwJsonException('Você precisar informar ' . $message);
            break;
        }  
    }

    return true;

}

/**
 * Retorna o fim da paginação
 * @param int $totalPages
 * @param int $page
 * @param int $lim
 * @return int
 */
function mount_paginate($totalPages, $page = 1, $lim = 5)
{
    $inicio =  $page >= $lim ? $page - 1 : 1;
    if (($page + $lim) <= $totalPages ){
        $fim = $page + $lim;
    }else{
        $fim = ceil($totalPages);
    }
    return [
        'fim' => $fim,
        'inicio' => $inicio,
        'actual' => $page,
        'totalPages' => $totalPages
    ];
}

/**
 * Retorna os tipos de horários
 *
 * @param int $type
 * @return object
 */
function get_types_horarios($type = -1)
{
    $list = [
        'Domingo',
        'Segunda-feira',
        'Terça-feira',
        'Quarta-feira',
        'Quinta-feira',
        'Sexta-feira',
        'Sábado',
        'Segunda a Sexta',
        'Segunda a Sábado',
        'Todos os Dias'
    ];
    if ($type == -1){
        return $list;
    }else{
        return array_key_exists($type,$list) ? $list[$type] : null;
    }
}

/**
 * Retorna o endereço formatado
 *
 * @param object $address
 * @return string
 */
function format_address($address)
{
    $complemento = "";
    if (isset($address->complemento) && $address->complemento != ''){
        $complemento = ", " . $address->complemento;
    }
    $string = $address->logradouro . ',Nº: ' . $address->numero . $complemento . ', ' . $address->bairro . ', ' . $address->cidade . '-' . $address->estado . ', CEP: ' . $address->cep;
    if (isset($address->referencia) && $address->referencia != ''){
        $string .= ', ' . $address->referencia;
    } 
    return $string;
}

/**
 * Retorna o número convertido
 *
 * @param integer $valor
 * @param boolean $bolExibirMoeda
 * @param boolean $bolPalavraFeminina
 * @return void
 */
function converteParaExtenso( $valor = 0, $bolExibirMoeda = true, $bolPalavraFeminina = false )
{

        $singular = null;
        $plural = null;

        if ( $bolExibirMoeda )
        {
            $singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
            $plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões","quatrilhões");
        }
        else
        {
            $singular = array("", "", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
            $plural = array("", "", "mil", "milhões", "bilhões", "trilhões","quatrilhões");
        }

        $c = array("", "cem", "duzentos", "trezentos", "quatrocentos","quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
        $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta","sessenta", "setenta", "oitenta", "noventa");
        $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze","dezesseis", "dezesete", "dezoito", "dezenove");
        $u = array("", "um", "dois", "três", "quatro", "cinco", "seis","sete", "oito", "nove");

        if ( $bolPalavraFeminina )
        {
            if ($valor == 1)
                $u = array("", "uma", "duas", "três", "quatro", "cinco", "seis","sete", "oito", "nove");
            else
                $u = array("", "um", "duas", "três", "quatro", "cinco", "seis","sete", "oito", "nove");

            $c = array("", "cem", "duzentas", "trezentas", "quatrocentas","quinhentas", "seiscentas", "setecentas", "oitocentas", "novecentas");
        }

        $z = 0;

        $valor = number_format( $valor, 2, ".", "." );
        $inteiro = explode( ".", $valor );

        for ( $i = 0; $i < count( $inteiro ); $i++ )
            for ( $ii = mb_strlen( $inteiro[$i] ); $ii < 3; $ii++ )
                $inteiro[$i] = "0" . $inteiro[$i];

        // $fim identifica onde que deve se dar junção de centenas por "e" ou por "," ;)
        $rt = null;
        $fim = count( $inteiro ) - ($inteiro[count( $inteiro ) - 1] > 0 ? 1 : 2);
        for ( $i = 0; $i < count( $inteiro ); $i++ )
        {
            $valor = $inteiro[$i];
            $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
            $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
            $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

            $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd && $ru) ? " e " : "") . $ru;
            $t = count( $inteiro ) - 1 - $i;
            $r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
            if ( $valor == "000")
                $z++;
            elseif ( $z > 0 )
                $z--;

            if ( ($t == 1) && ($z > 0) && ($inteiro[0] > 0) )
                $r .= ( ($z > 1) ? " de " : "") . $plural[$t];

            if ( $r )
                $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
        }

        $rt = mb_substr( $rt, 1 );

        return($rt ? trim( $rt ) : "zero");

}

/**
 * Verifica se existe uma palavra ou letra em um texto
 *
 * @param string $word
 * @param string $findme
 * @return boolean
 */
function word_exist_on_text($word,$findme)
{
    $pos = strpos($findme,$word);
    if ($pos === false) {
        return false;
    }
    return true;
}

/**
 * Retorna o link completo de um arquivo na pasta assets
 * @param string $file
 * @return string
 */
function asset($file)
{
    return \base_url_new() . "assets/{$file}";
}

/**
 * Retorna o tamanho formatado do arquivo
 *
 * @param int $size
 * @return string
 */
function formattedSizeFile($size){

    $kb = 1024;
    $mb = 1048576;
    $gb = 1073741824;
    $tb = 1099511627776;

    if ($size <= 0){
        return "0";
    }

    if($size < $kb){
        
       return "{$size} bytes";

    }else if( $size>= $kb && $size < $mb){

       $kilo = number_format($size/$kb,2);
       return "{$kilo} KB";

    }else if($size >= $mb && $size < $gb){
   
        $mega = number_format($size/$mb,2);
        return "{$mega} MB";

    }else if($size >= $gb && $size < $tb){

        $giga = number_format($size/$gb,2);
        return "{$giga} GB";
    }

}

/**
 * Retorna a lista de estados
 *
 * @return aray
 */
function list_uf()
{
    return array (
          0 => 
          array (
            'nome' => 'Acre',
            'sigla' => 'AC',
          ),
          1 => 
          array (
            'nome' => 'Alagoas',
            'sigla' => 'AL',
          ),
          2 => 
          array (
            'nome' => 'Amapá',
            'sigla' => 'AP',
          ),
          3 => 
          array (
            'nome' => 'Amazonas',
            'sigla' => 'AM',
          ),
          4 => 
          array (
            'nome' => 'Bahia',
            'sigla' => 'BA',
          ),
          5 => 
          array (
            'nome' => 'Ceará',
            'sigla' => 'CE',
          ),
          6 => 
          array (
            'nome' => 'Distrito Federal',
            'sigla' => 'DF',
          ),
          7 => 
          array (
            'nome' => 'Espírito Santo',
            'sigla' => 'ES',
          ),
          8 => 
          array (
            'nome' => 'Goiás',
            'sigla' => 'GO',
          ),
          9 => 
          array (
            'nome' => 'Maranhão',
            'sigla' => 'MA',
          ),
          10 => 
          array (
            'nome' => 'Mato Grosso',
            'sigla' => 'MT',
          ),
          11 => 
          array (
            'nome' => 'Mato Grosso do Sul',
            'sigla' => 'MS',
          ),
          12 => 
          array (
            'nome' => 'Minas Gerais',
            'sigla' => 'MG',
          ),
          13 => 
          array (
            'nome' => 'Pará',
            'sigla' => 'PA',
          ),
          14 => 
          array (
            'nome' => 'Paraíba',
            'sigla' => 'PB',
          ),
          15 => 
          array (
            'nome' => 'Paraná',
            'sigla' => 'PR',
          ),
          16 => 
          array (
            'nome' => 'Pernambuco',
            'sigla' => 'PE',
          ),
          17 => 
          array (
            'nome' => 'Piauí',
            'sigla' => 'PI',
          ),
          18 => 
          array (
            'nome' => 'Rio de Janeiro',
            'sigla' => 'RJ',
          ),
          19 => 
          array (
            'nome' => 'Rio Grande do Norte',
            'sigla' => 'RN',
          ),
          20 => 
          array (
            'nome' => 'Rio Grande do Sul',
            'sigla' => 'RS',
          ),
          21 => 
          array (
            'nome' => 'Rondônia',
            'sigla' => 'RO',
          ),
          22 => 
          array (
            'nome' => 'Roraima',
            'sigla' => 'RR',
          ),
          23 => 
          array (
            'nome' => 'Santa Catarina',
            'sigla' => 'SC',
          ),
          24 => 
          array (
            'nome' => 'São Paulo',
            'sigla' => 'SP',
          ),
          25 => 
          array (
            'nome' => 'Sergipe',
            'sigla' => 'SE',
          ),
          26 => 
          array (
            'nome' => 'Tocantins',
            'sigla' => 'TO',
          )
    );
}

if (!function_exists('cache_key')) {
    /**
     * gera uma chave baseado no endpoint da api, parametros de busca e paginação
     *
     * @param string $enpoint
     * @param array $params
     * @return mixed
     */
    function cache_key($enpoint, $params = null)
    {
        $key = str_replace('/', '.', $enpoint);
        if ($params) {
            $key .= '.' . hash('sha512', serialize($params));
        }
        return $key;
    }
}

/**
 * Retorna os menus com submenus formatos para uso no HTML
 *
 * @return array
 */
function return_menus()
{

    $cacheControl = CacheControl::getInstance();
    $cacheKey = cache_key("system/menus",null);

    $cacheData = $cacheControl->get($cacheKey);

    //Ainda tem CACHE
    if ($cacheData != null){
        return json_decode($cacheData);
    }

    //Verifica se está expirado e deleta o CACHE
    if ($cacheControl->checkExpired($cacheKey)){
        $cacheControl->delete($cacheKey);
    }

    $menusService = new MenuService();
    $getMenus = $menusService->get();
    $menus = [];
    foreach ($getMenus as $key => $value) {
        if ($value->root == 0){
            $object = (object) ['principal' => null,'submenus' => []];
            if ($value->permissions == ''){
                $value->permissions = [];
            }else{
                $value->permissions = explode(',',$value->permissions);
            }
            if ($value->tipos_empresa == ''){
                $value->tipos_empresa = [];
            }else{
                $value->tipos_empresa = explode(',',$value->tipos_empresa);
            }
            $value->variations = explode('&',$value->variations);
            $object->principal = $value;
            $menus[] = $object;
        }
    }

    foreach($menus as $key => $value){
        foreach($getMenus as $k => $v){
            if ($v->root == $value->principal->id){
                if ($v->permissions == ''){
                    $v->permissions = [];
                }else{
                    $v->permissions = explode(',',$v->permissions);
                }
                if ($v->tipos_empresa == ''){
                    $v->tipos_empresa = [];
                }else{
                    $v->tipos_empresa = explode(',',$v->tipos_empresa);
                }
                $v->variations = explode(',',$v->variations);
                $value->submenus[] = $v;
            }
        }
    }

    //Salva o novo cache
    $cacheControl->save(json_encode($menus),$cacheKey);
    return $menus;

}

/**
 * Retorna o valor em string para float
 *
 * @param string $value
 * @return float
 */
function str_to_float($value)
{
    if (empty($value)){
        return 0;
    }
    return str_replace(['.',','],['','.'],$value);
}

/**
 * Retorna o dados formatado para o banco
 *
 * @param string $key
 * @param object $value
 * @return object
 */
function validate_data($key,$value){
    $keysInt = [
        'id',
        'status',
        'type',
        'tipo'
    ];
    if (in_array($key,$keysInt)){
        return empty($value) ? 0 : intval($value);
    }
    $keysFloat = [
        'value',
        'valor',
        'price',
        'preco'
    ];
    if (in_array($key,$keysFloat)){
        return empty($value) ? 0 : floatval($value);
    }
    return empty($value) ? null : $value;
}

if (!function_exists('getTypesUsers')) {
    function getTypesUsers()
    {
        $service = new TypeUserService();
        return $service->getPermissoes();
    }
}

if (!function_exists('return_type')) {
    function return_type($type)
    {
        $types = getTypesUsers();
        if (isset($types[$type])){
            return $types[$type];
        }else{
            return "";
        }
    }
}

/**
 * Remove uma pasta
 *
 * @param string $dir
 * @return void
 */
function rrmdir($dir) {
    if (is_dir($dir)) {
      $objects = scandir($dir);
      foreach ($objects as $object) {
        if ($object != "." && $object != "..") {
          if (filetype($dir."/".$object) == "dir") 
             rrmdir($dir."/".$object); 
          else unlink   ($dir."/".$object);
        }
      }
      reset($objects);
      rmdir($dir);
    }
}

/**
 * Retorna a senha com hash
 *
 * @param string $senha
 * @return string
 */
function password_return($senha)
{
    return password_hash($senha, PASSWORD_DEFAULT,['cost' => 8]);
}

/**
 * Retorna o folder padrão de paginação
 *
 * @return string
 */
function folder_paginate_box(){
    return "helpers/_box-paginate.twig";
}

/**
 * Retorna a list de status produtos 
 *
 * @return array
 */
function list_status_product()
{
    return [
        Product::STATUS_ACTIVE => "Ativo",
        Product::STATUS_BLOCK => "Bloqueado"
    ];
}

/**
 * Retorna a lista de status do pedido
 *
 * @return array
 */
function list_status_order(){
    return [
        Order::STATUS_CREATED => "Criado",
        Order::STATUS_FINISH => "Finalizado",
        Order::SATTUS_BLOCK => "Cancelado"
    ];
}

/**
 * Retorna a lista de formas de pagamento
 *
 * @return array
 */
function list_type_payment(){
    return [
        Order::TYPE_MONEY => "Dinheiro",
        Order::TYPE_CREDIT_CARD => "Cartão de Crédito",
        Order::TYPE_CHECK => "Cheque",
        Order::TYPE_DEPOSIT => "Depósito/Transferência"
    ];
}