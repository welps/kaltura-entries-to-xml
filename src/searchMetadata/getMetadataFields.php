<?php
// Grab metadata fields for display
require_once '../KalturaConnector/KalturaServiceFactory.php';

$kalturaServiceFactory = new wcheng\KalturaEntriesToXML\Kaltura\KalturaServiceFactory();
$client = $kalturaServiceFactory->getKalturaClient();
$metadataFields = array();
$metadataProfileService = $kalturaServiceFactory->getKalturaMetadataProfileService();

$results = $metadataProfileService->listFields('27091');
foreach ($results->objects as $metadata) {
    array_push($metadataFields, $metadata->key);
}

$return = array();
$return['metadataFields'] = $metadataFields;

echo json_encode($return);
