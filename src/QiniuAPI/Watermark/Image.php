<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/9/22
 * Time: 03:53
 */

namespace QiniuAPI\Watermark;

use Qiniu\Utils;
use QiniuAPI\QiniuAPIParameter;

class Image extends QiniuAPIParameter{

    public static $name = 'image';

    protected $default_parameters = array(
        'image' => null ,
        'dissolve' => 100 ,
        'gravity' => null ,
    );

    public function imageUrl( $url ){
        return $this->setParameter( 'image' , Utils::Qiniu_Encode( $url ) );
    }

    public function dissolve( $int ){
        return $this->setParameter( 'dissolve' , $int );
    }

    public function gravity( Gravity $gravity ){
        return $this->setParameter( 'gravity' , $gravity );
    }

    public function parameterToString(){
        return '' . $this->getParameter( 'image' ) . '/dissolve/' . $this->getParameter( 'dissolve' ) . $this->getParameter( 'gravity' );
    }
} 