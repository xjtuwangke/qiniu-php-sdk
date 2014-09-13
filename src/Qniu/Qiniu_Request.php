<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-9-13
 * Time: 20:57
 */

namespace Qiniu;


class Qiniu_Request {

    public $URL;
    public $Header;
    public $Body;
    public $UA;

    public function __construct($url, $body)
    {
        $this->URL = $url;
        $this->Header = array();
        $this->Body = $body;
        $this->UA = Utils::Qiniu_UserAgent();
    }

} 