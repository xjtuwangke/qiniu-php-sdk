<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-9-14
 * Time: 1:39
 */

namespace Qiniu;



class QiniuRioPutExtra {

    public $Bucket = null;		// 必选（未来会没有这个字段）。
    public $Params = null;
    public $MimeType = null;
    public $ChunkSize = 0;		// 可选。每次上传的Chunk大小
    public $TryTimes = 3;		// 可选。尝试次数
    public $Progresses = null;	// 可选。上传进度：[]BlkputRet
    public $Notify = null;		// 进度通知：func(blkIdx int, blkSize int, ret *BlkputRet)
    public $NotifyErr = null;	// 错误通知：func(blkIdx int, blkSize int, err error)

    const QINIU_RIO_BLOCK_BITS = 22;
    //static $QINIU_RIO_BLOCK_SIZE = 1 << QINIU_RIO_BLOCK_BITS;


    public function __construct($bucket = null) {
        $this->Bucket = $bucket;
    }

    static function QINIU_RIO_BLOCK_SIZE(){
        return ( 1 << QiniuRioPutExtra::QINIU_RIO_BLOCK_BITS );
    }


    static function Qiniu_Rio_BlockCount($fsize) // => $blockCnt
    {
        return ($fsize + ( QiniuRioPutExtra::QINIU_RIO_BLOCK_SIZE()  - 1)) >> QiniuRioPutExtra::QINIU_RIO_BLOCK_BITS;
    }

// ----------------------------------------------------------
// internal func Qiniu_Rio_Mkblock/Mkfile

    static function Qiniu_Rio_Mkblock($self, $host, $reader, $size) // => ($blkputRet, $err)
    {
        if (is_resource($reader)) {
            $body = fread($reader, $size);
            if ($body === false) {
                $err = new QiniuError(0, 'fread failed');
                return array(null, $err);
            }
        } else {
            list($body, $err) = $reader->Read($size);
            if ($err !== null) {
                return array(null, $err);
            }
        }
        if (strlen($body) != $size) {
            $err = new QiniuError(0, 'fread failed: unexpected eof');
            return array(null, $err);
        }

        $url = $host . '/mkblk/' . $size;
        return Utils::Qiniu_Client_CallWithForm($self, $url, $body, 'application/octet-stream');
    }


    static function Qiniu_Rio_Mkfile($self, $host, $key, $fsize, $extra) // => ($putRet, $err)
    {
        $url = $host . '/mkfile/' . $fsize;
        if ($key !== null) {
            $url .= '/key/' . Utils::Qiniu_Encode($key);
        }
        if (!empty($extra->MimeType)) {
            $url .= '/mimeType/' . Utils::Qiniu_Encode($extra->MimeType);
        }

        if (!empty($extra->Params)) {
            foreach ($extra->Params as $k=>$v) {
                $url .= "/" . $k . "/" . Utils::Qiniu_Encode($v);
            }
        }

        $ctxs = array();
        foreach ($extra->Progresses as $prog) {
            $ctxs []= $prog['ctx'];
        }
        $body = implode(',', $ctxs);

        return Utils::Qiniu_Client_CallWithForm($self, $url, $body, 'application/octet-stream');
    }

} 