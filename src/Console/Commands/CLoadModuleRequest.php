<?php

namespace Makaveli\ModuleGenerator\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class CLoadModuleRequest extends GeneratorCommand
{
    protected $name = 'module:request';
    protected $signature = 'module:request
                        {name : Request name}
                        {module : Module name}
                        {--f=* : Fields (name,rules)}';

    protected $description = 'Create a new request for module';
    protected $type = 'Request';

    protected function getStub()
    {
        return __DIR__.'/../../stubs/request.stub';
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

        return "App\\Modules\\{$this->argument('module')}\\Requests";
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $module = $this->argument('module');
        $name = $this->argument('name');

        $rules = $this->buildRules('f');
        $messages = $this->buildMessages('f');

        $replace = [
            '{{ module }}' => $module,
            '{{ name }}' => $name,
            '{{ rules }}' => $rules,
            '{{ messages }}' => $messages
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

    protected function buildRules($option)
    {
        $rules = [];

        $values = $this->option($option) ?: [];
        foreach ($values as $value) {
            $params = explode(',', $value);
            $rules[] = "'{$params[0]}' => '{$params[1]}'";
        }

        return implode(",\n\t\t\t", $rules);
    }

    protected function buildMessages($option)
    {
        $messages = [];

        $values = $this->option($option) ?: [];
        foreach ($values as $value) {
            $params = explode(',', $value);

            $rules = explode('|', $params[1]);

            foreach ($rules as $rule) {
                $messages[] = "'{$params[0]}.{$rule}' => ''";
            }
        }

        return implode(",\n\t\t\t", $messages);
    }
}
