<?php

namespace LaravelDomainOriented\Unit;

use App\Domain\Test\TestFilterService;
use App\Domain\Test\TestSearchModel;
use App\Domain\Test\TestSearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use LaravelDomainOriented\Services\FilterService;
use LaravelDomainOriented\Tests\Cases\TestCase;

class SearchServiceTest extends TestCase
{
    private array $data = [
        ['name' => 'Test1'],
        ['name' => 'Test2'],
        ['name' => 'Test3'],
        ['name' => 'Test4'],
        ['name' => 'Test5'],
        ['name' => 'Test6'],
        ['name' => 'Test7'],
        ['name' => 'Test8'],
        ['name' => 'Test9'],
        ['name' => 'Test10'],
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('domain:create Test --force');
    }

    /** @test **/
    public function it_should_insert_a_item_and_get_same_from_search_service()
    {
        $this->migrateAndInsert();

        $request = new Request();
        $searchService = $this->app->make(TestSearchService::class);
        $data = $searchService->all($request);

        $this->assertCount(count($this->data), $data);
        $this->assertEquals('Test1', $data[0]['name']);
    }

    /** @test **/
    public function it_should_insert_a_item_and_get_nothing_from_search_service_with_filters()
    {
        $this->migrateAndInsert();

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
        $this->migrateAndInsert();

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
        $this->migrateAndInsert();

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

    private function migrateAndInsert()
    {
        $this->artisan('migrate', ['--database' => 'testing'])->run();
        DB::table('tests')->insert($this->data);
    }
}
