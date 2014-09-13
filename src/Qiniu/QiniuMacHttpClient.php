<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-9-13
 * Time: 21:02
 */

namespace Qiniu;


class QiniuMacHttpClient {

    public $Mac;

    public function __construct($mac)
    {
        $this->Mac = Utils::Qiniu_RequireMac( $mac );
    }

    public function RoundTrip($req) // => ($resp, $error)
    {
        $incbody = Utils::Qiniu_Client_incBody( $req );
        $token = $this->Mac->SignRequest($req, $incbody);
        $req->Header['Authorization'] = "QBox $token";
        return Utils::Qiniu_Client_do( $req );
    }

} 