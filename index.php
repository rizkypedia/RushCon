<?php
//require 'App/Config/bootstrap.php';
require __DIR__ . '/App/Config/bootstrap.php';

RushCon\Core\Dispatcher::dispatch($argv);
