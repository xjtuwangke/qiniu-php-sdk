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
\Qiniu\Conf::$QINIU_ACCESS_KEY = 'access key';
\Qiniu\Conf::$QINIU_SECRET_KEY = 'secret key';
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

1. 单个文件上传

    ```
$put = MyBucket::put( __FILE__ , 'test/this.php' );
// MyBucket::entry( 'test/this.php' )->put( __FILE__ );
var_dump( $put );
/*
 array (size=3)
  0 =>
    array (size=2)
      'hash' => string 'FgYSz1zq0d9mT5Nl_DkFGsuncYRy' (length=28)
      'key' => string 'test/this.php' (length=13)
  1 => null
  2 =>
    object(QiniuAPI\QiniuEntry)[9]
      protected 'bucket' =>
        object(MyBucket)[10]
      protected 'key' => string 'test/this.php' (length=13)
 */   
 ```

1. 查看文件

    ````
$ls = MyBucket::ls( 'test/this.php' );
//MyBucket::entry( 'test/this.php' )->ls();
var_dump( $ls );
/*
 array (size=2)
  0 =>
    array (size=4)
      'fsize' => int 1872
      'hash' => string 'FkYSYaJKtvKUwrOQk0D4RqFAIj3W' (length=28)
      'mimeType' => string 'application/x-httpd-php' (length=23)
      'putTime' => int 14152138860482670
  1 => null
 */
    ````

    ```
$ls = MyBucket::ls( 'no-file' );
//MyBucket::entry( 'no-file' )->ls();
var_dump( $ls );
/*
 array (size=2)
  0 => null
  1 =>
    object(Qiniu\QiniuError)[12]
      public 'Err' => string 'no such file or directory' (length=25)
      public 'Reqid' => string 'Dl0AAKrY2CCI2aMT' (length=16)
      public 'Details' => string '' (length=0)
      public 'Code' => int 612
 */
     ```
    
1. 删除文件

   ```
$rm = MyBucket::delete( 'no-file' );
//or $rm = MyBucket::entry( 'no-file' )->delete();
var_dump( $rm );
/*
 array (size=2)
  0 => boolean false
  1 =>
    object(Qiniu\QiniuError)[11]
      public 'Err' => string 'no such file or directory' (length=25)
      public 'Reqid' => string '10MAAGhE77nY2aMT' (length=16)
      public 'Details' => string '' (length=0)
      public 'Code' => int 612
 */
 ```
 
   ```
$rm = MyBucket::delete( 'test/this.php' );
//or $rm = MyBucket::entry( 'test/this.php' )->delete();
var_dump( $rm );
/*
 array (size=2)
  0 => boolean true
  1 => null
 */
   ```

1. 移动和复制

  ```
  $put = MyBucket::put( __FILE__ , 'test/this.php' );
  MyBucket::delete( 'test/that.php' );
  MyBucket::delete( 'test/that1.php' );
  MyBucket::delete( 'test/that2.php' );
  MyBucket::delete( 'test/that3.php' );
  $copy = MyBucket::entry( 'test/this.php' )->copy( MyBucket::entry( 'test/that.php' ) );
  var_dump( $copy );
  $copy = MyBucket::move( 'test/this.php' , MyBucket::entry( 'test/that.php' ) );
  var_dump( $copy );
  $move = MyBucket::entry( 'test/this.php' )->move( MyBucket::entry( 'test/that2.php') );
  var_dump( $move );
  $move = MyBucket::move( 'test/this.php' , MyBucket::entry( 'test/that3.php') );
  var_dump( $move );
  ```

  返回

  ```
  array (size=2)
    0 => boolean true
    1 => null
  array (size=2)
    0 => boolean false
    1 =>
      object(Qiniu\QiniuError)[11]
        public 'Err' => string 'file exists' (length=11)
        public 'Reqid' => string 'ak0AAPG88grG36MT' (length=16)
        public 'Details' => string '' (length=0)
        public 'Code' => int 614
  array (size=2)
    0 => boolean true
    1 => null
  array (size=2)
    0 => boolean false
    1 =>
      object(Qiniu\QiniuError)[12]
        public 'Err' => string 'no such file or directory' (length=25)
        public 'Reqid' => string '10MAAIK-MhzG36MT' (length=16)
        public 'Details' => string '' (length=0)
        public 'Code' => int 612
  ```

### 宏命令

```
\QiniuAPI\QiniuFop::marco( 'watermark' , function( \QiniuAPI\QiniuEntry $entry ){
    $watermark = new \QiniuAPI\Watermark\Watermark();
    $image1 = new \QiniuAPI\Watermark\Image();
    $gravity1 = new \QiniuAPI\Watermark\Gravity();
    $gravity1->gravity( \QiniuAPI\Watermark\Gravity::Gravity3 )->dx( 20 )->dy( 25 );
    $image1->gravity( $gravity1 )->imageUrl( 'http://www.baidu.com/img/baidu_jgylogo3.gif' )->dissolve( 50 );
    $watermark->addParameter( $image1 );
    return $entry->url( [ $watermark ] );
});
$watermarked = \QiniuAPI\QiniuFop::watermark( MyBucket::entry( 'upload/ftp/gofarms-1/测试/qiniu_test.jpg' ) );
```

```
\QiniuAPI\Avthumb\Avthumb::marco( 'basic' , function(){
    $avthumb = new \QiniuAPI\Avthumb\Avthumb();
    $avthumb->format('mp4')->audioBitRate( '192k' )->audioSamplingRate( 8000 )->videoFrameRate( 24 );
    return $avthumb;
});
$basicThumb = \QiniuAPI\Avthumb\Avthumb::basic();
echo $basicThumb;
//avthumb/mp4/ab/192k/ar/8000/r/24/vb/128k
//usage:: $url = MyBucket::entry( 'somevideo.mp4' )->url( [ \QiniuAPI\Avthumb\Avthumb::basic() ] );
```

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


1. 基本图片处理[ImageView2](http://developer.qiniu.com/docs/v6/api/reference/fop/image/imageview2.html)

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

