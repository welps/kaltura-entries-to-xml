<?php

class IndexTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        $this->visit('/')
            ->see('Metadata');

        $this->call('GET', 'foobar');
        $this->assertResponseStatus(404);

    }
}
