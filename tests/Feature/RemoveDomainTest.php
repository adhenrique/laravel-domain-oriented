<?php

namespace Feature;

use Illuminate\Console\OutputStyle;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use LaravelDomainOriented\Builder;
use LaravelDomainOriented\Commands\RemoveDomain;
use LaravelDomainOriented\Tests\Cases\TestCase;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Tester\CommandTester;

class RemoveDomainTest extends TestCase
{
    private RemoveDomain $command;
    private Filesystem $filesystem;
    private string $domainName = 'Test';
    private array $filesPaths = [
        'Controller' => 'app/Http/Controllers/',
        'Resource' => 'app/Domain/%s/',
        'SearchModel' => 'app/Domain/%s/',
        'PersistenceModel' => 'app/Domain/%s/',
        'SearchService' => 'app/Domain/%s/',
        'PersistenceService' => 'app/Domain/%s/',
        'FilterService' => 'app/Domain/%s/',
        'Migration' => 'database/migrations/',
    ];

    public function setUp(): void
    {
        parent::setUp();
        $console = $this->mock(ConsoleApplication::class)->makePartial();
        $console->__construct();

        $laravel = $this->mock(Application::class)->makePartial();
        $laravel->shouldReceive('make')->andReturnUsing(function ($arg) {
            return $this->app->make($arg);
        });

        $this->filesystem = new Filesystem();
        $this->command = new RemoveDomain($this->filesystem);

        $this->command->setLaravel($this->app);
        $this->command->setApplication($console);
    }

    private function runCommand(array $opts = [], array $inputs = []): CommandTester
    {
        $tester = new CommandTester($this->command);

        $this->app->bind(OutputStyle::class, function () use ($tester) {
            return new OutputStyle($tester->getInput(), $tester->getOutput());
        });

        $tester->setInputs($inputs);
        $tester->execute($opts);

        return $tester;
    }

    /** @test **/
    public function it_should_be_constructed()
    {
        $this->assertInstanceOf(RemoveDomain::class, $this->command);
    }

    /** @test **/
    public function it_should_cancel_operation()
    {
        $this->createStructure();
        $tester = $this->runCommand(['name' => $this->domainName]);

        $this->assertSame(-1, $tester->getStatusCode());
    }

    /** @test **/
    public function it_should_remove_structure()
    {
        $this->createStructure();
        $tester = $this->runCommand(['name' => $this->domainName], ['Yes']);

        $totalFiles = count($this->filesPaths);
        $removedFiles = 0;
        foreach ($this->filesPaths as $stubName => $finalPath) {
            $path = app_path(sprintf($finalPath, $this->domainName));
            $fileName = $this->domainName.$stubName.'.php';
            $file = $path.$fileName;

            if (!$this->filesystem->exists($file)) {
                $removedFiles++;
            }
        }

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertEquals($totalFiles, $removedFiles);
    }

    private function createStructure()
    {
        $builder = new Builder($this->filesystem);
        $builder->setNames($this->domainName);

        $builder->clear();
        $builder->run();
    }
}
