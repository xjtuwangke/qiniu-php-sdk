<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-9-19
 * Time: 17:46
 */

namespace QiniuAPI;

use Qiniu\QiniuMacHttpClient;
use Qiniu\QiniuPutExtra;
use Qiniu\QiniuRSGetPolicy;
use Qiniu\QiniuRSPutPolicy;
use QiniuAPI\PutPolicy\PutPolicy;
use Qiniu\RSUtils;

/**
 * Qiniu Bucket抽象类
 * @class QiniuBucket
 * @package QiniuAPI
 */

class QiniuBucket {

    /**
     * 七牛bucket的公共域名，比如bucket1.qiniudn.com或是bucket2.u.quniudn.com
     * @var string | null
     */
    protected static $domain = null;

    /**
     * 是否是私有bucket
     * @var bool
     */
    protected static $isPrivate = false;

    /**
     * bucket name
     * @var string|null
     */
    protected static $bucketName = null;

    /**
     * @param  string $domain   比如'bucket1'或是'bucket2.u'
     * @param null|string $bucketName bucket名,默认与$domain一致
     */
    public static function setDomain( $domain , $bucketName = null ){
        static::$domain = $domain . '.qiniudn.com';
        if( is_null( $bucketName ) ){
            $bucketName = $domain;
        }
        static::$bucketName = $bucketName;
    }

    /**
     * 返回Bucket::$domain
     * @return null|string
     */
    public static function domain(){
        return static::$domain;
    }

    /**
     * 返回bucket name
     * @return null|string
     */
    public static function bucketName(){
        return static::$bucketName;
    }

    /**
     * 设为私有bucket
     * @param bool $private
     */
    public static function setPrivate( $private = true ){
        static::$isPrivate = (boolean) $private;
    }

    /**
     * 根据$key和一系列FOP操作获取资源URL
     * @param       $key
     * @param array $fops
     * @return string
     */
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

    /**
     * 构造QiniuBucket下的QiniuEntry
     * @param null $key
     * @return QiniuEntry
     */
    public static function entry( $key = null ){
        $bucket = new static;
        $entry = new QiniuEntry( $bucket , $key );
        return $entry;
    }

    /**
     * 生成默认的PutPolicy
     * @return PutPolicy
     */
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

    /**
     * 获取putToken
     *
     * @example
     *     QiniuBucket::setDomain('rollong-sandbox' );
     *     $key = QiniuBucket::putToken( 'test' );
     *
     * @param $key
     * @return string
     */
    public static function putToken( $key ){
        $putPolicy = static::putPolicyFactory();
        $putPolicy->setScope( static::bucketName() , $key );
        return $putPolicy->token();
    }

    /**
     * 上传单个文件
     * @param                  $file  string 本地文件文件名
     * @param                  $key   string 七牛目标文件名
     * @param QiniuRSPutPolicy $putPolicy 上传策略
     * @return array [ $ret , $err , $qiniuEntry ]
     */
    public static function put( $file , $key , QiniuRSPutPolicy $putPolicy = null ){
        if( is_null( $putPolicy ) ){
            $putPolicy = new QiniuRSPutPolicy( static::$bucketName . ":" . $key );
        }
        $upToken = $putPolicy->Token(null);
        list($ret, $err) = QiniuPutExtra::Qiniu_PutFile($upToken, $key, $file , null);
        $qiniuEntry = static::entry( $key );
        return array( $ret , $err , $qiniuEntry );
    }

    /**
     * 删除单个文件
     * @param $key  string 七牛目标文件名
     * @return array [ $ret , $err ]
     */
    public static function delete( $key ){
        $client = new QiniuMacHttpClient(null);
        $ret = RSUtils::Qiniu_RS_Delete($client, static::$bucketName, $key);
        if( $ret ){
            return array( false , $ret );
        }
        else{
            return array( true , $ret );
        }
    }

    /**
     * @param $key string 七牛目标文件名
     * @return array [ $ret , $err ]
     */
    public static function ls( $key ){
        $client = new QiniuMacHttpClient(null);
        list($ret, $err) = RSUtils::Qiniu_RS_Stat( $client, static::$bucketName, $key );
        return array( $ret , $err );
    }

    /**
     * @param            $oldKey string 源文件名
     * @param QiniuEntry $target 目标文件
     * @return array
     */
    public static function copy( $oldKey , QiniuEntry $target ){
        $client = new QiniuMacHttpClient(null);
        $newBucketName = $target->bucket();
        $newKey = $target->key();
        $err = RSUtils::Qiniu_RS_Copy( $client , static::$bucketName , $oldKey , $newBucketName , $newKey );
        if( is_null( $err ) ){
            return array( true , $err );
        }
        else{
            return array( false , $err );
        }
    }

    /**
     * @param            $oldKey string 源文件名
     * @param QiniuEntry $target 目标文件
     * @return array
     */
    public static function move( $oldKey , QiniuEntry $target ){
        $client = new QiniuMacHttpClient(null);
        $newBucketName = $target->bucket();
        $newKey = $target->key();
        $err = RSUtils::Qiniu_RS_Move( $client , static::$bucketName , $oldKey , $newBucketName , $newKey );
        if( is_null( $err ) ){
            return array( true , $err );
        }
        else{
            return array( false , $err );
        }
    }

}