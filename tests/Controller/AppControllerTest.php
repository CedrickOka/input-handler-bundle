<?php
namespace Oka\InputHandlerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 *
 * @author Cedrick Oka Baidai <cedric.baidai@veone.net>
 *
 */
class AppControllerTest extends WebTestCase
{
    /**
     * @covers
     */
	public function testIndex()
	{
	    $client = static::createClient();
	    $client->request('POST', '/v1/rest', [], [], [
	        'CONTENT_TYPE' => 'application/json',
	    ], '{"email": "johndoe@exemple.com", "password": "johndoe@password"}');
		$content = json_decode($client->getResponse()->getContent(), true);
		
		$this->assertResponseStatusCodeSame(200);
		$this->assertResponseHeaderSame('Content-Type', 'application/json');
		$this->assertEquals('johndoe@exemple.com', $content['email']);
		$this->assertEquals('johndoe@password', $content['password']);
	}
	
	/**
	 * @covers
	 */
	public function testThatRequestIsNoAcceptable()
	{
	    $client = static::createClient();
	    $client->request('POST', '/v1/rest', [], [], [
	        'CONTENT_TYPE' => 'application/json',
	        'HTTP_Accept' => 'application/xml',
	    ], '{"email": "johndoe", "password": "johndoe@password"}');
	    
	    $this->assertResponseStatusCodeSame(406);
	    $this->assertResponseHeaderSame('Content-Type', 'text/xml; charset=UTF-8');
	}
	
	/**
	 * @covers
	 */
	public function testThatRequestIsNoValid()
	{
	    $client = static::createClient();
	    $client->request('POST', '/v1/rest', [], [], [
	        'CONTENT_TYPE' => 'application/json',
	    ], '{"email": "johndoe", "password": "johndoe@password"}');
	    $content = json_decode($client->getResponse()->getContent(), true);
	    
	    $this->assertResponseStatusCodeSame(400);
	    $this->assertResponseHeaderSame('Content-Type', 'application/json');
	    $this->assertEquals('[email]', $content['violations'][0]['propertyPath']);
	}
}
