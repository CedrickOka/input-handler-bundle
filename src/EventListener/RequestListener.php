<?php

namespace Oka\InputHandlerBundle\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class RequestListener
{
    private $formats;

    public function __construct(array $formats = [])
    {
        $this->formats = $formats;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if (false === $event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

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
