<?php

namespace Makaveli\ModuleGenerator\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class CLoadModuleController extends GeneratorCommand
{
    protected $name = 'module:controller';
    protected $signature = 'module:controller
                        {name : Controller name}
                        {module : Module name}
                        {--folder= : Optional folder name}
                        {--scope= : Scope (Shared, Private, Portal, Mobile)}
                        {--oL : Add list operation}
                        {--oS : Add show operation}
                        {--oC : Add create operation}
                        {--oU : Add update operation}
                        {--oD : Add delete operation}
                        {--oR : Add restore operation}';

    protected $description = 'Create a new controller for module';
    protected $type = 'Controller';

    protected function getStub()
    {
        return __DIR__.'/../../stubs/controller.stub';
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
        $scope = $this->option('scope') ?: 'Private';
        $validScopes = ['Shared', 'Private', 'Portal', 'Mobile'];

        if (!in_array($scope, $validScopes)) {
            $this->error("Invalid scope! Using 'Shared' as default");
            $scope = 'Shared';
        }

        $folder = $this->option('folder');
        $folderPath = $folder ? "\\{$folder}" : '';

        return "App\\Http\\Controllers\\API\\{$scope}{$folderPath}\\{$this->argument('module')}";
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

        $imports = [
            "use App\Http\Controllers\Controller;",
            "use App\\Modules\\{$module}\\Repositories\\{$module}Repository;",
            "use App\\Modules\\{$module}\\Resources\\{$module}Resource;",
            "use App\\Modules\\{$module}\\Resources\\{$module}FullResource;",
            "use App\\Modules\\{$module}\\Actions\\{$module}Actions;",
            "use App\Modules\Base\DTO\OnceDTO;\n",
            "use QueryBuilder\Resources\PaginatedCollection;",
            "use QueryBuilder\DTO\ListDTO;"
        ];

        $methods = [];
        if ($this->option('oL')) $methods[] = $this->buildMethodStub('index');
        if ($this->option('oS')) $methods[] = $this->buildMethodStub('show');
        if ($this->option('oC')) $methods[] = $this->buildMethodStub('store');
        if ($this->option('oU')) $methods[] = $this->buildMethodStub('update');
        if ($this->option('oD')) $methods[] = $this->buildMethodStub('destroy');
        if ($this->option('oR')) $methods[] = $this->buildMethodStub('restore');

        $replace = [
            '{{ imports }}' => implode("\n", $imports),
            '{{ methods }}' => implode("\n\n", $methods),
            '{{ module }}' => $this->argument('module'),
            '{{ model }}' => str_replace('Controller', '', $this->argument('name')),
        ];

        return str_replace(
            array_keys($replace),
            array_values($replace),
            $stub
        );
    }

    protected function buildMethodStub($type)
    {
        $stubPath = __DIR__."/../../stubs/controller/{$type}.stub";

        if (!file_exists($stubPath)) {
            $this->error("Stub for {$type} method not found!");
            return '';
        }

        return $this->files->get($stubPath);
    }
}
