<?php

namespace Makaveli\ModuleGenerator\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class CLoadModuleRepository extends GeneratorCommand
{
    protected $name = 'module:repository';
    protected $signature = 'module:repository
                        {name : Repository name}
                        {module : Module name}
                        {model : Model name}';

    protected $description = 'Create a new repository for module';
    protected $type = 'Repository';

    protected function getStub()
    {
        return __DIR__.'/../../stubs/repository.stub';
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

        return "App\\Modules\\{$this->argument('module')}\\Repositories";
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $module = $this->argument('module');
        $model = $this->argument('model');

        $replace = [
            '{{ module }}' => $module,
            '{{ model }}' => $model,
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
