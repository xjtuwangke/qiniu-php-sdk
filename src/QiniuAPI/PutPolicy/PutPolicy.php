<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/9/22
 * Time: 17:52
 */

namespace QiniuAPI\PutPolicy;

use QiniuAPI\QiniuFop;
use Qiniu\Utils;

class PutPolicy extends QiniuFop{

    protected $default_parameters = array(
        'scope' => null ,
        'deadline' => null ,
        'insertOnly' => 1 ,
        'endUser' => null ,
        'returnUrl' => null ,
        'returnBody' => null ,
        'callbackUrl' => null ,
        'callbackHost' => null ,
        'callbackBody' => null ,
        'persistentOps' => null ,
        'persistentNotifyUrl' => null ,
        'persistentPipeline' => null ,
        'saveKey' => null ,
        'fsizeLimit' => null , //8M
        'detectMime' => null ,
        'mimeLimit' => null ,
    );

    public function reset(){
        parent::reset();
        return $this->setExprire( 60 * 24 )->fsizeLimit( 8 );
    }

    public function setScope( $bucket , $key = null ){
        if( !is_null( $key ) ){
            return $this->setParameter( 'scope' , $bucket . ':' . $key );
        }
        else{
            return $this->setParameter( 'scope' , $bucket );
        }
    }

    public function setExprire( $minutes ){
        return $this->setParameter( 'deadline' , time() + 60 * $minutes );
    }

    public function insertOnly( $bool ){
        return $this->setParameter( 'insertOnly' , $bool ? 1 : 0 );
    }

    public function endUser( $endUser ){
        return $this->setParameter( 'endUser' , $endUser );
    }

    public function unsetCallback(){
        return $this->setParameter( 'callbackUrl' , null )->setParameter( 'callbackHost' , null )->setParameter( 'callbackBody' , null );
    }

    public function returnUrl( $url ){
        return $this->setParameter( 'returnUrl' , $url )->unsetCallback();
    }

    public function returnBody( array $body ){
        return $this->setParameter( 'returnBody' , json_encode( $body ) )->unsetCallback();
    }

    public function unsetReturn(){
        return $this->setParameter( 'returnUrl' , null )->setParameter( 'returnBody' , null );
    }

    public function callbackUrl( $url ){
        if( !is_array( $url ) ){
            $url = [ $url ];
        }
        $url = implode( ';' , $url );
        return $this->setParameter( 'callbackUrl' , $url )->unsetReturn();
    }

    public function callbackHost( $host ){
        return $this->setParameter( 'callbackHost' , $host )->unsetReturn();
    }

    public function callbackBody( array $body ){
        $params = array();
        foreach( $body as $key => $val ){
            $params[] = "{$key}=" . urlencode( $val );
        }
        return $this->setParameter( 'callbackBody' , implode( '&' , $params ) )->unsetReturn();
    }

    public function persistentOps( $fops ){
        if( !is_array( $fops ) ){
            $fops = [ $fops ];
        }
        $fops = implode( ';' , $fops );
        return $this->setParameter( 'persistentOps' , $fops );
    }

    public function persistentNotifyUrl( $url ){
        return $this->setParameter( 'persistentNotifyUrl' , $url );
    }

    public function persistentPipeline( $pipeline ){
        return $this->setParameter( 'persistentPipeline' , $pipeline );
    }

    public function saveKey( $key ){
        return $this->setParameter( 'saveKey' , $key );
    }

    public function fsizeLimit( $mb ){
        return $this->setParameter( 'fsizeLimit' , $mb * 1024 * 1024 );
    }

    public function detectMime( $bool = true ){
        return $this->setParameter( 'detectMime' , $bool ? 1 : 0 );
    }

    public function onlyImage(){
        return $this->setParameter( 'mimeLimit' , 'image/*' );
    }

    public function token(){
        $json = $this->toJson();
        return Utils::Qiniu_SignWithData( null , $json);
    }

} 