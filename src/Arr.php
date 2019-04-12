<?php
/**
 * | ---------------------------------------------------------------------------------------------------
 * | Author：johnxu <fsyzxz@163.com>.
 * | ---------------------------------------------------------------------------------------------------
 * | Home: https://www.johnxu.net.
 * | ---------------------------------------------------------------------------------------------------
 * | Data: 2019-04-12
 * | ---------------------------------------------------------------------------------------------------
 * | Desc: 数组操作类
 * | ---------------------------------------------------------------------------------------------------
 */

namespace johnxu\tool;

class Arr
{
    /**
     * 多维数组根据指定字段排序
     * @param array  $data
     * @param string $field
     * @param int    $sortBy
     * @return array
     */
    public static function multiSortByField(array $data, string $field, $sortBy = SORT_ASC): array
    {
        $rank = array_column($data, $field);
        array_multisort($rank, $sortBy, $data);

        return $data;
    }
}