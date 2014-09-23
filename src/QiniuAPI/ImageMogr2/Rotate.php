<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-9-20
 * Time: 20:40
 */

namespace QiniuAPI\ImageMogr2;

use QiniuAPI\QiniuAPIParameter;

/**
 * 旋转角度
 * 取值范围1-360，缺省为不旋转。
 * @class Rotate
 * @package QiniuAPI\ImageMogr2
 */
class Rotate extends QiniuAPIParameter{

    public static $name = 'rotate';

    protected $default_parameters = array(
        'degree' => 0 ,
    );

    public function degree( $degree ){
        $degree = (int) $degree;
        return $this->setParameter( 'degree' , $degree );
    }

    protected function parameterToString(){
        $degree = $this->getParameter( 'degree' );
        return $degree;
    }

} 