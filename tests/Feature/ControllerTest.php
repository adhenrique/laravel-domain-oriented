<?php

namespace Tests\Feature;

use Illuminate\Foundation\Auth\User;
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
        $this->router->post('tests', 'App\Http\Controllers\TestController@store');
        $this->router->put('tests/{id}', 'App\Http\Controllers\TestController@update');
        $this->router->delete('tests/{id}', 'App\Http\Controllers\TestController@destroy');
    }

    /** @test **/
    public function it_should_call_list_route_and_assert_count_of_items()
    {
        $this->loginWithFakeUser();
        $response = $this->getJson('tests');
        $response->assertOk();

        $data = json_decode($response->getContent(), true);

        $this->assertCount(count($this->data), $data['data']);
    }

    /** @test **/
    public function it_should_try_call_list_route_without_login_and_get_403()
    {
        $response = $this->getJson('tests');
        $response->assertForbidden();
    }

    /** @test **/
    public function it_should_call_find_route_and_assert_item()
    {
        $this->loginWithFakeUser();
        $response = $this->getJson('tests/1');
        $response->assertOk();

        $data = json_decode($response->getContent(), true);

        $this->assertEquals($this->data[0]['name'], $data['data']['name']);
    }

    /** @test **/
    public function it_should_call_find_route_with_non_existent_id_and_assert_status_404()
    {
        $this->loginWithFakeUser();
        $response = $this->getJson('tests/15');
        $response->assertStatus(404);
    }

    /** @test **/
    public function it_should_create_a_item()
    {
        $this->loginWithFakeUser();
        $response = $this->postJson('tests', [
            'name' => 'XXX'
        ]);
        $response->assertOk();

        $data = json_decode($response->getContent(), true);

        $this->assertEquals(11, $data['data']['id']);
    }

    /** @test **/
    public function it_should_try_create_a_item_and_assert_status_422()
    {
        $this->loginWithFakeUser();
        $response = $this->postJson('tests', [
            'name' => 1
        ]);
        $response->assertStatus(422);
    }

    /** @test **/
    public function it_should_update_a_item()
    {
        $this->loginWithFakeUser();
        $updateName = 'XXX';
        $response = $this->putJson('tests/1', [
            'name' => $updateName
        ]);
        $response->assertOk();

        $data = json_decode($response->getContent(), true);

        $this->assertTrue($data['data']['isUpdated']);
    }

    /** @test **/
    public function it_should_try_update_a_item_and_assert_status_422()
    {
        $this->loginWithFakeUser();
        $response = $this->putJson('tests/1', [
            'name' => 1
        ]);
        $response->assertStatus(422);
    }

    /** @test **/
    public function it_should_delete_a_item()
    {
        $this->loginWithFakeUser();
        $this->withoutMiddleware();
        $response = $this->deleteJson('tests/1');
        $response->assertOk();

        $data = json_decode($response->getContent(), true);

        $this->assertTrue($data['data']['isDeleted']);
        $this->assertSoftDeleted('tests');
    }

    private function loginWithFakeUser()
    {
        $user = new MyUserModel([
            'id' => 1,
            'name' => 'test user',
        ]);

        $this->be($user);
    }
}

class MyUserModel extends User {
    protected $fillable = [
        'id',
        'name',
    ];
}
