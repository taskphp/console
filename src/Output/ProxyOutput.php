<?php

namespace Task\Plugin\Console\Output;

use Symfony\Component\Console\Output\Output as BaseOutput;
use Task\Plugin\Stream\WritableInterface;

class ProxyOutput extends BaseOutput implements WritableInterface
{
    protected $target;

    public function setTarget(WritableInterface $target)
    {
        $this->target = $target;

        return $this;
    }

    public function doWrite($message, $newline)
    {
        $this->target->write($message . ($newline ? "\n" : ''));
    }
}
