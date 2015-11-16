<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use wcheng\KalturaEntriesToXML\Models\ServiceFactory\KalturaServiceFactory;

class MetadataController extends Controller
{
    public function getMetadataFields(KalturaServiceFactory $kalturaServiceFactory)
    {
        $client = $kalturaServiceFactory->getKalturaClient();
        $metadataFields = array();
        $metadataProfileService = $kalturaServiceFactory->getKalturaMetadataProfileService();

        $results = $metadataProfileService->listFields(env('metadataProfileId'));
        foreach ($results->objects as $metadata) {
            array_push($metadataFields, $metadata->key);
        }

        $return = array();
        $return['metadataFields'] = $metadataFields;

        return json_encode($return);
    }
}
