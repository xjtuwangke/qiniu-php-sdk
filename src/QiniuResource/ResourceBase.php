<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-9-19
 * Time: 17:46
 */

namespace QiniuResource;

use Qiniu\QiniuRSGetPolicy;

class ResourceBase {

    protected static $domain = null;

    protected static $isPrivate = false;

    public static function setDomain( $domain ){
        static::$domain = $domain;
    }

    public static function setPrivate( $private = true ){
        static::$isPrivate = (boolean) $private;
    }

    public static function getURL( $url ){
        if( false == static::$isPrivate ){
            return $url;
        }
        else{
            $getPolicy = new QiniuRSGetPolicy();
            return $getPolicy->MakeRequest( $url , null );
        }
    }


} 