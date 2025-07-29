<?php

namespace Makaveli\ModuleGenerator\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class CLoadModuleAction extends GeneratorCommand
{
    protected $name = 'module:action';
    protected $signature = 'module:action
                        {name : Action name}
                        {module : Module name}
                        {model : Model name}
                        {--oS : Add show operation}
                        {--oC : Add create operation}
                        {--oU : Add update operation}
                        {--oD : Add delete operation}
                        {--oR : Add restore operation}';

    protected $description = 'Create a new action for module';
    protected $type = 'Action';

    protected function getStub()
    {
        return __DIR__.'/../../stubs/action.stub';
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
        return "App\\Modules\\{$this->argument('module')}\\Actions";
    }

    public function handle()
    {
        if (!$this->argument('module')) {
            $this->input->setArgument('module', 'Base');
        }

        parent::handle();
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $module = $this->argument('module');
        $modelName = $this->argument('model');

        $methods = [];
        if ($this->option('oC')) $methods[] = $this->generateMethod('create', $modelName);
        if ($this->option('oU')) $methods[] = $this->generateMethod('update', $modelName);
        if ($this->option('oD')) $methods[] = $this->generateMethod('delete', $modelName);
        if ($this->option('oR')) $methods[] = $this->generateMethod('restore', $modelName);

        $replace = [
            '{{ module }}' => $module,
            '{{ model }}' => $modelName,
            '{{ modelRepository }}' => Str::camel($modelName) . 'Repository',
            '{{ modelService }}' => Str::camel($modelName) . 'Service',
            '{{ methods }}' => implode("\n", $methods),
        ];

        return str_replace(
            array_keys($replace),
            array_values($replace),
            $stub
        );
    }

    protected function generateMethod(string $type, string $modelName): string
    {
        $stubPath = __DIR__."/../../stubs/action/{$type}.stub";

        if (!file_exists($stubPath)) {
            $this->error("Stub for {$type} method not found!");
            return '';
        }

        $stub = $this->files->get($stubPath);

        $replace = [
            '{{ model }}' => $modelName,
            '{{ modelRepository }}' => Str::camel($modelName) . 'Repository',
            '{{ modelService }}' => Str::camel($modelName) . 'Service',
            '{{ MODEL }}' => Str::upper(Str::snake($modelName))
        ];

        return str_replace(
            array_keys($replace),
            array_values($replace),
            $stub
        );
    }
}

