<?php

namespace Storm23\LaravelRest;

use APIClient\Client as APIClient;;
use GuzzleHttp\Psr7\Response;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class AProxy
{
	private $client;

	abstract protected function getEndpoint();

	public function __construct($cid = null, $bearer = null)
	{
		if (!isset($cid)) {

			$cid = uniqid();
		}

		$this->client = new APIClient($this->getEndpoint(), env('VERIFY_SSL', true));
		$this->client->setConstantParams(['cid' => $cid]);

        if (isset($bearer)) {

            $this->client->setConstantHeaders(['Authorization' => 'Bearer '.$bearer]);
        }
	}

	protected function get($url, array $queryParams = null, $returnPaginator = false)
	{
		$response = $this->client->get($url, $queryParams);

		if ($returnPaginator && $this->hasPaginatorHeaders($response)) {

			return $this->getPaginator($response, $url);
		}

		return $this->getBody($response);
	}

	protected function post($url, array $formParams, array $queryParams = null)
	{
		$response = $this->client->post($url, $formParams, $queryParams);

		return $this->getBody($response);
	}

	protected function put($url, array $formParams, array $queryParams = null)
	{
		$response = $this->client->put($url, $formParams, $queryParams);

		return $this->getBody($response);
	}

	protected function patch($url, array $formParams, array $queryParams = null)
	{
		$response = $this->client->patch($url, $formParams, $queryParams);

		return $this->getBody($response);
	}

	protected function delete($url, array $queryParams = null)
	{
		$response = $this->client->delete($url, $queryParams);

		return $this->getBody($response);
	}

	private function hasPaginatorHeaders($response)
	{
		if ($response === false) {

			return false;
		}

		$header = $response->getHeader('Paginator');
		if (count($header) == 0) {

			return false;
		}

		return true;
	}

	private function getPaginator($response, $url)
	{
		$header = $response->getHeader('Paginator')[0];

		$res = explode('/', $header);

		$size = $res[0];
		$page = $res[1];
		$total = $res[2];
		$data = $this->getBody($response);

		$paginator = new LengthAwarePaginator($data, $total, $size, $page);

		return $paginator;
	}

	private function showErrors()
	{
        $message = [];
		foreach ($this->client->errors as $error) {

			$message[] = $error['message'];
		}

		return implode("\r\n", $message);
	}

	private function getBody($response)
	{
		if ($response === false) {

            throw new \Exception($this->showErrors());
		}

		$body = $response->getBody();

		try {

			$jsonBody = json_decode($body, true);
		}
		catch (\Exception $e) {

			throw new \Exception('Response is not json format');
		}

		return $jsonBody;
	}

	protected function getInFile($url, $params, $extension = '.csv.gz')
	{
		$url = $this->client->makeUrl($url, $params);

		$tmpDir = \Config::get('endpoints.tmpDir');

		if (!file_exists($tmpDir)) {

			mkdir($tmpDir, 0777);
			chmod($tmpDir, 0777);
		}

		$filePath = $tmpDir . '/FILE_' . time() . '_' . rand() . $extension;
		$ch = curl_init($url);
		$fp = fopen($filePath, "w");
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		curl_exec($ch);
		if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {

			$filePath = false;
		}

		curl_close($ch);
		fclose($fp);

		return ($filePath);
	}
}
