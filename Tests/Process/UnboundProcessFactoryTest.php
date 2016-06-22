<?php

namespace CarnegieLearning\UnboundLdapBundle\Tests\Process;

use CarnegieLearning\UnboundLdapBundle\Process\JavaExecutableFinder;
use CarnegieLearning\UnboundLdapBundle\Process\ProcessBuilderFactory;
use CarnegieLearning\UnboundLdapBundle\Process\UnboundProcessFactory;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @backupGlobals enabled
 * @coversDefaultClass CarnegieLearning\UnboundLdapBundle\Process\UnboundProcessFactory
 * @uses CarnegieLearning\UnboundLdapBundle\Process\UnboundProcessFactory
 */
class UnboundProcessFactoryTest extends \PHPUnit_Framework_TestCase
{

    protected $builder;

    protected $finder;

    protected $fixture;

    protected $processFactory;

    public function setUp()
    {
        $this->finder = $this->createMock(JavaExecutableFinder::class);
        $this->builder = $this->createMock(ProcessBuilder::class);

        $processFactory = $this->createMock(ProcessBuilderFactory::class);
        $processFactory->expects($this->once())->method('create')->willReturn($this->builder);

        $this->fixture = new UnboundProcessFactory($this->finder, $processFactory);
    }

    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $this->finder->expects($this->once())->method('find')->willReturn('/path/to/java');
        $this->fixture->create();
    }

    /**
     * @covers ::getJavaRuntime
     *
     * @expectedException Symfony\Component\Process\Exception\RuntimeException
     * @expectedExceptionMessage Unable to find Java binary to run server.
     */
    public function testGetJavaRuntime_ThrowsExceptionWhenNotFound()
    {
        $this->finder->expects($this->once())
            ->method('find')
            ->willReturn(false);
        $this->fixture->create();
    }

    /**
     * @covers ::create
     * @covers ::getJavaRuntime
     */
    public function testCreate()
    {
        $this->finder->expects($this->once())
            ->method('find')
            ->willReturn('/path/to/java');

        $this->builder->expects($this->once())
            ->method('setPrefix')
            ->with($this->equalTo('/path/to/java'));

        $this->assertSame($this->builder, $this->fixture->create());
    }
}
