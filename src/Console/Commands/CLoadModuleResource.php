<?php

namespace Makaveli\ModuleGenerator\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class CLoadModuleResource extends GeneratorCommand
{
    protected $name = 'module:resource';
    protected $signature = 'module:resource
                        {name : Resource name}
                        {module : Module name}
                        {--f=* : Fields (field,mapperField,resource,isArray)}
                        {--aF=* : Additional fields (field,mapperField,resource,isArray)}';

    protected $description = 'Create a new resource for module';
    protected $type = 'Resource';

    protected function getStub()
    {
        return __DIR__.'/../../stubs/resource.stub';
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

        return "App\\Modules\\{$this->argument('module')}\\Resources";
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $module = $this->argument('module');

        $fields = $this->buildFields('f');
        $additionalFields = $this->buildFields('aF');

        $replace = [
            '{{ module }}' => $module,
            '{{ fields }}' => $fields,
            '{{ additionalFields }}' => $additionalFields
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

    protected function buildFields($option)
    {
        $fields = [];

        $values = $this->option($option) ?: [];
        foreach ($values as $value) {
            $params = explode(',', $value);
            $field = array_key_exists(0, $params) ? $params[0] : null;
            $mapperField = (array_key_exists(1, $params) && ($params[1] !== 'null')) ? $params[1] : null;
            $resource = array_key_exists(2, $params) ? $params[2] : null;
            $isArray = array_key_exists(3, $params) ? true : false;

            if (empty($field)) continue;

            $fields[] = $this->buildField($field, $mapperField, $resource, $isArray);
        }

        return implode(",\n\t\t\t", $fields);
    }

    protected function buildField(
        string $field,
        ?string $mapperField,
        ?string $resource,
        bool $isArray = false
    ) {
        if (empty($resource) && empty($mapperField)) {
            return "'{$field}'" . ' => $this->' . "{$field}";
        }

        if (empty($resource)) {
            return "'{$field}'" . ' => $this->' . "{$mapperField}";
        }

        if (!$isArray && empty($mapperField)) {
            return "'{$field}' => new {$resource}(" . '$this->' . "{$field})";
        }

        if (!$isArray) {
            return "'{$field}' => new {$resource}(" . '$this->' . "{$mapperField})";
        }

        if (empty($mapperField)) {
            return "'{$field}' => {$resource}::collection(" . '$this->' . "{$field})";
        }

        return "'{$field}' => {$resource}::collection(" . '$this->' . "{$mapperField})";
    }
}
