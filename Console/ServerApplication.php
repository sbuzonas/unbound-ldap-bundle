<?php

namespace CarnegieLearning\UnboundLdapBundle\Console;

use CarnegieLearning\UnboundLdapBundle\Command\ServerRunCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;

class ServerApplication extends Application
{

    protected function getCommandName(InputInterface $input)
    {
        return 'unbound:server:run';
    }

    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();

        $defaultCommands[] = new ServerRunCommand;

        return $defaultCommands;
    }

    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();

        $inputDefinition->setArguments();

        return $inputDefinition;
    }
}
