<?php
require_once __DIR__ . '/../../vendor/autoload.php';

// Grab metadata fields for display

define('CONFIG_FILE', 'config.ini');
$configLocation = dirname(__FILE__) . '/../../' . CONFIG_FILE;
$kalturaConfig = parse_ini_file($configLocation);

$kalturaServiceFactory = new \wcheng\KalturaEntriesToXML\Models\ServiceFactory\KalturaServiceFactory($configLocation);
$client = $kalturaServiceFactory->getKalturaClient();
$metadataFields = array();
$metadataProfileService = $kalturaServiceFactory->getKalturaMetadataProfileService();

$results = $metadataProfileService->listFields($kalturaConfig['metadataProfileId']);
foreach ($results->objects as $metadata) {
    array_push($metadataFields, $metadata->key);
}

$return = array();
$return['metadataFields'] = $metadataFields;

echo json_encode($return);
