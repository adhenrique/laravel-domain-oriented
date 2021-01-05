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
        $this->router->get('tests/{id}', 'App\Http\Controllers\TestController@show');
    }

    /** @test **/
    public function it_should_call_list_route_and_assert_count_of_items()
    {
        $response = $this->getJson('tests');
        $response->assertOk();

        $data = json_decode($response->getContent(), true);

        $this->assertCount(count($this->data), $data['data']);
    }

    /** @test **/
    public function it_should_call_find_route_and_assert_item()
    {
        $response = $this->getJson('tests/1');
        $response->assertOk();

        $data = json_decode($response->getContent(), true);

        $this->assertEquals($this->data[0]['name'], $data['data']['name']);
    }

    /** @test **/
    public function it_should_call_find_route_with_non_existent_id_and_assert_status_404()
    {
        $response = $this->getJson('tests/15');
        $response->assertStatus(404);
    }
}
