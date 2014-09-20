<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-9-19
 * Time: 18:32
 */

namespace QiniuAPI;


class QiniuAPIParameter {

    public static $name = '';

    protected $parameters = array();

    protected $default_parameters = array();

    public function __construct(){
        $this->reset();
        return;
    }

    public function reset(){
        $this->parameters = $this->default_parameters;
        return $this;
    }

    public function __toString(){
        return "/" . static::$name . '/' . $this->parameterToString();
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

    protected function parameterToString(){
        return '';
    }
}