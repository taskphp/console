<?php

namespace Task\Plugin\Console;

use Task\Plugin\Stream\ReadableInterface;
use Task\Plugin\Stream\WritableInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Task\Plugin\Console\Output\ProxyOutput;

class CommandRunner implements ReadableInterface
{
    protected $parameters = [];

    public function __construct(Application $app, $commandName)
    {
        $command = $this->findCommand($app, $commandName);

        $command->setApplication($app);
        $command->mergeApplicationDefinition();

        $app->setAutoExit(false);

        $this->command = $command->getName();
        $this->definition = $command->getDefinition();
        $this->app = $app;
    }

    /**
     * Should throw InvalidArgumentException if command not found.
     */
    public function findCommand(Application $app, $commandName)
    {
        return $app->get($commandName);
    }

    public function run(OutputInterface $output = null)
    {
        $input = new ArrayInput(array_merge([
            'command' => $this->command
        ], $this->getParameters()));
        return $this->app->run($input, $output);
    }

    public function __call($method, array $arguments = [])
    {
        if (strpos($method, 'set') !== 0) {
            throw new \InvalidArgumentException("Unknown method $method");
        }

        $alias = $this->parseMethodName(substr($method, 3));
        $value = $arguments[0];

        if ($this->definition->hasOption($alias)) {
            $this->parameters["--$alias"] = $value;
        } elseif ($this->definition->hasArgument($alias)) {
            $this->parameters[$alias] = $value;
        } else {
            throw new \InvalidArgumentException("Unrecognised parameter $alias");
        }

        return $this;
    }

    public function parseMethodName($name)
    {
        $parts = preg_split('/(?<=[a-z])(?![a-z])/', $name, -1, PREG_SPLIT_NO_EMPTY);
        return implode('-', array_map('strtolower', $parts));
    }

    public function read()
    {
        $output = new BufferedOutput;
        $this->run($output);
        return $output->fetch();
    }

    public function pipe(WritableInterface $to)
    {
        if ($to instanceof OutputInterface) {
            $this->run($to);
            return $to;
        } else {
            return $this->pipe((new ProxyOutput)->setTarget($to));
        }
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function setParameter($param, $value)
    {
        $this->parameters[$param] = $value;
        return $this;
    }

}
