<?php
namespace wcheng\KalturaEntriesToXML\Models\Entries;

class KalturaSideEntriesTest extends \PHPUnit_Framework_TestCase
{
    protected $mockServiceFactory;
    protected $kalturaSideEntries;
    protected $mockClient;
    protected $mockCategoryEntryFilter;
    protected $mockMetadataFilter;
    protected $mockMetadataService;
    protected $mockCategoryEntryService;
    protected $mockCategoryService;
    private $testEntryId = '1_abcdefg';

    protected function setUp()
    {
        // Mock Kaltura Service API
        $this->setMockServiceFactory();
        $this->setMockClient();
        $this->setMockCategoryEntryFilter();
        $this->setMockMetadataFilter();
        $this->setMockMetadataService();
        $this->setMockCategoryEntryService();
        $this->setMockCategoryService();

        $this->setMockServiceFactoryMethods();
        $this->setMockClientMethods();

        $this->kalturaSideEntries = new \wcheng\KalturaEntriesToXML\Models\Entries\KalturaSideEntries($this->mockServiceFactory);

    }

    public function testGetCategoriesForEntryArguments()
    {
        $callbackToTestArguments = function ($mockCategoryEntryFilter, $mockPager) {
            $this->assertEquals($this->testEntryId, $mockCategoryEntryFilter->entryIdEqual);
            $this->assertNull($mockPager);

            return $this->getEmptyResultsObject();
        };

        $this->setMockCategoryEntryServiceMethodsWithCallback($callbackToTestArguments);

        $this->kalturaSideEntries->getCategoriesForEntry($this->testEntryId);
    }

    public function testGetCategoriesForEntryResults()
    {
        $callbackToReturnMockCategoryIds = function () {
            return $this->getMockCategoryIds();
        };

        $this->setMockCategoryEntryServiceMethodsWithCallback($callbackToReturnMockCategoryIds);

        $callbackToReturnMockFullCategoryNames = function ($mockCategoryId) {
            return $this->getMockCategoryFullNames($mockCategoryId);
        };

        $this->setMockCategoryServiceMethodsWithCallback($callbackToReturnMockFullCategoryNames);

        $testMockCategoryArray = $this->kalturaSideEntries->getCategoriesForEntry($this->testEntryId);

        $this->assertEquals("Mock Category - 12345", $testMockCategoryArray[0]);
        $this->assertEquals("Mock Category - 67890", $testMockCategoryArray[1]);
    }

    public function testGetMetadataForEntryArguments()
    {
        $callbackToTestArguments = function ($mockMetadataFilter, $mockPager) {
            $this->assertEquals($this->testEntryId, $mockMetadataFilter->objectIdEqual);
            $this->assertNull($mockPager);

            return $this->getEmptyResultsObject();
        };

        $this->setMockMetadataServiceMethodsWithCallback($callbackToTestArguments);

        $this->kalturaSideEntries->getMetadataForEntry($this->testEntryId);
    }

    public function testGetMetadataForEntryResults()
    {
        $callbackToReturnMockMetadataForEntry = function () {
            return $this->getMockMetadataResults();
        };

        $this->setMockMetadataServiceMethodsWithCallback($callbackToReturnMockMetadataForEntry);

        $testMockMetadataResults = $this->kalturaSideEntries->getMetadataForEntry($this->testEntryId);

        $this->assertXmlStringEqualsXmlString('<fakeEntry></fakeEntry>', $testMockMetadataResults);
    }

    public function getEmptyResultsObject()
    {
        $emptyResultsObject = new \stdClass;
        $emptyResultsObject->objects = array();

        return $emptyResultsObject;
    }

    public function getMockCategoryIds()
    {
        $mockCategoryEntry1 = new \stdClass;
        $mockCategoryEntry2 = new \stdClass;

        $mockCategoryEntry1->categoryId = 12345;
        $mockCategoryEntry2->categoryId = 67890;

        $mockCategoryEntries = new \stdClass;
        $mockCategoryEntries->objects = array($mockCategoryEntry1, $mockCategoryEntry2);

        return $mockCategoryEntries;
    }

    public function getMockCategoryFullNames($mockCategoryId)
    {
        if ($mockCategoryId == 12345) {
            $mockCategoryName = 'Mock Category - 12345';
        } elseif ($mockCategoryId == 67890) {
            $mockCategoryName = 'Mock Category - 67890';
        }

        $mockCategoryNameResults = new \stdClass;
        $mockCategoryNameResults->fullName = $mockCategoryName;

        return $mockCategoryNameResults;
    }

    public function getMockMetadataResults()
    {
        $mockMetadataEntry = new \stdClass;
        $mockMetadataEntry->xml = '<?xml version="1.0"?><fakeEntry></fakeEntry>';

        $mockMetadataResults = new \stdClass;
        $mockMetadataResults->objects = array($mockMetadataEntry);

        return $mockMetadataResults;
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

        $this->mockServiceFactory->method('getKalturaCategoryEntryFilter')
            ->will($this->returnValue($this->mockCategoryEntryFilter));

        $this->mockServiceFactory->method('getKalturaMetadataFilter')
            ->will($this->returnValue($this->mockMetadataFilter));

        $this->mockServiceFactory->method('getKalturaMetadataService')
            ->will($this->returnValue($this->mockMetadataService));
    }

    public function setMockClient()
    {
        $this->mockClient = $this->getMockBuilder('\Kaltura\Client\Client')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function setMockClientMethods()
    {
        $this->mockClient->method('getCategoryEntryService')
            ->will($this->returnValue($this->mockCategoryEntryService));

        $this->mockClient->method('getCategoryService')
            ->will($this->returnValue($this->mockCategoryService));
    }

    public function setMockCategoryEntryFilter()
    {
        $this->mockCategoryEntryFilter = $this->getMockBuilder('\Kaltura\Client\Type\CategoryEntryFilter')
            ->getMock();
    }

    public function setMockMetadataFilter()
    {
        $this->mockMetadataFilter = $this->getMockBuilder('\Kaltura\Client\Plugin\Metadata\Type\MetadataFilter')
            ->getMock();
    }

    public function setMockMetadataService()
    {
        $this->mockMetadataService = $this->getMockBuilder('\Kaltura\Client\Plugin\Metadata\Service\MetadataService')
            ->getMock();
    }

    public function setMockMetadataServiceMethodsWithCallback($callbackFunction)
    {
        $this->mockMetadataService->method('listAction')
            ->will($this->returnCallback($callbackFunction));
    }

    public function setMockCategoryEntryService()
    {
        $this->mockCategoryEntryService = $this->getMockBuilder('\Kaltura\Client\Service\CategoryEntryService')
            ->getMock();
    }

    public function setMockCategoryEntryServiceMethodsWithCallback($callbackFunction)
    {
        $this->mockCategoryEntryService->method('listAction')
            ->will($this->returnCallback($callbackFunction));
    }

    public function setMockCategoryService()
    {
        $this->mockCategoryService = $this->getMockBuilder('\Kaltura\Client\Service\CategoryService')
            ->getMock();
    }

    public function setMockCategoryServiceMethodsWithCallback($callbackFunction)
    {
        $this->mockCategoryService->method('get')
            ->will($this->returnCallback($callbackFunction));
    }
}
