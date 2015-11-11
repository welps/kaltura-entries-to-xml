<?php

namespace wcheng\KalturaEntriesToXML\XML;

// Takes a Kaltura search results and outputs XML file formatted for Bulk Upload digestion
class KalturaXML
{
    protected $kalturaServiceFactory;
    private $mClient;
    private $mNumEntries = 0;

    public function __construct(\wcheng\KalturaEntriesToXML\ServiceFactory\ServiceFactory $kalturaServiceFactory)
    {
        $this->kalturaServiceFactory = $kalturaServiceFactory;
        $this->mClient = $this->kalturaServiceFactory->getKalturaClient();
    }

    // Returns number of entries processed into XML
    public function getNumEntries()
    {
        return $this->mNumEntries;
    }

    public function getXML($results)
    {
        if ($results) {
            $item = '<?xml version="1.0" encoding="utf-8"?>' . "\r\n" . '<mrss xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="ingestion.xsd">' . "\r\n" . '<channel>' . "\r\n";

            foreach ($results->objects as $entry) {
                $this->mNumEntries++;

                $item .= $this->entryToXML($entry);
            }

            $item .= "</channel></mrss>";

            return $this->populateXML($item);
        }
    }

    private function entryToXML($entry)
    {
        $item = $this->openTag('item');
        $item .= $this->createElement('action', 'update');
        $item .= $this->createElement('entryId', $entry->id);
        $item .= $this->createElement('userId', $entry->userId);
        $item .= $this->createElement('name', htmlspecialchars($entry->name));
        $item .= $this->createElement('description', htmlspecialchars($entry->description));

        $item .= $this->openTag('tags');
        $item .= $this->getEntryTags($entry->tags);
        $item .= $this->closeTag('tags');

        $item .= $this->openTag('categories');
        $item .= $this->getEntryCategories($entry->id);
        $item .= $this->closeTag('categories');

        $item .= $this->createElement('accessControlId', $entry->accessControlId);
        $item .= $this->createElement('conversionProfileId', $entry->conversionProfileId);

        // Metadata isn't stored within the entry so we'll have to defer to another service
        $item .= $this->openTag('customDataItems');
        $item .= $this->openTag('customData metadataProfileId="27091"');
        $metadata = $this->getMetadataEntry($entry->id);
        $item .= $this->createElement('xmlData', $metadata);
        $item .= $this->closeTag('customData');
        $item .= $this->closeTag('customDataItems');

        $item .= $this->closeTag('item');

        return $item;
    }

    private function getEntryTags($tags)
    {
        $tagArray = explode(', ', $tags);
        $item = '';

        foreach ($tagArray as $tag) {
            $item .= $this->createElement('tag', htmlspecialchars($tag));
        }

        return $item;
    }

    private function getEntryCategories($entryId)
    {
        $categoryArray = $this->getCategoryName($entryId);
        $item = '';

        if ($categoryArray) {
            foreach ($categoryArray as $category) {
                $item .= $this->createElement('category', $category);
            }
        }

        return $item;
    }

    // Uses category service to return categories array if it exists
    private function getCategoryName($mediaId)
    {
        $filter = $this->kalturaServiceFactory->getKalturaCategoryEntryFilter();
        $filter->entryIdEqual = $mediaId;
        $pager = null;

        // Find category ids for media entry
        $categoryResults = $this->mClient->categoryEntry->listAction($filter, $pager);

        if (count($categoryResults->objects) <= 0) {
            return null;
        }

        // Crosslist IDs against category service and return array with full category names
        foreach ($categoryResults->objects as $categoryEntries) {
            $categoryListResults = $this->mClient->category->get($categoryEntries->categoryId);
            $categoryArray[] = htmlentities($categoryListResults->fullName);
        }

        return $categoryArray;
    }

    // Uses metadata service to return custom metadata if it exists
    private function getMetadataEntry($mediaId)
    {
        $metadataFilter = $this->kalturaServiceFactory->getKalturaMetadataFilter();
        $metadataFilter->objectIdEqual = $mediaId;
        $metadataPager = null;

        $metadataService = $this->kalturaServiceFactory->getKalturaMetadataService();
        $metadataResults = $metadataService->listAction($metadataFilter, $metadataPager);

        if (count($metadataResults->objects) <= 0) {
            return null;
        }

        // The Kaltura api will return this with an XML declaration for only some entries, unable to discern what triggers it
        $cleanedMetadata = str_replace('<?xml version="1.0"?>', "", $metadataResults->objects[0]->xml);

        // Return only XML contents for metadata for entry
        return $cleanedMetadata;
    }

    private function openTag($tag)
    {
        return '<' . $tag . ">\r\n";
    }

    private function closeTag($tag)
    {
        return '</' . $tag . ">\r\n";
    }

    private function createElement($tag, $contents)
    {
        return '<' . $tag . '>' . $contents . '</' . $tag . ">\r\n";
    }

    // Outputs XML to file for download
    private function populateXML($content)
    {
        $dateForFile = new \DateTime('now');
        $dateForFile = $dateForFile->format('Y-m-d-H-i-s');
        $filename = 'export/kalturaexport' . $dateForFile . '.xml';

        if (!file_exists($filename)) {
            touch($filename);
            file_put_contents($filename, $content);
        } else {
            file_put_contents($filename, $content);
        }

        return $filename;
    }
}
