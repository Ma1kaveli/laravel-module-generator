<?php

namespace Makaveli\ModuleGenerator\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class CLoadModuleModel extends GeneratorCommand
{
    protected $name = 'module:model';
    protected $signature = 'module:model
                        {name : Model name}
                        {module : Module name}
                        {--filter : Add filter functionality}
                        {--smb : Use SoftModelBase instead of Eloquent Model}
                        {--factory : Add factory functionality}
                        {--bT=* : BelongsTo relations (relationName,relationModel,relationKey)}
                        {--hM=* : HasMany relations (relationName,relationModel,relationKey)}
                        {--hO=* : HasOne relations (relationName,relationModel,relationKey)}
                        {--bTm=* : BelongsToMany relations (relationName,relationModel,pivotModel,foreignPivotKey,relatedPivotKey)}';

    protected $description = 'Create a new model in a module';
    protected $type = 'Model';

    protected function getStub()
    {
        return __DIR__.'/../../stubs/model.stub';
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
        return "App\\Modules\\{$this->argument('module')}\\Models";
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
        $modelName = $this->argument('name');

        $baseClass = $this->option('smb')
            ? 'SoftModelBase\Models\SoftModelBase'
                : 'Illuminate\Database\Eloquent\Model';

        $traits = [];

        if ($this->option('factory')) {
            $traits[] = 'HasFactory';
        }

        if ($this->option('filter')) {
            $traits[] = 'Filterable';
        }

        $traitsString = empty($traits) ? '' : 'use ' . implode(', ', $traits) . ';';

        $filterMethod = '';
        if ($this->option('filter')) {
            $filterMethod = $this->buildMethodStub('model/filter');
        }

        $factoryMethod = '';
        if ($this->option('factory')) {
            $factoryMethod = $this->buildMethodStub('model/factory');
        }

        $belongsToMethods = $this->buildRelationMethods('bT', 'model/belongs-to');
        $hasManyMethods = $this->buildRelationMethods('hM', 'model/has-many');
        $hasOneMethods = $this->buildRelationMethods('hO', 'model/has-one');
        $belongsToManyMethods = $this->buildRelationMethods('bTm', 'model/belongs-to-many');

        $imports = [];
        if ($this->option('factory')) {
            $imports[] = "use App\\Modules\\{$module}\\Database\\Factories\\{$modelName}Factory;";
        }

        if ($this->option('filter')) {
            $imports[] = "use App\\Modules\\{$module}\\Filters\\{$modelName}Filters;";
        }

        $baseImports = [
            "use {$baseClass};",
        ];

        if ($this->option('factory')) {
            $baseImports[] = 'use Illuminate\Database\Eloquent\Factories\HasFactory;';
        }

        if ($this->option('filter')) {
            $baseImports[] = 'use QueryBuilder\Traits\Filterable;';
        }

        if (!empty($belongsToMethods)) $baseImports[] = 'use Illuminate\Database\Eloquent\Relations\BelongsTo;';
        if (!empty($hasManyMethods)) $baseImports[] = 'use Illuminate\Database\Eloquent\Relations\HasMany;';
        if (!empty($hasOneMethods)) $baseImports[] = $baseImports[] = 'use Illuminate\Database\Eloquent\Relations\HasOne;';
        if (!empty($belongsToManyMethods)) $baseImports[] = 'use Illuminate\Database\Eloquent\Relations\BelongsToMany;';

        $imports = array_merge($imports, $baseImports);
        $importsString = implode("\n", $imports);

        $replace = [
            '{{ imports }}' => $importsString,
            '{{ baseClass }}' => class_basename($baseClass),
            '{{ traits }}' => $traitsString,
            '{{ module }}' => $module,
            '{{ model }}' => $modelName,
            '{{ filter }}' => $filterMethod,
            '{{ factory }}' => $factoryMethod,
            '{{ belongsToMethods }}' => $belongsToMethods,
            '{{ hasManyMethods }}' => $hasManyMethods,
            '{{ hasOneMethods }}' => $hasOneMethods,
            '{{ belongsToManyMethods }}' => $belongsToManyMethods,
            '{{ table }}' => $this->getTableName($modelName),
        ];

        return str_replace(
            array_keys($replace),
            array_values($replace),
            $stub
        );
    }

    protected function buildRelationMethods($option, $stubName)
    {
        $methods = [];
        $values = $this->option($option) ?: [];
        foreach ($values as $value) {
            $params = explode(',', $value);
            $methodStub = $this->buildRelationMethodStub($stubName, $params);
            if ($methodStub) {
                $methods[] = $methodStub;
            }
        }
        return implode("\n\n", $methods);
    }

    protected function buildRelationMethodStub($stubName, $params)
    {
        $stubPath = __DIR__."/../../stubs/{$stubName}.stub";
        if (!file_exists($stubPath)) {
            $this->error("Stub for {$stubName} not found!");
            return '';
        }

        $stub = file_get_contents($stubPath);

        $replacements = [];
        switch ($stubName) {
            case 'model/belongs-to':
            case 'model/has-many':
            case 'model/has-one':
                if (count($params) < 3) {
                    $this->error("Insufficient parameters for {$stubName}: expected 3, got ".count($params));
                    return '';
                }
                $replacements = [
                    '{{ relationName }}' => $params[0],
                    '{{ relationModel }}' => $params[1],
                    '{{ relationKey }}' => $params[2],
                ];
                break;
            case 'model/belongs-to-many':
                if (count($params) < 5) {
                    $this->error("Insufficient parameters for {$stubName}: expected 5, got ".count($params));
                    return '';
                }
                $replacements = [
                    '{{ relationName }}' => $params[0],
                    '{{ relationModel }}' => $params[1],
                    '{{ pivotModel }}' => $params[2],
                    '{{ foreignPivotKey }}' => $params[3],
                    '{{ relatedPivotKey }}' => $params[4],
                ];
                break;
            default:
                return '';
        }

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $stub
        );
    }

    protected function buildMethodStub($type)
    {
        $stubPath = __DIR__."/../../stubs/{$type}.stub";

        if (!file_exists($stubPath)) {
            $this->error("Stub for {$type} not found!");
            return '';
        }

        $stub = file_get_contents($stubPath);

        return str_replace(
            ['{{ model }}', '{{ module }}'],
            [$this->argument('name'), $this->argument('module')],
            $stub
        );
    }

    protected function getTableName($modelName)
    {
        return Str::snake(Str::plural($modelName));
    }
}
