<?php

namespace wcheng\KalturaEntriesToXML\Models\Entries;

class KalturaEntriesTest extends \PHPUnit_Framework_TestCase
{
    protected $mockServiceFactory;
    protected $kalturaEntries;
    protected $mockKalturaEntries;
    protected $mockClient;
    protected $mockMediaService;
    protected $mockPager;
    protected $mockFilter;
    protected $mockMetadataSearchItem;
    protected $mockSearchOperatorType;
    protected $mockSearchCondition;

    protected function setUp()
    {
        // Mock Kaltura Service API
        $this->setMockServiceFactory();
        $this->setMockClient();
        $this->setMockMediaService();
        $this->setMockFilter();
        $this->setMockPager();
        $this->setMockMetadataSearchItem();
        $this->setMockSearchOperatorType();
        $this->setMockSearchCondition();

        $this->setMockServiceFactoryMethods();
        $this->setMockClientMethods();

        // Mock Kaltura Entries so we can break apart components for further testing
        $this->mockKalturaEntries = $this->getMockBuilder('\wcheng\KalturaEntriesToXML\Models\Entries\KalturaEntries')
            ->setConstructorArgs(array($this->mockServiceFactory))
            ->setMethods(array('getAllMatchingEntries'))
            ->getMock();

        // Real Kaltura Entries so we can test getAllMatchingEntries method
        $this->kalturaEntries = new \wcheng\KalturaEntriesToXML\Models\Entries\KalturaEntries($this->mockServiceFactory);

        // Return the filter and pager for verification instead of pushing it through Kaltura's mock service
        $this->mockKalturaEntries->method('getAllMatchingEntries')
            ->will($this->returnCallback(
                function ($mockMediaEntryFilter, $mockFilterPager) {
                    return array($mockMediaEntryFilter, $mockFilterPager);
                }
            ));
    }

    public function testGetEntriesByName()
    {
        $arrayWithFilterAndPager = $this->mockKalturaEntries->getEntriesByName('Wildlife');

        $filterToTest = $arrayWithFilterAndPager[0];
        $pagerToTest = $arrayWithFilterAndPager[1];

        $this->assertEquals('Wildlife', $filterToTest->nameLike);
        $this->assertEquals(500, $pagerToTest->pageSize);
        $this->assertEquals(1, $pagerToTest->pageIndex);
    }

    public function testGetEntriesByCategory()
    {
        $arrayWithFilterAndPager = $this->mockKalturaEntries->getEntriesByCategory('Business');

        $filterToTest = $arrayWithFilterAndPager[0];
        $pagerToTest = $arrayWithFilterAndPager[1];

        $this->assertEquals('Business', $filterToTest->categoryAncestorIdIn);
        $this->assertEquals(500, $pagerToTest->pageSize);
        $this->assertEquals(1, $pagerToTest->pageIndex);
    }

    public function testGetEntriesByTags()
    {
        $arrayWithFilterAndPager = $this->mockKalturaEntries->getEntriesByTags('Art');

        $filterToTest = $arrayWithFilterAndPager[0];
        $pagerToTest = $arrayWithFilterAndPager[1];

        $this->assertEquals('Art', $filterToTest->tagsLike);
        $this->assertEquals(500, $pagerToTest->pageSize);
        $this->assertEquals(1, $pagerToTest->pageIndex);
    }

    public function testGetEntriesByMetadataCategory()
    {
        $arrayWithFilterAndPager = $this->mockKalturaEntries->getEntriesByMetadataCategory('Larry', 'Business', '555555');

        $filterToTest = $arrayWithFilterAndPager[0];
        $pagerToTest = $arrayWithFilterAndPager[1];

        $this->assertEquals('Larry', $filterToTest->advancedSearch->items[0]->value);
        $this->assertContains('Business', $filterToTest->advancedSearch->items[0]->field);
        $this->assertEquals(555555, $filterToTest->advancedSearch->metadataProfileId);
        $this->assertEquals(500, $pagerToTest->pageSize);
        $this->assertEquals(1, $pagerToTest->pageIndex);
    }

    public function testGetAllMatchingEntries()
    {
        $results = new \stdClass;
        $results->objects = array('Something to return');

        $this->mockMediaService->expects($this->at(0))
            ->method('listAction')
            ->will($this->returnValue($results));

        $emptyResults = new \stdClass;
        $emptyResults->objects = array();

        $this->mockMediaService->expects($this->at(1))
            ->method('listAction')
            ->will($this->returnValue($emptyResults));

        $resultsToTest = $this->kalturaEntries->getEntriesByName('Any search term');

        $this->assertEquals('Something to return', $resultsToTest->objects[0]);
    }

    public function setMockServiceFactory()
    {
        $this->mockServiceFactory = $this->getMockBuilder('\wcheng\KalturaEntriesToXML\Models\ServiceFactory\ServiceFactory')
            ->getMock();
    }

    public function setMockServiceFactoryMethods()
    {
        $this->mockServiceFactory->method('getKalturaClient')
            ->will($this->returnValue($this->mockClient));

        $this->mockServiceFactory->method('getKalturaFilterPager')
            ->will($this->returnValue($this->mockPager));

        $this->mockServiceFactory->method('getKalturaMediaEntryFilter')
            ->will($this->returnValue($this->mockFilter));

        $this->mockServiceFactory->method('getKalturaMetadataSearchItem')
            ->will($this->returnValue($this->mockMetadataSearchItem));

        $this->mockServiceFactory->method('getKalturaSearchOperatorType')
            ->will($this->returnValue($this->mockSearchOperatorType));

        $this->mockServiceFactory->method('getKalturaSearchCondition')
            ->will($this->returnValue($this->mockSearchCondition));

    }

    public function setMockClient()
    {
        $this->mockClient = $this->getMockBuilder('\Kaltura\Client\Client')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function setMockClientMethods()
    {
        $this->mockClient->method('getMediaService')
            ->will($this->returnValue($this->mockMediaService));
    }

    public function setMockMediaService()
    {
        $this->mockMediaService = $this->getMockBuilder('\Kaltura\Client\Service\MediaService')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function setMockFilter()
    {
        $this->mockFilter = $this->getMockBuilder('\Kaltura\Client\Type\MediaEntryFilter')
            ->getMock();
    }

    public function setMockPager()
    {
        $this->mockPager = $this->getMockBuilder('\Kaltura\Client\Type\FilterPager')
            ->getMock();
    }

    public function setMockMetadataSearchItem()
    {
        $this->mockMetadataSearchItem = $this->getMockBuilder('\Kaltura\Client\Plugin\Metadata\Type\MetadataSearchItem')
            ->getMock();
    }

    public function setMockSearchOperatorType()
    {
        $this->mockSearchOperatorType = $this->getMockBuilder('\Kaltura\Client\Enum\SearchOperatorType')
            ->getMock();
    }

    public function setMockSearchCondition()
    {
        $this->mockSearchCondition = $this->getMockBuilder('\Kaltura\Client\Type\SearchCondition')
            ->getMock();
    }
}
