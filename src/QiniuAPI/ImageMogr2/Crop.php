<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-9-20
 * Time: 20:07
 */

namespace QiniuAPI\ImageMogr2;
use QiniuAPI\QiniuAPIParameter;

/**
 * /crop/<Width>x		指定目标图片宽度，高度不变。取值范围0-10000。
 * /crop/x<Height>		指定目标图片高度，宽度不变。取值范围0-10000。
 * /crop/<Width>x<Height>		同时指定目标图片宽高。取值范围0-10000。
 *
 * /crop/!{cropSize}a<dx>a<dy>		相对于偏移锚点，向右偏移个像素，同时向下偏移个像素。
 * 取值范围0－1000。
 * /crop/!{cropSize}-<dx>a<dy>		相对于偏移锚点，向下偏移个像素，同时从指定宽度中减去个像素。
 * 取值范围0－1000。
 * /crop/!{cropSize}a<dx>-<dy>		相对于偏移锚点，向右偏移个像素，同时从指定高度中减去个像素。
 * 取值范围0－1000。
 * /crop/!{cropSize}-<dx>-<dy>		相对于偏移锚点，从指定宽度中减去个像素，同时从指定高度中减去个像素。
 * 取值范围0－1000
 */

class Crop extends QiniuAPIParameter{

    public static $name = 'crop';

    const Gravity1 = 'NorthWest';
    const Gravity2 = 'North';
    const Gravity3 = 'NorthEast';
    const Gravity4 = 'West';
    const Gravity5 = 'Center';
    const Gravity6 = 'East';
    const Gravity7 = 'SouthWest';
    const Gravity8 = 'South';
    const Gravity9 = 'SouthEast';

    protected $gravity = null;

    protected $default_parameters = array(
        'width' => null ,
        'height' => null ,
        'cropSize' => null , //'300x400'
        'dx' => 0 ,
        'dy' => 0 ,
    );

    public function __construct(){
        parent::__construct();
        $this->gravity( static::Gravity1 );
    }

    public function gravity( $gravity ){
        $this->gravity = $gravity;
        return $this;
    }

    public function widthAndHeight( $width , $height ){
        return $this->reset()->setParameter( 'width' , $width )->setParameter( 'height' , $height );
    }

    public function cropSize( $width , $height ){
        return $this->reset()->setParameter( 'cropSize' , $width . 'x' . $height );
    }

    public function dx( $dx ){
        $dx = (int) $dx;
        if( $dx >= 0 ){
            return $this->setParameter( 'dx' , 'a' . $dx );
        }
        else{
            return $this->setParameter( 'dx' , '-' . abs( $dx ) );
        }
    }

    public function dy( $dy ){
        $dy = (int) $dy;
        if( $dy >= 0 ){
            return $this->setParameter( 'dy' , 'a' . $dy );
        }
        else{
            return $this->setParameter( 'dy' , '-' . abs( $dy ) );
        }
    }

    protected function parameterToString(){
        $width = '';
        $height = '';
        if( $width = $this->getParameter( 'width' ) || $height = $this->getParameter( 'height' ) ){
            return "{$width}x{$height}";
        }
        if( $cropSize = $this->getParameter( 'cropSize' ) ){
            $dx = $this->getParameter( 'dx' );
            $dy = $this->getParameter( 'dy' );
            return "!{$cropSize}{$dx}{$dy}";
        }
        return '';
    }

    public function __toString(){
        $string = parent::__toString();
        return '/gravity/' . $this->gravity . $string;
    }
}