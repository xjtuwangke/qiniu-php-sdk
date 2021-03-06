<?php
namespace Qiniu;

class IoTest extends \PHPUnit_Framework_TestCase
{
	public $bucket;
	public $client;

	public function setUp()
	{
		initKeys();
		$this->client = new QiniuMacHttpClient(null);
		$this->bucket = QINIU_BUCKET_NAME;
	}

	public function testReqid()
	{
		$key = 'testReqid' . getTid();
		list($ret, $err) = QiniuPutExtra::Qiniu_PutFile("", $key, __file__, null);
		$this->assertNotNull($err);
		$this->assertNotNull($err->Reqid);
		var_dump($err);
	}

	public function testPutFile()
	{
		$key = 'testPutFile' . getTid();
		$err = RSUtils::Qiniu_RS_Delete($this->client, $this->bucket, $key);

		$putPolicy = new QiniuRSPutPolicy($this->bucket);
		$upToken = $putPolicy->Token(null);
		$putExtra = new QiniuPutExtra();
		$putExtra->Params = array('x:test'=>'test');
		$putExtra->CheckCrc = 1;
		list($ret, $err) = QiniuPutExtra::Qiniu_PutFile($upToken, $key, __file__, $putExtra);
		$this->assertNull($err);
		$this->assertArrayHasKey('hash', $ret);
		$this->assertArrayHasKey('x:test', $ret);
		var_dump($ret);

		list($ret, $err) = RSUtils::Qiniu_RS_Stat($this->client, $this->bucket, $key);
		$this->assertNull($err);
		var_dump($ret);

		$err = RSUtils::Qiniu_RS_Delete($this->client, $this->bucket, $key);
		$this->assertNull($err);
	}

	public function testPut()
	{
		$key = 'testPut' . getTid();
		$err = RSUtils::Qiniu_RS_Delete($this->client, $this->bucket, $key);

		$putPolicy = new QiniuRSPutPolicy($this->bucket);
		$upToken = $putPolicy->Token(null);
		$putExtra = new QiniuPutExtra();
		$putExtra->Params = array('x:test'=>'test');
		list($ret, $err) = QiniuPutExtra::Qiniu_Put($upToken, $key, "hello world!", $putExtra);
		$this->assertNull($err);
		$this->assertArrayHasKey('hash', $ret);
		$this->assertArrayHasKey('x:test', $ret);
		var_dump($ret);

		list($ret, $err) = RSUtils::Qiniu_RS_Stat($this->client, $this->bucket, $key);
		$this->assertNull($err);
		var_dump($ret);

		$err = RSUtils::Qiniu_RS_Delete($this->client, $this->bucket, $key);
		$this->assertNull($err);
	}

	public function testPut_sizelimit()
	{
		$key = 'testPut_sizelimit' . getTid();
		$err = RSUtils::Qiniu_RS_Delete($this->client, $this->bucket, $key);

		$putPolicy = new QiniuRSPutPolicy($this->bucket);
		$putPolicy->FsizeLimit = 1;
		$upToken = $putPolicy->Token(null);
		list($ret, $err) = QiniuPutExtra::Qiniu_Put($upToken, $key, "hello world!", null);
		$this->assertNull($ret);
		$this->assertEquals($err->Err, 'exceed FsizeLimit');
		var_dump($err);
	}

	public function testPut_mime_save()
	{
		$key = 'testPut_mime_save' . getTid();
		$err = RSUtils::Qiniu_RS_Delete($this->client, $this->bucket, $key);

		$putPolicy = new QiniuRSPutPolicy($this->bucket);
		$putPolicy->DetectMime = 1;
		$putPolicy->SaveKey = $key;
		$upToken = $putPolicy->Token(null);
		$putExtra = new QiniuPutExtra();
		$putExtra->MimeType = 'image/jpg';
		list($ret, $err) = QiniuPutExtra::Qiniu_PutFile($upToken, null, __file__, $putExtra);
		$this->assertNull($err);

		list($ret, $err) = RSUtils::Qiniu_RS_Stat($this->client, $this->bucket, $key);
		$this->assertNull($err);
		$this->assertEquals($ret['mimeType'], 'application/x-httpd-php');
		var_dump($ret);

		$err = RSUtils::Qiniu_RS_Delete($this->client, $this->bucket, $key);
		$this->assertNull($err);
	}

	public function testPut_mimetype() {
		$key = 'testPut_mimetype' . getTid();
		$err = RSUtils::Qiniu_RS_Delete($this->client, $this->bucket, $key);
		$scope = $this->bucket . ":" . $key;

		$putPolicy = new QiniuRSPutPolicy($scope);
		$putPolicy->ReturnBody = '{"key":$(key),"mimeType":$(mimeType)}';
		$upToken = $putPolicy->Token(null);

		$putExtra = new QiniuPutExtra();
		$putExtra->MimeType = 'image/jpg';

		list($ret1, $err1) = QiniuPutExtra::Qiniu_PutFile($upToken, $key, __file__, $putExtra);
		var_dump($ret1);
		$this->assertNull($err1);
		$this->assertEquals($ret1['mimeType'], 'image/jpg');

		list($ret2, $err2) = QiniuPutExtra::Qiniu_Put($upToken, $key, "hello world", $putExtra);
		var_dump($ret2);
		$this->assertNull($err2);
		$this->assertEquals($ret2['mimeType'], 'image/jpg');

		$err = RSUtils::Qiniu_RS_Delete($this->client, $this->bucket, $key);
	}

	public function testPut_exclusive() {
		$key = 'testPut_exclusive' . getTid();
		$scope = $this->bucket . ':' . $key;
		$err = RSUtils::Qiniu_RS_Delete($this->client, $this->bucket, $key);

		$putPolicy = new QiniuRSPutPolicy($scope);
		$putPolicy->InsertOnly = 1;
		$upToken = $putPolicy->Token(null);

		list($ret, $err) = QiniuPutExtra::Qiniu_Put($upToken, $key, "hello world!", null);
		$this->assertNull($err);
		list($ret, $err) = QiniuPutExtra::Qiniu_PutFile($upToken, $key, __file__, null);
		$this->assertNull($ret);
		$this->assertEquals($err->Code, 614);
		var_dump($err);

		list($ret, $err) = RSUtils::Qiniu_RS_Stat($this->client, $this->bucket, $key);
		$this->assertNull($err);
		$this->assertEquals($ret['mimeType'], 'application/octet-stream');
		var_dump($ret);

		$err = RSUtils::Qiniu_RS_Delete($this->client, $this->bucket, $key);
		$this->assertNull($err);
	}

	public function testPut_mimeLimit() {
		$key = 'testPut_mimeLimit' . getTid();
		$scope = $this->bucket . ':' . $key;
		$err = RSUtils::Qiniu_RS_Delete($this->client, $this->bucket, $key);

		$putPolicy = new QiniuRSPutPolicy($scope);
		$putPolicy->MimeLimit = "image/*";
		$upToken = $putPolicy->Token(null);

		list($ret, $err) = QiniuPutExtra::Qiniu_PutFile($upToken, $key, __file__, null);
		$this->assertNull($ret);
		$this->assertEquals($err->Err, "limited mimeType: this file type is forbidden to upload");
		var_dump($err);
	}
}

