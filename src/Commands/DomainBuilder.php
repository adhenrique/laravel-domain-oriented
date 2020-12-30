<?php

namespace LaravelDomainOriented\Commands;

use Illuminate\Console\Command;

class DomainBuilder extends Command
{
    protected array $names;
    protected $signature = 'make:domain
                            {name : The domain name}
                            {--force : Force re-create domain structure}';
    protected $description = 'Create new domain structure.';

    public function handle(): int
    {
        $name = $this->getNameInput();
        // todo

        return 0;
    }

    protected function getNameInput(): string
    {
        return trim($this->argument('name'));
    }

    protected function exit()
    {
        $this->line('');
        $this->line($this->getTransMessage('exit'));
        $this->line('');
    }

    private function getTransMessage($slug)
    {
        return trans("lang::messages.{$slug}");
    }
}
