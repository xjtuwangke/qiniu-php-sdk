<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/9/21
 * Time: 02:45
 */

namespace QiniuAPI\ImageMogr2;

use QiniuAPI\QiniuAPIParameter;

/**
 * 是否支持渐进显示
 * 取值范围：1 支持渐进显示，0不支持渐进显示(缺省为0)
 * 适用目标格式：jpg
 * 效果：网速慢时，图片显示由模糊到清晰。
 * @class Interlace
 * @package QiniuAPI\ImageMogr2
 */

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