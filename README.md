# Command debug for php-graphene-node-client



## Install debugger
- copy files to project with GrapheneNodeClient
- install docker
- install docker-compose
- cmd `cd prodect_dir`
- cmd `docker-compose up -d` (to stop use `docker-compose stop`)

##  Remove debugger from project
- cmd `cd prodect_dir`
- cmd `docker-compose down`
- delete all debugger files from project

## Basic Usage
Make tasted command call in Debug\Test.php
```php
<?php


namespace GrapheneNodeClient\Debug;

use GrapheneNodeClient\Commands\CommandQueryData;
use GrapheneNodeClient\Commands\Login\GetApiByNameCommand;
use GrapheneNodeClient\Debug\Connectors\WebSocket\GolosWSConnector;
use GrapheneNodeClient\Debug\Connectors\WebSocket\SteemitWSConnector;
use GrapheneNodeClient\Debug\TestCommand;


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
define('PATH', __DIR__ . '/..');
require __DIR__ . "/Autoloader.php"; // only in GrapheneNodeClient project
require __DIR__ . '/../vendor/autoload.php';



$command = new GetApiByNameCommand(new SteemitWSConnector());
$commandQueryData = new CommandQueryData();
$commandQueryData->setParams(
    ['follow_api']
);
$command->execute(
    $commandQueryData
);

```
Or use Debug\TestCommand.php as Template for your tested commands
```php
<?php

// code as above
use GrapheneNodeClient\Debug\TestCommand;

$command = new TestCommand(new GolosWSConnector());
$commandQueryData = new CommandQueryData();
$commandQueryData->setParams(
    ['follow_api']
);
$command->execute(
    $commandQueryData
);

```

test from cmd `docker-compose exec --user www-data php-fpm bash -c "php Debug/Test.php"`