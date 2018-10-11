<?php

namespace App\Tests;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class WebTest extends \App\Tests\TestCase
{
    use DatabaseMigrations;

    private $domainsTestSet;

    public function setUp()
    {
        parent::setUp();
        $this->domainsTestSet = factory('App\Domain', 1)->create();
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
        $this->get("/domains/{$this->domainsTestSet->first()->id}");
        $this->assertResponseOk();
    }

    public function testGetDomains()
    {
        $this->seeInDatabase('domains', ['name' => $this->domainsTestSet->first()->name]);
        $this->get('/domains');
        $this->assertResponseOk();
    }
}
