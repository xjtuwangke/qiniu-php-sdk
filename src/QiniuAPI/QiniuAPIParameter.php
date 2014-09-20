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

    protected $inUse = false;

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
        if( $this->inUse ){
            return "/" . static::$name . '/' . $this->parameterToString();
        }
        else{
            return '';
        }
    }

    public function inUse( $bool = true ){
        $this->inUse = (boolean) $bool;
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

    protected function parameterToString(){
        return '';
    }
}