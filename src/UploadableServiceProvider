<?php

namespace SergeyMiracle\Uploadable;

use Illuminate\Support\ServiceProvider;

class UploadableServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/uploadable.php' => config_path('uploadable.php'),
            ], 'config');
        }
    }


    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/uploadable.php', 'uploadable');
    }
}
