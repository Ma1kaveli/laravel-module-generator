<?php

namespace Makaveli\ModuleGenerator\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class CLoadModuleCommandSeed extends GeneratorCommand
{
    protected $name = 'module:command-seed';
    protected $signature = 'module:command-seed
                        {name : Seeder name}
                        {module : Module name}';

    protected $description = 'Create a new command for module';
    protected $type = 'Console Command';

    protected function getStub()
    {
        return __DIR__.'/../../stubs/console-seed.stub';
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
        return "App\\Modules\\{$this->argument('module')}\\Console\\Commands";
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $module = $this->argument('module');
        $name = $this->argument('name');
        $model = str_replace(['CLoadTest', 'CLoad'], '', $name);
        $isTest = strpos($name, 'Test') !== false;

        $testSubFolder = $isTest ? "Test\\" : "";
        $commandTestPrefix = $isTest ? 'test-' :  '';

        $imports = [
            "use App\\Modules\\{$module}\\Database\\Seeders\\{$testSubFolder}{$model}Seeder;"
        ];


        $replace = [
            '{{ module }}' => $module,
            '{{ command }}' => $commandTestPrefix . Str::kebab($model),
            '{{ seeder }}' => "{$model}Seeder",
            '{{ imports }}' => implode("\n", $imports)
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
