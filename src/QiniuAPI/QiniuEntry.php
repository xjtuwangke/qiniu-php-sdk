<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/9/22
 * Time: 06:09
 */

namespace QiniuAPI;

use Qiniu\QiniuRSPutPolicy;
use \Qiniu\Utils;

/**
 * Qiniu Entry抽象类
 * @class QiniuEntry
 * @package QiniuAPI
 */

class QiniuEntry {

    /**
     * Bucket指针
     * @var null|QiniuBucket
     */
    protected $bucket = null;

    /**
     * key
     * @var null|string
     */
    protected $key = null;

    public function __construct( QiniuBucket $bucket , $key = null ){
        $this->bucket = $bucket;
        $this->key = $key;
    }

    /**
     * @param QiniuBucket $bucket
     * @return $this
     */
    public function setBucket( QiniuBucket $bucket ){
        $this->bucket = $bucket;
        return $this;
    }

    /**
     * @param $key
     * @return $this
     */
    public function setKey( $key ){
        $this->key = $key;
        return $this;
    }

    /**
     * 获取bucketName
     * @return null|string
     */
    public function bucket(){
        if( $this->bucket ){
            return $this->bucket->bucketName();
        }
        else{
            return null;
        }
    }

    /**
     * @return null|string
     */
    public function key(){
        return $this->key;
    }

    /**
     * 返回bucket:key形式的entryURI
     * @return string
     */
    public function entryURI(){
        return $this->bucket() . ':' . $this->key();
    }

    /**
     * bucket:key形式的entryURI做urlsafe_base64_encode
     * @return mixed
     */
    public function encodedEntryURI(){
        return Utils::Qiniu_Encode( $this->entryURI() );
    }

    /**
     * 返回entry的公网url
     * @param array $fops fop操作组成的数组
     * @return string
     */
    public function url( $fops = array() ){
        return $this->bucket->makeURL( $this->key() , $fops );
    }


    /**
     * 返回image entry的imageInfo
     * @return array
     */
    public function imageInfo(){
        return $this->jsonResponse( [ 'imageInfo' ] );
    }

    /**
     * 返回image entry的exif信息
     * @return array
     */
    public function exif(){
        return $this->jsonResponse( [ 'exif' ] );
    }

    /**
     * 返回image entry的图片主色调信息
     * @return array
     */
    public function imageAve(){
        return $this->jsonResponse( [ 'imageAve' ] );
    }

    /**
     * 解析服务器返回的json response
     * @param array $fops
     * @return array
     */
    public function jsonResponse( array $fops ){
        $url = $this->bucket->makeURL( $this->key() , $fops );
        $ch = curl_init( $url );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $content = curl_exec($ch);
        curl_close($ch);
        return json_decode( $content , true );
    }

    /**
     * @param                  $file
     * @param QiniuRSPutPolicy $putPolicy
     * @return array
     */
    public function put( $file , QiniuRSPutPolicy $putPolicy = null ){
        return $this->bucket->put( $file , $this->key() , $putPolicy );
    }

    /**
     * 删除单个文件
     * @return array [ $ret , $err ]
     */
    public function delete(){
        return $this->bucket->delete( $this->key() );
    }

    /**
     * 获取文件信息
     * @return array [ $ret , $err ]
     */
    public function ls(){
       return $this->bucket->ls( $this->key() );
    }

    /**
     * 复制
     * @param QiniuEntry $target 目标文件
     * @return array
     */
    public function copy( QiniuEntry $target ){
        return $this->bucket->copy( $this->key() , $target );
    }

    /**
     * 移动
     * @param QiniuEntry $target 目标文件
     * @return array
     */
    public function move( QiniuEntry $target ){
        return $this->bucket->move( $this->key() , $target );
    }
} 