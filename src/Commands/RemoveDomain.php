<?php

namespace LaravelDomainOriented\Commands;

class RemoveDomain extends Command
{
    protected array $names;
    protected $signature = 'domain:remove
                            {name : The domain name}';
    protected $description = 'Remove domain structure.';

    public function handle(): int
    {
        $name = $this->getNameInput();
        $this->builder->setNames($name);

        $files = $this->builder->prepare();

        if (!count($files)) {
            $this->newLine();
            $this->warn($this->getTransMessage('not_exists', ['domain' => $name]));
            return $this->exit();
        }

        $remove = $this->confirm($this->getTransMessage('remove_ask', ['domain' => $name]));

        if (!$remove) {
            return $this->exit();
        }

        $this->builder->clear();

        return $this->finish('removed');
    }
}
