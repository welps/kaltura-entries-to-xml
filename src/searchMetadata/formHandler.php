<?php
require_once __DIR__ . '/../../vendor/autoload.php';

define('CONFIG_FILE', 'config.ini');
$configLocation = dirname(__FILE__) . '/../../' . CONFIG_FILE;
$kalturaConfig = parse_ini_file($configLocation);

$kalturaServiceFactory = new \wcheng\KalturaEntriesToXML\Models\ServiceFactory\KalturaServiceFactory($configLocation);

// Data to be returned to form via ajax
$return = array();
$return['hasSearchTerm'] = true;
$return['hasSelectMetadata'] = true;
$return['message'] = '';

if (!empty($_POST['search-term']) && !empty($_POST['select-metadata'])) {
    $searchTerm = htmlspecialchars($_POST['search-term']);
    $searchCategory = htmlspecialchars($_POST['select-metadata']);
    // Kaltura metadata EventDate uses unix timestamp -- kaltura api is bugged
    /*if ($_POST['select-metadata'] == "EventDate"){
    $_POST['search-term'] = strtotime($_POST['search-term'] . '+4 hours');
    }*/

    // Separate searches into different method calls
    $newSearch = new \wcheng\KalturaEntriesToXML\Models\Entries\KalturaEntries($kalturaServiceFactory);
    switch ($searchCategory) {
        case "kaltura-video-name":
            $results = $newSearch->getEntriesByName($searchTerm);
            break;

        case "kaltura-tags":
            $results = $newSearch->getEntriesByTags($searchTerm);
            break;

        case "kaltura-category":
            $results = $newSearch->getEntriesByCategory($searchTerm);
            break;

        default:
            $results = $newSearch->getEntriesByMetadataCategory($searchTerm, $searchCategory, $kalturaConfig['metadataProfileId']);
    }
}

if (empty($_POST['search-term'])) {
    $return['hasSearchTerm'] = false;
}
if (empty($_POST['select-metadata'])) {
    $return['hasSelectMetadata'] = false;
}

if (!empty($results)) {
    $kaltura = new \wcheng\KalturaEntriesToXML\Models\XML\KalturaXML($kalturaServiceFactory, $kalturaConfig['metadataProfileId']);
    $kalturaFileOutput = $kaltura->getXML($results);
    $kalturaNumEntries = $kaltura->getNumEntries();

    $return['message'] = '<p id="match">' . $kalturaNumEntries . ' matching entries for <strong>' . $searchTerm . '</strong> in <strong>' . $searchCategory . '</strong></p>
	<p id="file">Download File: <strong><a href=' . $kalturaFileOutput . '>' . basename($kalturaFileOutput) . '</a></strong></p>';

    if ($kalturaNumEntries > 0) {
        $return['message'] = '<p id="match">' . $kalturaNumEntries . ' matching entries for <strong>' . $searchTerm . '</strong> in <strong>' . $searchCategory . '</strong></p>
	    <p id="file">Download File: <strong><a href=' . $kalturaFileOutput . '>' . basename($kalturaFileOutput) . '</a></strong></p>';
    } else {
        $return['message'] = '<p id="nomatch">No results found for <strong>"' . $searchTerm . '"</strong> in <strong>' . $searchCategory . '</strong></p>';
    }
}

echo json_encode($return);
