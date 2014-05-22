<?php

namespace spec\Task\Plugin\Console\Output;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Output\OutputInterface;
use Task\Plugin\Stream\WritableInterface;

class ProxyOutputSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Task\Plugin\Console\Output\ProxyOutput');
    }

    function it_should_accept_output_interface(OutputInterface $target)
    {
        $this->setTarget($target);
        $this->getTarget()->shouldReturn($target);
    }

    function it_should_accept_writable_interface(WritableInterface $target)
    {
        $this->setTarget($target);
        $this->getTarget()->shouldReturn($target);
    }

    function it_should_proxy_to_target(WritableInterface $target)
    {
        $target->write('foo')->shouldBeCalled();

        $this->setTarget($target);
        $this->doWrite('foo', false);
    }

    function it_should_proxy_to_target_with_newline(WritableInterface $target)
    {
        $target->write('foo'.PHP_EOL)->shouldBeCalled();

        $this->setTarget($target);
        $this->doWrite('foo', true);
    }
}
