task/console
============

[![Build Status](https://travis-ci.org/taskphp/console.svg?branch=master)](https://travis-ci.org/taskphp/console)
[![Coverage Status](https://coveralls.io/repos/taskphp/console/badge.png)](https://coveralls.io/r/taskphp/console)

Example
=======
```php
use Some\Application;
use Task\Plugin\Console\ApplicationPlugin;

$project->inject(function ($container) {
    $app = new Application;
    $container['app'] = new ApplicationPlugin($app)
});

$project->addTask('run', ['app', function ($app) {
    $app->command('run')
        ->setVerbose(true)
        ->pipe($this->getOutput());
}]);
```

Installation
============

Add to `composer.json`:
```json
...
"require-dev": {
    "task/console" "~0.2"
}
...
```

Usage
=====

`ApplicationPlugin::command()` returns a `CommandRunner` which dynamically builds up command arguments and options with setter methods.

Given the following `InputDefinition`:
```php
[
    new InputOption('option', 'o', InputOption::VALUE_REQUIRED),
    new InputOption('flag', 'f', InputOption::VALUE_NONE),
    new InputArgument('arg', InputArgument::REQUIRED)
]
```
```php
$project->addTask('run', ['app', function ($app) {
    $app->command('run')
        ->setOption('foo')
        ->setFlag(true)
        ->setArg('wow')
        ->pipe($this->getOutput());
}]);
