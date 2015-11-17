<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use wcheng\KalturaEntriesToXML\Models\Entries\KalturaEntries;
use wcheng\KalturaEntriesToXML\Models\XML\KalturaXML;

class EntriesController extends Controller
{
    public function getXMLEntries(Request $request, KalturaEntries $kalturaEntries, KalturaXML $kalturaXML)
    {
        // Data to be returned to form via ajax
        $return = array();
        $return['hasSearchTerm'] = true;
        $return['hasSelectMetadata'] = true;
        $return['message'] = '';

        $searchTerm = htmlentities($request->input('search-term'));
        $searchCategory = htmlentities($request->input('select-metadata'));

        if (!empty($searchTerm) && !empty($searchCategory)) {

            switch ($searchCategory) {
                case "kaltura-video-name":
                    $results = $kalturaEntries->getEntriesByName($searchTerm);
                    break;

                case "kaltura-tags":
                    $results = $kalturaEntries->getEntriesByTags($searchTerm);
                    break;

                case "kaltura-category":
                    $results = $kalturaEntries->getEntriesByCategory($searchTerm);
                    break;

                default:
                    $results = $kalturaEntries->getEntriesByMetadataCategory($searchTerm, $searchCategory, env('metadataProfileId'));
            }
        }

        if (empty($searchTerm)) {
            $return['hasSearchTerm'] = false;
        }

        if (empty($searchCategory)) {
            $return['hasSelectMetadata'] = false;
        }

        if (!empty($results)) {
            $kalturaFileOutput = $kalturaXML->getXML($results);
            $kalturaNumEntries = $kalturaXML->getNumEntries();

            $return['message'] = '<p id="match">' . $kalturaNumEntries . ' matching entries for <strong>' . $searchTerm . '</strong> in <strong>' . $searchCategory . '</strong></p>
            <p id="file">Download File: <strong><a href=' . $kalturaFileOutput . '>' . basename($kalturaFileOutput) . '</a></strong></p>';

            if ($kalturaNumEntries > 0) {
                $return['message'] = '<p id="match">' . $kalturaNumEntries . ' matching entries for <strong>' . $searchTerm . '</strong> in <strong>' . $searchCategory . '</strong></p>
                <p id="file">Download File: <strong><a href=' . $kalturaFileOutput . '>' . basename($kalturaFileOutput) . '</a></strong></p>';
            } else {
                $return['message'] = '<p id="nomatch">No results found for <strong>"' . $searchTerm . '"</strong> in <strong>' . $searchCategory . '</strong></p>';
            }
        }

        return json_encode($return);
    }
}
