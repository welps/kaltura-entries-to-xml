<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use wcheng\KalturaEntriesToXML\Models\ServiceFactory\KalturaServiceFactory;

class KalturaServiceFactoryProvider extends ServiceProvider
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
        $kalturaConfig['serviceUrl'] = env('serviceUrl');
        $kalturaConfig['partnerId'] = env('partnerId');
        $kalturaConfig['adminSecret'] = env('adminSecret');
        $kalturaConfig['userId'] = env('userId');
        $kalturaConfig['metadataProfileId'] = env('metadataProfileId');
        $kalturaConfig['xmlStorageLocation'] = env('xmlStorageLocation');

        $this->app->bind('wcheng\KalturaEntriesToXML\Models\ServiceFactory\KalturaServiceFactory', function ($app) use ($kalturaConfig) {
            return new KalturaServiceFactory($kalturaConfig);
        });
    }
}
