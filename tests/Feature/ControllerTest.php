<?php

namespace Tests\Feature;

use Illuminate\Routing\Router;
use LaravelDomainOriented\Tests\Cases\DBTestCase;

class ControllerTest extends DBTestCase
{
    protected Router $router;

    public function setUp(): void
    {
        parent::setUp();

        $this->router = $this->app['router'];
        $this->router->get('tests', 'App\Http\Controllers\TestController@index');
    }

    /** @test **/
    public function it_should_call_list_route_and_assert_count_of_items()
    {
        $response = $this->getJson('tests');
        $response->assertOk();

        $data = json_decode($response->getContent(), true);

        $this->assertCount(count($this->data), $data['data']);
    }
}
