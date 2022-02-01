<?php

namespace Oka\InputHandlerBundle\DependencyInjection;

use Oka\InputHandlerBundle\EventListener\RequestFormatListener;
use Oka\InputHandlerBundle\Normalizer\ProblemNormalizer;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class OkaInputHandlerExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.yml');

        $requestListenerDefinition = $container->getDefinition('oka_input_handler.request_listener');
        $requestListenerDefinition->replaceArgument(0, $config['request']['formats']);

        if (null !== $config['error_handler']['default_request_format']) {
            $requestFormatListenerDefinition = $container->setDefinition('oka_input_handler.request_format_listener', new Definition(RequestFormatListener::class, [$config['error_handler']['default_request_format']]));
            $requestFormatListenerDefinition->addTag('kernel.event_listener', ['event' => 'kernel.exception', 'method' => 'onKernelException', 'priority' => 255]);
        }

        if (true === $config['error_handler']['override_problem_normalizer']) {
            $problemNormalizerDefinition = $container->setDefinition('oka_input_handler.problem_normalizer', new Definition(ProblemNormalizer::class, [new Parameter('kernel.debug')]));
            $problemNormalizerDefinition->addTag('serializer.normalizer', ['priority' => 255]);
        }
    }
}
