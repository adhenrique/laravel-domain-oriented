<?php

namespace LaravelDomainOriented;

use Carbon\Carbon;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Builder
{
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
    private Collection $names;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function prepare(): array
    {
        $exists = [];
        foreach ($this->filesPaths as $stubName => $finalPath) {
            if ($stubName !== 'Migration') {
                $exists[] = $this->checkClassExists($stubName, $finalPath);
            } else {
                $exists[] = $this->checkMigrationExists();
            }
        }

        return array_filter($exists);
    }

    public function run(): void
    {
        foreach ($this->filesPaths as $stubName => $finalPath) {
            $this->createFile($stubName, $finalPath);
        }
    }

    public function clear()
    {
        foreach ($this->filesPaths as $stubName => $finalPath) {
            $this->removeFile($stubName, $finalPath);
        }
    }

    public function createFile(string $stubName, string $finalPath): void
    {
        $stubFile = $this->filesystem->get($this->getStub($stubName));
        $content = $this->replacePlaceholders($stubFile);
        $path = base_path(sprintf($finalPath, $this->getDomainName()));
        $fileName = $this->getName('singularName', 'Dummy') . $stubName . '.php';

        if ($stubName === 'Migration') {
            $now = Carbon::now()->format('Y_m_d_His');
            $fileName = $now."_create_{$this->getName('tableName', 'dummies')}_table.php";
        }

        $file = $path.$fileName;
        $this->filesystem->put($file, $content);
    }

    // todo - we can use glob function as well, for the other files
    //  passing the domainName concatenated with the stubName ?
    public function removeFile(string $stubName, string $finalPath)
    {
        $path = base_path(sprintf($finalPath, $this->getDomainName()));
        $fileName = $this->getName('singularName', 'Dummy') . $stubName . '.php';
        $file = $path.$fileName;

        if ($stubName === 'Migration') {
            $file = $this->filesystem->glob($path.'*_create_'.$this->getName('tableName').'_table.php');
        }

        $this->filesystem->delete($file);
    }

    public function getStub($stubName): string
    {
        $relativePath = '/Stubs/' . $stubName . '.stub';

        return file_exists($customPath = base_path(trim($relativePath, '/')))
            ? $customPath
            : __DIR__.$relativePath;
    }

    public function setNames(string $name = 'Dummy'): void
    {
        $name = str_replace('_', ' ', trim($name));
        $name = str_replace('  ', ' ', $name);
        $singularName = Str::studly($name);
        $pluralName = Str::studly(Str::plural($name));
        $singularSnake = Str::slug($name);

        $this->names = collect([
            'singularName' => $singularName,
            'pluralName' => $pluralName,
            'singularSnake' => $singularSnake,
            'tableName' => Str::snake($pluralName),
        ]);
    }

    public function getNames(): Collection
    {
        return $this->names;
    }

    private function getName(string $slug, string $default = null): string
    {
        return $this->names->get($slug, $default);
    }

    public function getDomainName(): string
    {
        return $this->getName('singularName');
    }

    public function getDomainFolder(): string
    {
        return app_path('Domain/'.$this->getDomainName());
    }

    public function replacePlaceholders($stubFile)
    {
        $stubFile = str_replace('{{singularName}}', $this->names['singularName'], $stubFile);
        $stubFile = str_replace('{{pluralName}}', $this->names['pluralName'], $stubFile);
        return str_replace('{{tableName}}', $this->names['tableName'], $stubFile);
    }

    private function checkMigrationExists(): string
    {
        $migrationPath = base_path('database/migrations');
        $migrationFiles = $this->filesystem->glob($migrationPath.'/*.php');

        $exists = false;

        foreach ($migrationFiles as $migrationFile) {
            if (Str::contains($migrationFile, 'create_'.$this->getName('tableName').'_table')) {
                $exists = $migrationFile;
            }
        }

        return $exists;
    }

    private function checkClassExists(string $stubName, string $finalPath): string
    {
        $path = base_path(sprintf($finalPath, $this->getDomainName()));
        $fileName = $this->getName('singularName', 'Dummy') . $stubName . '.php';
        $file = $path.$fileName;

        return $this->filesystem->exists($file) ? $file : false;
    }

    public function createDomainFolder()
    {
        $this->filesystem->ensureDirectoryExists($this->getDomainFolder());
    }
}
