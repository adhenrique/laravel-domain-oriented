<?php

namespace LaravelDomainOriented\Commands;

use Illuminate\Console\Command;

class RemoveDomain extends Command
{
    protected array $names;
    protected $signature = 'domain:remove
                            {name : The domain name}';
    protected $description = 'Remove domain structure.';

    public function handle(): int
    {
        $name = $this->getNameInput();
        // ask to remove
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
