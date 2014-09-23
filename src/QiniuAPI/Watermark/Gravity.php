<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/9/22
 * Time: 03:46
 */

namespace QiniuAPI\Watermark;

use QiniuApi\QiniuAPIParameter;

/**
 * 水印锚点
 * @class Gravity
 * @package QiniuAPI\Watermark
 */
class Gravity extends QiniuAPIParameter{

    public static $name = 'gravity';

    /**
     * 偏移锚点左上
     */
    const Gravity1 = 'NorthWest';
    /**
     * 偏移锚点中上
     */
    const Gravity2 = 'North';
    /**
     * 偏移锚点右上角
     */
    const Gravity3 = 'NorthEast';
    /**
     * 偏移锚点左中
     */
    const Gravity4 = 'West';
    /**
     * 偏移锚点正中
     */
    const Gravity5 = 'Center';
    /**
     * 偏移锚点右中
     */
    const Gravity6 = 'East';
    /**
     * 偏移锚点左下
     */
    const Gravity7 = 'SouthWest';
    /**
     * 偏移锚点中下
     */
    const Gravity8 = 'South';
    /**
     * 偏移锚点右下
     */
    const Gravity9 = 'SouthEast';

    protected $gravity = null;

    protected $default_parameters = array(
        'dx' => 0 ,
        'dy' => 0 ,
    );

    public function __construct(){
        parent::__construct();
        $this->gravity( static::Gravity9 );
    }

    public function gravity( $gravity ){
        $this->gravity = $gravity;
        return $this;
    }

    /**
     * 横轴边距，单位:像素(px)，缺省值为10
     * @param $dx
     * @return $this
     */
    public function dx( $dx ){
        $dx = (int) $dx;
        return $this->setParameter( 'dx' , $dx );
    }

    /**
     * 纵轴边距，单位:像素(px)，缺省值为10
     * @param $dy
     * @return $this
     */
    public function dy( $dy ){
        $dy = (int) $dy;
        return $this->setParameter( 'dy' , $dy );
    }

    protected function parameterToString(){
        $dx = $this->getParameter( 'dx' );
        $dy = $this->getParameter( 'dy' );
        return "dx/{$dx}/dy/{$dy}";
    }

    public function __toString(){
        $string = parent::__toString();
        return '/gravity/' . $this->gravity . $string;
    }
}