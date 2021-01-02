<?php

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use LaravelDomainOriented\Models\Model;
use LaravelDomainOriented\Services\FilterService;
use LaravelDomainOriented\Tests\Cases\TestCase;

class FilterTest extends TestCase
{
    private Request $request;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = new Request();
    }

    /** @test  **/
    public function it_should_build_the_sql_query()
    {
        $builder = new MyEloquentModel();
        $builder = $builder
            ->query()
            ->whereIn('id', [1,2,3])
            ->where('name', 'test')
            ->where('outro', '>', 1234)
            ->whereBetween('date', ['2020-01-01', '2021-01-01'])
            ->where('test', 'value');
        $builder2 = new MyEloquentModel();

        $this->request->merge([
            'id' => [1,2,3],
            'name' => 'test',
            'outro' => [
                'operator' => '>',
                'value' => 1234
            ],
            'date' => [
                'start' => '2020-01-01',
                'end' => '2021-01-01'
            ],
            'test' => 'value',
        ]);

        $filterService = new MyFilterService();
        $filterService = $filterService->apply($builder2->query(), $this->request);
        $this->assertSame($builder->toSql(), $filterService->toSql());
        $this->assertEquals([
            1,2,3,
            'test',
            1234,
            '2020-01-01',
            '2021-01-01',
            'value',
        ], $filterService->getBindings());
    }
}

class MyEloquentModel extends Model
{
    //
}

class MyFilterService extends FilterService {
    protected array $fields = ['id', 'name', 'outro', 'date', 'test'];

    public function test($value): Builder
    {
        return $this->builder->where('test', $value);
    }
}
