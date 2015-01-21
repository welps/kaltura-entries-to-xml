<?php
// Grab metadata fields for display
require_once '../KalturaConnector/KalturaConnector.php';

$client = new KalturaConnector();
$client = $client->startKalturaConnection();
$metadataFields = array();

$filter = new KalturaMetadataProfileFilter();
$results = $client->metadataProfile->listFields('27091');
foreach ($results->objects as $metadata) {
    array_push($metadataFields, $metadata->key);
}

$return = array();
$return['metadataFields'] = $metadataFields;

echo json_encode($return);
