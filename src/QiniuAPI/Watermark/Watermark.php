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

class Watermark extends QiniuFop{

    protected static $name = 'watermark/3';

    public function addWatermark( QiniuAPIParameter $watermark ){
        $this->parameters[] = $watermark;
        return $this;
    }

} 