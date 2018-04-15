<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2017/8/26
 * Time: 13:19
 */

namespace Core\Module\BaseFunc;

if (PHP_MAJOR_VERSION < 5) {
    die("The PHP version is below 5");
}
/**
 * 设置响应状态
 * @param null $code 状态码
 * @return int|mixed|null   系统状态码
 */
function http_response_code($code = NULL)
{

    if ($code !== NULL) {

        switch ($code) {
            case 100:
                $text = 'Continue';
                break;
            case 101:
                $text = 'Switching Protocols';
                break;
            case 200:
                $text = 'OK';
                break;
            case 201:
                $text = 'Created';
                break;
            case 202:
                $text = 'Accepted';
                break;
            case 203:
                $text = 'Non-Authoritative Information';
                break;
            case 204:
                $text = 'No Content';
                break;
            case 205:
                $text = 'Reset Content';
                break;
            case 206:
                $text = 'Partial Content';
                break;
            case 300:
                $text = 'Multiple Choices';
                break;
            case 301:
                $text = 'Moved Permanently';
                break;
            case 302:
                $text = 'Moved Temporarily';
                break;
            case 303:
                $text = 'See Other';
                break;
            case 304:
                $text = 'Not Modified';
                break;
            case 305:
                $text = 'Use Proxy';
                break;
            case 400:
                $text = 'Bad Request';
                break;
            case 401:
                $text = 'Unauthorized';
                break;
            case 402:
                $text = 'Payment Required';
                break;
            case 403:
                $text = 'Forbidden';
                break;
            case 404:
                $text = 'Not Found';
                break;
            case 405:
                $text = 'Method Not Allowed';
                break;
            case 406:
                $text = 'Not Acceptable';
                break;
            case 407:
                $text = 'Proxy Authentication Required';
                break;
            case 408:
                $text = 'Request Time-out';
                break;
            case 409:
                $text = 'Conflict';
                break;
            case 410:
                $text = 'Gone';
                break;
            case 411:
                $text = 'Length Required';
                break;
            case 412:
                $text = 'Precondition Failed';
                break;
            case 413:
                $text = 'Request Entity Too Large';
                break;
            case 414:
                $text = 'Request-URI Too Large';
                break;
            case 415:
                $text = 'Unsupported Media Type';
                break;
            case 500:
                $text = 'Internal Server Error';
                break;
            case 501:
                $text = 'Not Implemented';
                break;
            case 502:
                $text = 'Bad Gateway';
                break;
            case 503:
                $text = 'Service Unavailable';
                break;
            case 504:
                $text = 'Gateway Time-out';
                break;
            case 505:
                $text = 'HTTP Version not supported';
                break;
            default:
                exit('Unknown http status code "' . htmlentities($code) . '"');
                break;
        }

        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

        header($protocol . ' ' . $code . ' ' . $text);

        $GLOBALS['http_response_code'] = $code;

    } else {

        $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);

    }

    return $code;

}

/**
 * 获取随机字节
 * @param $length
 * @return string
 */
function random_bytes($length)
{
    $bytes = "";
    for ($i = 0; $i < $length; ++$i) {
        $bytes .= chr(rand(0, 255));
    }
    return $bytes;
}

/**
 * 获取随机字符串
 * @param int $length 长度
 * @param bool $num 是否包含数字
 * @param bool $upperChar 是否包含大写字母
 * @param bool $lowerChar 是否包含小写字母
 * @return string           随机字符串
 */
function random_string($length, $num = true, $upperChar = true, $lowerChar = true)
{
    $str = "";
    if ($num && $upperChar && $lowerChar) {
        $start = 0;
        $end = 61;
    } elseif ($num && $upperChar) {
        $start = 0;
        $end = 35;
    } elseif ($num && $lowerChar) {
        $start = 36;
        $end = 71;
    } elseif ($upperChar && $lowerChar) {
        $start = 10;
        $end = 61;
    } elseif ($num) {
        $start = 0;
        $end = 9;
    } elseif ($upperChar) {
        $start = 10;
        $end = 35;
    } elseif ($lowerChar) {
        $start = 36;
        $end = 61;
    } else {
        return $str;
    }
    $all = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    for ($i = 0; $i < $length; ++$i) {
        $str .= $all[rand($start, $end)];
    }
    return $str;
}

/**
 * 获取随机字符串
 * @param int $length 长度
 * @param bool $num 是否包含数字
 * @param bool $upperChar 是否包含大写字母
 * @param bool $lowerChar 是否包含小写字母
 * @return string           随机字符串
 */
/*
function random_string ($length, $num = true, $upperChar = true, $lowerChar = true) {
    $str = "";
    $all = "";
    if($num){
        $all .= "0123456789";
    }
    if($upperChar){
        $all .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    }
    if($lowerChar){
        $all .= "abcdefghijklmnopqrstuvwxyz";
    }
    $end = strlen($all) - 1;
    if($end >= 0) {
        for ($i = 0; $i < $length; ++$i) {
            $str .= $all[rand(0, $end)];
        }
    }
    return $str;
}
*/

/**
 * 获取随机数字
 * @param $min
 * @param $max
 * @return int
 */
function random_int($min, $max)
{
    return rand($min, $max);
}

/**
 * 生成URL的参数字符串
 * @param array $arg
 * @return string
 */
function make_url_get($arg)
{
    $str_arg = "?";
    foreach ($arg as $k => $v) {
        $str_arg .= $k . "=" . $v . "&";
    }
    if ($str_arg === "?") {
        return "";
    }
    //以&结尾，则去掉末尾的&
    if (strrchr($str_arg, "&") == "&") {
        $str_arg = substr($str_arg, 0, strlen($str_arg) - 1);
    }
    return $str_arg;
}

/**
 * 获取客户端IP
 * @return array|false|string
 */
function getClientIP($only_real = false)
{
    if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    elseif (isset($_SERVER["HTTP_CLIENT_IP"]))
        $ip = $_SERVER["HTTP_CLIENT_IP"];
    elseif (isset($_SERVER["REMOTE_ADDR"]))
        $ip = $_SERVER["REMOTE_ADDR"];
    elseif (getenv("HTTP_X_FORWARDED_FOR"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    elseif (getenv("HTTP_CLIENT_IP"))
        $ip = getenv("HTTP_CLIENT_IP");
    elseif (getenv("REMOTE_ADDR"))
        $ip = getenv("REMOTE_ADDR");
    else
        $ip = "0.0.0.0";
    if (is_array($ip)) {
        if ($only_real) {
            $ip = $ip[0];
        } else {
            $ip = implode(",", $ip);
        }
    } elseif (is_string($ip)) {
        if ($only_real && strpos($ip, ',') !== false) {
            $ip = substr($ip, 0, strpos($ip, ','));
        }
    } else {
        $ip = "";
    }
    return (string)$ip;
}

function &string_encode_utf8($str)
{
    $len = strlen($str);
    $output = "";
    for ($i = 0; $i < $len; ++$i) {
        if ($str{$i} < 128) {
            $output .= "|" . ord($str{$i});
        } elseif ($str{$i} < 191) {
            $output .= ord($str{$i});
        } else {
            $output .= "|" . ord($str{$i});
        }
    }
    return $output;
}

/**
 * 字符串转十六进制编码
 * @param $str
 * @return string
 */
function &string_encode($str)
{
    $output = bin2hex($str);
    return $output;
}

/**
 * 十六进制编码转字符串
 * @param $code
 * @return string
 */
function &string_decode($code)
{
    $str = hex2bin($code);
    if (!is_string($str)) {
        $str = "";
    }
    return $str;
}

/**
 * 参数URL编码,从后往前，装不下了就丢掉,但编码顺序依旧为从前往后，由旧到新
 * @param $arr_url
 * @return string
 */
function &argurl_encode($arr_url)
{
    $str = "";
    $cnt = 0;
    //编码
    for ($i = count($arr_url) - 1; $i >= 0; --$i) {
        $tmp = string_encode($arr_url[$i]);
        $tmpn = strlen($tmp);
        //URL长度不要超过2000，
        //IE最大长度2083，google8182，firefox65536，safari80000
        //IIS默认
        if ($tmpn + $cnt > 1500) {
            break;
        }
        $str = $tmp . "|" . $str;
        $cnt += $tmpn + 1;
    }
    if ($cnt > 0 && $str{$cnt - 1} == '|') {
        $str = substr($str, 0, $cnt - 1);
    }
    return $str;
}

/**
 * 参数URL编码入栈到末尾
 * @param $code
 * @param $url
 * @return string
 */
function &argurl_encode_push(&$code, $url)
{
    $tmp = string_encode($url);
    $tmpn = strlen($tmp);
    $coden = strlen($code);
    while ($tmpn + $coden > 1500) {
        $pos = strpos($code, '|');
        if ($pos === false) {
            $code = "";
            $coden = 0;
        } else {
            $code = substr($code, $pos + 1);
            $coden -= ($pos + 1);
        }
        //$coden = strlen($code);
    }
    if ($coden > 0) {
        $code .= "|" . string_encode($url);
    } else {
        $code = string_encode($url);
    }
    return $code;
}

/**
 * 参数URL解码为URL数组
 * @param $code 十六进制字符串
 * @return array
 */
function &argurl_decode($code)
{
    //分割数据
    $arr = explode("|", $code);
    //解码
    for ($i = count($arr) - 1; $i >= 0; --$i) {
        $arr[$i] = string_decode($arr[$i]);
    }
    return $arr;
}

/**
 * 参数URL解码出栈最后一条
 * @param $code
 * @return string
 */
function &argurl_decode_pop(&$code)
{
    $pos = strrpos($code, '|');
    if ($pos === false) {
        return string_decode($code);
    } else {
        $tmp = string_decode(substr($code, $pos + 1));
        $code = substr($code, 0, $pos);
        return $tmp;
    }
}

/**
 * 数组添加值，并返回新数组
 * @param $arr array
 * @param $key string | null
 * @param $value string | null
 * @return array
 */
function &array_push_clone($arr, $key = null, $value = null)
{
    $ret = $arr;
    if (!is_null($value)) {
        if (is_null($key)) {
            $arr [] = $value;
        } else {
            $ret[$key] = $value;
        }
    }
    return $ret;
}

function ip2int($ip)
{
    if (strchr($ip, ":")) {
        $ip = "0.0.0.0";
    }
    return bindec(decbin(ip2long($ip)));
    //list($ip1,$ip2,$ip3,$ip4)=explode(".",$ip);
    //return ($ip1<<24)|($ip2<<16)|($ip3<<8)|($ip4);
}

function int2ip($num)
{
    if ($num > 0xFFFFFFFF) {
        return "0.0.0.0";
    }
    return long2ip($num);
}

/**
 *判断是否为移动设备
 */
function isMobile()
{
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset($_SERVER['HTTP_VIA'])) {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile');
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        echo "/(" . implode('|', $clientkeywords) . ")/i";
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}

/**
 *判断访问者是否为维信
 *
 */
function isWeixin()
{
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
        return true;
    } else {
        return false;
    }
}

/**
 * 格式化文件大小
 * @param $size 文件大小
 * @param int $precision 小数点精度
 * @return string 格式化字符串
 */
function formatFileSize($size, $precision = 0)
{
    $unit = array("B", "KB", "MB", "GB", "TB");
    $tmp = $size;
    $level = 0;
    while ($tmp > 1024 && $level < 5) {
        $tmp = (float)$tmp / 1024;
        ++$level;
    }
    $tmp = round($tmp, $precision);
    return "$tmp $unit[$level]";
}