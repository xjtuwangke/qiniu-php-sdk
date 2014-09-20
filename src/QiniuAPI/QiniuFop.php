<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/9/21
 * Time: 03:35
 */

namespace QiniuAPI;

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

    public function __toString(){
        $string = static::$name;
        foreach( $this->parameters as $parameter ){
            $string.= (string) $parameter;
        }
        return $string;
    }
}