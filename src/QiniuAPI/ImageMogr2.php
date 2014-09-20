<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-9-19
 * Time: 14:42
 */

namespace QiniuAPI;

class ImageMogr2 extends ImageView2{

    protected $parameters = array(
        'auto-orient' => true ,  //根据原图EXIF信息自动旋正，便于后续处理建议放在首位。
        'thumbnail'   => null ,  //参看缩放操作参数表，缺省为不缩放。
        'strip'       => null ,  // 去除图片中的元信息
        'gravity'     => null ,  // 参看裁剪锚点参数表，只影响其后的裁剪偏移参数，缺省为左上角（NorthWest）。
        'crop'        => null ,  //参看裁剪操作参数表，缺省为不裁剪。
        'quality'     => null ,  //图片质量 取值范围1-100，缺省为85 如原图质量小于指定质量，则使用原图质量。
        'rotate'      => null ,  //旋转角度 取值范围1-360，缺省为不旋转。
        'format'      => null ,  //支持jpg、gif、png、webp等，缺省为原图格式。参考支持转换的图片格式
        'blur'        => null ,  //高斯模糊参数 <radius>是模糊半径，取值范围是[1,50]，<sigma>是正态分布的标准差，必须大于0。
        'interlance'  => null ,  //是否支持渐进显示 取值范围：1 支持渐进显示，0不支持渐进显示(缺省为0) 适用目标格式：jpg 效果：网速慢时，图片显示由模糊到清晰。
    );


    public function __construct(){
    }

    public function auto_orient( $bool = true ){
        $this->parameters['auto-orient'] = (boolean) $bool;
        return $this;
    }


    public function thumbnail(){

    }



    public function crop(){

    }



} 