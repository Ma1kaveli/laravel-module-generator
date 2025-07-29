<?php

namespace Makaveli\ModuleGenerator\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class CLoadModuleFormDTO extends GeneratorCommand
{
    protected $name = 'module:form-dto';
    protected $signature = 'module:form-dto
                        {name : Form DTO name}
                        {module : Module name}
                        {model : Model name}
                        {--f= : Comma-separated form of fields in lowerCamelCase}';

    protected $description = 'Create a new form DTO for module';
    protected $type = 'FormDTO';

    protected function getStub()
    {
        return __DIR__.'/../../stubs/form-dto.stub';
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
        $model = $this->argument('model');
        $fields = $this->option('f');

        $result = $this->generateFields($fields);
        $resultInit = $this->generateFieldsInit($fields);
        $resultReturn = $this->generateFieldsReturn($fields);

        $replace = [
            '{{ module }}' => $module,
            '{{ fields }}' => $result,
            '{{ fieldsInit }}' => $resultInit,
            '{{ fieldsReturn }}' => $resultReturn,
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

    public function generateFieldsInit($fields) {
        $result = '';
        if ($fields) {
            $fieldsList = explode(',', $fields);

            foreach ($fieldsList as $field) {
                $field = trim($field);

                $result .= "        public readonly mixed $$field,\n";
            }
        }

        return $result;
    }

    public function generateFieldsReturn($fields) {
        $result = '';
        if ($fields) {
            $fieldsList = explode(',', $fields);

            foreach ($fieldsList as $field) {
                $field = trim($field);
                $dbField = Str::snake($field);

                $result .= "            $field: \$baseData['converted_data']['$dbField'],\n";
            }
        }

        return $result;
    }
}
