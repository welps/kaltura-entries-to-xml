<?php
namespace wcheng\KalturaEntriesToXML\Models\Entries;

class KalturaSideEntries
{
    private $mClient;
    protected $kalturaServiceFactory;

    public function __construct(\wcheng\KalturaEntriesToXML\Models\ServiceFactory\ServiceFactory $kalturaServiceFactory)
    {
        $this->kalturaServiceFactory = $kalturaServiceFactory;
        $this->mClient = $this->kalturaServiceFactory->getKalturaClient();
    }

    public function getCategoriesForEntry($kalturaMediaId)
    {
        $filter = $this->kalturaServiceFactory->getKalturaCategoryEntryFilter();
        $filter->entryIdEqual = $kalturaMediaId;
        $pager = null;

        $categoryResults = $this->mClient->getCategoryEntryService()->listAction($filter, $pager);

        if (count($categoryResults->objects) <= 0) {
            return null;
        }

        foreach ($categoryResults->objects as $categoryEntries) {
            $categoryListResults = $this->mClient->getCategoryService()->get($categoryEntries->categoryId);
            $categoryArray[] = htmlentities($categoryListResults->fullName);
        }

        return $categoryArray;
    }

    public function getMetadataForEntry($kalturaMediaId)
    {
        $metadataFilter = $this->kalturaServiceFactory->getKalturaMetadataFilter();
        $metadataFilter->objectIdEqual = $kalturaMediaId;
        $metadataPager = null;

        $metadataService = $this->kalturaServiceFactory->getKalturaMetadataService();
        $metadataResults = $metadataService->listAction($metadataFilter, $metadataPager);

        if (count($metadataResults->objects) <= 0) {
            return null;
        }

        // The Kaltura api will return this with an XML declaration for only some entries, unable to discern what triggers it
        $cleanedMetadata = str_replace('<?xml version="1.0"?>', "", $metadataResults->objects[0]->xml);

        return $cleanedMetadata;
    }
}
