<?php

namespace LaravelDomainOriented\Commands;

class CreateDomain extends Command
{
    protected $signature = 'domain:create
                            {name : The domain name}
                            {--f|force : Force re-create domain structure}';
    protected $description = 'Create a new domain structure.';

    // fixme - Is there a better way to do these conditionals?
    public function handle(): int
    {
        $name = $this->getNameInput();
        $force = $this->option('force');

        $this->builder->setNames($name);
        $this->builder->createDomainFolder();

        $files = $this->builder->prepare();

        if ($force) {
            $this->builder->clear();
            $this->builder->run();
            return $this->finish();
        }

        if (count($files)) {
            $this->info($this->getTransMessage('exists'));
            $this->newLine();

            foreach ($files as $file) {
                $this->comment($file);
            }

            $rewrite = $this->confirm($this->getTransMessage('rewrite_ask'));

            if (!$rewrite) {
                return $this->exit();
            }

            $this->builder->clear();
            $this->builder->run();
            return $this->finish();
        }

        $this->builder->run();
        return $this->finish();
    }
}
