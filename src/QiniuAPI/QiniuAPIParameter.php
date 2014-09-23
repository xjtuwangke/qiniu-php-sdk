<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-9-19
 * Time: 18:32
 */

namespace QiniuAPI;

/**
 * API参数(比如imagemogr2中的thumbnail)基类,其它API参数实现类继承这个类
 * @class QiniuAPIParameter
 * @package QiniuAPI
 */

class QiniuAPIParameter {

    /**
     * API名称
     * @var string
     */
    public static $name = '';

    /**
     * API中的参数
     * @var array
     */
    protected $parameters = array();

    /**
     * 默认参数
     * @var array
     */
    protected $default_parameters = array();

    public function __construct(){
        $this->reset();
        return;
    }

    /**
     * 参数reset
     * @return $this
     */
    public function reset(){
        $this->parameters = $this->default_parameters;
        return $this;
    }

    /**
     * 改API的名称
     * @return string
     */
    public function name(){
        return static::$name;
    }

    /**
     * @return string
     */
    public function __toString(){
        return "/" . $this->name() . '/' . $this->parameterToString();
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    protected function setParameter( $key , $value ){
        $this->parameters[$key] = $value;
        return $this;
    }

    /**
     * @param      $key
     * @param null $default
     * @return null
     */
    protected function getParameter( $key , $default = null ){
        if( array_key_exists( $key , $this->parameters ) ){
            return $this->parameters[$key];
        }
        else{
            return $default;
        }
    }

    /**
     * $this->parameters转换成字符串
     * @return string
     */
    protected function parameterToString(){
        return '';
    }
}