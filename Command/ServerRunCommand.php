<?php

namespace CarnegieLearning\UnboundLdapBundle\Command;

use CarnegieLearning\UnboundLdapBundle\Process\UnboundProcessFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use RuntimeException;

class ServerRunCommand extends ServerCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('unbound:server:run')
            ->setDescription('Runs UnboundID in-memory LDAP server')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $cliOutput = $output);

        $factory = new UnboundProcessFactory;
        try {
            $builder = $factory->create();
        } catch (RuntimeException $e) {
            $io->error($e->getMessage());

            return 1;
        }

        if ($port = $input->getOption('port')) {
            if ($this->isOtherServerProcessRunning($port)) {
                $io->error(sprintf('A process is already listening on port %d.', $port));

                return 1;
            }
        }

        $io->success('Quit the server with CONTROL-C.');

        $arguments = $this->getCommandArguments($input);

        if (!$output->isQuiet()) {
            $arguments[] = '--accessLogToStandardOut';
        }

        if ($output->isVeryVerbose()) {
            $arguments[] = '--ldapDebugLogToStandardOut';
        }
        $builder->setTimeout(null);

        foreach ($arguments as $argument) {
            $builder->add($argument);
        }

        $process = $builder->getProcess();

        $process->run(function($type, $message) use ($cliOutput) {
            switch ($type) {
                case Process::OUT:
                    $cliOutput->writeln($message);
                    break;
                case Process::ERR:
                default:
                    $cliOutput->getErrorOutput()->writeln($message);
                    break;
            }
        });

        return $process->getExitCode();
    }
}
