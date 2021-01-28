<?php

namespace Oka\InputHandlerBundle\Tests\Service;

use Oka\InputHandlerBundle\Service\ErrorResponseFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\ErrorHandler\Exception\FlattenException;

/**
 *
 * @author Cedrick Oka Baidai <cedric.baidai@veone.net>
 *
 */
class ErrorResponseFactoryTest extends WebTestCase
{
    /**
     * @covers
     */
    public function testCreate()
    {
        $kernel = static::bootKernel();
        /** @var ErrorResponseFactory $errorResponseFactory */
        $errorResponseFactory = $kernel->getContainer()->get('oka_input_handler.error_response.factory');
        $errorResponseFactory = static::$container->get('oka_input_handler.error_response.factory');
        $response = $errorResponseFactory->create(FlattenException::createFromThrowable(new \RuntimeException('Hello World!')), 400);
        $json = json_decode($response->getContent(), true);
        
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('Hello World!', $json['detail']);
        $this->assertEquals('application\json', $response->headers->get('Content-Type'));
    }
}
