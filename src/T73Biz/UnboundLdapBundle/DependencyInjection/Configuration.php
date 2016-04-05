<?php

namespace T73Biz\UnboundLdapBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('t73_biz_unbound_ldap');

        $rootNode
            ->children()
                ->arrayNode('unbound_server')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('bind_address')->defaultValue('127.0.0.1')->end()
                        ->scalarNode('port')->defaultValue('6389')->end()
                        ->scalarNode('base_dn')->defaultValue('dc=example,dc=com')->end()
                        ->scalarNode('ldif')->defaultValue('@T73BizUnboundLdapBundle/Resources/ldap/sample.ldif')
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
