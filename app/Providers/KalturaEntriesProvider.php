<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use wcheng\KalturaEntriesToXML\Models\Entries\KalturaEntries;

class KalturaEntriesProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('wcheng\KalturaEntriesToXML\Models\Entries\KalturaEntries', function ($app) {
            return new KalturaEntries($app['wcheng\KalturaEntriesToXML\Models\ServiceFactory\ServiceFactory']);
        });
    }
}
