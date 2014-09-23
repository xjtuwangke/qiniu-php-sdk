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
 * 图片水印
 * @class Image
 * @package QiniuAPI\Watermark
 */
class Image extends QiniuAPIParameter{

    public static $name = 'image';

    protected $default_parameters = array(
        'image' => null ,
        'dissolve' => 100 ,
        'gravity' => null ,
    );

    /**
     * 水印源图片网址，必须有效且返回一张图片
     * @param $url
     * @return $this
     */
    public function imageUrl( $url ){
        return $this->setParameter( 'image' , Utils::Qiniu_Encode( $url ) );
    }

    /**
     * 透明度，取值范围1-100，缺省值为100（完全不透明）
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
        return '' . $this->getParameter( 'image' ) . '/dissolve/' . $this->getParameter( 'dissolve' ) . $this->getParameter( 'gravity' );
    }
} 