<?php

//$loader = require __DIR__ . "/../vendor/autoload.php";
require __DIR__ . "/../vendor/autoload.php";

define( 'AccessKey' , 'Vhiv6a22kVN_zhtetbPNeG9sY3JUL1HG597EmBwQ' );
define( 'SecretKey' , 'b5b5vNg5nnkwkPfW5ayicPE_pj6hqgKMQEaWQ6JD' );
define( 'QINIU_BUCKET_NAME' , 'phpsdk' );
define( 'QINIU_KEY_NAME' , 'file_name' );

$tid = getenv("TRAVIS_JOB_NUMBER");

$testEnv = getenv("QINIU_TEST_ENV");

if (!empty($tid)) {
	$pid = getmypid();
	$tid = strstr($tid, ".");
	$tid .= "." . $pid;
}

function initKeys() {
    \Qiniu\Utils::Qiniu_SetKeys( AccessKey , SecretKey );
}

function getTid() {
	global $tid;
	return $tid;
}

function getTestEnv() {
	global $testEnv;
	return $testEnv;
}

class MockReader
{
	private $off = 0;

	public function __construct($off = 0)
	{
		$this->off = $off;
	}

	public function Read($bytes) // => ($data, $err)
	{
		$off = $this->off;
		$data = '';
		for ($i = 0; $i < $bytes; $i++) {
			$data .= chr(65 + ($off % 26)); // ord('A') = 65
			$off++;
		}
		$this->off = $off;
		return array($data, null);
	}
}

