<?php

namespace CarnegieLearning\UnboundLdapBundle\Tests\Process;

use CarnegieLearning\UnboundLdapBundle\Process\JavaExecutableFinder;
use Symfony\Component\Process\ExecutableFinder;

/**
 * @backupGlobals enabled
 * @coversDefaultClass CarnegieLearning\UnboundLdapBundle\Process\JavaExecutableFinder
 */
class JavaExecutableFinderTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        putenv('UNBOUNDID_JAVA_HOME=/tmp/unbound-java-home');
        putenv('JAVA_HOME=/tmp/java-home');
        putenv('PATH=/tmp/one:/tmp/two');
    }

    /**
     * @covers ::__construct
     * @uses CarnegieLearning\UnboundLdapBundle\Process\JavaExecutableFinder::find
     */
    public function testConstruct()
    {
        $finder = $this->createMock(ExecutableFinder::class);
        $finder->expects($this->once())->method('find');

        $fixture = new JavaExecutableFinder($finder);
        $fixture->find();
    }

    /**
     * @covers ::find
     * @uses CarnegieLearning\UnboundLdapBundle\Process\JavaExecutableFinder::__construct
     */
    public function testFind_FallsBackToPath()
    {
        putenv('UNBOUNDID_JAVA_HOME');
        putenv('JAVA_HOME');

        $finder = $this->createMock(ExecutableFinder::class);
        $finder->expects($this->once())
            ->method('find')
            ->with(
                $this->equalTo('java'),
                $this->isFalse(),
                $this->equalTo(array(
                    '/tmp/one',
                    '/tmp/two',
                ))
            )
            ->willReturn('/path/to/java');

        $fixture = new JavaExecutableFinder($finder);

        $this->assertEquals('/path/to/java', $fixture->find());
    }

    /**
     * @covers ::find
     * @uses CarnegieLearning\UnboundLdapBundle\Process\JavaExecutableFinder::__construct
     */
    public function testFind_UsesJavaHome()
    {
        putenv('UNBOUNDID_JAVA_HOME');

        $finder = $this->createMock(ExecutableFinder::class);
        $finder->expects($this->once())
            ->method('find')
            ->with(
                $this->equalTo('java'),
                $this->isFalse(),
                $this->equalTo(array(
                    '/tmp/java-home/bin/java',
                ))
            )
            ->willReturn('/path/to/java');

        $fixture = new JavaExecutableFinder($finder);

        $this->assertEquals('/path/to/java', $fixture->find());
    }

    /**
     * @covers ::find
     * @uses CarnegieLearning\UnboundLdapBundle\Process\JavaExecutableFinder::__construct
     */
    public function testFind_UsesUnboundEnvFirst()
    {
        $finder = $this->createMock(ExecutableFinder::class);
        $finder->expects($this->once())
            ->method('find')
            ->with(
                $this->equalTo('java'),
                $this->isFalse(),
                $this->equalTo(array(
                    '/tmp/unbound-java-home/bin/java',
                ))
            )
            ->willReturn('/path/to/java');

        $fixture = new JavaExecutableFinder($finder);

        $this->assertEquals('/path/to/java', $fixture->find());
    }
}
