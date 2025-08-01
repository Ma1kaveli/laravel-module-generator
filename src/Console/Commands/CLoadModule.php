<?php

namespace Makaveli\ModuleGenerator\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CLoadModule extends Command
{
    protected $name = 'module';
    protected $signature = 'module
                        {name : Module name}
                        {model : Model name}
                        {--a : Add Action}
                        {--cs : Add Command seed}
                        {--c : Add Controller}
                        {--ct : Add Constant}
                        {--f : Add Filter}
                        {--fy : Add Factory}
                        {--fdto : Add Form DTO}
                        {--ldto : Add List DTO}
                        {--j : Add Job}
                        {--m : Add Model}
                        {--r : Add Repository}
                        {--req : Add Request}
                        {--res : Add Resource}
                        {--ru : Add Rule}
                        {--se : Add Seeder}
                        {--s : Add Service}
                        {--t : Add Trait}
                        {--seT : Is test seeder}
                        {--folder= : Optional folder name for controller}
                        {--scope= : Scope (Shared, Private, Portal, Mobile) for controller location}
                        {--smb : Use SoftModelBase instead of Eloquent Model}
                        {--oL : Add list operation}
                        {--oS : Add show operation}
                        {--oC : Add create operation}
                        {--oU : Add update operation}
                        {--oD : Add delete operation}
                        {--oR : Add restore operation}
                        {--bT=* : BelongsTo relations (relationName,relationModel,relationKey)}
                        {--hM=* : HasMany relations (relationName,relationModel,relationKey)}
                        {--hO=* : HasOne relations (relationName,relationModel,relationKey)}
                        {--bTm=* : BelongsToMany relations (relationName,relationModel,pivotModel,foreignPivotKey,relatedPivotKey)}
                        {--fF= : Comma-separated form dto of fields in lowerCamelCase}
                        {--lF= : Comma-separated list dto of fields in lowerCamelCase}
                        {--rF=* : Fields (name,rules) for request}
                        {--resF=* : Fields (field,mapperField,resource,isArray) for resource}
                        {--resAF=* : Additional fields (field,mapperField,resource,isArray) for resource}
                        {--seF= : Comma-separated list of fields for seeder}
                        {--sF= : Comma-separated list of fields for create and update in lowerCamelCase for service}';

    protected $description = 'Create a new module for module';

    public function handle()
    {
        $module = $this->argument('name');
        $model = $this->argument('model');
        $isTestSeeder = $this->option('seT');

        $generators = [
            'action' => [
                'command' => 'module:action',
                'enabled' => $this->option('a'),
                'withModel' => true,
                'name' => "{$model}Actions",
                'options' => [
                    'oS' => 'oS',
                    'oC' => 'oC',
                    'oU' => 'oU',
                    'oD' => 'oD',
                    'oR' => 'oR',
                ],
            ],
            'command_seed' => [
                'command' => 'module:command-seed',
                'withModel' => false,
                'name' => $isTestSeeder ? "CLoadTest{$model}" : "CLoad{$model}",
                'enabled' => $this->option('cs'),
                'options' => [],
            ],
            'controller' => [
                'command' => 'module:controller',
                'withModel' => false,
                'enabled' => $this->option('c'),
                'name' => "{$model}Controller",
                'options' => [
                    'folder' => 'folder',
                    'scope' => 'scope',
                    'oL' => 'oL',
                    'oS' => 'oS',
                    'oC' => 'oC',
                    'oU' => 'oU',
                    'oD' => 'oD',
                    'oR' => 'oR',
                ],
            ],
            'constant' => [
                'command' => 'module:constant',
                'withModel' => false,
                'enabled' => $this->option('ct'),
                'name' => "{$model}Constants",
                'options' => [],
            ],
            'filter' => [
                'command' => 'module:filter',
                'withModel' => false,
                'enabled' => $this->option('f'),
                'name' => "{$model}Filters",
                'options' => [],
            ],
            'factory' => [
                'command' => 'module:factory',
                'withModel' => false,
                'enabled' => $this->option('fy'),
                'name' => "{$model}Factory",
                'options' => [],
            ],
            'formdto' => [
                'command' => 'module:form-dto',
                'withModel' => true,
                'enabled' => $this->option('fdto'),
                'name' => "{$model}FormDTO",
                'options' => [
                    'fF' => 'f',
                ],
            ],
            'listdto' => [
                'command' => 'module:list-dto',
                'enabled' => $this->option('ldto'),
                'name' => "{$model}ListDTO",
                'withModel' => false,
                'options' => [
                    'lF' => 'f',
                ],
            ],
            'job' => [
                'command' => 'module:job',
                'enabled' => $this->option('j'),
                'name' => "JProcess{$model}",
                'withModel' => false,
                'options' => [],
            ],
            'model' => [
                'command' => 'module:model',
                'enabled' => $this->option('m'),
                'name' => "{$model}",
                'withModel' => false,
                'options' => [
                    'smb' => 'smb',
                    'bT' => 'bT',
                    'hM' => 'hM',
                    'hO' => 'hO',
                    'bTm' => 'bTm',
                ],
            ],
            'repository' => [
                'command' => 'module:repository',
                'enabled' => $this->option('r'),
                'name' => "{$model}Repository",
                'withModel' => true,
                'options' => [],
            ],
            'create_request' => [
                'command' => 'module:request',
                'withModel' => false,
                'enabled' => $this->option('req'),
                'name' => "{$model}CreateRequest",
                'options' => [
                    'rF' => 'f',
                ],
            ],
            'update_request' => [
                'command' => 'module:request',
                'withModel' => false,
                'enabled' => $this->option('req'),
                'name' => "{$model}UpdateRequest",
                'options' => [
                    'rF' => 'f',
                ],
            ],
            'resource' => [
                'command' => 'module:resource',
                'withModel' => false,
                'enabled' => $this->option('res'),
                'name' => "{$model}Resource",
                'options' => [
                    'resF' => 'f',
                    'resAF' => 'aF',
                ],
            ],
            'rule' => [
                'command' => 'module:rule',
                'withModel' => false,
                'name' => "CustomRule",
                'enabled' => $this->option('ru'),
                'options' => [],
            ],
            'seeder' => [
                'command' => 'module:seeder',
                'withModel' => true,
                'name' => $isTestSeeder ? "{$model}TestSeeder" : "{$model}Seeder",
                'enabled' => $this->option('se'),
                'options' => [
                    'seF' => 'fields',
                    'seT' => 'test',
                ],
            ],
            'service' => [
                'command' => 'module:service',
                'withModel' => true,
                'enabled' => $this->option('s'),
                'name' => "{$model}Service",
                'options' => [
                    'sF' => 'fields',
                    'oC' => 'oC',
                    'oU' => 'oU',
                    'oD' => 'oD',
                    'oR' => 'oR',
                ],
            ],
            'trait' => [
                'command' => 'module:trait',
                'withModel' => false,
                'enabled' => $this->option('t'),
                'name' => "{$model}Trait",
                'options' => [],
            ],
        ];

        foreach ($generators as $key => $generator) {
            if (!$generator['enabled']) {
                continue;
            }

            $params = [
                'name' => $generator['name'],
                'module' => $module,
            ];

            // Добавляем модель для нужных команд
            if ($generator['withModel']) {
                $params['model'] = $model;
            }

            // Пробрасываем опции
            foreach ($generator['options'] as $currentOption => $childOption) {
                $value = $this->option($currentOption);

                if ($value !== null && $value !== false) {
                    $params['--' . $childOption] = $value;
                }
            }

            // Специальные условия для модели
            if ($key === 'model') {
                if ($this->option('f')) {
                    $params['--filter'] = true;
                }
                if ($this->option('fy')) {
                    $params['--factory'] = true;
                }
            }

            $this->call($generator['command'], $params);
        }

        $this->info("Module [{$module}] generated successfully!");
    }
}

