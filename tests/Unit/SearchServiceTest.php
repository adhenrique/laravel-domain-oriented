<?php

namespace LaravelDomainOriented\Unit;

use App\Domain\Test\TestFilterService;
use App\Domain\Test\TestSearchEntity;
use App\Domain\Test\TestSearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use LaravelDomainOriented\Services\FilterService;
use LaravelDomainOriented\Tests\Cases\TestCase;

class SearchServiceTest extends TestCase
{
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

        $this->assertCount(1, $data);
        $this->assertEquals('Test', $data[0]['name']);
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

    private function migrateAndInsert()
    {
        $this->artisan('migrate', ['--database' => 'testing'])->run();

        DB::table('tests')->insert([
            'name' => 'Test',
        ]);
    }
}
