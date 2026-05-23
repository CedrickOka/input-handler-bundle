<?php

namespace Oka\InputHandlerBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class ErrorResponseFactory
{
    public function __construct(private RequestStack $requestStack, private SerializerInterface $serializer)
    {
    }

    public function create($error, int $statusCode, ?string $format = null, array $context = []): Response
    {
        $mimeType = null;

        /** @var \Symfony\Component\HttpFoundation\Request $request */
        if ($request = $this->requestStack->getCurrentRequest()) {
            if (null === $format) {
                $format = $request->getRequestFormat($request->getPreferredFormat());
            }

            $mimeType = $request->getMimeType($format);
        }

        return new Response(
            $this->serializer->serialize($error, $format ?? 'json', $context),
            $statusCode,
            ['Content-Type' => $mimeType ?? 'application\json'],
            true
        );
    }
}
