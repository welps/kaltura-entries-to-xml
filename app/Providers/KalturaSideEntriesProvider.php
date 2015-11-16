<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use wcheng\KalturaEntriesToXML\Models\Entries\KalturaSideEntries;

class KalturaSideEntriesProvider extends ServiceProvider
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
        $this->app->bind('wcheng\KalturaEntriesToXML\Models\Entries\KalturaSideEntries', function ($app) {
            return new KalturaSideEntries($app['wcheng\KalturaEntriesToXML\Models\ServiceFactory\ServiceFactory']);
        });
    }
}
