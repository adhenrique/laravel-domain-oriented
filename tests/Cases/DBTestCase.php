<?php

namespace LaravelDomainOriented\Tests\Cases;

use Illuminate\Support\Facades\DB;

abstract class DBTestCase extends BaseTestCase
{
    protected array $data = [
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
    protected static bool $initialized = false;
    protected bool $insertItems = true;
    protected string $domainName = 'Test';

    public function setUp(): void
    {
        parent::setUp();
        if (!self::$initialized) {
            $this->artisan('domain:create ' . $this->domainName . ' --force');

            self::$initialized = true;
        }
        $this->makeDatabase();
    }

    private function makeDatabase()
    {
        $this->artisan('migrate', ['--database' => 'testing'])->run();

        if ($this->insertItems)
            DB::table('tests')->insert($this->data);
    }
}
