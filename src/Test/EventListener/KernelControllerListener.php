<?php

namespace Oka\InputHandlerBundle\Test\EventListener;

use Oka\InputHandlerBundle\Test\Model\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class KernelControllerListener implements EventSubscriberInterface
{
    public function onKernelController(ControllerEvent $event)
    {
        if (false === $event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $request->attributes->set('user', new User());
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
