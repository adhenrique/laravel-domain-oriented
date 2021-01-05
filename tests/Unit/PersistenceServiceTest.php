<?php

namespace Tests\Unit;

use App\Domain\Test\TestPersistenceService;
use App\Domain\Test\TestSearchService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use LaravelDomainOriented\Tests\Cases\TestCase;

class PersistenceServiceTest extends TestCase
{
    protected static bool $initialized = false;

    public function setUp(): void
    {
        parent::setUp();
        if (!self::$initialized) {
            $this->artisan('domain:create Test --force');

            self::$initialized = true;
        }
        $this->migrate();
    }

    /** @test **/
    public function it_should_store_a_item_and_search_same()
    {
        $persistenceService = $this->app->make(TestPersistenceService::class);
        $searchService = $this->app->make(TestSearchService::class);
        $name = 'ABC';

        $insertedData = $persistenceService->store([
            'name' => $name,
        ]);

        $searchedData = $searchService->findById(1);

        $this->assertEquals($insertedData, $searchedData['id']);
        $this->assertEquals($name, $searchedData['name']);
    }

    /** @test **/
    public function it_should_updated_a_item_and_compare_difference()
    {
        $persistenceService = $this->app->make(TestPersistenceService::class);
        $searchService = $this->app->make(TestSearchService::class);
        $name = 'ABC';
        $updateName = 'XYZ';

        $insertedData = $persistenceService->store([
            'name' => $name,
        ]);

        $isUpdated = $persistenceService->update([
            'name' => $updateName,
        ], $insertedData);

        $searchedData = $searchService->findById(1);

        $this->assertTrue($isUpdated);
        $this->assertNotEquals($name, $searchedData['name']);
        $this->assertEquals($updateName, $searchedData['name']);
    }

    /** @test **/
    public function it_should_throw_model_not_found_exception()
    {
        $persistenceService = $this->app->make(TestPersistenceService::class);
        $name = 'ABC';
        $updateName = 'XYZ';

        $this->expectException(ModelNotFoundException::class);

        $persistenceService->store([
            'name' => $name,
        ]);

        $persistenceService->update([
            'name' => $updateName,
        ], 2);
    }

    private function migrate()
    {
        $this->artisan('migrate', ['--database' => 'testing'])->run();
    }
}
