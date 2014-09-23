<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/9/22
 * Time: 03:53
 */

namespace QiniuAPI\Watermark;

use Qiniu\Utils;
use QiniuAPI\QiniuAPIParameter;

/**
 * 文字水印
 * @class Text
 * @package QiniuAPI\Watermark
 */
class Text extends QiniuAPIParameter{

    public static $name = 'text';

    protected $default_parameters = array(
        'text' => null ,
        'font' => null ,
        'fontsize' => 0 ,
        'fill' => '#000000' ,
        'dissolve' => 100 ,
        'gravity' => null ,
    );

    public function reset(){
        parent::reset();
        $this->font( '黑体' );
        return $this;
    }

    /**
     * 水印文字内容
     * @param $text
     * @return $this
     */
    public function text( $text ){
        return $this->setParameter( 'text' , Utils::Qiniu_Encode( $text ) );
    }

    /**
     * 水印文字字体
     * @param $font
     * @return $this
     */
    public function font( $font ){
        return $this->setParameter( 'font' , Utils::Qiniu_Encode( $font ) );
    }

    /**
     * 水印文字大小，单位: 缇，等于1/20磅，缺省值0（默认大小）
     * @param $fontSize
     * @return $this
     */
    public function fontSize( $fontSize ){
        return $this->setParameter( 'fontsize' , $fontSize );
    }

    /**
     * 水印文字颜色，RGB格式，可以是颜色名称（比如red）或十六进制（比如#FF0000），参考RGB颜色编码表，缺省为白色(TODO)
     * @param $color
     * @return $this
     */
    public function fill( $color ){
        return $this->setParameter( 'fill' , $color );
    }

    /**
     * 透明度，取值范围1-100，缺省值100（完全不透明）
     * @param $int
     * @return $this
     */
    public function dissolve( $int ){
        return $this->setParameter( 'dissolve' , $int );
    }

    public function gravity( Gravity $gravity ){
        return $this->setParameter( 'gravity' , $gravity );
    }

    public function parameterToString(){
        return '' . $this->getParameter( 'text' ) .
            '/font/' . $this->getParameter('font') .
            '/fontsize/' . $this->getParameter('fontsize')  .
            '/fill/' . $this->getParameter('fill') .
            '/dissolve/' . $this->getParameter( 'dissolve' ) .
            $this->getParameter( 'gravity' );
    }
} 