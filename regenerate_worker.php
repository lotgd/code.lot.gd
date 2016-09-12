<?php
require __DIR__ . '/regenerate.php';

$worker = new GearmanWorker();
$worker->addServer();
$worker->addFunction("regenerate", "regenerate_fn");

while (1) {
    print "[worker] Waiting for job...\n";
    $ret = $worker->work();
    if ($worker->returnCode() != GEARMAN_SUCCESS) {
        break;
    }
}
