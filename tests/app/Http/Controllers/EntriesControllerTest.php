<?php

class EntriesControllerTest extends TestCase
{
    public function testBasicExample()
    {
        $response = $this->action('POST', 'EntriesController@getXMLEntries');
    }
}
