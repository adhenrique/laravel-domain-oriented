<?php

namespace Tests\Feature;

use Illuminate\Console\OutputStyle;
use Illuminate\Filesystem\Filesystem;
use LaravelDomainOriented\Commands\CreateDomain;
use LaravelDomainOriented\Tests\Cases\TestCase;
use Symfony\Component\Console\Application as ConsoleApplication;
use Illuminate\Foundation\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CreateDomainTest extends TestCase
{
    private CreateDomain $command;
    private Filesystem $filesystem;
    private array $filesPaths = [
        'Controller' => 'Http/Controllers/',
        'Resource' => 'Domain/%s/',
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
        $this->command = new CreateDomain($this->filesystem);
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
        $this->assertInstanceOf(CreateDomain::class, $this->command);
    }

    /** @test **/
    public function it_should_create_structure()
    {
        $domainName = 'Test';
        $this->runCommand(['name' => $domainName]);

        $totalFiles = count($this->filesPaths);
        $createdFiles = 0;
        foreach ($this->filesPaths as $stubName => $finalPath) {
            $path = app_path(sprintf($finalPath, $domainName));
            $fileName = $domainName.$stubName.'.php';
            $file = $path.$fileName;

            if ($this->filesystem->exists($file)) {
                $createdFiles++;
            }
        }
        $this->assertEquals($totalFiles, $createdFiles);
    }

    /** @test  **/
    public function it_should_rewrite_structure()
    {
        $domainName = 'Test';
        $actualContent = $this->getActualContent($domainName);

        $tester = $this->runCommand(['name' => $domainName], ['yes']);

        $rewriteContent = $this->getRewriteContent($domainName);

        $this->assertNotEquals($rewriteContent, $actualContent);
        $this->assertSame(0, $tester->getStatusCode());
    }

    /** @test  **/
    public function it_should_force_rewrite_structure()
    {
        $domainName = 'Test';
        $actualContent = $this->getActualContent($domainName);

        $tester = $this->runCommand(['name' => $domainName, '--force' => true]);

        $rewriteContent = $this->getRewriteContent($domainName);

        $this->assertNotEquals($rewriteContent, $actualContent);
        $this->assertSame(0, $tester->getStatusCode());
    }

    private function getActualContent(string $domainName): array
    {
        $actualContent = [];

        foreach ($this->filesPaths as $stubName => $finalPath) {
            $path = app_path(sprintf($finalPath, $domainName));
            $fileName = $domainName.$stubName.'.php';
            $file = $path.$fileName;

            $this->filesystem->append($file, '//modified');
            $actualContent[] = [
                $file => $this->filesystem->get($file),
            ];
        }

        return $actualContent;
    }

    private function getRewriteContent(string $domainName): array
    {
        $rewriteContent = [];

        foreach ($this->filesPaths as $stubName => $finalPath) {
            $path = app_path(sprintf($finalPath, $domainName));
            $fileName = $domainName.$stubName.'.php';
            $file = $path.$fileName;

            $rewriteContent[] = [
                $file => $this->filesystem->get($file),
            ];
        }

        return $rewriteContent;
    }
}
