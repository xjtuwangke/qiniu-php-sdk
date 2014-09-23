<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/9/22
 * Time: 06:43
 */

namespace QiniuAPI\PermanentFOP;

use QiniuAPI\QiniuEntry;
use Qiniu\QiniuMac;
use Qiniu\QiniuMacHttpClient;
use Qiniu\Utils;
use Qiniu\Conf;

/**
 * 如果需要对已保存在空间中的资源进行云处理并将结果持久化，可以使用pfop接口。
 * @class PermanentFOP
 * @package QiniuAPI\PermanentFOP
 */
class PermanentFOP {

    /**
     * 提交表单,触发持久化处理
     * @param QiniuEntry $entry 被处理的entry
     * @param array      $fops  array of fops
     * @param string     $notifyURL  处理结果通知接收URL,请参考处理结果通知小节。
     * @param int        $force 强制执行数据处理。当服务端发现fops指定的数据处理结果已经存在，那就认为已经处理成功，避免重复处理浪费资源。加上本字段并设为1，则可强制执行数据处理并覆盖原结果。
     * @param null       $pipeline 为空则表示使用公用队列，处理速度比较慢。建议指定专用队列，转码的时候使用独立的计算资源
     */
    public static function pfop( QiniuEntry $entry , array $fops , $notifyURL , $force = 0 , $pipeline = null ){

        $encodedBucket = urlencode( $entry->bucket() );
        $encodedKey = urlencode( $entry->key() );

        $fops = implode( ';' , $fops );
        $encodedFops = urlencode($fops);
        $encodedNotifyURL = urlencode($notifyURL);

        $apiHost = Conf::$QINIU_API_HOST;
        $apiPath = "/pfop/";
        $requestBody = "bucket=$encodedBucket&key=$encodedKey&fops=$encodedFops&notifyURL=$encodedNotifyURL";
        if ($force !== 0) {
            $requestBody .= "&force=1";
        }
        if( !is_null( $pipeline ) ){
            $requestBody.= "$pipeline={$pipeline}";
        }
        $mac = new QiniuMac();
        $client = new QiniuMacHttpClient( $mac );
        list($ret, $err) = Utils::Qiniu_Client_CallWithForm($client, $apiHost . $apiPath, $requestBody);
        if ($err !== null) {
            echo "failed\n";
            var_dump($err);
        } else {
            echo "success\n";
            var_dump($ret);
        }
    }

    /**
     * 持久化处理notify回调
     * @return mixed
     */
    public static function notify(){
        $post_data = file_get_contents( 'php://input' );
        $results = @ json_decode( $post_data , true );
        return $results;
    }
} 