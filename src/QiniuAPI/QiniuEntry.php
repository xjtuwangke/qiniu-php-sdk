<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/9/22
 * Time: 06:09
 */

namespace QiniuAPI;

use \Qiniu\Utils;

class QiniuEntry {

    protected $bucket = null;

    protected $key = null;

    public function __construct( QiniuBucket $bucket , $key = null ){
        $this->bucket = $bucket;
        $this->key = $key;
    }

    public function setBucket( QiniuBucket $bucket ){
        $this->bucket = $bucket;
        return $this;
    }

    public function setKey( $key ){
        $this->key = $key;
        return $this;
    }

    public function bucket(){
        return $this->bucket->bucketName();
    }

    public function key(){
        return $this->key;
    }

    public function entryURI(){
        return $this->bucket() . ':' . $this->key();
    }

    public function encodedEntryURI(){
        return Utils::Qiniu_Encode( $this->entryURI() );
    }

    public function url( $fops = array() ){
        return $this->bucket->makeURL( $this->key() , $fops );
    }
} 