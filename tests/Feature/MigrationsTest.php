<?php

use Symfony\Component\Process\Process;

function runArtisan(string $database, string ...$arguments): Process
{
    $process = new Process(
        [PHP_BINARY, 'artisan', ...$arguments, '--force', '--no-interaction'],
        base_path(),
        ['DB_CONNECTION' => 'sqlite', 'DB_DATABASE' => $database, 'APP_ENV' => 'testing'],
    );

    return tap($process)->run();
}

it('rolls every migration back and applies them again', function () {
    $database = tempnam(sys_get_temp_dir(), 'migrations-').'.sqlite';
    touch($database);

    try {
        foreach (['migrate', 'migrate:reset', 'migrate'] as $command) {
            $process = runArtisan($database, $command);

            expect($process->isSuccessful())->toBeTrue("{$command} failed:\n".$process->getErrorOutput().$process->getOutput());
        }
    } finally {
        @unlink($database);
    }
});
