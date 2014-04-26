<?php

namespace spec\Task\Plugin\Console;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Task\Plugin\Console\CommandRunner;

class ApplicationPluginSpec extends ObjectBehavior
{
    function let(Application $app, Command $command)
    {
        $app->get('test')->willReturn($command);
        $this->beConstructedWith($app);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Task\Plugin\Console\ApplicationPlugin');
    }

    function it_should_set_autoexit(Application $app)
    {
        $app->setAutoExit(false)->shouldBeCalled();
        $this->getApplication();
    }

    function it_should_create_a_command_runner(Application $app, Command $command)
    {
        $app->setAutoExit(false)->shouldBeCalled();
        $this->command('test')->shouldHaveType('Task\Plugin\Console\CommandRunner');
    }
        
    /*function it_should_use_a_custom_command_runner(Application $app, CommandRunner $runner)
    {
        $app->setAutoExit(false)->shouldBeCalled();
        $this->beConstructedWith($app, get_class($runner));
        $this->command('test')->shouldHaveType(get_class($runner));
    }*/
}
