<?php
/**
 * | ---------------------------------------------------------------------------------------------------
 * | Author：johnxu <fsyzxz@163.com>.
 * | ---------------------------------------------------------------------------------------------------
 * | Home: https://www.johnxu.net.
 * | ---------------------------------------------------------------------------------------------------
 * | Data: 2019/1/11
 * | ---------------------------------------------------------------------------------------------------
 * | Desc: Set Or Read Config
 * | ---------------------------------------------------------------------------------------------------
 */

namespace johnxu\payment;

class Config
{
    private $config = array();

    static private $instance;

    private function __construct()
    {
    }

    /**
     * Get Instance
     *
     * @return Config
     */
    public static function getInstance()
    {
        if ( !self::$instance instanceof self )
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Set Config Value
     *
     * @example \johnxu\payment\Config::getInstance()->set( 'wx.url', 'http://www.baidu.com' );
     *
     * @param string $name
     * @param mixed  $value
     */
    public function set( $name, $value )
    {
        if ( strpos( $name, '.' ) )
        {
            $name = explode( '.', $name );
        }
        else
        {
            $name = array( $name );
        }
        if ( in_array( $name[0], array_keys( $this->config ) ) )
        {
            // 判断二级name是否存在
            if ( in_array( $name[1], array_keys( $this->config[$name[0]] ) ) )
            {
                $this->config[$name[0]][$name[1]] = $value;
            }
            else
            {
                $this->config[$name[0]] = $value;
            }
        }
        else
        {
            if ( count( $name ) > 1 )
            {
                $this->config[$name[0]] = array(
                    $name[1] => $value
                );
            }
            else
            {
                $this->config[$name[0]] = $value;
            }
        }
    }

    /**
     * Get Config Value
     *
     * @example
     * \johnxu\payment\Config::getInstance()->set( 'wx.url' ); // http://www.baidu.com
     * \johnxu\payment\Config::getInstance()->set( 'wx' ); // array ('url' => 'http://www.baidu.com')
     * \johnxu\payment\Config::getInstance()->set(); // array('wx' => array ('url' => 'http://www.baidu.com'))
     *
     * @param string $name
     *
     * @return array|mixed|string
     */
    public function get( $name = '' )
    {
        if ( !$name )
        {
            return $this->config;
        }
        else
        {
            if ( strpos( $name, '.' ) )
            {
                $name  = explode( '.', $name );
                $value = isset( $this->config[$name[0]] ) ? $this->config[$name[0]] : '';
                if ( count( $name ) > 1 && $value && is_array( $value ) )
                {
                    $value = isset( $value[$name[1]] ) ? $value[$name[1]] : '';
                }

                return $value;
            }
            else
            {
                return isset( $this->config[$name] ) ? $this->config[$name] : '';
            }
        }
    }

    /**
     * Validation
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return bool
     */
    public function has( $name, $value )
    {
        return $this->get( $name ) && $this->get( $name ) == $value;
    }
}
