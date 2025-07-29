<?php

namespace Makaveli\ModuleGenerator\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class CLoadModuleListDTO extends GeneratorCommand
{
    protected $name = 'module:list-dto';
    protected $signature = 'module:list-dto
                        {name : List DTO name}
                        {module : Module name}
                        {--f= : Comma-separated list of fields in lowerCamelCase}';

    protected $description = 'Create a new list DTO for module';
    protected $type = 'ListDTO';

    protected function getStub()
    {
        return __DIR__.'/../../stubs/list-dto.stub';
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

        return "App\\Modules\\{$this->argument('module')}\\DTO";
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $module = $this->argument('module');
        $fields = $this->option('f');

        $result = $this->generateFields($fields);

        $replace = [
            '{{ module }}' => $module,
            '{{ fields }}' => $result
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

    public function generateFields($fields) {
        $result = '';
        if ($fields) {
            $fieldsList = explode(',', $fields);

            foreach ($fieldsList as $field) {
                $field = trim($field);

                $result .= "'$field', ";
            }
        }

        return $result;
    }
}
