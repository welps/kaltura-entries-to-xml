<?php
namespace wcheng\KalturaEntriesToXML\Models\ServiceFactory;

use \Kaltura\Client\Client as KalturaClient;
use \Kaltura\Client\Configuration as KalturaConfiguration;
use \Kaltura\Client\Enum\SearchOperatorType as KalturaSearchOperatorType;
use \Kaltura\Client\Enum\SessionType as KalturaSessionType;
use \Kaltura\Client\Plugin\Metadata\Service\MetadataProfileService as KalturaMetadataProfileService;
use \Kaltura\Client\Plugin\Metadata\Service\MetadataService as KalturaMetadataService;
use \Kaltura\Client\Plugin\Metadata\Type\MetadataFilter as KalturaMetadataFilter;
use \Kaltura\Client\Plugin\Metadata\Type\MetadataSearchItem as KalturaMetadataSearchItem;
use \Kaltura\Client\Type\CategoryEntryFilter as KalturaCategoryEntryFilter;
use \Kaltura\Client\Type\FilterPager as KalturaFilterPager;
use \Kaltura\Client\Type\MediaEntryFilter as KalturaMediaEntryFilter;
use \Kaltura\Client\Type\SearchCondition as KalturaSearchCondition;

class KalturaServiceFactory implements \wcheng\KalturaEntriesToXML\Models\ServiceFactory\ServiceFactory
{
    private $kalturaConfig;
    private $isAdmin = true;
    private $kalturaClient;

    public function __construct($kalturaConfig)
    {
        $this->kalturaConfig = $kalturaConfig;
    }

    public function getKalturaClient()
    {
        $kalturaConfiguration = $this->getKalturaConfiguration();
        $kalturaConfiguration->setServiceUrl($this->kalturaConfig['serviceUrl']);
        $kalturaConfiguration->setCurlTimeout(120);

        $this->kalturaClient = new KalturaClient($kalturaConfiguration);
        $sessionType = ($this->isAdmin) ? KalturaSessionType::ADMIN : KalturaSessionType::USER;

        $ks = $this->kalturaClient->generateSession($this->kalturaConfig['adminSecret'], $this->kalturaConfig['userId'], $sessionType, $this->kalturaConfig['partnerId']);
        $this->kalturaClient->setKs($ks);

        return $this->kalturaClient;
    }

    public function getKalturaConfiguration()
    {
        return new KalturaConfiguration();
    }

    public function getKalturaMetadataService()
    {
        return new KalturaMetadataService($this->kalturaClient);
    }

    public function getKalturaMetadataProfileService()
    {
        return new KalturaMetadataProfileService($this->kalturaClient);
    }

    public function getKalturaFilterPager()
    {
        return new KalturaFilterPager();
    }

    public function getKalturaCategoryEntryFilter()
    {
        return new KalturaCategoryEntryFilter();
    }

    public function getKalturaMediaEntryFilter()
    {
        return new KalturaMediaEntryFilter();
    }

    public function getKalturaMetadataSearchItem()
    {
        return new KalturaMetadataSearchItem();
    }

    public function getKalturaMetadataFilter()
    {
        return new KalturaMetadataFilter();
    }

    public function getKalturaSearchOperatorType()
    {
        return new KalturaSearchOperatorType();
    }

    public function getKalturaSearchCondition()
    {
        return new KalturaSearchCondition();
    }
}
