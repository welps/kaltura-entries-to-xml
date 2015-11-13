<?php
namespace wcheng\KalturaEntriesToXML\Models\Entries;

// Provides search options and retrieves results from Kaltura

class KalturaEntries
{
    private $mClient;
    protected $kalturaServiceFactory;

    public function __construct(\wcheng\KalturaEntriesToXML\Models\ServiceFactory\ServiceFactory $kalturaServiceFactory)
    {
        $this->kalturaServiceFactory = $kalturaServiceFactory;
        $this->mClient = $this->kalturaServiceFactory->getKalturaClient();
    }

    public function getEntriesByName($searchTerm)
    {
        if (ctype_space($searchTerm)) {
            return null;
        } else {
            $pager = $this->setFilterPager(1, 500);
            $filter = $this->setMediaEntryFilter('nameLike', $searchTerm);

            return $this->getAllMatchingEntries($filter, $pager);
        }

    }

    // this search will include all sub-categories of the specified category
    public function getEntriesByCategory($searchTerm)
    {
        if (ctype_space($searchTerm)) {
            return null;
        } else {
            $pager = $this->setFilterPager(1, 500);
            $filter = $this->setMediaEntryFilter('categoryAncestorIdIn', $searchTerm);

            return $this->getAllMatchingEntries($filter, $pager);
        }

    }

    public function getEntriesByTags($searchTerm)
    {
        if (ctype_space($searchTerm)) {
            return null;
        } else {
            $pager = $this->setFilterPager(1, 500);
            $filter = $this->setMediaEntryFilter('tagsLike', $searchTerm);

            return $this->getAllMatchingEntries($filter, $pager);
        }

    }

    public function getEntriesByMetadataCategory($searchTerm, $searchCategory, $metadataProfileId)
    {
        if (ctype_space($searchTerm) || ctype_space($searchCategory)) {
            return null;
        } else {
            $pager = $this->setFilterPager(1, 500);

            $filterAdvancedSearch = $this->kalturaServiceFactory->getKalturaMetadataSearchItem();
            $kalturaSearchOperatorType = $this->kalturaServiceFactory->getKalturaSearchOperatorType();

            $filterAdvancedSearch->type = $kalturaSearchOperatorType::SEARCH_OR;
            $filterAdvancedSearch->metadataProfileId = $metadataProfileId;

            $filterAdvancedSearchItems = $this->kalturaServiceFactory->getKalturaSearchCondition();
            $filterAdvancedSearchItems->field = "/*[local-name()='metadata']/*[local-name()='" . $searchCategory . "']";
            $filterAdvancedSearchItems->value = $searchTerm;

            $filterAdvancedSearch->items = array($filterAdvancedSearchItems);

            $filter = $this->setMediaEntryFilter('advancedSearch', $filterAdvancedSearch);

            return $this->getAllMatchingEntries($filter, $pager);
        }
    }

    public function getAllMatchingEntries($filter, $pager)
    {
        $results = $this->mClient->getMediaService()->listAction($filter, $pager);
        $hasMoreResults = true;

        // Since we can only receive 500 entries per page, we'll have to loop through and merge these results
        while ($hasMoreResults == true) {
            $pager->pageIndex++;
            $moreResults = $this->mClient->getMediaService()->listAction($filter, $pager);

            if (count($moreResults->objects) != 0) {
                $results = $this->combineResults($results, $moreResults);
            } else {
                $hasMoreResults = false;
            }
        }

        return $results;
    }

    // Merges the two Kaltura media list results together. Retains similar hierarchy to what the API returns.
    private function combineResults($firstSet, $secondSet)
    {
        return (object) array("objects" => array_merge((array) $firstSet->objects, (array) $secondSet->objects));
    }

    private function setFilterPager($pageIndex, $pageSize)
    {
        $pager = $this->kalturaServiceFactory->getKalturaFilterPager();
        $pager->pageIndex = $pageIndex;
        $pager->pageSize = $pageSize;

        return $pager;
    }

    private function setMediaEntryFilter($filterCategory, $valueToSet)
    {
        $filter = $this->kalturaServiceFactory->getKalturaMediaEntryFilter();
        $filter->$filterCategory = $valueToSet;

        return $filter;
    }
};
