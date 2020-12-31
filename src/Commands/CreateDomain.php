<?php

namespace LaravelDomainOriented\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use LaravelDomainOriented\Builder;

class CreateDomain extends Command
{
    protected array $names;
    protected $signature = 'domain:create
                            {name : The domain name}
                            {--f|force : Force re-create domain structure}';
    protected $description = 'Create a new domain structure.';
    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
    }

    // fixme - Is there a better way to do these conditionals?
    public function handle(): int
    {
        $name = $this->getNameInput();
        $force = $this->option('force');
        $builder = new Builder($this->filesystem, $name);
        $files = $builder->prepare();

        if ($force) {
            $builder->clear();
            $builder->run();
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

            $builder->clear();
            $builder->run();
            return $this->finish();
        }

        $builder->run();
        return $this->finish();
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

    protected function finish(): int
    {
        $this->newLine();
        $this->line($this->getTransMessage('finish'));
        $this->newLine();
        return 0;
    }

    private function getTransMessage($slug)
    {
        return trans("lang::messages.{$slug}");
    }
}
