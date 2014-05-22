<?php

namespace Task\Plugin\Console\Output;

use Symfony\Component\Console\Output\Output as BaseOutput;
use Task\Plugin\Stream\WritableInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProxyOutput extends BaseOutput implements WritableInterface
{
    protected $target;

    public function setTarget($target)
    {
        if (!($target instanceof WritableInterface || $target instanceof OutputInterface)) {
            throw new \InvalidArgumentException('Unknown target type');
        }

        $this->target = $target;
        return $this;
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function doWrite($message, $newline)
    {
        $this->target->write($message . ($newline ? PHP_EOL : ''));
    }
}
