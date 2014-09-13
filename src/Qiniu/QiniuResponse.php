<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-9-13
 * Time: 20:58
 */

namespace Qiniu;


class QiniuResponse {
    public $StatusCode;
    public $Header;
    public $ContentLength;
    public $Body;

    public function __construct($code, $body)
    {
        $this->StatusCode = $code;
        $this->Header = array();
        $this->Body = $body;
        $this->ContentLength = strlen($body);
    }
} 