<?php

namespace Makaveli\ModuleGenerator\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class CLoadModuleJob extends GeneratorCommand
{
    protected $name = 'module:job';
    protected $signature = 'module:job
                        {name : Job name}
                        {module : Module name}';

    protected $description = 'Create a new job for module';
    protected $type = 'Job';

    protected function getStub()
    {
        return __DIR__.'/../../stubs/job.stub';
    }

    /**
     * Generate namespace
     *
     * @param mixed $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {

        return "App\\Modules\\{$this->argument('module')}\\Jobs";
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $module = $this->argument('module');

        $replace = [
            '{{ module }}' => $module,
        ];

        return str_replace(
            array_keys($replace),
            array_values($replace),
            $stub
        );
    }

    public function handle()
    {
        if (!$this->argument('module')) {
            $this->input->setArgument('module', 'Base');
        }

        parent::handle();
    }
}
