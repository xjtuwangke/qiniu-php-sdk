<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/9/21
 * Time: 02:42
 */

namespace QiniuAPI\ImageMogr2;

use QiniuAPI\QiniuAPIParameter;


class Blur extends QiniuAPIParameter{

    public static $name = 'blur';

    protected $default_parameters = array(
        'radius' => 0 ,
        'sigma'  => 1 ,
    );

    public function radius( $radius ){
        return $this->setParameter( 'radius' , $radius );
    }

    public function sigma( $sigma ){
        return $this->setParameter( 'sigma' , $sigma );
    }

    protected function parameterToString(){
        return  $this->getParameter( 'radius' ) . 'x' . $this->getParameter( 'sigma');
    }

} 