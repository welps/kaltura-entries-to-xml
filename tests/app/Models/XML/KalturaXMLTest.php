<?php
namespace wcheng\KalturaEntriesToXML\Models\XML;

class KalturaXMLTest extends \PHPUnit_Framework_TestCase
{
    protected $mockKalturaSideEntries;
    protected $kalturaXML;
    protected $kalturaTestConfig;
    protected $mockMetadataCategoryId = 12345;

    protected function setUp()
    {
        $this->mockKalturaSideEntries = $this->getMockBuilder('\wcheng\KalturaEntriesToXML\Models\Entries\KalturaSideEntries')
            ->disableOriginalConstructor()
            ->getMock();

        $this->setKalturaSideEntriesMethods();

        $this->kalturaXML = new KalturaXML($this->mockKalturaSideEntries, $this->mockMetadataCategoryId, '');
    }

    public function testGetXML()
    {
        $xmlFilename = $this->kalturaXML->getXML($this->getMockEntriesToTestXML());

        $this->assertXmlFileEqualsXmlFile($xmlFilename, __DIR__ . '/res/KalturaXMLTest-Example.xml');

        unlink($xmlFilename);
    }

    public function testGetsNumEntries()
    {
        $xmlFilename = $this->kalturaXML->getXML($this->getMockEntriesToTestXML());
        unlink($xmlFilename);

        $numEntriesShouldBe = count($this->getMockEntriesToTestXML()->objects);

        $this->assertEquals($numEntriesShouldBe, $this->kalturaXML->getNumEntries());
    }

    public function getMockEntriesToTestXML()
    {
        $mockEntry1 = $this->getMockEntry('2_abcdef', 'jdoe', 'Brooklyn', 'oh, no, my, rent');
        $mockEntry2 = $this->getMockEntry('1_abcdefg', 'csagan', 'Cosmos', 'intergalactic, space, aliens');
        $mockEntries = new \stdClass;
        $mockEntries->objects = array($mockEntry1, $mockEntry2);

        return $mockEntries;
    }

    public function getMockEntry($id, $userId, $name, $tags)
    {
        $mockEntry = new \stdClass;
        $mockEntry->id = $id;
        $mockEntry->userId = $userId;
        $mockEntry->name = $name;
        $mockEntry->description = 'Mock Description';
        $mockEntry->tags = $tags;
        $mockEntry->accessControlId = 12345;
        $mockEntry->conversionProfileId = 12345;

        return $mockEntry;
    }

    public function setKalturaSideEntriesMethods()
    {
        $this->mockKalturaSideEntries->method('getMetadataForEntry')
            ->will($this->returnValue($this->getMockMetadata()));

        $this->mockKalturaSideEntries->method('getCategoriesForEntry')
            ->will($this->returnValue($this->getMockCategories()));
    }

    public function getMockMetadata()
    {
        $mockMetadata = '<mockMetadata>Mock Data Item</mockMetadata>';

        return $mockMetadata;
    }

    public function getMockCategories()
    {
        $mockCategory1 = 'Mock Category 1 - 12345';
        $mockCategory2 = 'Mock Category 2 - 45678';
        $categoryArray = array($mockCategory1, $mockCategory2);

        return $categoryArray;
    }
}
