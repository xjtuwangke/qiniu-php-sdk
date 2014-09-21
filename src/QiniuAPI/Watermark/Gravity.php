<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/9/22
 * Time: 03:46
 */

namespace QiniuAPI\Watermark;

use QiniuApi\QiniuAPIParameter;


class Gravity extends QiniuAPIParameter{

    public static $name = 'gravity';

    const Gravity1 = 'NorthWest';
    const Gravity2 = 'North';
    const Gravity3 = 'NorthEast';
    const Gravity4 = 'West';
    const Gravity5 = 'Center';
    const Gravity6 = 'East';
    const Gravity7 = 'SouthWest';
    const Gravity8 = 'South';
    const Gravity9 = 'SouthEast';

    protected $gravity = null;

    protected $default_parameters = array(
        'dx' => 0 ,
        'dy' => 0 ,
    );

    public function __construct(){
        parent::__construct();
        $this->gravity( static::Gravity9 );
    }

    public function gravity( $gravity ){
        $this->gravity = $gravity;
        return $this;
    }

    public function dx( $dx ){
        $dx = (int) $dx;
        return $this->setParameter( 'dx' , $dx );
    }

    public function dy( $dy ){
        $dy = (int) $dy;
        return $this->setParameter( 'dy' , $dy );
    }

    protected function parameterToString(){
        $dx = $this->getParameter( 'dx' );
        $dy = $this->getParameter( 'dy' );
        return "dx/{$dx}/dy/{$dy}";
    }

    public function __toString(){
        $string = parent::__toString();
        return '/gravity/' . $this->gravity . $string;
    }
}