<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/9/22
 * Time: 06:04
 */

namespace QiniuAPI\SaveAs;

use QiniuAPI\QiniuEntry;
use QiniuAPI\QiniuFop;

class SaveAs extends QiniuFop{

    protected static $name = 'saveas';

    protected $url = null;

    protected $default_parameters = array(
        '__EncodedEntryURI' => null ,
        'sign' => null ,
    );

    public function urlSaveAsEntry( $url , QiniuEntry $entry ){
        $url = preg_replace( '/^http\:\/\//i' , '' , $url );
        $encodedEntryURI = $entry->encodedEntryURI();
        $newUrl = $url . '|saveas/' . $encodedEntryURI;
        $qiniuMac = new \Qiniu\QiniuMac();
        $this->setParameter( '__EncodedEntryURI' , $encodedEntryURI );
        $this->setParameter( 'sign' , $qiniuMac->Sign( $newUrl ) );
        return $this;
    }

}