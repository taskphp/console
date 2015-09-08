<?php

namespace spec\Task\Plugin\Console;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Task\Plugin\Console\Output\Output;
use Task\Plugin\Stream\WritableInterface;

class CommandRunnerSpec extends ObjectBehavior
{
    function it_is_initializable(Application $app, Command $command)
    {
        $this->setup($app, $command);
        $this->shouldHaveType('Task\Plugin\Console\CommandRunner');
    }

    function it_should_throw_on_no_command()
    {
        $app = new Application;
        $this->shouldThrow('InvalidArgumentException')->during('__construct', [$app, 'test']);
    }

    function it_should_run_a_command(Application $app, Command $command, OutputInterface $output)
    {
        $this->setup($app, $command);

        $input = new ArrayInput(['command' => 'test']);

        $app->run($input, $output)->shouldBeCalled();
        $this->run($output);
    }

    function it_should_run_a_command_with_arguments(Application $app, Command $command, InputDefinition $definition, OutputInterface $output)
    {
        $this->setup($app, $command, $definition);

        $definition->hasOption('wow')->willReturn(false);
        $definition->hasArgument('wow')->willReturn(true);

        $this->setWow(true);
        $input = new ArrayInput(['command' => 'test', 'wow' => true]);

        $app->run($input, $output)->shouldBeCalled();
        $this->run($output);
    }

    function it_should_run_a_command_with_options(Application $app, Command $command, InputDefinition $definition, OutputInterface $output)
    {
        $this->setup($app, $command, $definition);

        $definition->hasOption('foo-bar')->willReturn(true);

        $this->setFooBar('baz');
        $input = new ArrayInput(['command' => 'test', '--foo-bar' => 'baz']);

        $app->run($input, $output)->shouldBeCalled();
        $this->run($output);
    }

    function it_should_run_a_command_with_manually_set_parameters(Application $app, Command $command, InputDefinition $definition, OutputInterface $output)
    {
        $this->setup($app, $command, $definition);

        $this->setParameter('--foo', 'bar');
        $input = new ArrayInput(['command' => 'test', '--foo' => 'bar']);

        $app->run($input, $output)->shouldBeCalled();
        $this->run($output);
    }

    function it_should_throw_on_unknown_argument(Application $app, Command $command, InputDefinition $definition)
    {
        $this->setup($app, $command, $definition);

        $definition->hasArgument('foo')->willReturn(false);
        $definition->hasOption('foo')->willReturn(false);

        $this->shouldThrow('InvalidArgumentException')->duringSetFoo('bar');
    }

    function it_should_throw_on_unknown_method(Application $app, Command $command)
    {
        $this->setup($app, $command);
        $this->shouldThrow('InvalidArgumentException')->duringNope();
    }

    function it_should_read_buffered_output()
    {
        $app = new Application;
        $app->register('test')->setCode(function ($input, $output) {
            $output->write('foo');
        });

        $this->beConstructedWith($app, 'test');

        $this->read()->shouldReturn('foo');
    }

    function it_should_pipe_to_output(Output $output)
    {
        $app = new Application;
        $app->register('test')->setCode(function ($input, $output) {
            $output->write('foo');
        });

        $this->beConstructedWith($app, 'test');

        $output->write('foo')->shouldBeCalled();

        $this->pipe($output);
    }

    function it_should_pipe_to_output_proxy(WritableInterface $output)
    {
        $app = new Application;
        $app->register('test')->setCode(function ($input, $output) {
            $output->write('foo');
        });

        $this->beConstructedWith($app, 'test');

        $output->write('foo')->shouldBeCalled();

        $this->pipe($output);
    }

    private function setup($app, $command, $definition = null)
    {
        $app->setAutoExit(false)->shouldBeCalled();
        $app->get('test')->willReturn($command);
        $command->setApplication($app)->shouldBeCalled();
        $command->mergeApplicationDefinition()->shouldBeCalled();
        $command->getName()->willReturn('test');
        $command->getDefinition()->willReturn($definition);

        $this->beConstructedWith($app, 'test');
    }
}
