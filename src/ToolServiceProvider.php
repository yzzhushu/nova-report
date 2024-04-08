<?php

namespace Jshxl\Report;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Jshxl\Report\Nova\JshxlReport;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Http\Middleware\Authenticate;
use Laravel\Nova\Nova;
use Jshxl\Report\Http\Middleware\Authorize;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->app->booted(function () {
            $this->routes();
        });
        // 加载数据库迁移文件
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // 注册文件发布
        if ($this->app->runningInConsole()) $this->registerPublishing();

        // 注册Nova资源对象
        Nova::resources([JshxlReport::class]);

        // 如果publish了语言文件，则加载publish的语言文件，否则加载默认的语言文件
        $this->loadJsonTranslationsFrom(file_exists(lang_path('vendor/report')) ?
            lang_path('vendor/report') : __DIR__ . '/../resources/lang');

        // 如果publish了配置文件，则加载publish的配置文件，否则加载默认的配置文件
        $this->mergeConfigFrom(file_exists(config_path('jshxl_report.php')) ?
            config_path('jshxl_report.php') : __DIR__ . '/../config/jshxl_report.php', 'jshxl_report');

        Nova::serving(function (ServingNova $event) {
            //
        });
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Nova::router(['nova', Authenticate::class, Authorize::class], 'report')
            ->namespace('Jshxl\Report\Http\Controllers')
            ->group(__DIR__.'/../routes/inertia.php');

        Route::middleware(['nova', Authorize::class])
            ->prefix('nova-vendor/report')
            ->group(__DIR__.'/../routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->commands([
            Console\PublishReport::class,
        ]);
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing(): void
    {
        $this->publishes([
            __DIR__ . '/../resources/lang' => lang_path('vendor/report'),
        ], 'jshxl-report-lang');

        $this->publishes([
            __DIR__ . '/../config' => config_path(),
        ], 'jshxl-report-config');
    }
}
