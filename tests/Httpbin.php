<?php
namespace Storm23\LaravelRest\Tests;

use Storm23\LaravelRest\AProxy;

class Httpbin extends AProxy
{
	protected function getEndpoint()
	{
		return 'https://httpbin.org';
	}

	public function makeGet()
	{
		$url = '/get';

		return $this->get($url);
	}

	public function makePut()
	{
		$url = '/put';

		return $this->put($url, ['param1' => 'test']);
	}

	public function makePost()
	{
		$url = '/post';

		return $this->post($url, ['param1' => 'test']);
	}

	public function makeDelete()
	{
		$url = '/delete';

		return $this->delete($url, ['param1' => 'test']);
	}

	public function makePatch()
	{
		$url = '/patch';

		return $this->patch($url, ['param1' => 'test']);
	}

	public function makeStatus($code)
	{
		$url = sprintf('/status/%s', $code);

		return $this->get($url);
	}
}
