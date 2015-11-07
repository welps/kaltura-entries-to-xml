<?php
// Grab metadata fields for display
require_once '../KalturaConnector/KalturaConnector.php';
use \Kaltura\Client\Plugin\Metadata\Service\MetadataProfileService as KalturaMetadataProfileService;

$client = new KalturaConnector();
$client = $client->startKalturaConnection();
$metadataFields = array();
$metadataProfileService = new KalturaMetadataProfileService($client);

$results = $metadataProfileService->listFields('27091');
foreach ($results->objects as $metadata) {
    array_push($metadataFields, $metadata->key);
}

$return = array();
$return['metadataFields'] = $metadataFields;

echo json_encode($return);
