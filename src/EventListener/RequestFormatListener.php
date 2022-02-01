<?php

namespace Oka\InputHandlerBundle\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class RequestFormatListener
{
    private $defaultFormat;

    public function __construct(string $defaultFormat)
    {
        $this->defaultFormat = $defaultFormat;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        if (false === $event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (null !== $request->getRequestFormat(null)) {
            return;
        }

        $request->setRequestFormat($this->defaultFormat);
    }
}
