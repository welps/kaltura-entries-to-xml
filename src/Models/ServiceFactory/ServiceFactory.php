<?php
namespace wcheng\KalturaEntriesToXML\Models\ServiceFactory;

interface ServiceFactory
{
    public function getKalturaClient();
    public function getKalturaConfiguration();
    public function getKalturaMetadataService();
    public function getKalturaMetadataProfileService();
    public function getKalturaFilterPager();
    public function getKalturaCategoryEntryFilter();
    public function getKalturaMediaEntryFilter();
    public function getKalturaMetadataSearchItem();
    public function getKalturaMetadataFilter();
    public function getKalturaSearchOperatorType();
    public function getKalturaSearchCondition();
}
