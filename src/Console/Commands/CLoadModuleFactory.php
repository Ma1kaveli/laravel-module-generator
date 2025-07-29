<?php

namespace Makaveli\ModuleGenerator\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class CLoadModuleFactory extends GeneratorCommand
{
    protected $name = 'module:factory';
    protected $signature = 'module:factory
                        {name : Factory name}
                        {module : Module name}';

    protected $description = 'Create a new factory for module';
    protected $type = 'Factory';

    protected function getStub()
    {
        return __DIR__.'/../../stubs/factory.stub';
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
        return "App\\Modules\\{$this->argument('module')}\\Database\\Factories";
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $module = $this->argument('module');
        $name = $this->argument('name');

        $replace = [
            '{{ module }}' => $module,
            '{{ model }}' => str_replace('Factory', '', $name),
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
