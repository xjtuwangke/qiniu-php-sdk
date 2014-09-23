<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/9/22
 * Time: 06:00
 */

namespace QiniuAPI\VFrame;

use \QiniuAPI\QiniuFop;

/**
 * 视频截图 vframe
 * @class VFrame
 * @package QiniuAPI\VFrame
 */
class VFrame extends QiniuFop{

    protected static $name = 'vframe';

    protected $default_parameters = array(
        '__format' => null ,
        'offset' => null ,
        'w' => null ,
        'h' => null ,
        'rotate' => null ,
    );

    /**
     * 输出的目标截图格式，支持jpg、png等。
     * @param $format string
     * @return $this
     */
    public function format( $format ){
        return $this->setParameter( '__format' , $format );
    }

    /**
     * 指定截取视频的时刻，单位：秒。
     * @param $offset int
     * @return $this
     */
    public function offset( $offset ){
        return $this->setParameter( 'offset' , $offset );
    }

    /**
     * 缩略图宽度，单位：像素（px），取值范围为1-1920。
     * @param $width
     * @return $this
     */
    public function width( $width ){
        return $this->setParameter( 'width' , $width );
    }

    /**
     * 缩略图高度，单位：像素（px），取值范围为1-1080。
     * @param $height
     * @return $this
     */
    public function height( $height ){
        return $this->setParameter( 'height' , $height );
    }

    /**
     * 指定顺时针旋转的度数，可取值为90、180、270、auto，默认为不旋转。
     * @param $degree
     * @return $this
     */
    public function rotate( $degree ){
        return $this->setParameter( 'rotate' , $degree );
    }

}