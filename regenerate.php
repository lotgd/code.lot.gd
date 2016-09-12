<?php
require_once __DIR__ . '/vendor/autoload.php';

use Composer\Satis\Command\BuildCommand;
use Composer\Satis\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\StreamOutput;
use Github\Client as Github;

function regenerate_fn(GearmanJob $job) {
    echo "[worker] Received job: " . $job->handle() . "\n";

    // Pull newest satis.json file.
    echo "[worker] Downloading latest satis.json...\n";
    $github = new Github();
    $github->authenticate(getenv('GITHUB_TOKEN'), null, Github::AUTH_HTTP_TOKEN);
    $satisjson = $github->api('repo')->contents()->download('lotgd', 'code.lot.gd', 'satis.json');
    echo "[worker]   Got " . mb_strlen($satisjson, '8bit') . " bytes.\n";
    file_put_contents('satis.json', $satisjson);

    echo "[worker] Regenerating using Satis...\n";

    $application = new Application();
    $application->setAutoExit(false);

    $input = new ArrayInput(array(
        '--no-interaction' => 'true',
        'command' => 'build',
        'file' => 'satis.json',
        'output-dir' => './generated',
        '--skip-errors' => 'true'
    ));
    $output = new BufferedOutput();
    $ret = $application->run($input, $output);
    $content = $output->fetch();
    $line = strtok($content, "\n");
    while ($line !== false) {
        $line = strtok("\n");
        echo "[worker] " . $line . "\n";
    }

    echo "[worker] Satis return value: {$ret}\n";
    return $ret;
}
