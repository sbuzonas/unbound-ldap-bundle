<?php
namespace T73Biz\UnboundLdapBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use T73Biz\UnboundLdapBundle\Tests\Kernel\TestKernel;

class UnboundServerRunCommandTest extends \PHPUnit_Framework_TestCase
{
    use TestKernel;

    public function testExecute()
    {
        $application = new Application($this->kernel);

        $command = $application->find('unbound:server:run');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                'name'    => 'Fabien',
                '--yell'  => true,
            )
        );

        echo ($commandTester->getDisplay());

    }
}
