<?php

namespace App\Exceptions;

use Exception;

class Handler extends Exception
{
    public function register(): void
{
    $this->reportable(function (\Throwable $e) {
        // Write raw error to a simple file — bypasses Monolog entirely
        file_put_contents(
            storage_path('logs/raw-error.txt'),
            date('Y-m-d H:i:s') . ' ' . get_class($e) . ': ' . $e->getMessage()
            . ' in ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL,
            FILE_APPEND
        );
    });
}
}
