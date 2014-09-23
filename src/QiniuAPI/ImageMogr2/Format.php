<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/9/21
 * Time: 02:39
 */

namespace QiniuAPI\ImageMogr2;

use QiniuAPI\QiniuAPIParameter;

/**
 * 图片格式 jpg|png|...
 * @class Format
 * @package QiniuAPI\ImageMogr2
 */

class Format extends QiniuAPIParameter{

    public static $name = 'format';

    protected $default_parameters = array(
        'format' => 'jpg' ,
    );

    public function format( $format ){
        return $this->setParameter( 'format' , $format );
    }

    protected function parameterToString(){
        return $this->getParameter( 'format' );
    }

} 