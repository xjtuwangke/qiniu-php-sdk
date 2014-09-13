<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-9-13
 * Time: 21:19
 */

namespace Qiniu;


class RSUtils {

    static function  Qiniu_RS_Put($self, $bucket, $key, $body, $putExtra) // => ($putRet, $err)
    {
        $putPolicy = new QiniuRSPutPolicy("$bucket:$key");
        $upToken = $putPolicy->Token($self->Mac);
        return QiniuPutExtra::Qiniu_Put($upToken, $key, $body, $putExtra);
    }

    static function  Qiniu_RS_PutFile($self, $bucket, $key, $localFile, $putExtra) // => ($putRet, $err)
    {
        $putPolicy = new QiniuRSPutPolicy("$bucket:$key");
        $upToken = $putPolicy->Token($self->Mac);
        return QiniuPutExtra::Qiniu_PutFile($upToken, $key, $localFile, $putExtra);
    }

    static function  Qiniu_RS_Rput($self, $bucket, $key, $body, $fsize, $putExtra) // => ($putRet, $err)
    {
        $putPolicy = new QiniuRSPutPolicy("$bucket:$key");
        $upToken = $putPolicy->Token($self->Mac);
        if ($putExtra == null) {
            $putExtra = new QiniuRioPutExtra($bucket);
        } else {
            $putExtra->Bucket = $bucket;
        }
        return QiniuRioUploadClient::Qiniu_Rio_Put($upToken, $key, $body, $fsize, $putExtra);
    }

    static function  Qiniu_RS_RputFile($self, $bucket, $key, $localFile, $putExtra) // => ($putRet, $err)
    {
        $putPolicy = new QiniuRSPutPolicy("$bucket:$key");
        $upToken = $putPolicy->Token($self->Mac);
        if ($putExtra == null) {
            $putExtra = new QiniuRioPutExtra($bucket);
        } else {
            $putExtra->Bucket = $bucket;
        }
        return QiniuRioUploadClient::Qiniu_Rio_PutFile($upToken, $key, $localFile, $putExtra);
    }

    static function  Qiniu_RS_MakeBaseUrl($domain, $key) // => $baseUrl
    {
        $keyEsc = str_replace("%2F", "/", rawurlencode($key));
        return "http://$domain/$keyEsc";
    }

    static function  Qiniu_RS_URIStat($bucket, $key)
    {
        return '/stat/' . Utils::Qiniu_Encode("$bucket:$key");
    }

    static function  Qiniu_RS_URIDelete($bucket, $key)
    {
        return '/delete/' . Utils::Qiniu_Encode("$bucket:$key");
    }

    static function  Qiniu_RS_URICopy($bucketSrc, $keySrc, $bucketDest, $keyDest)
    {
        return '/copy/' . Utils::Qiniu_Encode("$bucketSrc:$keySrc") . '/' . Utils::Qiniu_Encode("$bucketDest:$keyDest");
    }

    static function  Qiniu_RS_URIMove($bucketSrc, $keySrc, $bucketDest, $keyDest)
    {
        return '/move/' . Utils::Qiniu_Encode("$bucketSrc:$keySrc") . '/' . Utils::Qiniu_Encode("$bucketDest:$keyDest");
    }

    static function  Qiniu_RS_Stat($self, $bucket, $key) // => ($statRet, $error)
    {
        $QINIU_RS_HOST = Conf::$QINIU_RS_HOST;
        $uri = RSUtils::Qiniu_RS_URIStat($bucket, $key);
        return Utils::Qiniu_Client_Call($self, $QINIU_RS_HOST . $uri);
    }

    static function  Qiniu_RS_Delete($self, $bucket, $key) // => $error
    {
        $QINIU_RS_HOST = Conf::$QINIU_RS_HOST;
        $uri = RSUtils::Qiniu_RS_URIDelete($bucket, $key);
        return Utils::Qiniu_Client_CallNoRet($self, $QINIU_RS_HOST . $uri);
    }

    static function  Qiniu_RS_Move($self, $bucketSrc, $keySrc, $bucketDest, $keyDest) // => $error
    {
        $QINIU_RS_HOST = Conf::$QINIU_RS_HOST;
        $uri = RSUtils::Qiniu_RS_URIMove($bucketSrc, $keySrc, $bucketDest, $keyDest);
        return Utils::Qiniu_Client_CallNoRet($self, $QINIU_RS_HOST . $uri);
    }

    static function  Qiniu_RS_Copy($self, $bucketSrc, $keySrc, $bucketDest, $keyDest) // => $error
    {
        $QINIU_RS_HOST = Conf::$QINIU_RS_HOST;
        $uri = RSUtils::Qiniu_RS_URICopy($bucketSrc, $keySrc, $bucketDest, $keyDest);
        return Utils::Qiniu_Client_CallNoRet($self, $QINIU_RS_HOST . $uri);
    }

    static function  Qiniu_RS_Batch($self, $ops) // => ($data, $error)
    {
        $QINIU_RS_HOST = Conf::$QINIU_RS_HOST;
        $url = $QINIU_RS_HOST . '/batch';
        $params = 'op=' . implode('&op=', $ops);
        return Utils::Qiniu_Client_CallWithForm($self, $url, $params);
    }

    static function  Qiniu_RS_BatchStat($self, $entryPaths)
    {
        $params = array();
        foreach ($entryPaths as $entryPath) {
            $params[] = RSUtils::Qiniu_RS_URIStat($entryPath->bucket, $entryPath->key);
        }
        return RSUtils::Qiniu_RS_Batch($self,$params);
    }

    static function  Qiniu_RS_BatchDelete($self, $entryPaths)
    {
        $params = array();
        foreach ($entryPaths as $entryPath) {
            $params[] = RSUtils::Qiniu_RS_URIDelete($entryPath->bucket, $entryPath->key);
        }
        return RSUtils::Qiniu_RS_Batch($self, $params);
    }

    static function  Qiniu_RS_BatchMove($self, $entryPairs)
    {
        $params = array();
        foreach ($entryPairs as $entryPair) {
            $src = $entryPair->src;
            $dest = $entryPair->dest;
            $params[] = RSUtils::Qiniu_RS_URIMove($src->bucket, $src->key, $dest->bucket, $dest->key);
        }
        return RSUtils::Qiniu_RS_Batch($self, $params);
    }

    static function  Qiniu_RS_BatchCopy($self, $entryPairs)
    {
        $params = array();
        foreach ($entryPairs as $entryPair) {
            $src = $entryPair->src;
            $dest = $entryPair->dest;
            $params[] = RSUtils::Qiniu_RS_URICopy($src->bucket, $src->key, $dest->bucket, $dest->key);
        }
        return RSUtils::Qiniu_RS_Batch($self, $params);
    }

} 