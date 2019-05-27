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

namespace johnxu\tool;

use johnxu\tool\traits\Singleton;

class Config
{
    private $config = array();

    use Singleton;

    /**
     * Set Config Value
     *
     * @param string $name
     * @param mixed  $value
     * @example \johnxu\tool\Config::getInstance()->set( 'wx.url', 'http://www.baidu.com' );
     *
     */
    public function set($name, $value)
    {
        if (strpos($name, '.')) {
            $name = explode('.', $name);
        } else {
            $name = array($name);
        }
        if (in_array($name[0], array_keys($this->config))) {
            $this->config[$name[0]][$name[1]] = $value;
        } else {
            if (count($name) > 1) {
                $this->config[$name[0]] = array(
                    $name[1] => $value,
                );
            } else {
                $this->config[$name[0]] = $value;
            }
        }
    }

    /**
     * Get Config Value
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return array|mixed|string
     * @example
     * \johnxu\tool\Config::getInstance()->set( 'wx.url' ); // http://www.baidu.com
     * \johnxu\tool\Config::getInstance()->set( 'wx' ); // array ('url' => 'http://www.baidu.com')
     * \johnxu\tool\Config::getInstance()->set(); // array('wx' => array ('url' => 'http://www.baidu.com'))
     *
     */
    public function get($name = '', $default = '')
    {
        if (!$name) {
            return $this->config;
        } else {
            if (strpos($name, '.')) {
                $name  = explode('.', $name);
                $value = isset($this->config[$name[0]]) ? $this->config[$name[0]] : $default;
                if (count($name) > 1 && $value && is_array($value)) {
                    $value = isset($value[$name[1]]) ? $value[$name[1]] : $default;
                }

                return $value;
            } else {
                return isset($this->config[$name]) ? $this->config[$name] : $default;
            }
        }
    }

    /**
     * Batch Config
     *
     * @param array $config
     */
    public function batch(array $config)
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * Validation
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return bool
     */
    public function has($name, $value = null)
    {
        if ($result = $this->get($name)) {
            return $result;
        } else {
            if ($value && $value == $this->get($name)) {
                return true;
            } else {
                return false;
            }
        }
    }
}

