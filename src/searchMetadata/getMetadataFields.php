<?php
// Grab metadata fields for display
require_once '../KalturaConnector/KalturaServiceFactory.php';
use \Kaltura\Client\Plugin\Metadata\Service\MetadataProfileService as KalturaMetadataProfileService;

$client = new wcheng\KalturaEntriesToXML\Kaltura\KalturaServiceFactory();
$client = $client->getKalturaClient();
$metadataFields = array();
$metadataProfileService = new KalturaMetadataProfileService($client);

$results = $metadataProfileService->listFields('27091');
foreach ($results->objects as $metadata) {
    array_push($metadataFields, $metadata->key);
}

$return = array();
$return['metadataFields'] = $metadataFields;

echo json_encode($return);
