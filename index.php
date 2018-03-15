<?php

set_time_limit(0);
ini_set('max_execution_time', 0);

$incPath = __DIR__ . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR;

require $incPath . 'Demon.php';

// post curl sender
require $incPath . 'ISerner.php';
require $incPath . 'PostCurlSender.php';

// email notifier
require $incPath . 'INotifier.php';
require $incPath . 'EmailNotifier.php';

$d = new Demon(new PostCurlSender(), new EmailNotifier());

$d->run();

die('Done!' . PHP_EOL);

