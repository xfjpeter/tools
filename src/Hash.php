<?php
/**
 * | ---------------------------------------------------------------------------------------------------
 * | Author：johnxu <fsyzxz@163.com>.
 * | ---------------------------------------------------------------------------------------------------
 * | Home: https://www.johnxu.net.
 * | ---------------------------------------------------------------------------------------------------
 * | Data: 2019/2/26
 * | ---------------------------------------------------------------------------------------------------
 * | Desc: Hash加密
 * | ---------------------------------------------------------------------------------------------------
 */

namespace johnxu\tool;

/**
 * Class Hash
 *
 * @package johnxu\tool
 */
class Hash
{
    /**
     * hash加密
     *
     * @param string $value
     * @param int    $cost
     *
     * @return string
     */
    public static function make( string $value, int $cost = 10 ): string
    {
        return password_hash( $value, PASSWORD_BCRYPT, [ 'cost' => $cost ] );
    }

    /**
     * 验证hash加密是否正确
     *
     * @param string $value
     * @param string $hashValue
     *
     * @return bool
     */
    public static function check( string $value, string $hashValue ): bool
    {
        return password_verify( $value, $hashValue );
    }
}
