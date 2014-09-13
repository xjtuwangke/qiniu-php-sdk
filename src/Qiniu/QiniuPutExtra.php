<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-9-13
 * Time: 21:21
 */

namespace Qiniu;


class QiniuPutExtra {

    public $Params = null;
    public $MimeType = null;
    public $Crc32 = 0;
    public $CheckCrc = 0;


    static function Qiniu_Put($upToken, $key, $body, $putExtra) // => ($putRet, $err)
    {
        $QINIU_UP_HOST = Conf::$QINIU_UP_HOST;

        if ($putExtra === null) {
            $putExtra = new QiniuPutExtra;
        }

        $fields = array('token' => $upToken);
        if ($key === null) {
            $fname = '?';
        } else {
            $fname = $key;
            $fields['key'] = $key;
        }
        if ($putExtra->CheckCrc) {
            $fields['crc32'] = $putExtra->Crc32;
        }
        if ($putExtra->Params) {
            foreach ($putExtra->Params as $k=>$v) {
                $fields[$k] = $v;
            }
        }

        $files = array(array('file', $fname, $body, $putExtra->MimeType));

        $client = new QiniuHttpClient;
        return   Utils::Qiniu_Client_CallWithMultipartForm($client, $QINIU_UP_HOST, $fields, $files);
    }

    static function createFile($filename, $mime)
    {
        // PHP 5.5 introduced a CurlFile object that deprecates the old @filename syntax
        // See: https://wiki.php.net/rfc/curl-file-upload
        if (function_exists('curl_file_create')) {
            return curl_file_create($filename, $mime);
        }

        // Use the old style if using an older version of PHP
        $value = "@{$filename}";
        if (!empty($mime)) {
            $value .= ';type=' . $mime;
        }

        return $value;
    }

    static function Qiniu_PutFile($upToken, $key, $localFile, $putExtra) // => ($putRet, $err)
    {
        $QINIU_UP_HOST = Conf::$QINIU_UP_HOST;

        if ($putExtra === null) {
            $putExtra = new QiniuPutExtra;
        }

        $fields = array('token' => $upToken, 'file' => QiniuPutExtra::createFile($localFile, $putExtra->MimeType));
        if ($key === null) {
            $fname = '?';
        } else {
            $fname = $key;
            $fields['key'] = $key;
        }
        if ($putExtra->CheckCrc) {
            if ($putExtra->CheckCrc === 1) {
                $hash = hash_file('crc32b', $localFile);
                $array = unpack('N', pack('H*', $hash));
                $putExtra->Crc32 = $array[1];
            }
            $fields['crc32'] = sprintf('%u', $putExtra->Crc32);
        }
        if ($putExtra->Params) {
            foreach ($putExtra->Params as $k=>$v) {
                $fields[$k] = $v;
            }
        }

        $client = new QiniuHttpClient;
        return Utils::Qiniu_Client_CallWithForm($client, $QINIU_UP_HOST, $fields, 'multipart/form-data');
    }

} 