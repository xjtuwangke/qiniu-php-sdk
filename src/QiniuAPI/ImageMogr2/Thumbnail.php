<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-9-19
 * Time: 18:44
 */

namespace QiniuAPI\ImageMogr2;

use QiniuAPI\QiniuAPIParameter;

/**
 * /thumbnail/!<Scale>p		基于原图大小，按指定百分比缩放。
 * 取值范围0-1000。
 * /thumbnail/!<Scale>px		以百分比形式指定目标图片宽度，高度等比缩放。
 * 取值范围0-1000。
 * /thumbnail/!x<Scale>p		以百分比形式指定目标图片高度，宽度等比缩放。
 * 取值范围0-1000。
 *
 * /thumbnail/<Width>x		指定目标图片宽度，高度等比缩放。
 * 取值范围0-10000。
 * /thumbnail/x<Height>		指定目标图片高度，宽度等比缩放。
 * 取值范围0-10000。
 * /thumbnail/<Width>x<Height>		限定长边，短边自适应缩放，将目标图片限制在指定宽高矩形内。
 * 取值范围0-10000。
 * /thumbnail/!<Width>x<Height>r		限定短边，长边自适应缩放，目标图片会延伸至指定宽高矩形外。
 * 取值范围0-10000。
 * /thumbnail/<Width>x<Height>!		限定目标图片宽高值，忽略原图宽高比例，按照指定宽高值强行缩略，可能导致目标图片变形。
 * 取值范围0-10000。
 * /thumbnail/<Width>x<Height>>		当原图尺寸大于给定的宽度或高度时，按照给定宽高值缩小。
 * 取值范围0-10000。
 * /thumbnail/<Width>x<Height><		当原图尺寸小于给定的宽度或高度时，按照给定宽高值放大。
 * 取值范围0-10000。
 * /thumbnail/<Area>@		按原图高宽比例等比缩放，缩放后的像素数量不超过指定值。
 * 取值范围0-100000000。
 * @class Thumbnail
 * @package QiniuAPI\ImageMogr2
 */
class Thumbnail extends QiniuAPIParameter{

    public static $name = 'thumbnail';

    protected $default_parameters = array(
        'scale' => null ,
        'width' => null ,
        'height' => null ,
        'contain' => true ,
        'ratio' => true ,
        'maxWidth' => null ,
        'maxHeight' => null ,
        'minWidth' => null ,
        'minHeight' => null ,
        'maxPixels' => null ,
    );

    public function widthAndHeight( $width , $height ){
        return $this->setParameter( 'width' , $width )
            ->setParameter( 'height' , $height )
            ->setParameter( 'scale' , null );
    }

    public function maxWidthAndHeight( $width , $height ){
        return $this->reset()->setParameter( 'maxWidth' , $width )->setParameter( 'maxHeight' , $height );
    }

    public function minWidthAndHeight( $width , $height ){
        return $this->reset()->setParameter( 'minWidth' , $width )->setParameter( 'minHeight' , $height );
    }

    public function scale( $scale ){
        if( ! is_null( $scale ) ){
            $scale = strtolower( $scale );
            $scale = str_replace( '%' , 'p' , $scale );
            if( preg_match( '/^\d+/$' , $scale ) ){
                $scale.= 'p';
            }
            $scale = '!' . $scale;
        }
        return $this->reset()->setParameter( 'scale' , $scale );
    }

    public function contain( $bool = true ){
        $bool = ( boolean ) $bool;
        return $this->setParameter( 'contain' , $bool );
    }

    public function keepRatio( $bool = true ){
        $bool = ( boolean ) $bool;
        return $this->setParameter( 'ratio' , $bool );
    }

    protected function parameterToString(){
        if( $scale =  $this->getParameter( 'scale') ){
            return $scale;
        }
        $width = $this->getParameter( 'width' , '' );
        $height = $this->getParameter( 'height' , '' );
        $maxWidth = $this->getParameter( 'maxWidth' , '' );
        $maxHeight = $this->getParameter( 'maxHeight' , '' );
        $minWidth = $this->getParameter( 'minWidth' , '' );
        $minHeight = $this->getParameter( 'minHeight' , '' );
        if( $width || $height ){
            if( false == $this->getParameter('contain') ){
                return "!{$width}x{$height}r";
            }
            if( false == $this->getParameter( 'keepRatio' ) ){
                return "{$width}x{$height}!";
            }
        }
        if( $maxWidth && $maxHeight ){
            return "{$maxWidth}x{$maxHeight}>";
        }
        if( $minWidth && $minHeight ){
            return "{$minWidth}x{$minHeight}<";
        }
        if( $maxPixels = $this->getParameter( 'maxPixels' ) ){
            return "{$maxPixels}@";
        }
        return '';
    }
} 