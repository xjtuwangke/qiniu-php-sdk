<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-9-14
 * Time: 2:41
 */

namespace Qiniu;


class QiniuMac {

    public $AccessKey;
    public $SecretKey;

    public function __construct($accessKey = null , $secretKey = null )
    {
        if( is_null( $accessKey ) ){
            $accessKey = Conf::$QINIU_ACCESS_KEY;
        }
        if( is_null( $secretKey ) ){
            $secretKey = Conf::$QINIU_SECRET_KEY;
        }
        $this->AccessKey = $accessKey;
        $this->SecretKey = $secretKey;
    }

    public function Sign($data) // => $token
    {
        $sign = hash_hmac('sha1', $data, $this->SecretKey, true);
        return $this->AccessKey . ':' . Utils::Qiniu_Encode($sign);
    }

    public function SignWithData($data) // => $token
    {
        $data = Utils::Qiniu_Encode($data);
        return $this->Sign($data) . ':' . $data;
    }

    public function SignRequest($req, $incbody) // => ($token, $error)
    {
        $url = $req->URL;
        $url = parse_url($url['path']);
        $data = '';
        if (isset($url['path'])) {
            $data = $url['path'];
        }
        if (isset($url['query'])) {
            $data .= '?' . $url['query'];
        }
        $data .= "\n";

        if ($incbody) {
            $data .= $req->Body;
        }
        return $this->Sign($data);
    }

    public function VerifyCallback($auth, $url, $body) // ==> bool
    {
        $url = parse_url($url);
        $data = '';
        if (isset($url['path'])) {
            $data = $url['path'];
        }
        if (isset($url['query'])) {
            $data .= '?' . $url['query'];
        }
        $data .= "\n";

        $data .= $body;
        $token = 'QBox ' . $this->Sign($data);
        return $auth === $token;
    }
} 