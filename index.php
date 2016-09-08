<?php
require_once __DIR__ . '/vendor/autoload.php';

use Composer\Satis\Command\BuildCommand;
use Composer\Satis\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__ . '/logs/the.log',
));

$app->get('/', function () use ($app) {
    return $app->sendFile('./generated/index.html');
});

$app->get('/packages.json', function () use ($app) {
    return $app->sendFile('./generated/packages.json');
});

$app->get('/include/{filename}', function ($filename) use ($app) {
    $filepath = './generated/include/' . $filename;
    if (!file_exists($filepath)) {
        $app->abort(404);
    }

    return $app->sendFile($filepath);
});

$app->match('/github-hook', function () use ($app) {
    // Composer needs this to be set to operate.
    putenv('COMPOSER_HOME=' . __DIR__);

    $app['monolog']->addNotice("Regenerating using Satis...");

    $application = new Application();
    $application->setAutoExit(false);

    $input = new ArrayInput(array(
        '--no-interaction' => 'true',
        'command' => 'build',
        'file' => 'satis.json',
        'output-dir' => './generated',
        '--skip-errors' => 'true'
    ));
    $output = new StreamOutput(fopen('./logs/satis.log', 'a', false));
    $output->writeln('=== ' . date(DateTime::ISO8601));

    $ret = $application->run($input, $output);

    $app['monolog']->addNotice("Satis return value: {$ret}");

    return new Response('', 204);
});

$app->run();
