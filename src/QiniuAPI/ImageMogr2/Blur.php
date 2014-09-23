<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/9/21
 * Time: 02:42
 */

namespace QiniuAPI\ImageMogr2;

use QiniuAPI\QiniuAPIParameter;

/**
 * 模糊操作
 * /blur/<radius>x<sigma>
 * 高斯模糊参数
 * <radius>是模糊半径，取值范围是[1,50]，<sigma>是正态分布的标准差，必须大于0。
 * @class Blur
 * @package QiniuAPI\ImageMogr2
 */

class Blur extends QiniuAPIParameter{

    public static $name = 'blur';

    protected $default_parameters = array(
        'radius' => 0 ,
        'sigma'  => 1 ,
    );

    /**
     * <radius>是模糊半径，取值范围是[1,50]
     * @param $radius
     * @return $this
     */
    public function radius( $radius ){
        return $this->setParameter( 'radius' , $radius );
    }

    /**
     * <sigma>是正态分布的标准差，必须大于0
     * @param $sigma
     * @return $this
     */
    public function sigma( $sigma ){
        return $this->setParameter( 'sigma' , $sigma );
    }

    protected function parameterToString(){
        return  $this->getParameter( 'radius' ) . 'x' . $this->getParameter( 'sigma');
    }

} 