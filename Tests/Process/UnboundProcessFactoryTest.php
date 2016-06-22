<?php

namespace CarnegieLearning\UnboundLdapBundle\Tests\Process;

use CarnegieLearning\UnboundLdapBundle\Process\JavaExecutableFinder;
use CarnegieLearning\UnboundLdapBundle\Process\UnboundProcessFactory;

/**
 * @backupGlobals enabled
 * @coversDefaultClass CarnegieLearning\UnboundLdapBundle\Process\UnboundProcessFactory
 */
class UnboundProcessFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers ::__construct
     * @uses CarnegieLearning\UnboundLdapBundle\Process\UnboundProcessFactory::create
     * @uses CarnegieLearning\UnboundLdapBundle\Process\UnboundProcessFactory::getJavaRuntime
     */
    public function testConstruct()
    {
        $finder = $this->createMock(JavaExecutableFinder::class);
        $finder->expects($this->once())->method('find')->willReturn('/path/to/java');

        $fixture = new UnboundProcessFactory($finder);
        $fixture->create();
    }

    /**
     * @covers ::getJavaRuntime
     * @uses CarnegieLearning\UnboundLdapBundle\Process\UnboundProcessFactory::__construct
     * @uses CarnegieLearning\UnboundLdapBundle\Process\UnboundProcessFactory::create
     *
     * @expectedException Symfony\Component\Process\Exception\RuntimeException
     * @expectedExceptionMessage Unable to find Java binary to run server.
     */
    public function testGetJavaRuntime_ThrowsExceptionWhenNotFound()
    {
        $finder = $this->createMock(JavaExecutableFinder::class);
        $finder->expects($this->once())->method('find')->willReturn(false);

        $fixture = new UnboundProcessFactory($finder);
        $fixture->create();
    }
}
