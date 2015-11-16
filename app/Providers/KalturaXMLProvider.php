<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use wcheng\KalturaEntriesToXML\Models\XML\KalturaXML;

class KalturaXMLProvider extends ServiceProvider
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
        $this->app->bind('wcheng\KalturaEntriesToXML\Models\XML\KalturaXML', function ($app) {
            $metadataProfileId = env('metadataProfileId');
            $xmlStorageLocation = env('xmlStorageLocation');

            return new KalturaXML($app['wcheng\KalturaEntriesToXML\Models\Entries\KalturaSideEntries'], $metadataProfileId, $xmlStorageLocation);
        });
    }
}
