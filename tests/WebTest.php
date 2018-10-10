<?php

namespace App\Tests;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class WebTest extends \App\Tests\TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        factory('App\Domain', 3)->create();
    }

    public function testGetIndex()
    {
        $this->get('/');
        $this->assertResponseOk();
    }

    public function testPostDomains()
    {
        $this->post('/domains', ['url' => 'www.example.com']);
        $this->seeInDatabase('domains', ['name' => 'www.example.com']);
    }

    public function testGetDomainsShow()
    {
        $this->get('/domains/1');
        $this->assertResponseOk();
    }
}
