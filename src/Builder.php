<?php

namespace LaravelDomainOriented;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Str;

class Builder
{
    private Filesystem $filesystem;
    private array $filesPaths = [
        'Controller' => 'Http/Controllers/',
        'Resource' => 'Domain/%s/',
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
            if ($file = $this->validate($stubName, $finalPath)) {
                $exists[] = $file;
            }
        }

        return $exists;
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
        list($path, $fileName) = $this->getPathAndFile($stubName, $finalPath);
        $file = $path.$fileName;

        $this->filesystem->put($file, $content);
    }

    public function removeFile(string $stubName, string $finalPath)
    {
        list($path, $fileName) = $this->getPathAndFile($stubName, $finalPath);
        $file = $path.$fileName;

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

    private function validate(string $stubName, string $finalPath): ?string
    {
        list($path, $fileName) = $this->getPathAndFile($stubName, $finalPath);
        $file = $path.$fileName;

        return $this->filesystem->exists($file) ? $file : null;
    }

    public function getPathAndFile(string $stubName, string $finalPath): array
    {
        $path = app_path(sprintf($finalPath, $this->getDomainName()));
        $fileName = $this->getName('singularName', 'Dummy') . $stubName . '.php';

        return [$path, $fileName];
    }

    public function createDomainFolder()
    {
        $this->filesystem->ensureDirectoryExists($this->getDomainFolder());
    }
}
