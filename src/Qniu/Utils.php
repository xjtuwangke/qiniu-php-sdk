<?php

namespace Qiniu;

class Utils{

    static function Qiniu_Encode($str) // URLSafeBase64Encode
    {
        $find = array('+', '/');
        $replace = array('-', '_');
        return str_replace($find, $replace, base64_encode($str));
    }


    static function Qiniu_Decode($str)
    {
        $find = array('-', '_');
        $replace = array('+', '/');
        return base64_decode(str_replace($find, $replace, $str));
    }

    static function Qiniu_Client_ret($resp) // => ($data, $error)
    {
        $code = $resp->StatusCode;
        $data = null;
        if ($code >= 200 && $code <= 299) {
            if ($resp->ContentLength !== 0) {
                $data = json_decode($resp->Body, true);
                if ($data === null) {
                    $err_msg = function_exists('json_last_error_msg') ? json_last_error_msg() : "error with content:" . $resp->Body;
                    $err = new Qiniu_Error(0, $err_msg);
                    return array(null, $err);
                }
            }
            if ($code === 200) {
                return array($data, null);
            }
        }
        return array($data, Utils::Qiniu_ResponseError($resp));
    }

    static function Qiniu_Client_Call($self, $url) // => ($data, $error)
    {
        $u = array('path' => $url);
        $req = new Qiniu_Request($u, null);
        list($resp, $err) = $self->RoundTrip($req);
        if ($err !== null) {
            return array(null, $err);
        }
        return Utils::Qiniu_Client_ret($resp);
    }

    static function Qiniu_Client_CallNoRet($self, $url) // => $error
    {
        $u = array('path' => $url);
        $req = new Qiniu_Request($u, null);
        list($resp, $err) = $self->RoundTrip($req);
        if ($err !== null) {
            return array(null, $err);
        }
        if ($resp->StatusCode === 200) {
            return null;
        }
        return Utils::Qiniu_ResponseError($resp);
    }

    static function Qiniu_Client_CallWithForm(
        $self, $url, $params, $contentType = 'application/x-www-form-urlencoded') // => ($data, $error)
    {
        $u = array('path' => $url);
        if ($contentType === 'application/x-www-form-urlencoded') {
            if (is_array($params)) {
                $params = http_build_query($params);
            }
        }
        $req = new Qiniu_Request($u, $params);
        if ($contentType !== 'multipart/form-data') {
            $req->Header['Content-Type'] = $contentType;
        }
        list($resp, $err) = $self->RoundTrip($req);
        if ($err !== null) {
            return array(null, $err);
        }
        return Utils::Qiniu_Client_ret($resp);
    }

// --------------------------------------------------------------------------------

    static function Qiniu_Client_CallWithMultipartForm($self, $url, $fields, $files)
    {
        list($contentType, $body) = Utils::Qiniu_Build_MultipartForm($fields, $files);
        return Utils::Qiniu_Client_CallWithForm($self, $url, $body, $contentType);
    }

    static function Qiniu_Build_MultipartForm($fields, $files) // => ($contentType, $body)
    {
        $data = array();
        $mimeBoundary = md5(microtime());

        foreach ($fields as $name => $val) {
            array_push($data, '--' . $mimeBoundary);
            array_push($data, "Content-Disposition: form-data; name=\"$name\"");
            array_push($data, '');
            array_push($data, $val);
        }

        foreach ($files as $file) {
            array_push($data, '--' . $mimeBoundary);
            list($name, $fileName, $fileBody, $mimeType) = $file;
            $mimeType = empty($mimeType) ? 'application/octet-stream' : $mimeType;
            $fileName = Utils::Qiniu_escapeQuotes($fileName);
            array_push($data, "Content-Disposition: form-data; name=\"$name\"; filename=\"$fileName\"");
            array_push($data, "Content-Type: $mimeType");
            array_push($data, '');
            array_push($data, $fileBody);
        }

        array_push($data, '--' . $mimeBoundary . '--');
        array_push($data, '');

        $body = implode("\r\n", $data);
        $contentType = 'multipart/form-data; boundary=' . $mimeBoundary;
        return array($contentType, $body);
    }

    static function Qiniu_UserAgent() {
        $SDK_VER = Conf::$SDK_VER;
        $sdkInfo = "QiniuPHP/$SDK_VER";

        $systemInfo = php_uname("s");
        $machineInfo = php_uname("m");

        $envInfo = "($systemInfo/$machineInfo)";

        $phpVer = phpversion();

        $ua = "$sdkInfo $envInfo PHP/$phpVer";
        return $ua;
    }

    static function Qiniu_escapeQuotes($str)
    {
        $find = array("\\", "\"");
        $replace = array("\\\\", "\\\"");
        return str_replace($find, $replace, $str);
    }

// --------------------------------------------------------------------------------
// class Qiniu_Header

    static function Qiniu_Header_Get($header, $key) // => $val
    {
        $val = @$header[$key];
        if (isset($val)) {
            if (is_array($val)) {
                return $val[0];
            }
            return $val;
        } else {
            return '';
        }
    }

    static function Qiniu_ResponseError($resp) // => $error
    {
        $header = $resp->Header;
        $details = Utils::Qiniu_Header_Get($header, 'X-Log');
        $reqId =Utils:: Qiniu_Header_Get($header, 'X-Reqid');
        $err = new Qiniu_Error($resp->StatusCode, null);

        if ($err->Code > 299) {
            if ($resp->ContentLength !== 0) {
                if (Utils::Qiniu_Header_Get($header, 'Content-Type') === 'application/json') {
                    $ret = json_decode($resp->Body, true);
                    $err->Err = $ret['error'];
                }
            }
        }
        $err->Reqid = $reqId;
        $err->Details = $details;
        return $err;
    }

// --------------------------------------------------------------------------------
// class Qiniu_Client

    static function Qiniu_Client_incBody($req) // => $incbody
    {
        $body = $req->Body;
        if (!isset($body)) {
            return false;
        }

        $ct = Utils::Qiniu_Header_Get($req->Header, 'Content-Type');
        if ($ct === 'application/x-www-form-urlencoded') {
            return true;
        }
        return false;
    }

    static function Qiniu_Client_do($req) // => ($resp, $error)
    {
        $ch = curl_init();
        $url = $req->URL;
        $options = array(
            CURLOPT_USERAGENT => $req->UA,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => false,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_URL => $url['path']
        );
        $httpHeader = $req->Header;
        if (!empty($httpHeader))
        {
            $header = array();
            foreach($httpHeader as $key => $parsedUrlValue) {
                $header[] = "$key: $parsedUrlValue";
            }
            $options[CURLOPT_HTTPHEADER] = $header;
        }
        $body = $req->Body;
        if (!empty($body)) {
            $options[CURLOPT_POSTFIELDS] = $body;
        } else {
            $options[CURLOPT_POSTFIELDS] = "";
        }
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        $ret = curl_errno($ch);
        if ($ret !== 0) {
            $err = new Qiniu_Error(0, curl_error($ch));
            curl_close($ch);
            return array(null, $err);
        }
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        $responseArray = explode("\r\n\r\n", $result);
        $responseArraySize = sizeof($responseArray);
        $respHeader = $responseArray[$responseArraySize-2];
        $respBody = $responseArray[$responseArraySize-1];

        list($reqid, $xLog) = getReqInfo($respHeader);

        $resp = new Qiniu_Response($code, $respBody);
        $resp->Header['Content-Type'] = $contentType;
        $resp->Header["X-Reqid"] = $reqid;
        return array($resp, null);
    }

    static function getReqInfo($headerContent) {
        $headers = explode("\r\n", $headerContent);
        $reqid = null;
        $xLog = null;
        foreach($headers as $header) {
            $header = trim($header);
            if(strpos($header, 'X-Reqid') !== false) {
                list($k, $v) = explode(':', $header);
                $reqid = trim($v);
            } elseif(strpos($header, 'X-Log') !== false) {
                list($k, $v) = explode(':', $header);
                $xLog = trim($v);
            }
        }
        return array($reqid, $xLog);
    }

    static function Qiniu_SetKeys($accessKey, $secretKey)
    {
        Conf::$QINIU_ACCESS_KEY = $accessKey;
        Conf::$QINIU_SECRET_KEY = $secretKey;
    }

    static function Qiniu_RequireMac($mac) // => $mac
    {
        if (isset($mac)) {
            return $mac;
        }

        $QINIU_ACCESS_KEY = Conf::$QINIU_ACCESS_KEY;
        $QINIU_SECRET_KEY = Conf::$QINIU_SECRET_KEY;

        return new Qiniu_Mac($QINIU_ACCESS_KEY, $QINIU_SECRET_KEY);
    }

    static function Qiniu_Sign($mac, $data) // => $token
    {
        return Utils::Qiniu_RequireMac($mac)->Sign($data);
    }

    static function Qiniu_SignWithData($mac, $data) // => $token
    {
        return Utils::Qiniu_RequireMac($mac)->SignWithData($data);
    }

}
