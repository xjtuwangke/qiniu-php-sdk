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

/**
 * Qiniu上传凭证
 * @class PutPolicy
 * @package QiniuAPI\PutPolicy
 */
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

    /**
     * 重置参数
     * @return $this
     */
    public function reset(){
        parent::reset();
        return $this->setExprire( 60 * 24 )->fsizeLimit( 8 );
    }

    /**
     * 指定上传的目标资源空间（Bucket）和资源名（Key）
     * 有两种格式：
     * 1. <bucket>，表示允许用户上传文件到指定的 bucket。在这种模式下文件只能“新增”，若已存在同名资源则会失败；
     * 2. <bucket>:<key>，表示只允许用户上传指定key的文件。在这种模型下文件默认允许“修改”，已存在同名资源则会本次覆盖。如果希望只能上传指定key的文件，并且不允许修改，那么可以将下面的 insertOnly 属性值设为 1。
     * @param      $bucket
     * @param null $key
     * @return $this
     */
    public function setScope( $bucket , $key = null ){
        if( !is_null( $key ) ){
            return $this->setParameter( 'scope' , $bucket . ':' . $key );
        }
        else{
            return $this->setParameter( 'scope' , $bucket );
        }
    }

    /**
     * 上传请求授权有效时间(分钟数)
     * @param $minutes
     * @return $this
     */
    public function setExprire( $minutes ){
        return $this->setParameter( 'deadline' , time() + 60 * $minutes );
    }

    /**
     * 限定为“新增”语意
     * 如果设置为非0值，则无论scope设置为什么形式，仅能以新增模式上传文件。
     * @param $bool
     * @return $this
     */
    public function insertOnly( $bool ){
        return $this->setParameter( 'insertOnly' , $bool ? 1 : 0 );
    }

    /**
     * 唯一属主标识
     * 特殊场景下非常有用，比如根据App-Client标识给图片或视频打水印。
     * @param $endUser
     * @return $this
     */
    public function endUser( $endUser ){
        return $this->setParameter( 'endUser' , $endUser );
    }

    /**
     * 取消callbackXXX设置 callbackXXX与returnXXX不能共存
     * @return $this
     */
    public function unsetCallback(){
        return $this->setParameter( 'callbackUrl' , null )->setParameter( 'callbackHost' , null )->setParameter( 'callbackBody' , null );
    }

    /**
     * Web端文件上传成功后，浏览器执行303跳转的URL
     * 通常用于HTML Form上传。
     * 文件上传成功后会跳转到<returnUrl>?upload_ret=<queryString>, <queryString>包含returnBody内容。
     * 如不设置returnUrl，则直接将returnBody的内容返回给客户端。
     * @param $url
     * @return $this
     */
    public function returnUrl( $url ){
        return $this->setParameter( 'returnUrl' , $url )->unsetCallback();
    }

    /**
     * 上传成功后，自定义七牛云最终返回給上传端（在指定returnUrl时是携带在跳转路径参数中）的数据
     * 支持魔法变量和自定义变量。returnBody 要求是合法的 JSON 文本。如：{"key": $(key), "hash": $(etag), "w": $(imageInfo.width), "h": $(imageInfo.height)}。
     * @param array $body
     * @return $this
     */
    public function returnBody( array $body ){
        return $this->setParameter( 'returnBody' , json_encode( $body ) )->unsetCallback();
    }

    /**
     * 取消returnXXX设置 callbackXXX与returnXXX不能共存
     * @return $this
     */
    public function unsetReturn(){
        return $this->setParameter( 'returnUrl' , null )->setParameter( 'returnBody' , null );
    }

    /**
     * 上传成功后，七牛云向App-Server发送POST请求的URL
     * 必须是公网上可以正常进行POST请求并能响应HTTP/1.1 200 OK的有效URL。
     * 另外，为了给客户端有一致的体验，我们要求 callbackUrl 返回包 Content-Type 为 "application/json"，即返回的内容必须是合法的 JSON 文本。
     * 出于高可用的考虑，本字段允许设置多个 callbackUrl(用 ; 分隔)，在前一个 callbackUrl 请求失败的时候会依次重试下一个 callbackUrl。
     * 一个典型例子是 http://<ip1>/callback;http://<ip2>/callback，并同时指定下面的 callbackHost 字段。
     * 在 callbackUrl 中使用 ip 的好处是减少了对 dns 解析的依赖，可改善回调的性能和稳定性。
     * @param $url array|string
     * @return $this
     */
    public function callbackUrl( $url ){
        if( !is_array( $url ) ){
            $url = [ $url ];
        }
        $url = implode( ';' , $url );
        return $this->setParameter( 'callbackUrl' , $url )->unsetReturn();
    }

    /**
     * 上传成功后，七牛云向App-Server发送回调通知时的 Host 值，仅当同时设置了 callbackUrl 时有效。
     * @param $host string
     * @return $this
     */
    public function callbackHost( $host ){
        return $this->setParameter( 'callbackHost' , $host )->unsetReturn();
    }

    /**
     * 上传成功后，七牛云向App-Server发送POST请求的数据
     * 支持魔法变量和自定义变量。
     * callbackBody 要求是合法的 url query string。如：key=$(key)&hash=$(etag)&w=$(imageInfo.width)&h=$(imageInfo.height)。
     * @param array $body
     * @return $this
     */
    public function callbackBody( array $body ){
        $params = array();
        foreach( $body as $key => $val ){
            $params[] = "{$key}=" . urlencode( $val );
        }
        return $this->setParameter( 'callbackBody' , implode( '&' , $params ) )->unsetReturn();
    }

    /**
     * 资源上传成功后触发执行的预转持久化处理指令列表
     * 每个指令是一个API规格字符串，多个指令用“;”分隔。
     * 请参看详解与示例。
     * @param $fops array|QiniuFop
     * @return $this
     */
    public function persistentOps( $fops ){
        if( !is_array( $fops ) ){
            $fops = [ $fops ];
        }
        $fops = implode( ';' , $fops );
        return $this->setParameter( 'persistentOps' , $fops );
    }

    /**
     * 接收预转持久化结果通知的URL
     * 必须是公网上可以正常进行POST请求并能响应HTTP/1.1 200 OK的有效URL。
     * @param $url string
     * @return $this
     */
    public function persistentNotifyUrl( $url ){
        return $this->setParameter( 'persistentNotifyUrl' , $url );
    }

    /**
     * 转码队列名
     * 资源上传成功后，触发转码时指定独立的队列进行转码。为空则表示使用公用队列，处理速度比较慢。建议使用专用队列
     * @param $pipeline string
     * @return $this
     */
    public function persistentPipeline( $pipeline ){
        return $this->setParameter( 'persistentPipeline' , $pipeline );
    }

    /**
     * 自定义资源名
     * 支持魔法变量及自定义变量。这个字段仅当用户上传的时候没有主动指定key的时候起作用。
     * @param $key
     * @return $this
     */
    public function saveKey( $key ){
        return $this->setParameter( 'saveKey' , $key );
    }

    /**
     * 限定上传文件的大小，单位：MB
     * 超过限制的上传内容会被判为上传失败，返回413状态码。
     * @param $mb float
     * @return $this
     */
    public function fsizeLimit( $mb ){
        return $this->setParameter( 'fsizeLimit' , $mb * 1024 * 1024 );
    }

    /**
     * 开启MimeType侦测功能
     * 设为非0值，则忽略上传端传递的文件MimeType信息，使用七牛服务器侦测内容后的判断结果
     * 默认设为0值，如上传端指定了MimeType则直接使用该值，否则按如下顺序侦测MimeType值：
     * 1. 检查文件扩展名；
     * 2. 检查Key扩展名；
     * 3. 侦测内容。
     * 如不能侦测出正确的值，会默认使用 application/octet-stream 。
     * @param bool $bool
     * @return $this
     */
    public function detectMime( $bool = true ){
        return $this->setParameter( 'detectMime' , $bool ? 1 : 0 );
    }

    /**
     * 仅可上传图片
     * @return $this
     */
    public function onlyImage(){
        return $this->setParameter( 'mimeLimit' , 'image/*' );
    }

    /**
     * 根据上传策略获取上传token
     * @return string
     */
    public function token(){
        $json = $this->toJson();
        return Utils::Qiniu_SignWithData( null , $json);
    }

} 