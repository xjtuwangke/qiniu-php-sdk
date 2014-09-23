<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-9-20
 * Time: 20:37
 */

namespace QiniuAPI\ImageMogr2;

use QiniuAPI\QiniuAPIParameter;

/**
 * 图片质量
 * 取值范围1-100，缺省为85
 * 如原图质量小于指定质量，则使用原图质量。
 * @class Quality
 * @package QiniuAPI\ImageMogr2
 */

class Quality extends QiniuAPIParameter{

    public static $name = 'quality';

    protected $default_parameters = array(
        'quality' => 85 ,
    );

    public function quality( $quality ){
        $quality = (int) $quality;
        return $this->setParameter( 'quality' , $quality );
    }

    protected function parameterToString(){
        $quality = $this->getParameter( 'quality' );
        return $quality;
    }
} 