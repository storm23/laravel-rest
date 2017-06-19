<?php
namespace Storm23\LaravelRest\Tests;

use Orchestra\Testbench\TestCase;

class AProxyTest extends TestCase
{
	public function testGet()
	{
		$cid = uniqid();
		$client = new Httpbin($cid);

		$res = $client->makeGet();
		$url = 'https://httpbin.org/get?cid='.$cid;

		$this->assertEquals($url, $res['url']);
	}

	public function testPut()
	{
		$cid = uniqid();
		$client = new Httpbin($cid);

		$res = $client->makePut();
		$url = 'https://httpbin.org/put?cid='.$cid;

		$this->assertEquals($url, $res['url']);
		$this->assertEquals('test', $res['json']['param1']);
	}

	public function testPost()
	{
		$cid = uniqid();
		$client = new Httpbin($cid);

		$res = $client->makePost();
		$url = 'https://httpbin.org/post?cid='.$cid;

		$this->assertEquals($url, $res['url']);
		$this->assertEquals('test', $res['json']['param1']);
	}

	public function testPatch()
	{
		$cid = uniqid();
		$client = new Httpbin($cid);

		$res = $client->makePatch();
		$url = 'https://httpbin.org/patch?cid='.$cid;

		$this->assertEquals($url, $res['url']);
		$this->assertEquals('test', $res['json']['param1']);
	}

	public function testDelete()
	{
		$cid = uniqid();
		$client = new Httpbin($cid);

		$res = $client->makeDelete();
		$url = 'https://httpbin.org/delete?param1=test&cid='.$cid;

		$this->assertEquals($url, $res['url']);
	}

	public function testMalformedUrl()
	{
		$client = new MalformedUrl(uniqid());
		$catchedError = false;

		try {

			$res = $client->makeGet();
		}
		catch (\Exception $e) {

			$catchedError = true;
		}

		$this->assertEquals(true, $catchedError);
	}

	public function testError500()
	{
		$cid = uniqid();
		$client = new Httpbin($cid);

		$catchedError = false;

		try {

			$res = $client->makeStatus(500);
		}
		catch (\Exception $e) {

			$catchedError = true;
		}

		$this->assertEquals(true, $catchedError);
	}
}
