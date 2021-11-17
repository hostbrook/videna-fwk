<?php

/**
 * Add debug info or exception of fatal errors info log file
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */

namespace Videna\Core;


abstract class Log
{

    /**
     * Put errors and debug info in log file
     * @param array $data Array with debug info
     * @param mixed $die The output message if stop script is required
     * @return void
     */
    public static function add($data = ['Break Point'], $die = FALSE)
    {

        $fp = fopen(PATH_APP_LOG, "a");

        $logLine = date("Y-m-d H:i:s") . ($die ? ' FATAL ERROR' : ' DEBUG/INFO') . "\r\n";
        foreach ($data as $error_descr) $logLine .= $error_descr . "\r\n";
        $logLine .= "\r\n";

        $result = fwrite($fp, $logLine);
        fclose($fp);

        if ($die) {

            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) and $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
                die(json_encode([
                    'response' => 500,
                    'status' => $die
                ]));
            }

            http_response_code(500);

            if (APP_DEBUG) die($die);
            die('A fatal error has occurred.');
        }
    }


    /**
     * Read log file
     * @param string $file Path to log file
     * @return array of strings of log file
     */
    public static function read($file = PATH_APP_LOG)
    {

        if (!is_file($file)) return false;

        $lines = file($file);

        foreach ($lines as $line_num => $line) $log[$line_num] = $line;

        return $log;
    }


    /**
     * Delete log file
     * @param string $file Path to log file
     * @return void
     */
    public static function delete($file = PATH_APP_LOG)
    {

        if (is_file($file)) {
            if (unlink($file)) {
                return 200;
            } else return 500;
        }

        return 404;
    }
}
