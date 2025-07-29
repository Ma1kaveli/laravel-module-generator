<?php

namespace Makaveli\ModuleGenerator\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class CLoadModuleSeeder extends GeneratorCommand
{
    protected $name = 'module:seeder';
    protected $signature = 'module:seeder
                        {name : Seeder name}
                        {module : Module name}
                        {model : Model name}
                        {--test : Is test seeder}
                        {--fields= : Comma-separated list of fields}';

    protected $description = 'Create a new seeder for module';
    protected $type = 'Seeder';

    protected function getStub()
    {
        return __DIR__.'/../../stubs/seeder.stub';
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
        $isTest = $this->option('test');
        $prefixDir = $isTest ? "\\Test\\" : "";

        return "App\\Modules\\{$this->argument('module')}\\Database\\Seeders{$prefixDir}";
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
        $isTest = $this->option('test');
        $seederName = $this->argument('name');
        $modelName = $this->argument('model');
        $fields = $this->option('fields');

        $imports = ["use App\\Modules\\{$module}\\Models\\{$modelName};"];

        if (!$isTest) {
            $imports[] = "use App\\Modules\\{$module}\\Constants\\{$modelName}Constants;";
        }

        $runMethodContent = $this->getRunFunctionStub($isTest);

        $runMethodContent = str_replace(
            ['{{ model }}', '{{ seeder }}'],
            [$modelName, Str::snake($seederName)],
            $runMethodContent
        );

        $methods = [];
        if (!$isTest) {
            $methods = [
                $this->generateMethod('create', $modelName, $fields),
                $this->generateMethod('update', $modelName, $fields)
            ];
        }

        $replace = [
            '{{ namespace }}' => $this->getDefaultNamespace(''),
            '{{ imports }}' => implode("\n", $imports),
            '{{ seederName }}' => $seederName,
            '{{ model }}' => $modelName,
            '{{ action }}' => $runMethodContent,
            '{{ methods }}' => implode("\n", $methods),
        ];

        return str_replace(
            array_keys($replace),
            array_values($replace),
            $stub
        );
    }

    protected function getRunFunctionStub(bool $isTest)
    {
        $stubPath = $isTest
            ? __DIR__."/../../stubs/seeder/test.stub"
            : __DIR__."/../../stubs/seeder/constants.stub";

        if (!file_exists($stubPath)) {
            $this->error("Stub for run method not found!");
            return '';
        }

        return $this->files->get($stubPath);
    }

    protected function generateMethod(string $type, string $modelName, ?string $fields): string
    {
        $stubPath = __DIR__."/../../stubs/seeder/{$type}.stub";

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

                if ($type === 'create') {
                    $fieldsCode .= "            '$field' => \$data['$field'],\n";
                } elseif ($type === 'update') {
                    $fieldsCode .= "        \$data->$field = \$newData['$field'];\n";
                }
            }
        }

        return str_replace('{{ fields }}', $fieldsCode, $stub);
    }
}
