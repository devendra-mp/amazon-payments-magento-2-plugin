<?php

namespace Fixtures;

use Bex\Behat\Magento2InitExtension\Fixtures\BaseFixture;
use Symfony\Component\Process\Process;

class CommandExecutor extends BaseFixture
{
    public function runCommand($commandName)
    {
        $command = "php bin/magento {$commandName}";
        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \LogicException("Command {$command} failed with output: " . PHP_EOL . $process->getOutput());
        }
    }
}
