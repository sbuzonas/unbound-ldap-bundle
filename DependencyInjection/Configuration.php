<?php

namespace CarnegieLearning\UnboundLdapBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('cli_unbound_ldap');

        $rootNode
            ->children()
                ->arrayNode('server')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('port')
                            ->min(1)
                            ->max(65535)
                            ->defaultValue(6389)
                        ->end()
                        ->arrayNode('base_dn')
                            ->prototype('scalar')->end()
                        ->end()
                        ->scalarNode('ldif')->end()
                        ->scalarNode('schema')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
