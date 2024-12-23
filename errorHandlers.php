<?php
class CustomErrorHandler
{
    public static function exceptionHandler(Throwable $exception)
    {
        if (is_numeric($exception->getCode()) && $exception->getCode() >= 400 && $exception->getCode() <= 599)
            http_response_code($exception->getCode());
        else
            http_response_code(500);

        echo json_encode(['error' => $exception->getMessage(), 'line' => $exception->getLine(), 'file' => $exception->getFile()]);
    }
    public static function errorHandler(
        int $errno,
        string $errorStr,
        string $errfile = '',
        int $errorLine = 0,
        array $errcontext = []
    ): never {
        throw new ErrorException($errorStr, 0, $errno, $errfile, $errorLine);
    }
}
