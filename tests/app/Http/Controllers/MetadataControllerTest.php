<?php

class MetadataControllerTest extends TestCase
{
    public function testBasicExample()
    {
        $response = $this->action('GET', 'MetadataController@getMetadataFields');
    }
}
