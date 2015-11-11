<?php
// Grab metadata fields for display
require_once '../ServiceFactory/KalturaServiceFactory.php';

$kalturaServiceFactory = new \wcheng\KalturaEntriesToXML\ServiceFactory\KalturaServiceFactory();
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
