<?php

namespace CarnegieLearning\UnboundLdapBundle\Process;

use Symfony\Component\Process\ExecutableFinder;

class JavaExecutableFinder
{

    /**
     * @var ExecutableFinder
     */
    private $executableFinder;

    public function __construct(ExecutableFinder $executableFinder = null)
    {
        $this->executableFinder = $executableFinder ?: new ExecutableFinder;
    }

    public function find()
    {
        if ($unboundJavaHome = getenv('UNBOUNDID_JAVA_HOME')) {
            $dirs = array($unboundJavaHome . '/bin/java');
        } elseif ($javaHome = getenv('JAVA_HOME')) {
            $dirs = array($javaHome . '/bin/java');
        } else {
            $dirs = explode(PATH_SEPARATOR, getenv('PATH'));
        }

        return $this->executableFinder->find('java', false, $dirs);
    }
}
