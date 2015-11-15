<?php
namespace wcheng\KalturaEntriesToXML\Models\XML;

// Takes a Kaltura search results and outputs XML file formatted for Bulk Upload digestion
class KalturaXML
{
    protected $kalturaSideEntries;
    private $xmlFile;
    private $metadataProfileId;
    private $xmlStorageLocation;
    private $mNumEntries = 0;

    public function __construct(\wcheng\KalturaEntriesToXML\Models\Entries\KalturaSideEntries $kalturaSideEntries, $metadataProfileId, $xmlStorageLocation)
    {
        $this->kalturaSideEntries = $kalturaSideEntries;
        $this->metadataProfileId = $metadataProfileId;
        $this->xmlStorageLocation = $xmlStorageLocation;
    }

    public function getNumEntries()
    {
        return $this->mNumEntries;
    }

    public function getXML($results)
    {
        if ($results) {
            $this->xmlFile = '<?xml version="1.0" encoding="utf-8"?>' . "\r\n" . '<mrss xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="ingestion.xsd">' . "\r\n" . '<channel>' . "\r\n";

            foreach ($results->objects as $entry) {
                $this->mNumEntries++;

                $this->entryToXML($entry);
            }

            $this->xmlFile .= "</channel></mrss>";

            return $this->populateXML($this->xmlFile);
        }
    }

    private function entryToXML($entry)
    {
        $this->xmlFile .= $this->openTag('item');
        $this->xmlFile .= $this->createElement('action', 'update');
        $this->xmlFile .= $this->createElement('entryId', $entry->id);
        $this->xmlFile .= $this->createElement('userId', $entry->userId);
        $this->xmlFile .= $this->createElement('name', htmlspecialchars($entry->name));
        $this->xmlFile .= $this->createElement('description', htmlspecialchars($entry->description));

        $this->xmlFile .= $this->openTag('tags');
        $this->getEntryTags($entry->tags);
        $this->xmlFile .= $this->closeTag('tags');

        $this->xmlFile .= $this->openTag('categories');
        $this->getEntryCategories($entry->id);
        $this->xmlFile .= $this->closeTag('categories');

        $this->xmlFile .= $this->createElement('accessControlId', $entry->accessControlId);
        $this->xmlFile .= $this->createElement('conversionProfileId', $entry->conversionProfileId);

        // Metadata isn't stored within the entry so we'll have to defer to another service
        $this->xmlFile .= $this->openTag('customDataItems');
        $this->xmlFile .= $this->openTag('customData metadataProfileId="' . $this->metadataProfileId . '"');
        $metadata = $this->kalturaSideEntries->getMetadataForEntry($entry->id);
        $this->xmlFile .= $this->createElement('xmlData', $metadata);
        $this->xmlFile .= $this->closeTag('customData');
        $this->xmlFile .= $this->closeTag('customDataItems');

        $this->xmlFile .= $this->closeTag('item');
    }

    private function getEntryTags($tags)
    {
        $tagArray = explode(', ', $tags);

        foreach ($tagArray as $tag) {
            $this->xmlFile .= $this->createElement('tag', htmlspecialchars($tag));
        }
    }

    private function getEntryCategories($entryId)
    {
        $categoryArray = $this->kalturaSideEntries->getCategoriesForEntry($entryId);

        if ($categoryArray) {
            foreach ($categoryArray as $category) {
                $this->xmlFile .= $this->createElement('category', $category);
            }
        }
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

    private function populateXML($content)
    {
        $dateForFile = new \DateTime('now');
        $dateForFile = $dateForFile->format('Y-m-d-H-i-s');
        $filename = $this->xmlStorageLocation . $dateForFile . uniqid() . mt_rand() . '.xml';

        if (!file_exists($filename)) {
            touch($filename);
            file_put_contents($filename, $content);
        } else {
            file_put_contents($filename, $content);
        }

        return $filename;
    }
}
