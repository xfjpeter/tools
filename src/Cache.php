<?php
/**
 * | ---------------------------------------------------------------------------------------------------
 * | Author：johnxu <fsyzxz@163.com>.
 * | ---------------------------------------------------------------------------------------------------
 * | Home: https://www.johnxu.net.
 * | ---------------------------------------------------------------------------------------------------
 * | Data: 2018/5/30 0030
 * | ---------------------------------------------------------------------------------------------------
 * | Desc:
 * | ---------------------------------------------------------------------------------------------------
 */

namespace johnxu\tool;

/**
 * Class Cache
 *
 * @package johnxu\tool
 */
class Cache
{
    /**
     * @var string
     */
    private $path = './cache/';
    /**
     * @var string
     */
    private $key = '9fb6eb4db04fcd344bcb04ec9fd54d7d2be47e16';
    /**
     * @var int
     */
    private $timeout = 0;
    /**
     * @var string
     */
    private $scope = 'default';
    /**
     * @var string
     */
    private $error = '';
    /**
     * @var string
     */
    private $ext = '.txt';
    /**
     * @var
     */
    private static $instance;

    /**
     * get instance
     *
     * @access public
     * @return $this
     */
    public static function getInstance()
    {
        if (!static::$instance instanceof static) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * 设置缓存
     *
     * @access public
     * @param  string $key
     * @param  mixed  $value
     * @param string  $scope
     * @return mixed
     */
    public function set(string $key, $value, $scope = 'default')
    {
        static::getInstance()->scope = $scope;

        if (static::getInstance()->setContent($key, $value)) {

            return static::get($key, $scope);
        } else {
            static::getInstance()->error = '写入文件缓存文件失败';

            return false;
        }
    }

    /**
     * 获取缓存内容
     *
     * @param  string $key
     * @param string  $scope
     * @return null
     */
    public function get(string $key, $scope = 'default')
    {
        static::getInstance()->scope = $scope;
        $content                     = static::getInstance()->getContent();
        // 判断文件是否过期
        $filename = $this->getFileName();
        if (!$content) {
            return null;
        }
        if ($content['expire'] == 0) {
            if (strpos($key, '.')) {
                $keys = explode('.', $key);
                $tmp  = $content['data'];
                foreach ($keys as $item) {
                    if (isset($tmp[$item])) {
                        $tmp = $tmp[$item];
                    } else {
                        return null;
                    }
                }
            } else {
                $tmp = isset($content['data'][$key]) ? $content['data'][$key] : null;
            }
            return $tmp;
        } else if (time() - filectime($filename) > $content['expire']) {
            // 删除文件
            unlink($filename);
            static::getInstance()->error = '文件已过期';
            return null;
        } else {
            static::getInstance()->error = '读取缓存文件失败';
            return null;
        }
    }

    /**
     * 清空某项值
     *
     * @access public
     * @param string $key
     * @param string $scope
     * @return mixed
     */
    public function delete(string $key, $scope = 'default')
    {
        return static::getInstance()->set($key, null, $scope);
    }

    /**
     * 清除整个域文件
     *
     * @param string $scope
     * @return bool
     */
    public function clear($scope = 'default')
    {
        static::getInstance()->scope = $scope;
        $filename                    = static::getInstance()->getFileName();
        if (is_file($filename)) {
            return unlink($filename);
        } else {
            return false;
        }
    }

    /**
     * 获取错误信息
     *
     * @return string
     */
    public function getError()
    {
        return static::getInstance()->error;
    }

    /**
     * 写入文件
     *
     * @access private
     * @param string $key
     * @param mixed  $data
     * @return bool
     */
    private function setContent(string $key, $data): bool
    {
        $filename = static::getInstance()->getFileName();
        // 如何文件夹不存在，创建
        if (!is_dir(static::getInstance()->path)) {
            static::getInstance()->mkdirs(static::getInstance()->path);
        }
        $content = static::getInstance()->getContent();
        if ($content) {
            $content['data'][$key] = $data;
            $data                  = $content;
        } else {
            $data = [
                'data'   => [
                    $key => $data,
                ],
                'expire' => static::getInstance()->timeout
            ];
        }
        $fp = fopen($filename, 'w');
        if ($fp) {
            fwrite($fp, serialize($data));
            fclose($fp);

            return true;
        } else {
            return false;
        }
    }

    /**
     * 读取文件内容
     *
     * @access private
     * @return null|string
     */
    private function getContent()
    {
        $filename = static::getInstance()->getFileName();
        if (!is_file($filename)) {
            return null;
        }
        $fp      = fopen($filename, 'r');
        $content = '';
        if ($fp) {
            while (($buffer = fgets($fp, 4096)) !== false) {
                $content .= $buffer;
            }
            if (!feof($fp)) {
                static::getInstance()->error = '读取文件出现异常';
            }
            fclose($fp);
        }

        return unserialize($content);
    }

    /**
     * 获取文件名称
     *
     * @access private
     * @return string
     */
    private function getFileName()
    {
        return static::getInstance()->path . hash_hmac('sha1', static::getInstance()->scope, static::getInstance()->key, false) . static::getInstance()->ext;
    }

    /**
     * 递归创建目录
     *
     * @access private
     * @param string $path
     * @return bool
     */
    private function mkdirs(string $path): bool
    {
        if (!is_dir($path)) {
            if (!static::getInstance()->mkdirs(dirname($path))) {
                return false;
            }
            if (!mkdir($path, 0777)) {
                return false;
            }
        }
        return true;
    }


}