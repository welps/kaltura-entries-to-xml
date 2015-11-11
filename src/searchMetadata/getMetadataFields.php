<?php
// Grab metadata fields for display
require_once '../ServiceFactory/KalturaServiceFactory.php';

define('CONFIG_FILE', 'config.ini');
$configLocation = dirname(__FILE__) . '/../../' . CONFIG_FILE;

$kalturaServiceFactory = new \wcheng\KalturaEntriesToXML\ServiceFactory\KalturaServiceFactory($configLocation);
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
