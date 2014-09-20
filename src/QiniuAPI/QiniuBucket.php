<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-9-19
 * Time: 17:46
 */

namespace QiniuAPI;

use Qiniu\QiniuRSGetPolicy;
use Qiniu\RSUtils;

class QiniuBucket {

    protected static $domain = null;

    protected static $isPrivate = false;

    public static function setDomain( $domain ){
        static::$domain = $domain;
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


} 