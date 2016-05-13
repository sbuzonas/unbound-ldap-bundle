<?php
namespace CarnegieLearning\UnboundLdapBundle\Tests\Command;

use CarnegieLearning\UnboundLdapBundle\Command\UnboundServerRunCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;

class UnboundServerRunCommandTest extends \PHPUnit_Framework_TestCase
{


    public function testExecute()
    {
        $application = new Application(new \AppKernel('test', true));
        $application->add(new UnboundServerRunCommand('127.0.0.1', 6389, 'dc=example,dc=com', 'Tests/Fixtures/sample.ldif'));
        
        $command = $application->find('unbound:server:run');
        $commandTester = new CommandTester($command);
//        $commandTester->execute(['-f']);

    }
}
