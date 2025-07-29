<?php

namespace Makaveli\ModuleGenerator\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class CLoadModuleService extends GeneratorCommand
{
    protected $name = 'module:service';
    protected $signature = 'module:service
                        {name : Service name}
                        {module : Module name}
                        {model : Model name}
                        {--oC : Add create operation}
                        {--oU : Add update operation}
                        {--oD : Add delete operation}
                        {--oR : Add restore operation}
                        {--fields= : Comma-separated list of fields for create and update in lowerCamelCase}';

    protected $description = 'Create a new service for module';
    protected $type = 'Service';

    protected function getStub()
    {
        return __DIR__.'/../../stubs/service.stub';
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
        return "App\\Modules\\{$this->argument('module')}\\Services";
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
        $fields = $this->option('fields');

        $methods = [];
        if ($this->option('oC')) $methods[] = $this->generateMethod('create', $modelName, $fields);
        if ($this->option('oU')) $methods[] = $this->generateMethod('update', $modelName, $fields);
        if ($this->option('oD')) $methods[] = $this->generateMethod('delete', $modelName, null);
        if ($this->option('oR')) $methods[] = $this->generateMethod('restore', $modelName, null);

        $replace = [
            '{{ module }}' => $module,
            '{{ model }}' => $modelName,
            '{{ methods }}' => implode("\n", $methods),
        ];

        return str_replace(
            array_keys($replace),
            array_values($replace),
            $stub
        );
    }

    protected function generateMethod(string $type, string $modelName, ?string $fields): string
    {
        $stubPath = __DIR__."/../../stubs/service/{$type}.stub";

        if (!file_exists($stubPath)) {
            $this->error("Stub for {$type} method not found!");
            return '';
        }

        $stub = $this->files->get($stubPath);
        $stub = str_replace('{{ model }}', $modelName, $stub);

        $fieldsCode = '';
        if ($fields) {
            $fieldsList = explode(',', $fields);

            foreach ($fieldsList as $field) {
                $field = trim($field);
                $dbField = Str::snake($field);

                $fieldsCode .= "            '$dbField' => \$dto->$field,\n";
            }
        }

        return str_replace('{{ fields }}', $fieldsCode, $stub);
    }
}

