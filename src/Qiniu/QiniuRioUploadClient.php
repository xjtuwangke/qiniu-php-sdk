<?php

namespace Qiniu;
// ----------------------------------------------------------
// class QiniuRioUploadClient

class QiniuRioUploadClient
{
	public $uptoken;

	public function __construct($uptoken)
	{
		$this->uptoken = $uptoken;
	}

	public function RoundTrip($req) // => ($resp, $error)
	{
		$token = $this->uptoken;
		$req->Header['Authorization'] = "UpToken $token";
		return Utils::Qiniu_Client_do($req);
	}

    static function Qiniu_Rio_Put($upToken, $key, $body, $fsize, $putExtra) // => ($putRet, $err)
    {
        $QINIU_UP_HOST = Conf::$QINIU_UP_HOST;

        $self = new QiniuRioUploadClient($upToken);

        $progresses = array();
        $uploaded = 0;
        while ($uploaded < $fsize) {
            $tried = 0;
            $tryTimes = ($putExtra->TryTimes > 0) ? $putExtra->TryTimes : 1;
            $blkputRet = null;
            $err = null;
            if ($fsize < $uploaded + QiniuRioPutExtra::QINIU_RIO_BLOCK_SIZE() ) {
                $bsize = $fsize - $uploaded;
            } else {
                $bsize = QiniuRioPutExtra::QINIU_RIO_BLOCK_SIZE();
            }
            while ($tried < $tryTimes) {
                list($blkputRet, $err) = QiniuRioPutExtra::Qiniu_Rio_Mkblock($self, $QINIU_UP_HOST, $body, $bsize);
                if ($err === null) {
                    break;
                }
                $tried += 1;
                continue;
            }
            if ($err !== null) {
                return array(null, $err);
            }
            if ($blkputRet === null ) {
                $err = new QiniuError(0, "rio: uploaded without ret");
                return array(null, $err);
            }
            $uploaded += $bsize;
            $progresses []= $blkputRet;
        }

        $putExtra->Progresses = $progresses;
        return QiniuRioPutExtra::Qiniu_Rio_Mkfile($self, $QINIU_UP_HOST, $key, $fsize, $putExtra);
    }

    static function Qiniu_Rio_PutFile($upToken, $key, $localFile, $putExtra) // => ($putRet, $err)
    {
        $fp = fopen($localFile, 'rb');
        if ($fp === false) {
            $err = new QiniuError(0, 'fopen failed');
            return array(null, $err);
        }

        $fi = fstat($fp);
        $result = QiniuRioUploadClient::Qiniu_Rio_Put($upToken, $key, $fp, $fi['size'], $putExtra);
        fclose($fp);
        return $result;
    }

}

