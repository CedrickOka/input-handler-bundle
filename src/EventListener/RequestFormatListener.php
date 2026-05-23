<?php

namespace Oka\InputHandlerBundle\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class RequestFormatListener
{
    public function __construct(private ?string $defaultFormat = null)
    {
    }

    public function onKernelException(ExceptionEvent $eventArgs)
    {
        if (false === $eventArgs->isMainRequest()) {
            return;
        }

        $request = $eventArgs->getRequest();

        if (null !== $request->getRequestFormat(null)) {
            return;
        }

        $request->setRequestFormat($this->defaultFormat);
    }
}
