<?php

namespace Tests\Unit;

use App\Domain\Test\TestPersistenceService;
use App\Domain\Test\TestSearchService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use LaravelDomainOriented\Tests\Cases\DBTestCase;

class PersistenceServiceTest extends DBTestCase
{
    protected bool $insertItems = false;

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
    public function it_should_update_a_item_and_compare_difference()
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

    /** @test **/
    public function it_should_delete_item_and_throw_when_search_it()
    {
        $persistenceService = $this->app->make(TestPersistenceService::class);
        $searchService = $this->app->make(TestSearchService::class);
        $id = 1;

        $this->expectException(ModelNotFoundException::class);

        $isDeleted = $persistenceService->destroy($id);
        $searchService->findById(1);

        $this->assertTrue($isDeleted);
    }
}
