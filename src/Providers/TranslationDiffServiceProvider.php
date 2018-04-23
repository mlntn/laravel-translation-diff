<?php

namespace Mlntn\Translation\Providers;

use Illuminate\Support\ServiceProvider;
use Mlntn\Translation\Commands\CheckTranslations;

class TranslationDiffServiceProvider extends ServiceProvider
{

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CheckTranslations::class,
            ]);
        }
    }

}
