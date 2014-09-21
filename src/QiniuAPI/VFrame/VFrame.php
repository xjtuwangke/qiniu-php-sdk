<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/9/22
 * Time: 06:00
 */

namespace QiniuAPI\VFrame;

use \QiniuAPI\QiniuFop;

class VFrame extends QiniuFop{

    protected static $name = 'vframe';

    protected $default_parameters = array(
        '__format' => null ,
        'offset' => null ,
        'w' => null ,
        'h' => null ,
        'rotate' => null ,
    );

    public function format( $format ){
        return $this->setParameter( '__format' , $format );
    }

    public function offset( $offset ){
        return $this->setParameter( 'offset' , $offset );
    }

    public function width( $width ){
        return $this->setParameter( 'width' , $width );
    }

    public function height( $height ){
        return $this->setParameter( 'height' , $height );
    }

    public function rotate( $degree ){
        return $this->setParameter( 'rotate' , $degree );
    }

}