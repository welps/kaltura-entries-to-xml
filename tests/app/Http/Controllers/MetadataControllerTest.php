<?php

class MetadataControllerTest extends TestCase
{
    protected $mockKalturaServiceFactory;
    protected $mockMetadataProfileService;
    protected $mockJson;

    public function setUp()
    {
        parent::setUp();

        $this->mockKalturaServiceFactory = \Mockery::mock('wcheng\KalturaEntriesToXML\Models\ServiceFactory\KalturaServiceFactory');
        $this->mockMetadataProfileService = \Mockery::mock('stdClass');

        $this->setKalturaServiceFactoryMethods();
        $this->setMetadataProfileServiceMethods();

        $this->mockJson['metadataFields'] = array('Fake Metadata Category 1', 'Fake Metadata Category 2');
    }

    public function testGetMetadataFields()
    {
        $this->app->instance('wcheng\KalturaEntriesToXML\Models\ServiceFactory\KalturaServiceFactory', $this->mockKalturaServiceFactory);

        $this->get('/getMetadataFields')
            ->seeJson($this->mockJson);
    }

    public function setKalturaServiceFactoryMethods()
    {
        $this->mockKalturaServiceFactory
            ->shouldReceive('getKalturaClient')
            ->andReturn(\Mockery::self())
            ->mock();

        $this->mockKalturaServiceFactory
            ->shouldReceive('getKalturaMetadataProfileService')
            ->andReturn(\Mockery::self())
            ->mock();
    }

    public function setMetadataProfileServiceMethods()
    {
        $this->mockMetadataProfileService
            ->shouldReceive('listFields')
            ->andReturn($this->getMockMetadataSearchResults())
            ->mock();
    }

    public function getMockMetadataSearchResults()
    {
        $results = \Mockery::mock('stdClass');

        $mockField1 = $this->getMockMetadataField('Fake Metadata Category 1');
        $mockField2 = $this->getMockMetadataField('Fake Metadata Category 2');

        $results->objects = array($mockField1, $mockField2);

        return $results;
    }

    public function getMockMetadataField($fieldName)
    {
        $metadataField = \Mockery::mock('stdClass');
        $metadataField->key = $fieldName;

        return $metadataField;
    }
}
