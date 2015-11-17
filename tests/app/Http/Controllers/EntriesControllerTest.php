<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;

class EntriesControllerTest extends TestCase
{
    use WithoutMiddleware;

    protected $mockKalturaEntries;
    protected $mockKalturaXML;

    public function setUp()
    {
        parent::setUp();

        $this->mockKalturaEntries = \Mockery::mock('wcheng\KalturaEntriesToXML\Models\Entries\KalturaEntries');
        $this->mockKalturaXML = \Mockery::mock('wcheng\KalturaEntriesToXML\Models\XML\KalturaXML');

        $this->setKalturaEntriesMethods();
        $this->setKalturaGetXML('wildlife.xml');

        $this->app->instance('wcheng\KalturaEntriesToXML\Models\Entries\KalturaEntries', $this->mockKalturaEntries);
        $this->app->instance('wcheng\KalturaEntriesToXML\Models\XML\KalturaXML', $this->mockKalturaXML);
    }

    public function testGetXMLEntriesWithResults()
    {
        $this->setKalturaGetNumEntries(20);
        $response = $this->postToGetEntries('Wild Life', 'kaltura-video-name');
        $this->assertContains('20 matching entries', $response->original);
        $this->assertContains('Wild Life', $response->original);

        $response = $this->postToGetEntries('Animals', 'kaltura-tags');
        $this->assertContains('Animals', $response->original);

        $response = $this->postToGetEntries('Business', 'kaltura-category');
        $this->assertContains('Business', $response->original);

        $response = $this->postToGetEntries('Freedom', 'kaltura-metadata-search');
        $this->assertContains('Freedom', $response->original);
    }

    public function testGetXMLEntriesWithNoResults()
    {
        $this->setKalturaGetNumEntries(0);

        $response = $this->postToGetEntries('No Results', 'kaltura-metadata-search');
        $this->assertContains('No results found', $response->original);
    }

    public function testGetXMLEntriesWithNoArguments()
    {
        $response = $this->postToGetEntries('', '');
        $this->assertContains('"hasSearchTerm":false', $response->original);
        $this->assertContains('"hasSelectMetadata":false', $response->original);
    }

    public function postToGetEntries($searchTerm, $searchCategory)
    {
        return $this->call('POST', '/getEntries', ['search-term' => $searchTerm, 'select-metadata' => $searchCategory]);
    }

    public function setKalturaEntriesMethods()
    {
        $this->mockKalturaEntries
            ->shouldReceive('getEntriesByName')
            ->andReturn(\Mockery::self())
            ->mock();

        $this->mockKalturaEntries
            ->shouldReceive('getEntriesByTags')
            ->andReturn(\Mockery::self())
            ->mock();

        $this->mockKalturaEntries
            ->shouldReceive('getEntriesByCategory')
            ->andReturn(\Mockery::self())
            ->mock();

        $this->mockKalturaEntries
            ->shouldReceive('getEntriesByMetadataCategory')
            ->andReturn(\Mockery::self())
            ->mock();
    }

    public function setKalturaGetXML($filename)
    {
        $this->mockKalturaXML
            ->shouldReceive('getXML')
            ->andReturn($filename)
            ->mock();
    }

    public function setKalturaGetNumEntries($int)
    {
        $this->mockKalturaXML
            ->shouldReceive('getNumEntries')
            ->andReturn($int)
            ->mock();
    }
}
