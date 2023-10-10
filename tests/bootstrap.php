<?php

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (file_exists(dirname(__DIR__).'/config/bootstrap.php')) {
    require dirname(__DIR__).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}

if (filter_var($_ENV['RESET_TEST_DB'] ?? 'false', FILTER_VALIDATE_BOOL)) {
    $kernel = new Kernel(environment: 'test', debug: false);
    $kernel->boot();

    $cli = new Application($kernel);
    $cli->setCatchExceptions(false);
    $cli->setAutoExit(false);

    $cli->run(new ArrayInput([
        'command' => 'doctrine:database:drop',
        '--if-exists' => true,
        '--force' => true,
    ]));
    $cli->run(new ArrayInput([
        'command' => 'doctrine:database:create',
    ]));
    $cli->run(new ArrayInput([
        'command' => 'doctrine:migrations:migrate',
        '--no-interaction' => true,
    ]));
    $cli->run(new ArrayInput([
        'command' => 'doctrine:fixtures:load',
        '--append' => true,
    ]));

    $kernel->shutdown();
}
