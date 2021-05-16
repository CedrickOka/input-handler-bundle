<?php

namespace Oka\InputHandlerBundle\DependencyInjection;

use Oka\InputHandlerBundle\Serializer\Normalizer\ProblemNormalizer;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 *
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 *
 */
class OkaInputHandlerExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        
        if (true === $config['error_handler']['override_problem_normalizer']) {
            $problemNormalizerDefinition = $container->setDefinition(ProblemNormalizer::class, new Definition(ProblemNormalizer::class, [new Parameter('kernel.debug')]));
            $problemNormalizerDefinition->addTag('serializer.normalizer', ['priority' => 255]);
        }
    }
}
