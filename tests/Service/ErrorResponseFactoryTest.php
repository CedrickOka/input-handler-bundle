<?php

namespace Oka\InputHandlerBundle\Tests\Service;

use Oka\InputHandlerBundle\Service\ErrorResponseFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Cedrick Oka Baidai <cedric.baidai@veone.net>
 */
class ErrorResponseFactoryTest extends WebTestCase
{
    /**
     * @covers
     */
    public function testCreate()
    {
        static::bootKernel();

        /** @var ErrorResponseFactory $errorResponseFactory */
        $errorResponseFactory = static::$container->get('oka_input_handler.error_response.factory');

        $response = $errorResponseFactory->create(FlattenException::createFromThrowable(new \RuntimeException('Hello World!')), 400);
        $json = json_decode($response->getContent(), true);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('Hello World!', $json['detail']);
        $this->assertEquals('application\json', $response->headers->get('Content-Type'));
    }
}
