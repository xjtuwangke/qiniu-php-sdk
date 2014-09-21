<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-9-19
 * Time: 4:36
 */

namespace QiniuAPI\ImageView2;

use \QiniuAPI\QiniuFop;

class ImageView2 extends QiniuFop{

    protected $crop = false;

    protected $longEdge = null;

    protected $shortEdge = null;

    protected $width  = null;

    protected $height = null;

    protected $edge = true;

    protected $contain = true;

    protected $quality = 85;

    protected $format = null;

    protected $interlace = 0;

    protected static $name = 'imageView2';

    public function __construct(){
        return $this->reset();
    }

    public function reset(){
        $this->crop = false;
        $this->longEdge = null;
        $this->shortEdge = null;
        $this->width = null;
        $this->height = null;
        $this->edge = true;
        $this->contain = true;
        $this->quality = 85;
        $this->format = null;
        $this->interlace = 0;
        return $this;
    }

    public function quality( $quality ){
        $this->quality = $quality;
        return $this;
    }

    public function crop( $crop = true ){
        $this->crop = (boolean)$crop;
        return $this;
    }

    public function longAndShortEdge( $long , $short ){
        $this->longEdge = $long;
        $this->shortEdge = $short;
        $this->edge = true;
        return $this;
    }

    public function widthAndHeight( $width , $height ){
        $this->width = $width;
        $this->height = $height;
        $this->edge = false;
        return $this;
    }

    public function cover(){
        $this->contain = false;
        return $this;
    }

    public function contain(){
        $this->contain = true;
        return $this;
    }

    public function format( $format ){
        $this->format = $format;
        return $this;
    }

    public function interlace( $bool ){
        $this->interlace = $bool?1:0;
        return $this;
    }

    protected function imageViewSetWidthAndHeight(){
        $string = '';
        if( $this->edge ){
            if( $this->longEdge ){
                $string.= '/w/' . $this->longEdge;
            }
            if( $this->shortEdge ){
                $string.= '/h/' . $this->shortEdge;
            }
        }
        else{
            if( $this->width ){
                $string.= '/w/' . $this->width;
            }
            if( $this->height ){
                $string.= '/h/' . $this->height;
            }
        }
        return $string;
    }

    public function __toString(){
        if( false == $this->crop && true == $this->edge && true == $this->contain ){ //011
            /*
             * 限定缩略图的长边最多为<LongEdge>，短边最多为<ShortEdge>，进行等比缩放，不裁剪。如果只指定 w 参数则表示限定长边（短边自适应），只指定 h 参数则表示限定短边（长边自适应）。
             */
            $this->mode = 0;
        }
        elseif( true == $this->crop && false == $this->edge ){ //10x
            /*
             * 限定缩略图的宽最少为<Width>，高最少为<Height>，进行等比缩放，居中裁剪。
             * 转后的缩略图通常恰好是 <Width>x<Height> 的大小（有一个边缩放的时候会因为超出矩形框而被裁剪掉多余部分）。
             * 如果只指定 w 参数或只指定 h 参数，代表限定为长宽相等的正方图。
             */
            $this->mode = 1;
        }
        elseif( false == $this->crop && false == $this->edge && true == $this->contain ){ //001
            /*
             * 限定缩略图的宽最多为<Width>，高最多为<Height>，进行等比缩放，不裁剪。
             * 如果只指定 w 参数则表示限定长边（短边自适应），只指定 h 参数则表示限定短边（长边自适应）。
             * 它和模式0类似，区别只是限定宽和高，不是限定长边和短边。
             * 从应用场景来说，模式0适合移动设备上做缩略图，模式2适合PC上做缩略图。
             */
            $this->mode = 2;
        }
        elseif( false == $this->crop && false == $this->edge && false == $this->contain ){ //000
            /*
             * 限定缩略图的宽最少为<Width>，高最少为<Height>，进行等比缩放，不裁剪。
             * 你可以理解为模式1是模式3的结果再做居中裁剪得到的。
             */
            $this->mode = 3;
        }
        elseif( false == $this->crop && true == $this->edge && false == $this->contain ){ //010
            /*
             * 限定缩略图的长边最少为<LongEdge>，短边最少为<ShortEdge>，进行等比缩放，不裁剪。
             * 这个模式很适合在手持设备做图片的全屏查看（把这里的长边短边分别设为手机屏幕的分辨率即可），生成的图片尺寸刚好充满整个屏幕（某一个边可能会超出屏幕）。
             */
            $this->mode = 4;
        }
        elseif( true == $this->crop && true == $this->edge && false == $this->contain ){ //110
            /*
             * 限定缩略图的长边最少为<LongEdge>，短边最少为<ShortEdge>，进行等比缩放，居中裁剪。
             * 同上模式4，但超出限定的矩形部分会被裁剪。
             */
            $this->mode = 5;
        }
        elseif( true == $this->crop && true == $this->edge && true == $this->contain ){ //111
            /*
             * 限定缩略图的长边最少为<LongEdge>，短边最少为<ShortEdge>，进行等比缩放，居中裁剪。
             * 同上模式4，但超出限定的矩形部分会被裁剪。
             */
            $this->mode = 5;
        }

        $fop = static::$name . '/' . $this->mode . $this->imageViewSetWidthAndHeight() . '/q/' . $this->quality;
        if( $this->format ){
            $fop.= '/format/' . $this->format;
        }
        if( $this->interlace ){
            $fop.= '/interlace/' . $this->interlace;
        }
        return $fop;

    }

} 