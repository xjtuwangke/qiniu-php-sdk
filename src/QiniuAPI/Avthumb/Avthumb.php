<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/9/22
 * Time: 05:21
 */

namespace QiniuAPI\Avthumb;

use Qiniu\Utils;
use \QiniuAPI\QiniuFop;

class Avthumb extends QiniuFop{

    protected static $name = 'avthumb';

    const Gravity1 = 'NorthWest';
    const Gravity2 = 'North';
    const Gravity3 = 'NorthEast';
    const Gravity4 = 'West';
    const Gravity5 = 'Center';
    const Gravity6 = 'East';
    const Gravity7 = 'SouthWest';
    const Gravity8 = 'South';
    const Gravity9 = 'SouthEast';

    protected $default_parameters = array(
        '__format' => null ,
        'ab' => '128k' ,
        'aq' => null ,
        'ar' => '8000' ,
        'r' => '24' ,
        'vb' => '128k' ,
        'vcodec' => null ,
        'acodec' => null ,
        'scodec' => null ,
        'ss' => null ,
        't' => null ,
        's' => null ,
        'autoscale' => null ,
        'stripmeta' => null ,
        'rotate' => null ,
        'wmImage' => null ,
        'wmGravity' => null ,
        'writeXing' => null ,
        'an' => null ,
        'vn' => null ,
    );

    public function format( $format ){
        return $this->setParameter( '__format' , $format );
    }

    public function audioBitRate( $ab ){
        return $this->setParameter( 'ab' , $ab )->setParameter( 'aq' , null );
    }

    public function audioQuality( $aq ){
        return $this->setParameter( 'ab' , null )->setParameter( 'aq' , $aq );
    }

    public function audioSamplingRate( $ar ){
        return $this->setParameter( 'ar' , $ar );
    }

    public function videoFrameRate( $r )
    {
        return $this->setParameter('r', $r);
    }

    public function videoBitRate( $vb ){
        return $this->setParameter( 'vb' , $vb );
    }

    public function videoCodec( $vcodec ){
        return $this->setParameter( 'vcodec' , $vcodec );
    }

    public function audioCodec( $acodec ){
        return $this->setParameter( 'acodec' , $acodec );
    }

    public function videoSubtitleCodec( $scodec ){
        return $this->setParameter( 'scodec' , $scodec );
    }

    public function videoSeekStart( $ss ){
        return $this->setParameter( 'ss' , $ss );
    }

    public function videoDuration( $t ){
        return $this->setParameter( 't' , $t );
    }

    public function videoResolution( $s ){
        return $this->setParameter( 's' , $s );
    }

    public function videoAutoscale( $bool = 1 ){
        return $this->setParameter( 'autoscale' , ( $bool ) ? 1 : 0 );
    }

    public function stripMeta( $bool = 1 ){
        return $this->setParameter( 'stripmeta' ,  ( $bool ) ? 1 : 0 );
    }

    public function videoRotate( $degree ){
        return $this->setParameter( 'rotate' ,  $degree );
    }

    public function videoWatermark( $url , $gravity = null ){
        if( is_null( $gravity ) ){
            $gravity = static::Gravity3;
        }
        $url = Utils::Qiniu_Encode( $url );
        return $this->setParameter( 'wmImage' , $url )->setParameter( 'wmGravity' , $gravity );
    }

    public function audioXing( $bool = 1 ){
        return $this->setParameter( 'writeXing' , ( $bool ) ? 1 : 0 );
    }

    public function noAudio( $bool = 1 ){
        return $this->setParameter( 'an' , ( $bool ) ? 1 : 0 );
    }

    public function noVideo( $bool = 1 ){
        return $this->setParameter( 'vn' , ( $bool ) ? 1 : 0 );
    }

}