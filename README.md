# Qiniu Resource Storage SDK for PHP

Forked from https://github.com/qiniu/php-sdk/


## 许可证

Copyright (c) 2012-2014 qiniu.com

基于 MIT 协议发布:

* [www.opensource.org/licenses/MIT](http://www.opensource.org/licenses/MIT)

## USAGE

### First of all

1. 设置access key和secret key

```
Conf::$QINIU_ACCESS_KEY = 'access key';
Conf::$QINIU_SECRET_KEY = 'secret key';
```

2. setup a bucket

```
class MyBucket extends \QiniuAPI\QiniuBucket{

    /**
     * 七牛bucket的公共域名，比如bucket1.qiniudn.com或是bucket2.u.quniudn.com
     * @var string | null
     */
    protected static $domain = 'gofarms-dev.qiniudn.com';

    /**
     * 是否是私有bucket
     * @var bool
     */
    protected static $isPrivate = true;

    /**
     * bucket name
     * @var string|null
     */
    protected static $bucketName = 'gofarms-dev';

}
```

### 新建文件实例

```
$entry = MyBucket::entry( 'upload/ftp/gofarms-1/测试/qiniu_test.jpg' );
```

获取文件实例的'裸'URL

```
$entry->url();
```

### 文件上传

文档TBD

### 图像处理

1. 水印接口[watermark](http://developer.qiniu.com/docs/v6/api/reference/fop/image/watermark.html)


```
$watermark = new \QiniuAPI\Watermark\Watermark();
$image1 = new \QiniuAPI\Watermark\Image();
$gravity1 = new \QiniuAPI\Watermark\Gravity();
$gravity1->gravity( \QiniuAPI\Watermark\Gravity::Gravity3 )->dx( 20 )->dy( 25 );
$image1->gravity( $gravity1 )->imageUrl( 'http://www.baidu.com/img/baidu_jgylogo3.gif' )->dissolve( 50 );
$text2  = new \QiniuAPI\Watermark\Text();
$gravity2 = new \QiniuAPI\Watermark\Gravity();
$gravity2->gravity( \QiniuAPI\Watermark\Gravity::Gravity9 )->dx( 21 )->dy( 22 );
$text2->gravity( $gravity2 )->text( '测试文字' )->font( '楷体' )->fontSize( 40 )->fill( '#FFFFFF' )->dissolve( 51 );
$watermark->addParameter( $image1 )->addParameter( $text2 );

$url = MyBucket::entry( 'upload/ftp/gofarms-1/测试/qiniu_test.jpg' )->url( [ $watermark ] );
```

2. 基本图片处理[ImageView2](http://developer.qiniu.com/docs/v6/api/reference/fop/image/imageview2.html)

```
$imageView2 = new ImageView2();
$imageView2->widthAndHeight( 200 ,100 )->format( 'jpg' )->interlace( true );
$url = MyBucket::entry( 'upload/ftp/gofarms-1/测试/qiniu_test.jpg' )->url( [ $imageView2 ] );
```

3. 高级图片处理[imageMogr2](http://developer.qiniu.com/docs/v6/api/reference/fop/image/imagemogr2.html)

```
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
$url = MyBucket::entry( 'upload/ftp/gofarms-1/测试/qiniu_test.jpg' )->url( [ $imageMog2 ] );
```

4. 图片基本信息[imageInfo](http://developer.qiniu.com/docs/v6/api/reference/fop/image/imageinfo.html)

```
$infoArray = MyBucket::entry( 'upload/ftp/gofarms-1/测试/qiniu_test.jpg' )->imageInfo();
```

5. 图片exif信息[exif](http://developer.qiniu.com/docs/v6/api/reference/fop/image/exif.html)

```
$infoArray = MyBucket::entry( 'upload/ftp/gofarms-1/测试/qiniu_test.jpg' )->exif();
```

6. 图片主色调[imageAve](http://developer.qiniu.com/docs/v6/api/reference/fop/image/imageave.html)

```
$infoArray = MyBucket::entry( 'upload/ftp/gofarms-1/测试/qiniu_test.jpg' )->imageAve();
```

### 音视频

1. Avthumb

\QiniuAPI\VFrame\VFrame
\QiniuAPI\Avthumb\Avthumb

其余TBD

```
$avthumb = new \QiniuAPI\Avthumb\Avthumb();
$avthumb->format('mp4')->audioBitRate( '192k' )->audioSamplingRate( 8000 )->videoFrameRate( 24 );
```

