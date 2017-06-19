<?php
namespace Storm23\LaravelRest\Tests;

use Orchestra\Testbench\TestCase;
use Storm23\LaravelRest\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class ResponseTest extends TestCase
{
    public function testSuccess()
    {
        $response = Response::success('success message');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('HTTP_200: success message', $response->getData()->message);
    }

    public function testRedirect()
    {
        $response = Response::redirect('http://test.com/redirect');

        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals('HTTP_301: Redirect.', $response->getData()->message);
        $this->assertTrue($response->headers->contains('location', 'http://test.com/redirect'));
    }

    public function testPaginate()
    {
        $items = ['apple', 'pear', 'peach'];
        $paginator = new LengthAwarePaginator($items, 10, 3, 1);
        $paginator->withPath('http://test.com/paginate');

        $response = Response::paginate($paginator);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('paginator', '3/1/10'));

        var_dump($response);
    }
}
