<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/9/21
 * Time: 03:35
 */

namespace QiniuAPI;

/**
 * FOP(比如imageMogr2,avthumb等)基类
 * @class QiniuFop
 * @package QiniuAPI
 */

class QiniuFop {

    protected static $name = '';

    protected $parameters = array();

    protected $default_parameters = array();

    public function __construct(){
        $this->reset();
        return $this;
    }

    public function reset(){
        $this->parameters = $this->default_parameters;
        return $this;
    }

    public function addParameter( QiniuAPIParameter $parameter ){
        $this->parameters[] = $parameter;
        return $this;
    }

    protected function setParameter( $key , $value ){
        $this->parameters[$key] = $value;
        return $this;
    }

    protected function getParameter( $key , $default = null ){
        if( array_key_exists( $key , $this->parameters ) ){
            return $this->parameters[$key];
        }
        else{
            return $default;
        }
    }

    public function name(){
        return static::$name;
    }

    public function __toString(){
        $string = $this->name();
        foreach( $this->parameters as $key => $parameter ){
            if( is_object( $parameter ) ){
                $string.= (string) $parameter;
            }
            elseif( is_string( $parameter ) || is_numeric( $parameter )){
                if( substr( $key , 0 , 2 ) != '__' ){
                    $string.= '/' . $key . '/' . $parameter;
                }
                else{
                    $string.= '/' . $parameter ;
                }
            }
        }
        return $string;
    }

    public function toArray(){
        $result = array();
        foreach( $this->parameters as $key => $parameter ){
            if( is_string( $parameter ) || is_numeric( $parameter )){
                $result[$key] = $parameter;
            }
        }
        return $result;
    }

    public function toJson(){
        return json_encode( $this->toArray() );
    }
}