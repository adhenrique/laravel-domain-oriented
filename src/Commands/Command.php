<?php

namespace LaravelDomainOriented\Commands;

use Illuminate\Filesystem\Filesystem;
use LaravelDomainOriented\Builder;

class Command extends \Illuminate\Console\Command
{
    protected Builder $builder;

    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
        $this->builder = new Builder($filesystem);
    }

    protected function getNameInput(): string
    {
        return trim($this->argument('name'));
    }

    protected function exit(): int
    {
        $this->newLine();
        $this->line($this->getTransMessage('exit'));
        $this->newLine();
        return -1;
    }

    protected function finish(string $slug = 'finish'): int
    {
        $this->newLine();
        $this->line($this->getTransMessage($slug));
        $this->newLine();
        return 0;
    }

    protected function getTransMessage(string $slug, array $replace = []): string
    {
        return trans("lang::messages.{$slug}", $replace);
    }
}
