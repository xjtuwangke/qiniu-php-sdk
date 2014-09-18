<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-9-19
 * Time: 4:36
 */

namespace QiniuImage;

use Qiniu\QiniuImageInfo;
use Qiniu\QiniuImageView2;
use Qiniu\RSUtils;
use Qiniu\QiniuRSGetPolicy;

class Thumbnail {

    protected static $domain = null;

    protected $imageInfo = null;

    protected $imageView2 = null;

    protected $cutting = false;

    protected $longEdge = null;

    protected $shortEdge = null;

    protected $width  = null;

    protected $height = null;

    protected $edge = true;

    protected $contain = true;

    protected static $isPrivate = false;

    public static function setDomain( $domain ){
        static::$domain = $domain;
    }

    public static function setPrivate( $private = true ){
        static::$isPrivate = (boolean) $private;
    }

    public function __construct(){
        $this->imageInfo = new QiniuImageInfo();
        $this->imageView2 = new QiniuImageView2();
        return $this;
    }

    public function quality( $quality ){
        $this->imageView2->Quality = $quality;
        return $this;
    }

    public function cut( $cut = true ){
        $this->cutting = (boolean)$cut;
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
        $this->imageView2->Format = $format;
        return $this;
    }

    protected function imageViewSetWidthAndHeight(){
        if( $this->edge ){
            if( $this->longEdge ){
                $this->imageView2->Width = $this->longEdge;
            }
            if( $this->shortEdge ){
                $this->imageView2->Height = $this->shortEdge;
            }
        }
        else{
            if( $this->width ){
                $this->imageView2->Width = $this->width;
            }
            if( $this->height ){
                $this->imageView2->Height = $this->height;
            }
        }
        return $this;
    }

    public function imageView2( $key ){
        $this->imageViewSetWidthAndHeight();
        if( false == $this->cutting && true == $this->edge && true == $this->contain ){ //011
            /*
             * 限定缩略图的长边最多为<LongEdge>，短边最多为<ShortEdge>，进行等比缩放，不裁剪。如果只指定 w 参数则表示限定长边（短边自适应），只指定 h 参数则表示限定短边（长边自适应）。
             */
            $this->imageView2->Mode = 0;
        }
        elseif( true == $this->cutting && false == $this->edge ){ //10x
            /*
             * 限定缩略图的宽最少为<Width>，高最少为<Height>，进行等比缩放，居中裁剪。
             * 转后的缩略图通常恰好是 <Width>x<Height> 的大小（有一个边缩放的时候会因为超出矩形框而被裁剪掉多余部分）。
             * 如果只指定 w 参数或只指定 h 参数，代表限定为长宽相等的正方图。
             */
            $this->imageView2->Mode = 1;
        }
        elseif( false == $this->cutting && false == $this->edge && true == $this->contain ){ //001
            /*
             * 限定缩略图的宽最多为<Width>，高最多为<Height>，进行等比缩放，不裁剪。
             * 如果只指定 w 参数则表示限定长边（短边自适应），只指定 h 参数则表示限定短边（长边自适应）。
             * 它和模式0类似，区别只是限定宽和高，不是限定长边和短边。
             * 从应用场景来说，模式0适合移动设备上做缩略图，模式2适合PC上做缩略图。
             */
            $this->imageView2->Mode = 2;
        }
        elseif( false == $this->cutting && false == $this->edge && false == $this->contain ){ //000
            /*
             * 限定缩略图的宽最少为<Width>，高最少为<Height>，进行等比缩放，不裁剪。
             * 你可以理解为模式1是模式3的结果再做居中裁剪得到的。
             */
            $this->imageView2->Mode = 3;
        }
        elseif( false == $this->cutting && true == $this->edge && false == $this->contain ){ //010
            /*
             * 限定缩略图的长边最少为<LongEdge>，短边最少为<ShortEdge>，进行等比缩放，不裁剪。
             * 这个模式很适合在手持设备做图片的全屏查看（把这里的长边短边分别设为手机屏幕的分辨率即可），生成的图片尺寸刚好充满整个屏幕（某一个边可能会超出屏幕）。
             */
            $this->imageView2->Mode = 4;
        }
        elseif( true == $this->cutting && true == $this->edge && false == $this->contain ){ //110
            /*
             * 限定缩略图的长边最少为<LongEdge>，短边最少为<ShortEdge>，进行等比缩放，居中裁剪。
             * 同上模式4，但超出限定的矩形部分会被裁剪。
             */
            $this->imageView2->Mode = 5;
        }
        elseif( true == $this->cutting && true == $this->edge && true == $this->contain ){ //111
            /*
             * 限定缩略图的长边最少为<LongEdge>，短边最少为<ShortEdge>，进行等比缩放，居中裁剪。
             * 同上模式4，但超出限定的矩形部分会被裁剪。
             */
            $this->imageView2->Mode = 5;
        }

        $baseUrl = RSUtils::Qiniu_RS_MakeBaseUrl( static::$domain , $key );
        $imgViewUrl = $this->imageView2->MakeRequest( $baseUrl );

        if( false == static::$isPrivate ){
            return $imgViewUrl;
        }
        else{
            $getPolicy = new QiniuRSGetPolicy();
            return $getPolicy->MakeRequest( $imgViewUrl , null );
        }

    }

    public function imageMogr2( $key ){

    }

} 