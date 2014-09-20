<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/9/21
 * Time: 02:45
 */

namespace QiniuAPI\ImageMogr2;

use QiniuAPI\QiniuAPIParameter;


class Interlace extends QiniuAPIParameter{

    public static $name = 'interlace';

    protected $default_parameters = array(
        'interlace' => 1 ,
    );

    public function enable( $bool = true ){
        $interlace = ( $bool )? 1:0;
        return $this->setParameter( 'interlace' , $interlace );
    }

    protected function parameterToString(){
        return $this->getParameter( 'interlace' );
    }

} 