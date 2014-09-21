<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/9/22
 * Time: 06:18
 */

namespace QiniuAPI;


use QiniuAPI\SaveAs\SaveAs;

class QiniuEntryTest extends \PHPUnit_Framework_TestCase {

    public function testEntry(){
        QiniuBucket::setDomain( 'testDoamin' , 'qiniuphotos' );
        $entry = QiniuBucket::entry( 'gogopher.jpg' );
        $this->assertEquals( $entry->entryURI() , 'qiniuphotos:gogopher.jpg' );
        $this->assertEquals( $entry->encodedEntryURI() , 'cWluaXVwaG90b3M6Z29nb3BoZXIuanBn' );
        echo $entry->url();
    }

    public function testSaveAs(){
        initKeys();
        QiniuBucket::setDomain( 't-test' );
        $entry = QiniuBucket::entry( 'Ship-thumb-200.jpg' );
        $url = 'http://t-test.qiniudn.com/Ship.jpg?imageView/2/w/200/h/200';
        $saveAs = new SaveAs();
        $saveAs->urlSaveAsEntry( $url , $entry );
        $this->assertEquals( 'saveas/dC10ZXN0OlNoaXAtdGh1bWItMjAwLmpwZw==/sign/Vhiv6a22kVN_zhtetbPNeG9sY3JUL1HG597EmBwQ:IBM3E28ZN_1MaKU4TDF5kQ3Kux8=' , $saveAs->__toString() );
    }
} 