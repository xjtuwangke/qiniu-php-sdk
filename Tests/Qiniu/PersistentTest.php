<?php
namespace Qiniu;

class PersistentTest extends \PHPUnit_Framework_TestCase
{
	public $bucket;
	public $client;

	public function setUp()
	{
		initKeys();
		$this->client = new QiniuMacHttpClient(null);
		$this->bucket = QINIU_BUCKET_NAME;
	}

	public function testPutFileWithPersistentOps()
	{
		$key = 'testPutFileWithPersistentOps' . getTid();
		$err = RSUtils::Qiniu_RS_Delete($this->client, $this->bucket, $key);

		$putPolicy = new QiniuRSPutPolicy($this->bucket);
		$putPolicy->PersistentOps = 'avthumb/mp3';
		$putPolicy->PersistentNotifyUrl = 'http://someurl/abc';
		$upToken = $putPolicy->Token(null);
		$putExtra = new QiniuPutExtra();
		$putExtra->CheckCrc = 1;
		list($ret, $err) = QiniuPutExtra::Qiniu_PutFile($upToken, $key, __file__, $putExtra);
		$this->assertNull($err);
		$this->assertArrayHasKey('hash', $ret);
		$this->assertArrayHasKey('persistentId', $ret);
		var_dump($ret);

		list($ret, $err) = RSUtils::Qiniu_RS_Stat($this->client, $this->bucket, $key);
		$this->assertNull($err);
		var_dump($ret);

		$err = RSUtils::Qiniu_RS_Delete($this->client, $this->bucket, $key);
		$this->assertNull($err);
	}

	public function testPutWithPersistentOps()
	{
		$key = 'testPutWithPersistentOps' . getTid();
		$err = RSUtils::Qiniu_RS_Delete($this->client, $this->bucket, $key);

		$putPolicy = new QiniuRSPutPolicy($this->bucket);
		$putPolicy->PersistentOps = 'avthumb/mp3';
		$putPolicy->PersistentNotifyUrl = 'http://someurl/abc';
		$upToken = $putPolicy->Token(null);
		list($ret, $err) = QiniuPutExtra::Qiniu_Put($upToken, $key, "hello world!", null);
		$this->assertNull($err);
		$this->assertArrayHasKey('hash', $ret);
		$this->assertArrayHasKey('persistentId', $ret);
		var_dump($ret);

		list($ret, $err) = RSUtils::Qiniu_RS_Stat($this->client, $this->bucket, $key);
		$this->assertNull($err);
		var_dump($ret);

		$err = RSUtils::Qiniu_RS_Delete($this->client, $this->bucket, $key);
		$this->assertNull($err);
	}
}

