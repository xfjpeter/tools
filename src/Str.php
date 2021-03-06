<?php
/**
 * @author johnxu <fsyzxz@163.com>
 *
 * @link   https://www.johnxu.net
 */

namespace johnxu\tool;

use Exception;
use johnxu\tool\traits\Singleton;

/**
 * string operator
 */
class Str
{
    use Singleton;

    /**
     * reverse string
     *
     * @access public
     *
     * @param string $string
     * @return string
     * @author johnxu <fsyzxz@163.com>
     */
    public function reverse(string $string): string
    {
        return strrev($string);
    }

    /**
     * string to lower
     *
     * @access public
     *
     * @param string $string 字符串
     *
     * @return string
     * @author johnxu <fsyzxz@163.com>
     *
     */
    public function lower(string $string): string
    {
        return strtolower($string);
    }

    /**
     * string to upper
     *
     * @access public
     *
     * @param string $string 字符串
     *
     * @return string
     * @author johnxu <fsyzxz@163.com>
     *
     */
    public function upper(string $string): string
    {
        return strtoupper($string);
    }

    /**
     * substring
     *
     * @access public
     *
     * @param string $string
     * @param int    $start  cut start
     * @param int    $length cut length
     *
     * @return string
     * @author johnxu <fsyzxz@163.com>
     *
     */
    public function cut(string $string, int $start = 0, $length = null): string
    {
        return mb_substr($string, $start, $length);
    }

    /**
     * Determine whether there is a string
     *
     * @access public
     *
     * @param string $haystack lookup in the string
     * @param string $needle   query string
     *
     * @return boolean
     * @author johnxu <fsyzxz@163.com>
     *
     */
    public function has(string $haystack, string $needle): bool
    {
        return !(strpos($haystack, $needle) === false);
    }

    /**
     * remove needle in haystack
     *
     * @access public
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return string
     * @author johnxu <fsyzxz@163.com>
     *
     */
    public function remove(string $haystack, string $needle): string
    {
        $haystack = self::getInstance()->convertToArray($haystack);
        foreach ($haystack as $key => $item) {
            if (self::getInstance()->has($needle, $item)) {
                unset($haystack[$key]);
            }
        }

        return self::getInstance()->arrayToString($haystack);
    }

    /**
     * array string to string
     *
     * @access public
     *
     * @param array $array
     *
     * @return string
     * @author johnxu <fsyzxz@163.com>
     *
     */
    public function arrayToString(array $array): string
    {
        return implode(null, $array);
    }

    /**
     * convert string to array
     *
     * @access public
     *
     * @param string $string
     *
     * @return array
     * @author johnxu <fsyzxz@163.com>
     *
     */
    public function convertToArray(string $string): array
    {
        return str_split($string, 1);
    }

    /**
     * Convert the initials into uppercase
     *
     * @access public
     *
     * @param string $string
     *
     * @return string
     * @author johnxu <fsyzxz@163.com>
     *
     */
    public function ucfirst(string $string): string
    {
        return ucfirst($string);
    }

    /**
     * Convert the initials of the list to uppercase
     *
     * @access public
     *
     * @param string $string
     *
     * @return string
     * @author johnxu <fsyzxz@163.com>
     *
     */
    public function ucwords(string $string): string
    {
        return ucwords($string);
    }

    /**
     * Line-to-line character encoding
     *
     * @param string      $string
     * @param string      $to
     * @param string|null $from
     *
     * @return string
     */
    public function encoding(string $string, string $to = 'UTF-8', string $from = null): string
    {
        $from = $from ?: mb_detect_encoding($string, 'UTF-8, CP850, ISO-8859-15', true);

        return mb_convert_encoding($string, $to, $from);
    }

    /**
     * generate trade no
     *
     * @access public
     *
     * @param int|integer $length trade no length
     *
     * @return string
     * @author johnxu <fsyzxz@163.com>
     *
     */
    public function generateTradeNo(int $length = 10): string
    {
        return date('YmdHis') . self::getInstance()->cut(
                self::getInstance()->arrayToString(
                    array_map(
                        'ord', str_split(
                            self::getInstance()->cut(uniqid(), 6, 13), 1)
                    )),
                0,
                $length
            );
    }

    /**
     * generate uid
     *
     * @param string $prefix
     *
     * @return string
     */
    public function generateUid(string $prefix = ''): string
    {
        return $prefix . self::getInstance()->arrayToString(
                array_map(
                    'ord', str_split(
                        self::getInstance()->cut(uniqid(), 6, 13), 1)
                ));
    }

    /**
     * Getting machine code, exp:8E8363C1-094E0EDC-7D67C393
     *
     * @param int    $block
     * @param int    $blockSize
     * @param string $split
     *
     * @return string
     * @throws Exception
     */
    public function getMachineCode(int $block = 3, int $blockSize = 8, string $split = '-'): string
    {
        $result = [];
        for ($i = 0; $i < $block; $i++) {
            array_push($result, bin2hex((random_bytes($blockSize / 2))));
        }

        $result = implode($split, $result);

        return strtoupper($result);
    }

    /**
     * 格式化字节大小
     * @param float  $size
     * @param string $delimiter
     * @return string
     */
    public function formatBytes(float $size, string $delimiter = '')
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        for ($i = 0; $size >= 1024 && $i < 6; $i++) {
            $size /= 1024;
        }

        return round($size, 2) . $delimiter . $units[$i];
    }

    /**
     * forbidden
     */
    final public function __clone()
    {
    }
}
