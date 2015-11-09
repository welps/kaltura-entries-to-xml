<?php
require_once '../KalturaConnector/KalturaServiceFactory.php';

use \Kaltura\Client\Type\FilterPager as KalturaFilterPager;
use \Kaltura\Client\Type\MediaEntryFilter as KalturaMediaEntryFilter;

// Provides search options and retrieves results from Kaltura
// Results can be passed to Kaltura2XML for an export

class SearchMetadata
{
    private $mClient;

    public function __construct()
    {
        $this->getConnection();
    }
    // Initiate connection with Kaltura
    private function getConnection()
    {
        $this->mClient = new wcheng\KalturaEntriesToXML\Kaltura\KalturaServiceFactory();
        $this->mClient = $this->mClient->getKalturaClient();
    }

    // Grabs entries by entry name
    public function getEntriesByName($searchTerm)
    {
        if (ctype_space($searchTerm)) {
            return null;
        } else {
            $pager = new KalturaFilterPager();
            $pager->pageIndex = 1;
            $pager->pageSize = 500;

            $filter = new KalturaMediaEntryFilter();
            $filter->nameLike = $searchTerm;

            return $this->getSearchResults($filter, $pager);
        }

    }

    // Grabs entries by category using categoryID, this search will include all sub-categories of the specified category
    public function getEntriesByCategory($searchTerm)
    {
        if (ctype_space($searchTerm)) {
            return null;
        } else {
            $pager = new KalturaFilterPager();
            $pager->pageIndex = 1;
            $pager->pageSize = 500;

            $filter = new KalturaMediaEntryFilter();
            $filter->categoryAncestorIdIn = $searchTerm;

            return $this->getSearchResults($filter, $pager);
        }

    }

    // Grabs entries by tags using tagsLike property
    public function getEntriesByTags($searchTerm)
    {
        if (ctype_space($searchTerm)) {
            return null;
        } else {
            $pager = new KalturaFilterPager();
            $pager->pageIndex = 1;
            $pager->pageSize = 500;

            $filter = new KalturaMediaEntryFilter();
            $filter->tagsLike = $searchTerm;

            return $this->getSearchResults($filter, $pager);
        }

    }

    // Grabs entries by metadata category, needs the category to match (use Metadata profile service to grab categories), and the profile ID
    public function getEntriesByMetadataCategory($searchTerm, $searchCategory, $metadataProfileId)
    {
        if (ctype_space($searchTerm) || ctype_space($searchCategory)) {
            return null;
        } else {
            $pager = new KalturaFilterPager();
            $pager->pageIndex = 1;
            $pager->pageSize = 500;

            $filter = new KalturaMediaEntryFilter();
            $filterAdvancedSearch = new KalturaMetadataSearchItem();
            $filterAdvancedSearch->type = KalturaSearchOperatorType::SEARCH_OR;
            $filterAdvancedSearch->metadataProfileId = $metadataProfileId; // Obtained by calling metadataProfile service and getting the profile ID
            $filterAdvancedSearchItems = new KalturaSearchCondition();
            $filterAdvancedSearchItems->field = "/*[local-name()='metadata']/*[local-name()='" . $searchCategory . "']"; // Replace FieldName with the name obtained by calling metadataProfile service and showing defined fields
            $filterAdvancedSearchItems->value = $searchTerm;
            $filterAdvancedSearch->items = array($filterAdvancedSearchItems);
            $filter->advancedSearch = $filterAdvancedSearch;

            return $this->getSearchResults($filter, $pager);
        }
    }

    public function getSearchResults($filter, $pager)
    {
        $results = $this->mClient->getMediaService()->listAction($filter, $pager);
        $isMoreResults = true;

        // Since we can only receive 500 entries per page, we'll have to loop through and merge these results
        while ($isMoreResults == true) {
            $pager->pageIndex++;
            $results2 = $this->mClient->getMediaService()->listAction($filter, $pager);

            if (count($results2->objects) != 0) {
                $results = $this->combineResults($results, $results2);
            } else {
                $isMoreResults = false;
            }
        }

        return $results;
    }

    // Merges the two Kaltura media list results together. Retains similar hierarchy to what the API returns.
    private function combineResults($firstSet, $secondSet)
    {
        return (object) array("objects" => array_merge((array) $firstSet->objects, (array) $secondSet->objects));
    }
};