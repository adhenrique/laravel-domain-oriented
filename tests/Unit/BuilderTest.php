<?php

namespace Tests\Unit;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Enumerable;
use LaravelDomainOriented\Builder;
use LaravelDomainOriented\Tests\Cases\BasicTestCase;

class BuilderTest extends BasicTestCase
{
    private Builder $builder;
    private Filesystem $filesystem;
    private array $filesPaths = [
        'Controller' => 'app/Http/Controllers/',
        'Resource' => 'app/Domain/%s/',
        'SearchModel' => 'app/Domain/%s/',
        'PersistenceModel' => 'app/Domain/%s/',
        'SearchService' => 'app/Domain/%s/',
        'PersistenceService' => 'app/Domain/%s/',
        'FilterService' => 'app/Domain/%s/',
        'ValidateService' => 'app/Domain/%s/',
        'Migration' => 'database/migrations/',
        'Factory' => 'database/factories/',
        'Seeder' => 'database/seeders/',
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->filesystem = new Filesystem();
        $this->builder = new Builder();
        $this->builder->setNames();
    }

    /** @test **/
    public function it_should_be_constructed()
    {
        $this->assertInstanceOf(Builder::class, $this->builder);
    }

    /** @test **/
    public function it_should_return_the_stub_file_path()
    {
        $stubName = 'Controller';
        $stubPath = $this->builder->getStub($stubName);
        $this->assertStringContainsString('Stubs/' . $stubName . '.stub', $stubPath);
    }

    /** @test **/
    public function it_should_get_array_of_names()
    {
        $names = $this->builder->getNames();

        $this->assertInstanceOf(Enumerable::class, $names);
        $this->assertIsArray($names->toArray());
        $this->assertEquals([
            "singularName" => "Dummy",
            "pluralName" => "Dummies",
            "singularSnake" => "dummy",
            "tableName" => "dummies"
        ], $names->toArray());
    }

    /** @test **/
    public function it_should_get_dummy_domain_name()
    {
        $domainName = $this->builder->getDomainName();

        $this->assertEquals('Dummy', $domainName);
    }

    /** @test **/
    public function it_should_replace_placeholders()
    {
        $fileName = 'Controller';

        $stubPath = $this->builder->getStub($fileName);
        $stubFile = $this->filesystem->get($stubPath);

        $content = $this->builder->replacePlaceholders($stubFile);

        $this->assertEquals('<?php

namespace App\Http\Controllers;

use App\Domain\Dummy\DummyPersistenceService;
use App\Domain\Dummy\DummyResource;
use App\Domain\Dummy\DummySearchService;
use App\Domain\Dummy\DummyValidateService;
use LaravelDomainOriented\Controller\Controller;

class DummyController extends Controller
{
    protected $resource = DummyResource::class;

    public function __construct(
        DummyPersistenceService $persistenceService,
        DummySearchService $searchService,
        DummyValidateService $validateService
    ) {
        $this->persistenceService = $persistenceService;
        $this->searchService = $searchService;
        $this->validateService = $validateService;
    }
}
', $content);
    }

    /** @test **/
    public function it_should_return_domain_folder_name()
    {
        $domainName = 'A spectacular domain';
        $this->builder->setNames($domainName);
        $domainFolder = $this->builder->getDomainFolder();

        $this->assertEquals(base_path('app/Domain/'.$this->builder->getDomainName()), $domainFolder);
    }

    /** @test **/
    public function it_should_domain_directory_exists()
    {
        $domainName = 'A spectacular domain';
        $this->builder->setNames($domainName);
        $this->builder->createDomainFolder();

        $this->assertDirectoryExists(base_path('app/Domain/'.$this->builder->getDomainName()));
    }

    /** @test **/
    public function it_should_create_a_controller_file()
    {
        $stubName = 'Controller';
        $finalPath = 'app/Http/Controllers/';
        $this->builder->createFile($stubName, $finalPath);
        $fileName = $this->builder->getDomainName().'Controller.php';

        $path = base_path($finalPath.$fileName);

        $this->assertFileExists($path);
    }

    /** @test **/
    public function it_should_remove_controller_file()
    {
        $stubName = 'Controller';
        $finalPath = 'app/Http/Controllers/';
        $this->builder->createFile($stubName, $finalPath);
        $this->builder->removeFile($stubName, $finalPath);
        $fileName = $this->builder->getDomainName().'Controller.php';

        $path = base_path($finalPath.$fileName);

        $this->assertFileDoesNotExist($path);
    }

    /** @test **/
    public function it_should_create_a_migration_file()
    {
        $stubName = 'Migration';
        $finalPath = 'database/migrations/';
        $this->builder->createFile($stubName, $finalPath);
        $path = base_path(sprintf($finalPath, $this->builder->getDomainName()));
        $migration = $this->filesystem->glob($path.'*_create_'.$this->builder->getNames()['tableName'].'_table.php');

        $this->assertFileExists($migration[0]);
    }

    /** @test **/
    public function it_should_remove_migration_file()
    {
        $stubName = 'Migration';
        $finalPath = 'database/migrations/';
        $this->builder->createFile($stubName, $finalPath);
        $this->builder->removeFile($stubName, $finalPath);
        $path = base_path(sprintf($finalPath, $this->builder->getDomainName()));
        $migration = $this->filesystem->glob($path.'*_create_'.$this->builder->getNames()['tableName'].'_table.php');

        $this->assertCount(0, $migration);
    }

    /** @test **/
    public function it_should_prepare_return_array()
    {
        $this->builder->clear();
        $this->builder->run();
        $files = $this->builder->prepare();
        $totalFiles = count($this->filesPaths);

        $this->assertIsArray($files);
        $this->assertCount($totalFiles, $files);
    }

    /** @test **/
    public function it_should_run_and_create_all_files()
    {
        $this->builder->clear();
        $this->builder->run();

        $totalFiles = count($this->filesPaths);
        $createdFiles = 0;
        foreach ($this->filesPaths as $stubName => $finalPath) {
            $path = base_path(sprintf($finalPath, $this->builder->getDomainName()));
            $fileName = $this->builder->getNames()['singularName'] . $stubName . '.php';
            $file = $path.$fileName;
            $migration = [];

            if ($stubName === 'Migration') {
                $migration = $this->filesystem->glob($path.'*_create_'.$this->builder->getNames()['tableName'].'_table.php');
            }

            if ($this->filesystem->exists($file) || count($migration)) {
                $createdFiles++;
            }
        }

        $this->assertEquals($totalFiles, $createdFiles);
    }

    /** @test **/
    public function it_should_insert_domain_name_to_file()
    {
        $domainName = 'Test';
        $this->builder->createDomainsFile();
        $this->builder->setNames($domainName);
        $this->builder->insertDomain();

        $path = app_path('domains.php');
        $domains = require $path;

        $this->assertContains($domainName, $domains);
    }

    /** @test **/
    public function it_should_remove_domain_name_to_file()
    {
        $domainName = 'Test';
        $this->builder->createDomainsFile();
        $this->builder->setNames($domainName);
        $this->builder->removeDomain();

        $path = app_path('domains.php');
        $domains = require $path;

        $this->assertNotContains($domainName, $domains);
    }
}
