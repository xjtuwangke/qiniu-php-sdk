<?php
namespace Qiniu;

class RioTest extends \PHPUnit_Framework_TestCase
{
	public $bucket;
	public $client;

	public function setUp()
	{
		initKeys();
		$this->client = new QiniuMacHttpClient(null);
		$this->bucket = QINIU_BUCKET_NAME;
	}

	public function testMockReader()
	{
		$reader = new \MockReader;
		list($data) = $reader->Read(5);
		$this->assertEquals($data, "ABCDE");

		list($data) = $reader->Read(27);
		$this->assertEquals($data, "FGHIJKLMNOPQRSTUVWXYZABCDEF");
	}

	public function testPut()
	{
		if (getTestEnv() == "travis") {
			return;
		}
		$key = 'testRioPut' . getTid();
		$err = RSUtils::Qiniu_RS_Delete($this->client, $this->bucket, $key);

		$putPolicy = new QiniuRSPutPolicy($this->bucket);
		$upToken = $putPolicy->Token(null);
		$putExtra = new QiniuRioPutExtra($this->bucket);
		$putExtra->Params = array('x:test'=>'test');
		$reader = new \MockReader;
		list($ret, $err) = QiniuRioUploadClient::Qiniu_Rio_Put($upToken, $key, $reader, 5, $putExtra);
		$this->assertNull($err);
		$this->assertEquals($ret['hash'], "Fnvgeq9GDVk6Mj0Nsz2gW2S_3LOl");
		$this->assertEquals($ret['x:test'], "test");
		var_dump($ret);

		list($ret, $err) = RSUtils::Qiniu_RS_Stat($this->client, $this->bucket, $key);
		$this->assertNull($err);
		var_dump($ret);

		$err = RSUtils::Qiniu_RS_Delete($this->client, $this->bucket, $key);
		$this->assertNull($err);
	}

	public function testLargePut()
	{
		if (getTestEnv() == "travis") {
			return;
		}
		$key = 'testRioLargePut' . getTid();
		$err = RSUtils::Qiniu_RS_Delete($this->client, $this->bucket, $key);

		$putPolicy = new QiniuRSPutPolicy($this->bucket);
		$upToken = $putPolicy->Token(null);
		$putExtra = new QiniuRioPutExtra($this->bucket);
		$putExtra->Params = array('x:test'=>'test');
		$reader = new \MockReader;
		list($ret, $err) = QiniuRioUploadClient::Qiniu_Rio_Put($upToken, $key, $reader, QiniuRioPutExtra::QINIU_RIO_BLOCK_SIZE() + 5, $putExtra);
		$this->assertNull($err);
		$this->assertEquals($ret['hash'], "lgQEOCZ8Ievliq8XOfZmWTndgOll");
		$this->assertEquals($ret['x:test'], "test");
		var_dump($ret);

		list($ret, $err) = RSUtils::Qiniu_RS_Stat($this->client, $this->bucket, $key);
		$this->assertNull($err);
		var_dump($ret);

		$err = RSUtils::Qiniu_RS_Delete($this->client, $this->bucket, $key);
		$this->assertNull($err);
	}
}

