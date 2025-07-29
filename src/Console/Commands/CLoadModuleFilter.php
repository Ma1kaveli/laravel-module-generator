<?php

namespace Makaveli\ModuleGenerator\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class CLoadModuleFilter extends GeneratorCommand
{
    protected $name = 'module:filter';
    protected $signature = 'module:filter
                        {name : Filter name}
                        {module : Module name}';

    protected $description = 'Create a new filter for module';
    protected $type = 'Filter';

    protected function getStub()
    {
        return __DIR__.'/../../stubs/filter.stub';
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

        return "App\\Modules\\{$this->argument('module')}\\Filters";
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $module = $this->argument('module');
        $name = $this->argument('name');

        $replace = [
            '{{ module }}' => $module,
            '{{ name }}' => $name,
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
