<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-9-13
 * Time: 23:17
 */

namespace Qiniu;


class Qiniu_ImageInfo {

    public function MakeRequest($url)
    {
        return $url . "?imageInfo";
    }

} 