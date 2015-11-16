<?php
namespace wcheng\KalturaEntriesToXML\Models\ServiceFactory;

class KalturaServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $kalturaServiceFactory;
    protected $config;

    protected function setUp()
    {
        $this->config = parse_ini_file(dirname(__FILE__) . '/../../../config-tests.ini');
        $this->kalturaServiceFactory = new \wcheng\KalturaEntriesToXML\Models\ServiceFactory\KalturaServiceFactory($this->config);
    }

    public function testGetKalturaClient()
    {
        $client = $this->kalturaServiceFactory->getKalturaClient();

        $this->assertInstanceOf('\Kaltura\Client\Client', $client);
    }

    public function testGetKalturaConfiguration()
    {
        $kalturaConfiguration = $this->kalturaServiceFactory->getKalturaConfiguration();

        $this->assertInstanceOf('\Kaltura\Client\Configuration', $kalturaConfiguration);
    }

    public function testGetKalturaMetadataService()
    {
        $kalturaMetadataService = $this->kalturaServiceFactory->getKalturaMetadataService();

        $this->assertInstanceOf('\Kaltura\Client\Plugin\Metadata\Service\MetadataService', $kalturaMetadataService);
    }

    public function testGetKalturaMetadataProfileService()
    {
        $kalturaMetadataProfileService = $this->kalturaServiceFactory->getKalturaMetadataProfileService();

        $this->assertInstanceOf('\Kaltura\Client\Plugin\Metadata\Service\MetadataProfileService', $kalturaMetadataProfileService);
    }

    public function testGetKalturaFilterPager()
    {
        $kalturaFilterPager = $this->kalturaServiceFactory->getKalturaFilterPager();

        $this->assertInstanceOf('\Kaltura\Client\Type\FilterPager', $kalturaFilterPager);
    }

    public function testGetKalturaCategoryEntryFilter()
    {
        $kalturaCategoryEntryFilter = $this->kalturaServiceFactory->getKalturaCategoryEntryFilter();

        $this->assertInstanceOf('\Kaltura\Client\Type\CategoryEntryFilter', $kalturaCategoryEntryFilter);
    }

    public function testGetKalturaMediaEntryFilter()
    {
        $kalturaMediaEntryFilter = $this->kalturaServiceFactory->getKalturaMediaEntryFilter();

        $this->assertInstanceOf('\Kaltura\Client\Type\MediaEntryFilter', $kalturaMediaEntryFilter);
    }

    public function testGetKalturaMetadataSearchItem()
    {
        $kalturaMetadataSearchItem = $this->kalturaServiceFactory->getKalturaMetadataSearchItem();

        $this->assertInstanceOf('\Kaltura\Client\Plugin\Metadata\Type\MetadataSearchItem', $kalturaMetadataSearchItem);
    }

    public function testGetKalturaMetadataFilter()
    {
        $kalturaMetadataFilter = $this->kalturaServiceFactory->getKalturaMetadataFilter();

        $this->assertInstanceOf('\Kaltura\Client\Plugin\Metadata\Type\MetadataFilter', $kalturaMetadataFilter);
    }

    public function testGetKalturaSearchOperatorType()
    {
        $kalturaSearchOperatorType = $this->kalturaServiceFactory->getKalturaSearchOperatorType();

        $this->assertInstanceOf('\Kaltura\Client\Enum\SearchOperatorType', $kalturaSearchOperatorType);
    }

    public function testGetKalturaSearchCondition()
    {
        $kalturaSearchCondition = $this->kalturaServiceFactory->getKalturaSearchCondition();

        $this->assertInstanceOf('\Kaltura\Client\Type\SearchCondition', $kalturaSearchCondition);
    }
}
