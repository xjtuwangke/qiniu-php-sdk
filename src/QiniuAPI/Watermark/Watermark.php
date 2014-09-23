<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/9/22
 * Time: 03:44
 */

namespace QiniuAPI\Watermark;

use \QiniuAPI\QiniuFop;
use \QiniuAPI\QiniuAPIParameter;

/**
 * 水印（watermark）
 * @class Watermark
 * @package QiniuAPI\Watermark
 */
class Watermark extends QiniuFop{

    protected static $name = 'watermark/3';

    /**
     * 增加一个文字水印(QiniuAPI\Watermark\Text)或是图片水印(QiniuAPI\Watermark\Image)
     * @param QiniuAPIParameter $watermark
     * @return $this
     */
    public function addWatermark( QiniuAPIParameter $watermark ){
        $this->parameters[] = $watermark;
        return $this;
    }

} 