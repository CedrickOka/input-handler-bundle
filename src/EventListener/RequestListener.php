<?php

namespace Oka\InputHandlerBundle\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class RequestListener
{
    public function __construct(private array $formats = [])
    {
    }

    public function onKernelRequest(RequestEvent $eventArgs)
    {
        if (false === $eventArgs->isMainRequest()) {
            return;
        }

        $request = $eventArgs->getRequest();

        foreach ($this->formats as $name => $value) {
            $request->setFormat($name, array_merge($request->getMimeTypes($name), $value['mime_types']));
        }

        $mimeTypes = $request->getMimeTypes('form');

        if (true === in_array('multipart/form-data', $mimeTypes)) {
            return;
        }

        $mimeTypes[] = 'multipart/form-data';
        $request->setFormat('form', $mimeTypes);
    }
}
