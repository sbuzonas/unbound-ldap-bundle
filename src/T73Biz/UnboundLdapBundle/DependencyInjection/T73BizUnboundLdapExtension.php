<?php

namespace T73Biz\UnboundLdapBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class T73BizUnboundLdapExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('unbound_server.bind_address', $config['unbound_server']['bind_address']);
        $container->setParameter('unbound_server.port', $config['unbound_server']['port']);
        $container->setParameter('unbound_server.base_dn', $config['unbound_server']['base_dn']);
        $container->setParameter('unbound_server.ldif', $config['unbound_server']['ldif']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

}
