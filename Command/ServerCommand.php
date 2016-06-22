<?php

namespace CarnegieLearning\UnboundLdapBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

abstract class ServerCommand extends ContainerAwareCommand
{

    /**
     * @var UnboundProcessFactory
     */
    protected $processFactory;

    public function __construct(UnboundProcessFactory $processFactory = null)
    {
        parent::__construct();

        $this->processFactory = $processFactory ?: new UnboundProcessFactory;
    }


    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDefinition($this->createInputDefinition())
            ->setHelp(<<<'EOHELP'

OPTIONS

  <info>-p</info>, <info>--port</info> {port}
    If a value is specified, then it must be an integer between 1 and 65535.
    If no port is specified, then the server will automatically select a free
    port to use.

  <info>-b</info>, <info>--base-dn</info> {baseDN}
    Only entries at or below one of the defined base DNs may be created in
    the server. At least one base DN must be defined, but multiple base DNs
    may be specified as separate arguments.

  <info>-S</info>, <info>--use-schema-file</info> {path}
    If the path specified is a file, then it must be an LDIF file containing
    a single entry that is a valid LDAP subschema subentry. If the path
    specified is a directory, then any files contained in it will be examined
    in lexicographic order by name to create an aggregate schema.

EXAMPLES

  Creates an initially-empty directory server instance listening on an
  automatically-selected port that will allow entries below <comment>dc=example,dc=net</comment>
  and will not perform any schema validation.

    <comment>%command.name% --base-dn dc=example,dc=net</comment>

  Creates a directory server instance listening on port 1389 that is initially
  populated with the data from the test.ldif and will allow entries below
  <comment>dc=example,dc=net</comment>.

    <comment>%command.name% --base-dn dc=example,dc=net test.ldif</comment>

EOHELP
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        if (!class_exists('Symfony\Component\Process\Process')) {
            return false;
        }

        return parent::isEnabled();
    }

    /**
     * Determines the name of the lock file for a particular UnboundID server process.
     *
     * @param int $port The port the server should run on
     *
     * @return string The filename
     */
    protected function getLockFile($port)
    {
        return sys_get_temp_dir() . '/unboundid-' . $port . '.pid';
    }

    protected function isOtherServerProcessRunning($port)
    {
        $lockFile = $this->getLockFile($port);

        $fp = @fsockopen('localhost', $port, $errno, $errstr, 5);

        if (false !== $fp) {
            fclose($fp);

            return true;
        }

        return false;
    }

    protected function getCommandArguments(InputInterface $input)
    {
        $arguments = array();

        if ($ldifFile = $input->getArgument('ldif-file')) {
            $arguments[] = '--ldif-file';
            $arguments[] = $this->getRealPath($ldifFile);
        }

        if ($port = (int) $input->getOption('port')) {
            $arguments[] = '--port';
            $arguments[] = $port;
        }

        foreach ($input->getOption('base-dn') as $baseDn) {
            $arguments[] = '--base-dn';
            $arguments[] = $baseDn;
        }

        if ($input->getOption('use-default-schema')) {
            $arguments[] = '--use-default-schema';
        }

        if ($schemaFile = $input->getOption('use-schema-file')) {
            $arguments[] = '--use-schema-file';
            $arguments[] = $this->getRealPath($schemaFile);
        }

        return $arguments;
    }

    protected function getRealPath($path)
    {
        return realpath($path);
    }

    private function createInputDefinition()
    {
        return new InputDefinition(array(
            new InputArgument('ldif-file', InputArgument::OPTIONAL, 'Path to LDIF'),

            // Connectivity Arguments
            new InputOption('port', 'p', InputOption::VALUE_REQUIRED, 'The port on which the server should listen for client requests.'),

            // Data Arguments
            new InputOption('base-dn', 'b', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'The base DN to use for the server.', array('dc=example,dc=com')),
            new InputOption('use-default-schema', 's', InputOption::VALUE_NONE, 'Indicates that the server should use a default set of standard schema.'),
            new InputOption('use-schema-file', 'S', InputOption::VALUE_REQUIRED, 'The path to a file or directory containing schema definitions to use for the server.'),
        ));
    }
}