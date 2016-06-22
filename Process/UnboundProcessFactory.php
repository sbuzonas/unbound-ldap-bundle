<?php

namespace CarnegieLearning\UnboundLdapBundle\Process;

use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\ProcessUtils;

class UnboundProcessFactory
{

    const UNBOUND_SERVER_CLASS = 'com.unboundid.ldap.listener.InMemoryDirectoryServerTool';

    /**
     * @var ExecutableFinder
     */
    private $executableFinder;

    /**
     * @var ProcessBuilderFactory
     */
    private $processBuilderFactory;

    public function __construct(
        JavaExecutableFinder  $executableFinder      = null,
        ProcessBuilderFactory $processBuilderFactory = null
    ) {
        $this->executableFinder      = $executableFinder      ?: new JavaExecutableFinder;
        $this->processBuilderFactory = $processBuilderFactory ?: new ProcessBuilderFactory;
    }

    public function create()
    {
        $builder = $this->processBuilderFactory->create();
        $builder->setPrefix($this->getJavaRuntime());
        $builder->setArguments([
            '-cp',
            realpath(__DIR__ . '/../Resources/unboundid/unboundid-ldapsdk-se.jar'),
            self::UNBOUND_SERVER_CLASS,
        ]);

        return $builder;
    }

    protected function getJavaRuntime()
    {
        if (false === $binary = $this->executableFinder->find()) {
            throw new RuntimeException('Unable to find Java binary to run server.');
        }

        return $binary;
    }
}
