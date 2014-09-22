<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/9/22
 * Time: 18:54
 */

namespace QiniuAPI;

class QiniuUploadTest extends \PHPUnit_Framework_TestCase
{

    public function testToken()
    {
        initKeys();
        QiniuBucket::setDomain('rollong-sandbox' );
        echo QiniuBucket::putToken( 'test' );
    }
}