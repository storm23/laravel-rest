<?php
namespace Storm23\LaravelRest\Tests;

use Storm23\LaravelRest\AProxy;

class MalformedUrl extends AProxy
{
	protected function getEndpoint()
	{
		return '';
	}

	public function makeGet()
	{
		$url = '/get';

		return $this->get($url);
	}
}
