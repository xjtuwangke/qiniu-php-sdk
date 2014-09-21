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

class Text extends QiniuAPIParameter{

    public static $name = 'text';

    protected $default_parameters = array(
        'text' => null ,
        'font' => null ,
        'fontsize' => 0 ,
        'fill' => '#000000' ,
        'dissolve' => 100 ,
        'gravity' => null ,
    );

    public function reset(){
        parent::reset();
        $this->font( '黑体' );
        return $this;
    }

    public function text( $text ){
        return $this->setParameter( 'text' , Utils::Qiniu_Encode( $text ) );
    }

    public function font( $font ){
        return $this->setParameter( 'font' , Utils::Qiniu_Encode( $font ) );
    }

    public function fontSize( $fontSize ){
        return $this->setParameter( 'fontsize' , $fontSize );
    }

    public function fill( $color ){
        return $this->setParameter( 'fill' , $color );
    }

    public function dissolve( $int ){
        return $this->setParameter( 'dissolve' , $int );
    }

    public function gravity( Gravity $gravity ){
        return $this->setParameter( 'gravity' , $gravity );
    }

    public function parameterToString(){
        return '' . $this->getParameter( 'text' ) .
            '/font/' . $this->getParameter('font') .
            '/fontsize/' . $this->getParameter('fontsize')  .
            '/fill/' . $this->getParameter('fill') .
            '/dissolve/' . $this->getParameter( 'dissolve' ) .
            $this->getParameter( 'gravity' );
    }
} 