<?php
namespace Oka\InputHandlerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 *
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 *
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('oka_input_handler');
        /** @var \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();
        
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('error_handler')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('override_problem_normalizer')->defaultTrue()->end()
                    ->end()
                ->end()
            ->end();
        
        return $treeBuilder;
    }
}
