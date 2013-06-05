<?php

/**
 * @desc WHAT?
 * @author 夏琳泰 mailto:resia@dev.ppstream.com
 * @since  2013-05-06 12:49:48
 * @throws 注意:无DB异常处理
 */

function json_encode($array = NULL)
{
    include_once 'code/json_helper.php';
    $json_helper = new Services_JSON();
    $result =$json_helper->encode($array);
    return $result;
}

/**
 * json obj -> array
 */
function object2array($array)
{
    if(is_object($array))
    {
        $array = (array)$array;
    }
    if(is_array($array))
    {
        foreach($array as $key=>$value)
        {
            $array[$key] = object2array($value);
        }
    }
    return $array;
}
/**
 * @desc WHAT?
 * @author 夏琳泰 mailto:resia@dev.ppstream.com
 * @since  2013-05-06 12:49:48
 * @throws 注意:无DB异常处理
 */
function json_decode($string = NULL)
{
    include_once 'code/json_helper.php';
    $json_helper = new Services_JSON();
    $result =$json_helper->decode($string);
    return object2array($result);
}

/**
 * @desc WHAT?
 * @author 夏琳泰 mailto:resia@dev.ppstream.com
 * @since  2012-12-19 16:44:23
 * @throws 注意:无DB异常处理
 */
function file_put_contents($filename, $data)
{
    $fp = fopen($filename, 'w');
    fwrite($fp, $data);
    return fclose($fp);
}

/**
 * @desc WHAT?
 * @author 夏琳泰 mailto:resia@dev.ppstream.com
 * @since  2012-12-19 16:48:51
 * @throws 注意:无DB异常处理
 */
function stripos($haystack, $needle)
{
    return strpos(strtolower($haystack), strtolower($needle));
}

/**
 * @desc WHAT?
 * @author 夏琳泰 mailto:resia@dev.ppstream.com
 * @since  2012-12-19 16:51:50
 * @throws 注意:无DB异常处理
 */
function http_build_query($data, $prefix = '', $sep = '', $key = '')
{
    $ret = array();
    foreach ((array)$data as $k => $v) {
        if (is_int($k) && $prefix != null) {
            $k = urlencode($prefix . $k);
        }
        if ((!empty($key)) || ($key === 0)) $k = $key . '[' . urlencode($k) . ']';
        if (is_array($v) || is_object($v)) {
            array_push($ret, http_build_query($v, '', $sep, $k));
        } else {
            array_push($ret, $k . '=' . urlencode($v));
        }
    }
    if (empty($sep)) $sep = ini_get('arg_separator.output');
    return implode($sep, $ret);
}// http_build_query

