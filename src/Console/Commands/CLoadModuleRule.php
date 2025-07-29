<?php

namespace Makaveli\ModuleGenerator\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class CLoadModuleRule extends GeneratorCommand
{
    protected $name = 'module:rule';
    protected $signature = 'module:rule
                        {name : Rule name}
                        {module : Module name}';

    protected $description = 'Create a new rule for module';
    protected $type = 'Rule';

    protected function getStub()
    {
        return __DIR__.'/../../stubs/rule.stub';
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

        return "App\\Modules\\{$this->argument('module')}\\Rules";
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $module = $this->argument('module');
        $name = $this->argument('name');

        $replace = [
            '{{ module }}' => $module,
            '{{ name }}' => $name,
            '{{ funcName }}' => \Illuminate\Support\Str::camel($name)
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
