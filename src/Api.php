<?php
/**
 * | ---------------------------------------------------------------------------------------------------
 * | Author：johnxu <fsyzxz@163.com>.
 * | ---------------------------------------------------------------------------------------------------
 * | Home: https://www.johnxu.net.
 * | ---------------------------------------------------------------------------------------------------
 * | Data: 2019/1/25
 * | ---------------------------------------------------------------------------------------------------
 * | Desc: 接口权限管理
 * | ---------------------------------------------------------------------------------------------------
 */

namespace johnxu\tool;

use think\Container;
use think\Db;
use think\Request;

/**
 * Class Api
 *
 * @package johnxu\tool
 */
class Api extends Restful
{
    /**
     * @var mixed 用户授权信息
     */
    protected $auth;

    /**
     * @var Request
     */
    protected $request;
    protected $secret = 'fsyzxz@163.com';
    /**
     * @var string 用户表名
     */
    protected $tableUser = 'user';
    /**
     * @var string token表名
     */
    protected $tableToken = 'token';

    /**
     * user 表
     *CREATE TABLE `user`  (
     * `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
     * `uid` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '生成的唯一用户ID',
     * `username` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '帐号',
     * `password` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '密码',
     * `email` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '邮箱',
     * `phone` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '手机号码',
     * `create_at` int(10) NOT NULL COMMENT '注册时间',
     * `update_at` int(10) NOT NULL DEFAULT 0 COMMENT '更新时间',
     * `last_login_ip` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '最后一次登录的ip',
     * `last_login_time` int(10) DEFAULT NULL COMMENT '最后一次登录的时间',
     * PRIMARY KEY (`id`) USING BTREE
     * ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci
     *
     * token 表
     * CREATE TABLE `token`  (
     * `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
     * `token` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
     * `user_id` int(11) NOT NULL,
     * `login_ip` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
     * `login_time` int(10) UNSIGNED DEFAULT NULL,
     * `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '(0不可用，1可用)',
     * PRIMARY KEY (`id`) USING BTREE
     * ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;
     */

    /**
     * Api constructor.
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \ReflectionException
     */
    public function __construct()
    {
        $this->request = Container::get( 'request' );

        // 验证登录
        $this->verifyLogin();

        // 验证权限
        $this->verifyAuth();
    }

    /**
     * 注册绑定
     *
     * @login false
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function register()
    {
        $username = $this->request->post( 'username' );
        $password = $this->request->post( 'password' );
        if ( empty( $username ) || empty( $password ) )
        {
            $this->fail( self::HTTP_RESPONSE_PARAM_ERROR, 1001, 'Parameter error.' );
        }
        else
        {
            // 检测验证码是否存在
            if ( Db::name( $this->tableUser )->where( [ 'username' => $username ] )->find() )
            {
                $this->fail( self::HTTP_RESPONSE_EXISTS, 1003, 'User already exists.' );
            }
            $data = array(
                'uid'             => Str::instance()->generateUid( 'u' ),
                'username'        => $username,
                'password'        => hash_hmac( 'sha256', $password, $this->secret ),
                'last_login_ip'   => $this->request->ip(),
                'last_login_time' => time(),
                'create_at'       => time(),
                'update_at'       => time()
            );
            $res  = Db::name( $this->tableUser )->insertGetId( $data );
            if ( method_exists( $this, 'registerSuccess' ) )
            {
                $this->registerSuccess( array_merge( $data, array( 'id' => $res ) ) );
            }

            $res
                ? $this->ok( self::HTTP_RESPONSE_CREATED, $this->auth( $res, $data ) )
                : $this->fail( self::HTTP_RESPONSE_PARAM_ERROR, 1004, 'Create user fail' );
        }
    }

    /**
     * 授权登录
     *
     * @login false
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function authorization()
    {
        // authorization 授权
        if ( $username = $this->request->server( 'PHP_AUTH_USER' ) )
        {
            $password = $this->request->server( 'PHP_AUTH_PW' );
        }
        else
        {
            $username = $this->request->post( 'username' );
            $password = $this->request->post( 'password' );
        }

        if ( !$password || !$username )
        {
            $this->fail( self::HTTP_RESPONSE_PARAM_ERROR, 1001, 'Parameter error.' );
        }
        // 验证帐号密码是否正确
        $user = Db::name( $this->tableUser )->where( [ 'username' => $username ] )->find();
        if ( !$user )
        {
            $this->fail( self::HTTP_RESPONSE_UN_AUTHORIZATION, 1002, 'Unauthorized' );
        }
        elseif ( hash_hmac( 'sha256', $password, $this->secret ) != $user['password'] )
        {
            $this->fail( self::HTTP_RESPONSE_UN_AUTHORIZATION, 1002, 'Unauthorized' );
        }
        else
        {
            var_dump( method_exists( $this, 'authorizationSuccess' ) );
            if ( method_exists( $this, 'authorizationSuccess' ) )
            {
                $this->authorizationSuccess( $user );
            }
            // 授权成功
            $this->ok( self::HTTP_RESPONSE_OK, $this->auth( $user['id'], $user ) );
        }
    }

    /**
     * 退出登录
     *
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function logout()
    {
        Db::name( $this->tableToken )->where( [ 'user_id' => $this->auth['id'] ] )->delete();

        $this->ok( self::HTTP_RESPONSE_UPDATED, [] );
    }

    /**
     * Auth
     *
     * @param       $userId
     * @param array $extend
     *
     * @return array
     * @throws \Exception
     */
    protected function auth( $userId, $extend = array() )
    {
        Db::name( $this->tableToken )->where( [ 'user_id' => $userId ] )->setField( 'status', 0 );

        // 设置其他的token过期，新增token并启用
        $data = array(
            'token'      => strtolower( Str::instance()->getMachineCode( 4 ) ),
            'user_id'    => $userId,
            'login_time' => time(),
            'status'     => 1,
            'login_ip'   => $this->request->ip()
        );
        $res  = Db::name( $this->tableToken )->insert( $data );

        if ( $res )
        {
            if ( isset( $extend['password'] ) )
            {
                unset( $extend['password'] );
            }
            if ( isset( $extend['id'] ) )
            {
                unset( $extend['id'] );
            }

            return array_merge( $extend, array( 'token' => $data['token'] ) );
        }
        else
        {
            $this->fail( self::HTTP_RESPONSE_UN_AUTHORIZATION, 1002, 'Unauthorized' );
        }
    }

    /**
     * 检测token是否过期
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function verifyLogin()
    {
        if ( !$this->auth )
        {
            $token = $this->request->header( 'token' );
            $token = $token ? $token : $this->request->param( 'token' );
            if ( $token )
            {
                $res = Db::name( $this->tableToken )->where( [ 'token' => $token, 'status' => 1 ] )->find();
                if ( !$res )
                {
                    $this->fail( self::HTTP_RESPONSE_UN_AUTHORIZATION, 1002, 'Unauthorized' );
                }
                else
                {
                    $this->auth = Db::name( $this->tableUser )->hidden( [ 'password' ] )->where( [ 'id' => $res['user_id'] ] )->find();
                }
            }
        }
    }

    /**
     * verify auth
     *
     * @throws \ReflectionException
     */
    protected function verifyAuth()
    {
        $reflection = new \ReflectionMethod( $this, $this->request->action() );
        $document   = $reflection->getDocComment();
        if ( $document )
        {
            if ( preg_match( '/\@login\s+(true|false)/', $document, $res ) )
            {
                if ( isset( $res[1] ) || $res[1] == 'false' )
                {
                    return;
                }
            }
        }

        if ( !$this->auth )
        {
            $this->fail( self::HTTP_RESPONSE_UN_AUTHORIZATION, 1002, 'Unauthorized' );
        }
    }
}
