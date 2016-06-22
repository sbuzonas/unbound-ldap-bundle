<?php

namespace CarnegieLearning\UnboundLdapBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class CarnegieLearningUnboundLdapExtension extends Extension
{

    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return 'http://www.carnegielearning.com/schema/dic/' . $this->getAlias();
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'cli_unbound_ldap';
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config['server'] as $k => $v) {
            $this->setContainerParameter($container, $k, $v);
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

    private function setContainerParameter(ContainerBuilder $container, $key, $value)
    {
        $param = sprintf('cli_unbound_ldap.%s', $key);
        $container->setParameter($param, $value);
    }
}
