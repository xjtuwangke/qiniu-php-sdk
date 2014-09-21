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

class PermanentFOP {

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

    public static function notify(){
        $post_data = file_get_contents( 'php://input' );
        $results = @ json_decode( $post_data , true );
        return $results;
    }
} 