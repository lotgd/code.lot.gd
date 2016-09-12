<?php
require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => '/var/www/html/logs/code-lotgd.log',
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
    $client = new GearmanClient();
    $client->addServer();
    $client->doBackground("regenerate", "don't care");

    return new Response('', 204);
});

$app->run();
