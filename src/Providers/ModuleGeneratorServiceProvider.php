<?php

namespace Makaveli\ModuleGenerator\Providers;

use Illuminate\Support\ServiceProvider;

use Makaveli\ModuleGenerator\Console\Commands\CLoadModule;
use Makaveli\ModuleGenerator\Console\Commands\CLoadModuleAction;
use Makaveli\ModuleGenerator\Console\Commands\CLoadModuleCommandSeed;
use Makaveli\ModuleGenerator\Console\Commands\CLoadModuleConstant;
use Makaveli\ModuleGenerator\Console\Commands\CLoadModuleController;
use Makaveli\ModuleGenerator\Console\Commands\CLoadModuleFactory;
use Makaveli\ModuleGenerator\Console\Commands\CLoadModuleFilter;
use Makaveli\ModuleGenerator\Console\Commands\CLoadModuleFormDTO;
use Makaveli\ModuleGenerator\Console\Commands\CLoadModuleJob;
use Makaveli\ModuleGenerator\Console\Commands\CLoadModuleListDTO;
use Makaveli\ModuleGenerator\Console\Commands\CLoadModuleModel;
use Makaveli\ModuleGenerator\Console\Commands\CLoadModuleRepository;
use Makaveli\ModuleGenerator\Console\Commands\CLoadModuleRequest;
use Makaveli\ModuleGenerator\Console\Commands\CLoadModuleResource;
use Makaveli\ModuleGenerator\Console\Commands\CLoadModuleRule;
use Makaveli\ModuleGenerator\Console\Commands\CLoadModuleSeeder;
use Makaveli\ModuleGenerator\Console\Commands\CLoadModuleService;
use Makaveli\ModuleGenerator\Console\Commands\CLoadModuleTrait;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish stubs
        $this->publishes([
            __DIR__.'/../stubs' => base_path('stubs'),
        ], 'module-generator-stubs');

        // Register console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                CLoadModule::class,
                CLoadModuleAction::class,
                CLoadModuleController::class,
                CLoadModuleModel::class,
                CLoadModuleRequest::class,
                CLoadModuleCommandSeed::class,
                CLoadModuleConstant::class,
                CLoadModuleFactory::class,
                CLoadModuleFilter::class,
                CLoadModuleFormDTO::class,
                CLoadModuleJob::class,
                CLoadModuleListDTO::class,
                CLoadModuleRepository::class,
                CLoadModuleResource::class,
                CLoadModuleRule::class,
                CLoadModuleSeeder::class,
                CLoadModuleService::class,
                CLoadModuleTrait::class,
            ]);
        }
    }
}