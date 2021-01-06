<?php

namespace Tests\Unit;

use Illuminate\Validation\ValidationException;
use LaravelDomainOriented\Services\ValidateService;
use LaravelDomainOriented\Tests\Cases\BasicTestCase;

class ValidateServiceTest extends BasicTestCase
{
    private MyCustomValidateService $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new MyCustomValidateService();
    }

    /** @test **/
    public function it_should_validate_and_return_data()
    {
        $data = [
            'name' => 'Test name',
            'phone' => 1234,
        ];

        $validated = $this->validator->handle($data);

        $this->assertEquals($data, $validated);
    }

    /** @test **/
    public function it_should_validate_and_return_only_rules_indexes_data()
    {
        $data = [
            'name' => 'Test name',
            'phone' => 1234,
            'outro' => 'ABC',
        ];

        $validated = $this->validator->handle($data);

        $this->assertNotEquals($data, $validated);
        unset($data['outro']);
        $this->assertEquals($data, $validated);
    }

    /** @test **/
    public function it_should_throw_an_exception()
    {
        $data = [
            'name' => 'Test name',
            'phone' => 'asd',
        ];

        $this->expectException(ValidationException::class);

        $data = $this->validator->handle($data);
    }
}

class MyCustomValidateService extends ValidateService
{
    protected array $rules = [
        'name' => 'required|string',
        'phone' => 'integer',
    ];
}
