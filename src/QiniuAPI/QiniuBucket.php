<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-9-19
 * Time: 17:46
 */

namespace QiniuAPI;

use Qiniu\QiniuRSGetPolicy;
use QiniuAPI\PutPolicy\PutPolicy;
use Qiniu\RSUtils;

class QiniuBucket {

    protected static $domain = null;

    protected static $isPrivate = false;

    protected static $bucketName = null;

    public static function setDomain( $domain , $bucketName = null ){
        static::$domain = $domain . '.qiniudn.com';
        if( is_null( $bucketName ) ){
            $bucketName = $domain;
        }
        static::$bucketName = $bucketName;
    }

    public static function domain(){
        return static::$domain;
    }

    public static function bucketName(){
        return static::$bucketName;
    }

    public static function setPrivate( $private = true ){
        static::$isPrivate = (boolean) $private;
    }

    public static function makeURL( $key , $fops = array() ){
        $baseUrl = RSUtils::Qiniu_RS_MakeBaseUrl( static::$domain , $key );
        if( ! empty( $fops ) ){
            $baseUrl.= '?' . implode( '|' , $fops );
        }
        if( false == static::$isPrivate ){
            return $baseUrl;
        }
        else{
            $getPolicy = new QiniuRSGetPolicy();
            return $getPolicy->MakeRequest( $baseUrl , null );
        }
    }

    public static function entry( $key = null ){
        $bucket = new static;
        $entry = new QiniuEntry( $bucket , $key );
        return $entry;
    }

    public static function putPolicyFactory(){
        $policy = new PutPolicy();
        $policy->setScope( static::bucketName() )
            ->detectMime()
            ->returnBody( array(
                'hash' => '$(etag)' ,
                'fname' => '$(fname)' ,
                'mime'  => '$(mimeType)' ,
                'width' => '$(imageInfo.width)' ,
                'height' => '$(imageInfo.height)' ,
                'ext' => '$(ext)' ,
            ) );
        return $policy;
    }

    public static function putCallback(){
        //{"hash":"Fh-FspHbcv9S66tS76vazV4vrO6y","fname":"qiniu_test.jpg","mime":"image/jpeg","width":"245","height":"300","ext":".jpg"}
    }

    public static function putToken( $key ){
        $putPolicy = static::putPolicyFactory();
        $putPolicy->setScope( static::bucketName() , $key );
        return $putPolicy->token();
    }

}