<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-9-13
 * Time: 21:15
 */

namespace Qiniu;


class Qiniu_RS_GetPolicy{

    public $Expires;

    public function MakeRequest($baseUrl, $mac) // => $privateUrl
    {
        $deadline = $this->Expires;
        if ($deadline == 0) {
            $deadline = 3600;
        }
        $deadline += time();

        $pos = strpos($baseUrl, '?');
        if ($pos !== false) {
            $baseUrl .= '&e=';
        } else {
            $baseUrl .= '?e=';
        }
        $baseUrl .= $deadline;

        $token = Utils::Qiniu_Sign($mac, $baseUrl);
        return "$baseUrl&token=$token";
    }
}