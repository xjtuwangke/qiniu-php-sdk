<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-9-13
 * Time: 20:57
 */

namespace Qiniu;


class QiniuError {

    public $Err;	 // string
    public $Reqid;	 // string
    public $Details; // []string
    public $Code;	 // int

    public function __construct($code, $err)
    {
        $this->Code = $code;
        $this->Err = $err;
    }

} 