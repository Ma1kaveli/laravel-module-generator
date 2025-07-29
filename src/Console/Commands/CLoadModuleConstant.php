<?php

namespace Makaveli\ModuleGenerator\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class CLoadModuleConstant extends GeneratorCommand
{
    protected $name = 'module:constant';
    protected $signature = 'module:constant
                        {name : Constant name}
                        {module : Module name}';

    protected $description = 'Create a new constant for module';
    protected $type = 'Constant';

    protected function getStub()
    {
        return __DIR__.'/../../stubs/constant.stub';
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

        return "App\\Modules\\{$this->argument('module')}\\Constants";
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $module = $this->argument('module');
        $modelName = $this->argument('name');

        $replace = [
            '{{ module }}' => $module,
            '{{ name }}' => $modelName,
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
