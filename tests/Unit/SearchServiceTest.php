<?php

namespace Tests\Unit;

use App\Domain\Test\TestSearchService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use LaravelDomainOriented\Tests\Cases\DBTestCase;

class SearchServiceTest extends DBTestCase
{
    /** @test **/
    public function it_should_assert_a_item_from_search_service()
    {
        $request = new Request();
        $searchService = $this->app->make(TestSearchService::class);
        $data = $searchService->all($request);

        $this->assertCount(count($this->data), $data);
        $this->assertEquals('Test1', $data[0]['name']);
    }

    /** @test **/
    public function it_should_get_nothing_from_search_service_with_filters()
    {
        $request = new Request();
        $request->merge([
            'filters' => [
                'name' => 'xxx'
            ],
        ]);
        $searchService = $this->app->make(TestSearchService::class);
        $data = $searchService->all($request);

        $this->assertCount(0, $data);
    }

    /** @test **/
    public function it_should_paginate_the_result()
    {
        $request = new Request();
        $request->merge([
            'paginate' => [
                'per_page' => 1,
            ],
        ]);
        $searchService = $this->app->make(TestSearchService::class);
        $data = $searchService->all($request);
        $data = $data->toArray();

        $this->assertCount(1, $data['data']);
        $this->assertEquals(1, $data['per_page']);
    }

    /** @test **/
    public function it_should_filter_and_paginate()
    {
        $request = new Request();
        $request->merge([
            'filters' => [
                'name' => [
                    'operator' => 'like',
                    'value' => '%Test1%',
                ]
            ],
            'paginate' => [
                'per_page' => 1,
                'page' => 2,
            ],
        ]);
        $searchService = $this->app->make(TestSearchService::class);
        $data = $searchService->all($request);
        $data = $data->toArray();

        $this->assertCount(1, $data['data']);
        $this->assertEquals(2, $data['total']);
        $this->assertEquals(2, $data['current_page']);
        $this->assertEquals('Test10', $data['data'][0]['name']);
    }

    /** @test **/
    public function it_should_find_by_id()
    {
        $searchService = $this->app->make(TestSearchService::class);
        $data = $searchService->findById(1);

        $this->assertEquals('Test1', $data['name']);
    }

    /** @test **/
    public function it_should_throw_model_not_found_exception()
    {
        $searchService = $this->app->make(TestSearchService::class);
        $this->expectException(ModelNotFoundException::class);

        $searchService->findById(15);
    }
}
