<?php

namespace Tests\Feature;

use Illuminate\Console\OutputStyle;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use LaravelDomainOriented\Commands\CreateDomain;
use LaravelDomainOriented\Tests\Cases\BasicTestCase;
use Symfony\Component\Console\Application as ConsoleApplication;
use Illuminate\Foundation\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CreateDomainTest extends BasicTestCase
{
    private CreateDomain $command;
    private Filesystem $filesystem;
    private array $filesPaths = [
        'Controller' => 'app/Http/Controllers/',
        'Resource' => 'app/Domain/%s/',
        'SearchModel' => 'app/Domain/%s/',
        'PersistenceModel' => 'app/Domain/%s/',
        'SearchService' => 'app/Domain/%s/',
        'PersistenceService' => 'app/Domain/%s/',
        'FilterService' => 'app/Domain/%s/',
        'StoreRequest' => 'app/Domain/%s/',
        'Migration' => 'database/migrations/',
    ];
    private string $domainName = 'Test';
    private string $tableName = '';

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
        $this->tableName = Str::snake(Str::studly(Str::plural($this->domainName)));
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
        $this->runCommand(['name' => $this->domainName]);

        $totalFiles = count($this->filesPaths);
        $createdFiles = 0;
        foreach ($this->filesPaths as $stubName => $finalPath) {
            $path = base_path(sprintf($finalPath, $this->domainName));
            $fileName = $this->domainName.$stubName.'.php';
            $file = $path.$fileName;
            $migration = [];

            if ($stubName === 'Migration') {
                $migration = $this->filesystem->glob($path.'*_create_'.$this->tableName.'_table.php');
            }

            if ($this->filesystem->exists($file) || count($migration)) {
                $createdFiles++;
            }
        }
        $this->assertEquals($totalFiles, $createdFiles);
    }

    /** @test  **/
    public function it_should_ask_to_rewrite_structure()
    {
        $actualContent = $this->getActualContent();

        $tester = $this->runCommand(['name' => $this->domainName], ['yes']);

        $rewriteContent = $this->getRewriteContent();

        $this->assertNotEquals($rewriteContent, $actualContent);
        $this->assertSame(0, $tester->getStatusCode());
    }

    /** @test  **/
    public function it_should_force_rewrite_structure()
    {
        $actualContent = $this->getActualContent();

        $tester = $this->runCommand(['name' => $this->domainName, '--force' => true], ['yes']);

        $rewriteContent = $this->getRewriteContent();

        $this->assertNotEquals($rewriteContent, $actualContent);
        $this->assertSame(0, $tester->getStatusCode());
    }

    private function getActualContent(): array
    {
        $actualContent = [];

        foreach ($this->filesPaths as $stubName => $finalPath) {
            $path = base_path(sprintf($finalPath, $this->domainName));
            $fileName = $this->domainName.$stubName.'.php';
            $file = $path.$fileName;

            if ($stubName === 'Migration') {
                $migration = $this->filesystem->glob($path.'*_create_'.$this->tableName.'_table.php');
                $this->filesystem->append($migration[0], '//modified');
                $actualContent[] = [
                    $migration[0] => $this->filesystem->get($migration[0]),
                ];
            } else {
                $this->filesystem->append($file, '//modified');
                $actualContent[] = [
                    $file => $this->filesystem->get($file),
                ];
            }
        }

        return $actualContent;
    }

    private function getRewriteContent(): array
    {
        $rewriteContent = [];

        foreach ($this->filesPaths as $stubName => $finalPath) {
            $path = base_path(sprintf($finalPath, $this->domainName));
            $fileName = $this->domainName.$stubName.'.php';
            $file = $path.$fileName;

            if ($stubName === 'Migration') {
                $migration = $this->filesystem->glob($path.'*_create_'.$this->tableName.'_table.php');
                $rewriteContent[] = [
                    $migration[0] => $this->filesystem->get($migration[0]),
                ];
            } else {
                $rewriteContent[] = [
                    $file => $this->filesystem->get($file),
                ];
            }
        }

        return $rewriteContent;
    }

    protected function seeInConsoleOutput($searchStrings)
    {
        if (! is_array($searchStrings)) {
            $searchStrings = [$searchStrings];
        }

        $output = Artisan::output();

        foreach ($searchStrings as $searchString) {
            $this->assertStringContainsString((string) $searchString, $output);
        }
    }
}
