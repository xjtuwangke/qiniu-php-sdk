<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-9-13
 * Time: 21:17
 */

namespace Qiniu;


class QiniuRSEntryPathPair{
    public $src;
    public $dest;

    public function __construct($src, $dest)
    {
        $this->src = $src;
        $this->dest = $dest;
    }
}
