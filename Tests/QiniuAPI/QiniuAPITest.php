<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/9/22
 * Time: 02:52
 */

namespace QiniuAPI;

use QiniuAPI\ImageView2\ImageView2;
use QiniuAPI\ImageMogr2\ImageMogr2;
use QiniuAPI\Watermark\Watermark;

class QiniuAPITest extends \PHPUnit_Framework_TestCase
{

    protected function watermark(){
        $watermark = new Watermark();
        $image1 = new \QiniuAPI\Watermark\Image();
        $gravity1 = new \QiniuAPI\Watermark\Gravity();
        $gravity1->gravity( \QiniuAPI\Watermark\Gravity::Gravity3 )->dx( 20 )->dy( 25 );
        $image1->gravity( $gravity1 )->imageUrl( 'http://baidu.com/index.jpg' )->dissolve( 50 );
        $text2  = new \QiniuAPI\Watermark\Text();
        $gravity2 = new \QiniuAPI\Watermark\Gravity();
        $gravity2->gravity( \QiniuAPI\Watermark\Gravity::Gravity9 )->dx( 21 )->dy( 22 );
        $text2->gravity( $gravity2 )->text( '测试文字' )->font( '楷体' )->fontSize( 2 )->fill( '#FFFFFF' )->dissolve( 51 );
        $watermark->addParameter( $image1 )->addParameter( $text2 );
        return $watermark;
    }

    protected function imageView2(){
        $imageView2 = new ImageView2();
        $imageView2->widthAndHeight( 200 ,100 )->format( 'jpg' )->interlace( true );
        return $imageView2;
    }

    protected function imageMogr2(){
        $imageMog2 = new ImageMogr2();
        $quality = new \QiniuAPI\ImageMogr2\Quality();
        $blur    = new \QiniuAPI\ImageMogr2\Blur();
        $format  = new \QiniuAPI\ImageMogr2\Format();
        $interlace = new \QiniuAPI\ImageMogr2\Interlace();
        $rotate = new \QiniuAPI\ImageMogr2\Rotate();
        $thumbnail = new \QiniuAPI\ImageMogr2\Thumbnail();
        $crop = new \QiniuAPI\ImageMogr2\Crop();

        $imageMog2
            ->addParameter( $thumbnail->keepRatio()->maxWidthAndHeight( 300 , 200 ) )
            ->addParameter( $crop->gravity( \QiniuAPI\ImageMogr2\Crop::Gravity3 )->cropSize( 100 , 150 )->dx( -2 )->dy( 3 ) )
            ->addParameter( $quality->quality( 90 ) )
            ->addParameter( $blur->radius( 20 )->sigma( 3 ) )
            ->addParameter( $format->format( 'jpg' ) )
            ->addParameter( $interlace->enable() )
            ->addParameter( $rotate->degree( 90 ) );
        return $imageMog2;
    }

    protected function avthumb(){
        $avthumb = new \QiniuAPI\Avthumb\Avthumb();
        $avthumb->format('mp4')->audioBitRate( '192k' )->audioSamplingRate( 8000 )->videoFrameRate( 24 );
        return $avthumb;
    }


    public function testImageView2()
    {
        $imageView2 = $this->imageView2();
        $this->assertEquals( 'imageView2/2/w/200/h/100/q/85/format/jpg/interlace/1' , $imageView2 );
        $imageView2->reset()->widthAndHeight( 300 , 200 )->format( 'png' )->cover()->quality( 100 );
        $this->assertEquals( 'imageView2/3/w/300/h/200/q/100/format/png' , $imageView2 );
        $imageView2->reset()->longAndShortEdge( 300 , 200 )->format( 'png' )->contain()->crop()->quality( 100 );
        $this->assertEquals( 'imageView2/5/w/300/h/200/q/100/format/png' , $imageView2 );
        $this->imageView2 = $imageView2;
    }

    public function testImageMogr2(){
        $imageMogr2 = $this->imageMogr2();
        $this->assertEquals( $imageMogr2 , 'imageMogr2/auto-orient/strip/thumbnail/300x200>/gravity/NorthEast/crop/!100x150-2a3/quality/90/blur/20x3/format/jpg/interlace/1/rotate/90' );
    }

    public function testWatermark(){
        $watermark = $this->watermark();
        $this->assertEquals( $watermark , 'watermark/3/image/aHR0cDovL2JhaWR1LmNvbS9pbmRleC5qcGc=/dissolve/50/gravity/NorthEast/gravity/dx/20/dy/25/text/5rWL6K-V5paH5a2X/font/5qW35L2T/fontsize/2/fill/#FFFFFF/dissolve/51/gravity/SouthEast/gravity/dx/21/dy/22' );
    }

    public function testBucket(){
        QiniuBucket::setDomain( 'test' );
        $url = QiniuBucket::makeURL( 'some_file' , [ $this->imageView2() , $this->imageMogr2() ] );
        $this->assertEquals( $url ,
            'http://test.qiniudn.com/some_file?imageView2/2/w/200/h/100/q/85/format/jpg/interlace/1|imageMogr2/auto-orient/strip/thumbnail/300x200>/gravity/NorthEast/crop/!100x150-2a3/quality/90/blur/20x3/format/jpg/interlace/1/rotate/90');
    }

    public function testAvthumb(){
        $avthumb = $this->avthumb();
        $this->assertEquals( $avthumb , 'avthumb/mp4/ab/192k/ar/8000/r/24/vb/128k' );
    }
}

