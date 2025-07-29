<?php

namespace Makaveli\ModuleGenerator\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class CLoadModuleTrait extends GeneratorCommand
{
    protected $name = 'module:trait';
    protected $signature = 'module:trait
                        {name : Trait name}
                        {module : Module name}';

    protected $description = 'Create a new trait for module';
    protected $type = 'Trait';

    protected function getStub()
    {
        return __DIR__.'/../../stubs/trait.stub';
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

        return "App\\Modules\\{$this->argument('module')}\\Traits";
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
