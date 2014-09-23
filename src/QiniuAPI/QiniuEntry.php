<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/9/22
 * Time: 06:09
 */

namespace QiniuAPI;

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
} 