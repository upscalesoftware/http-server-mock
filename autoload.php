<?php

$autoloadDirChoices = [
    __DIR__ . '/../..',     // /path/to/project/vendor/upscale/http-server-mock/
    __DIR__ . '/vendor',    // /path/to/project/
];
foreach ($autoloadDirChoices as $autoloadDir) {
    $autoloadScript = $autoloadDir . '/autoload.php';
    if (file_exists($autoloadScript)) {
        return require $autoloadScript;
    }
}
throw new \RuntimeException('Installation is incomplete. Please run "composer install".');
