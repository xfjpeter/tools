<?php
/**
 * | ---------------------------------------------------------------------------------------------------
 * | Author：johnxu <fsyzxz@163.com>.
 * | ---------------------------------------------------------------------------------------------------
 * | Home: https://www.johnxu.net.
 * | ---------------------------------------------------------------------------------------------------
 * | Data: 2019/2/26
 * | ---------------------------------------------------------------------------------------------------
 * | Desc: 时间操作
 * | ---------------------------------------------------------------------------------------------------
 */

namespace johnxu\tool;

class Time
{
    /**
     * 获取今天的时间戳（开始到结束）
     *
     * @return array
     */
    public static function today()
    {
        return self::getStartAndEndTimestamp();
    }

    /**
     * 获取昨天的开始和结束的时间戳
     *
     * @return array
     */
    public static function yesterday()
    {
        return self::getStartAndEndTimestamp( strtotime( '-1 day' ) );
    }

    /**
     * 本周开始和结束的时间戳
     *
     * @return array
     */
    public static function week()
    {
        list( $y, $m, $d, $w ) = explode( '-', date( 'Y-m-d-w' ) );
        $w = $w == 0 ? 7 : $w;

        return [
            mktime( 0, 0, 0, $m, $d - $w + 1, $y ),
            mktime( 23, 59, 59, $m, $d - $w + 7, $y )
        ];
    }

    /**
     * 上周开始和结束的时间戳
     *
     * @return array
     */
    public static function lastWeek()
    {
        $timestamp = time();

        return [
            strtotime( date( 'Y-m-d', strtotime( "last week Monday", $timestamp ) ) ),
            strtotime( date( 'Y-m-d', strtotime( "last week Sunday", $timestamp ) ) ) + 24 * 3600 - 1
        ];
    }

    /**
     * 本月开始和结束的时间戳
     *
     * @return array
     */
    public static function month()
    {
        list( $y, $m, $t ) = explode( '-', date( 'Y-m-t' ) );

        return [
            mktime( 0, 0, 0, $m, 1, $y ),
            mktime( 23, 59, 59, $m, $t, $y )
        ];
    }

    /**
     * 返回上个月开始和结束的时间戳
     *
     * @return array
     */
    public static function lastMonth()
    {
        $y     = date( 'Y' );
        $m     = date( 'm' );
        $begin = mktime( 0, 0, 0, $m - 1, 1, $y );
        $end   = mktime( 23, 59, 59, $m - 1, date( 't', $begin ), $y );

        return [ $begin, $end ];
    }

    /**
     * 返回今年开始和结束的时间戳
     *
     * @return array
     */
    public static function year()
    {
        $y = date( 'Y' );

        return [
            mktime( 0, 0, 0, 1, 1, $y ),
            mktime( 23, 59, 59, 12, 31, $y )
        ];
    }

    /**
     * 返回去年开始和结束的时间戳
     *
     * @return array
     */
    public static function lastYear()
    {
        $year = date( 'Y' ) - 1;

        return [
            mktime( 0, 0, 0, 1, 1, $year ),
            mktime( 23, 59, 59, 12, 31, $year )
        ];
    }

    /**
     * 获取几天前零点到现在/昨日结束的时间戳
     *
     * @param int  $day 天数
     * @param bool $now 返回现在或者昨天结束时间戳
     *
     * @return array
     */
    public static function dayToNow( $day = 1, $now = true )
    {
        $end = time();
        if ( !$now )
        {
            list( $foo, $end ) = self::yesterday();
        }

        return [
            mktime( 0, 0, 0, date( 'm' ), date( 'd' ) - $day, date( 'Y' ) ),
            $end
        ];
    }

    /**
     * 返回几天前的时间戳
     *
     * @param int $day
     *
     * @return int
     */
    public static function daysAgo( $day = 1 )
    {
        $nowTime = time();

        return $nowTime - self::daysToSecond( $day );
    }

    /**
     * 返回几天后的时间戳
     *
     * @param int $day
     *
     * @return int
     */
    public static function daysAfter( $day = 1 )
    {
        $nowTime = time();

        return $nowTime + self::daysToSecond( $day );
    }

    /**
     * 天数转换成秒数
     *
     * @param int $day
     *
     * @return int
     */
    public static function daysToSecond( $day = 1 )
    {
        return $day * 86400;
    }

    /**
     * 周数转换成秒数
     *
     * @param int $week
     *
     * @return int
     */
    public static function weekToSecond( $week = 1 )
    {
        return self::daysToSecond() * 7 * $week;
    }

    /**
     * 获取年月日时分秒
     *
     * @param int|null $timestamp
     *
     * @return array
     */
    public static function getDateTime( int $timestamp = null )
    {
        $timestamp = $timestamp ?? time();
        $year      = date( 'Y', $timestamp );
        $month     = date( 'm', $timestamp );
        $day       = date( 'd', $timestamp );
        $hour      = date( 'H', $timestamp );
        $minutes   = date( 'm', $timestamp );
        $second    = date( 's', $timestamp );
        $week      = date( 'w', $timestamp );

        return [ $year, $month, $day, $hour, $minutes, $second, $week ];
    }

    /**
     * 获取开始和结束的时间戳
     *
     * @param int|null $timestamp
     *
     * @return array
     */
    private static function getStartAndEndTimestamp( int $timestamp = null )
    {
        [ $year, $month, $day, $hour, $minutes, $second ] = self::getDateTime( $timestamp );
        $startTimestamp = mktime( 0, 0, 0, $month, $day, $year );
        $endTimestamp   = mktime( 23, 59, 59, $month, $day, $year );

        return [ $startTimestamp, $endTimestamp ];
    }
}
