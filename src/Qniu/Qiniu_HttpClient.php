<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-9-13
 * Time: 21:01
 */

namespace Qiniu;


class Qiniu_HttpClient {
    public function RoundTrip($req) // => ($resp, $error)
    {
        return Utils::Qiniu_Client_do( $req );
    }
} 