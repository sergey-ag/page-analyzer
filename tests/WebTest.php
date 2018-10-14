<?php

namespace App\Tests;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client;

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
        $doc = file_get_contents('tests/fixtures/testpage.html');
        $mock = new MockHandler([
            new Response(200, [], $doc)
        ]);
        $handler = HandlerStack::create($mock);
        app()->bind('Guzzle', function ($app) use ($handler) {
            return new Client(['handler' => $handler]);
        });
        $this->post('/domains', ['url' => 'www.example.com']);
        $this->seeInDatabase('domains', [
            'name' => 'www.example.com',
            'content_length' => strlen($doc),
            'response_code' => 200,
            'body' => $doc,
            'header' => 'Header',
            'keywords' => 'testing mocking stubing guzzle',
            'description' => 'Test webpage'
        ]);
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
