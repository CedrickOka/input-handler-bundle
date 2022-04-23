<?php

namespace Oka\InputHandlerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Cedrick Oka Baidai <cedric.baidai@veone.net>
 */
class AppControllerTest extends WebTestCase
{
    /**
     * @covers
     */
    public function testCheck()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/v1/rest/check',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
            '{"email": "johndoe@exemple.com", "password": "johndoe@password"}'
        );
        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertEquals('johndoe@exemple.com', $content['email']);
        $this->assertEquals('johndoe@password', $content['password']);
    }

    /**
     * @covers
     * @depends testCheck
     */
    public function testCreate()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/v1/rest/create',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            '{"email": "johndoe@exemple.com", "password": "johndoe@password"}'
        );
        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertEquals('johndoe@exemple.com', $content['email']);
        $this->assertEquals('johndoe@password', $content['password']);
    }

    /**
     * @covers
     * @depends testCreate
     */
    public function testUpdate()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/v1/rest/update',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'CONTENT_Accept' => 'application/json',
            ],
            '{"email": "johndoe@exemple.com", "password": "johndoe@password"}'
        );
        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertEquals('johndoe@exemple.com', $content['email']);
        $this->assertEquals('johndoe@password', $content['password']);
    }

    /**
     * @covers
     * @depends testUpdate
     */
    public function testThatRequestIsNoAcceptable()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/v1/rest/check',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/xml',
            ],
            '{"email": "johndoe@password.ci", "password": "johndoe@password"}'
        );

        $this->assertResponseStatusCodeSame(406);
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }

    /**
     * @covers
     * @depends testThatRequestIsNoAcceptable
     */
    public function testThatRequestIsNoValid()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/v1/rest/check',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
            '{"email": "johndoe", "password": "johndoe@password"}'
        );
        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(400);
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertEquals('[email]', $content['violations'][0]['propertyPath']);
    }

    /**
     * @covers
     * @depends testThatRequestIsNoValid
     */
    public function testThatProblemNormalizerIsOverride()
    {
        $client = static::createClient();
        $client->request('GET', '/v1/rest/check', [], [], ['HTTP_Accept' => 'application/json']);
        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(405);
        $this->assertEquals('Method Not Allowed', $content['title']);
    }
}
