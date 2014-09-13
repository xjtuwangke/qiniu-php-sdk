<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-9-13
 * Time: 21:16
 */

namespace Qiniu;


class Qiniu_RS_EntryPath{
    public $bucket;
    public $key;

    public function __construct($bucket, $key)
    {
        $this->bucket = $bucket;
        $this->key = $key;
    }
}