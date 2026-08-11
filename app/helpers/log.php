<?php

function logError(Throwable|string $error, string $file = 'errors'): void
{
    if ($error instanceof Throwable) {
        $message = sprintf(
            "[%s]\nFile: %s\nLine: %d\n\n%s",
            $error->getMessage(),
            $error->getFile(),
            $error->getLine(),
            $error->getTraceAsString()
        );
    } else {
        $message = $error;
    }

    $logDir = __DIR__ . '/../logs';

    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }

    $logFile = "{$logDir}/{$file}.log";

    $entry = sprintf(
        "[%s] %s%s",
        date('Y-m-d H:i:s'),
        $message,
        PHP_EOL
    );

    file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
}
