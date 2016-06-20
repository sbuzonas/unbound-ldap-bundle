<?php
namespace CarnegieLearning\UnboundLdapBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

abstract class UnboundServerCommand extends Command implements ContainerAwareInterface
{
    /**
     * @var string The address to bind to.
     */
    protected $address;

    /**
     * @var int The port to listen on.
     */
    protected $port;

    /**
     * @var string The base DN for the LDAP server.
     */
    protected $baseDn;

    /**
     * @var string The path to the LDIF seed file.
     */
    protected $ldif;

    /**
     * @var ContainerInterface|null
     */
    private $container;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * UnboundServerCommand constructor.
     *
     * @param null|string $address
     * @param integer $port
     * @param string $baseDn
     * @param string $ldif
     */
    public function __construct($address, $port, $baseDn, $ldif)
    {
        $this->address = $address;
        $this->port = $port;
        $this->baseDn = $baseDn;
        $this->ldif = $ldif;
        $this->filesystem = new Filesystem();

        parent::__construct();
    }

    /**
     * @return ContainerInterface
     *
     * @throws \LogicException
     */
    protected function getContainer()
    {
        if (null === $this->container) {
            $application = $this->getApplication();

            if (null === $application) {
                throw new \LogicException('The container cannot be retrieved as the application instance is not yet set.');
            }

            $this->container = $application->getKernel()->getContainer();
        }

        return $this->container;
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
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
     * Determines the name of the lock file for LDAP server process.
     *
     * @return string The filename
     */
    protected function getLockFile()
    {
        return sprintf('%s/%s-%s.pid', sys_get_temp_dir(), $this->address, $this->port);
    }

    /**
     * This will create the pid file if it doesn't exist with the pid of the process written in it.
     *
     * @param integer $pid
     */
    protected function setLockFile($pid)
    {
        if (!$this->filesystem->exists($this->getLockFile())) {
            $this->filesystem->dumpFile($this->getLockFile(), $pid);
        }
    }

    protected function removeLockFile()
    {
        $this->filesystem->remove($this->getLockFile());
    }

    /**
     * @return string
     */
    protected function getLockFilePid()
    {
        ob_start();
        readfile($this->getLockFile());
        $pid = ob_get_contents();
        ob_end_clean();

        return $pid;
    }

    protected function killOtherServer()
    {
        $pid = $this->getLockFilePid();
        $this->removeLockFile();
        $kill = new Process('kill -9 ' . $pid);
        try {
            $kill->mustRun();
            echo $kill->getOutput();
        } catch (ProcessFailedException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * This determines if a previous version of the server is already running.
     *
     * @param $address
     *
     * @return bool
     */
    protected function isOtherServerProcessRunning($address)
    {
        if ($this->filesystem->exists($this->getLockFile())) {
            $kill = new Process('kill -0 ' . $this->getLockFilePid());
            $kill->run();

            return $kill->isSuccessful();
        }

        list($hostname, $port) = explode(':', $address);

        $fp = @fsockopen($hostname, $port, $errno, $errstr, 5);

        if (false !== $fp) {
            fclose($fp);

            return true;
        }

        return false;
    }
}
