<?php

namespace CarnegieLearning\UnboundLdapBundle\Process;

use Symfony\Component\Process\ProcessBuilder;

class ProcessBuilderFactory
{

    public function create()
    {
        return new ProcessBuilder;
    }
}
