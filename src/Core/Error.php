<?php

/**
 * Error and exception handler
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */

namespace Videna\Core;


class Error
{

    /**
     * Error handler. Convert all errors to Exceptions by throwing an ErrorException.
     *
     * @param int $level  Error level
     * @param string $message  Error message
     * @param string $file  Filename the error was raised in
     * @param int $line  Line number in the file
     * @return void
     */
    public static function errorHandler($level, $message, $file, $line)
    {
        if (error_reporting() !== 0) {  // to keep the @ operator working
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Exception handler.
     *
     * @param Exception $exception  The exception
     * @return void
     */
    public static function exceptionHandler($exception)
    {

        Log::add([
            'Uncaught exception code: ' . $exception->getCode(),
            'Description: ' . trim($exception->getMessage()),
            'Thrown in File: ' . $exception->getFile() . ', Line:' . $exception->getLine(),
            'Stack trace:',
            $exception->getTraceAsString()
        ], 'FATAL Error: ' . $exception->getMessage());
    }
}
